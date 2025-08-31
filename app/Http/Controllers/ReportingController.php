<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Debtor;
use App\Models\Project;
use App\Models\Repayment;
use Carbon\Carbon;

// PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date as XlsDate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class ReportingController extends Controller
{
    public function index()
    {
        return redirect()->route('reporting.outstanding');
    }

    /* ===================== OUTSTANDING ===================== */

    private function fetchOutstanding(array $filters)
    {
        $month   = $filters['month']   ?? null;
        $year    = (int)($filters['year'] ?? now()->year);
        $branch  = trim((string)($filters['branch']  ?? ''));
        $area    = trim((string)($filters['area']    ?? ''));
        $q       = trim((string)($filters['q']       ?? ''));
        $project = $filters['project'] ?? null;
        $quarter = $filters['quarter'] ?? null;

        $monthsForQuarter = [1=>[1,2,3], 2=>[4,5,6], 3=>[7,8,9], 4=>[10,11,12]];
        $quarterMonths = $quarter ? ($monthsForQuarter[$quarter] ?? []) : [];

        $query = Debtor::query()
            ->with(['project', 'details' => function($q){ $q->latest('id'); }])
            ->where('status','approved');

        if ($project) $query->where('project_id', $project);

        if ($q !== '') {
            $query->where(fn($x)=>$x->where('nopen','ilike',"%{$q}%")
                                    ->orWhere('name','ilike',"%{$q}%"));
        }

        if ($branch !== '' || $area !== '') {
            $query->whereHas('details', function($d) use ($branch,$area) {
                if ($branch !== '') $d->where('branch','ilike',"%{$branch}%");
                if ($area   !== '') $d->where('area','ilike',"%{$area}%");
            });
        }

        $query->where(function($wrap) use($year,$month,$quarterMonths){
            $wrap->whereHas('details', function($d) use($year,$month,$quarterMonths){
                $d->whereYear('start_credit_date', $year);
                if ($month) $d->whereMonth('start_credit_date', $month);
                elseif ($quarterMonths) $d->whereRaw('EXTRACT(MONTH FROM start_credit_date) IN ('.implode(',', $quarterMonths).')');
            })->orWhere(function($f) use($year,$month,$quarterMonths){
                $f->whereYear('akad_date', $year);
                if ($month) $f->whereMonth('akad_date', $month);
                elseif ($quarterMonths) $f->whereRaw('EXTRACT(MONTH FROM akad_date) IN ('.implode(',', $quarterMonths).')');
            });
        });

        return $query->orderBy('name')->get()->map(function ($d) {
            $det = optional($d->details)->first();
            return (object)[
                'nopen'       => (string)$d->nopen,
                'name'        => $d->name,
                'plafond'     => (float)$d->plafond,
                'outstanding' => (float)$d->outstanding,
                'start_date'  => $det->start_credit_date ?? $d->akad_date,
                'end_date'    => $det->end_credit_date ?? null,
                'branch'      => $det->branch ?? '',
                'area'        => $det->area ?? '',
                'project'     => $d->project?->name ?? '',
                'product'     => $det->submission_type ?? '',
                'payer'       => $det->payer ?? '',
            ];
        });
    }

    public function outstanding(Request $r)
    {
        $filters = [
            'month'   => $r->filled('month') ? (int)$r->month : null,
            'year'    => (int)$r->input('year', now()->year),
            'branch'  => $r->input('branch',''),
            'area'    => $r->input('area',''),
            'q'       => $r->input('q',''),
            'project' => $r->integer('project_id') ?: null,
            'quarter' => $r->integer('quarter') ?: null,
        ];

        $rows     = $this->fetchOutstanding($filters);
        $projects = Project::orderBy('name')->get(['id','name']);

        return view('reporting.outstanding', compact('rows','projects','filters'));
    }

    public function exportOutstanding(Request $r)
    {
        $filters = [
            'month'   => $r->filled('month') ? (int)$r->month : null,
            'year'    => (int)$r->input('year', now()->year),
            'branch'  => $r->input('branch',''),
            'area'    => $r->input('area',''),
            'q'       => $r->input('q',''),
            'project' => $r->integer('project_id') ?: null,
            'quarter' => $r->integer('quarter') ?: null,
        ];
        $rows = $this->fetchOutstanding($filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Outstanding');

        $headers = [
            'Nopen','Nama Debitur','Plafond','Outstanding',
            'Tgl Kredit','Tgl Lunas','Cabang','Area','Project','Product','Taspen/Asabri'
        ];
        $sheet->fromArray($headers, null, 'A1');

        $rnum = 2;
        foreach ($rows as $row) {
            $sheet->setCellValueExplicit("A{$rnum}", (string)$row->nopen, DataType::TYPE_STRING);
            $sheet->setCellValue("B{$rnum}", $row->name);
            $sheet->setCellValue("C{$rnum}", $row->plafond);
            $sheet->setCellValue("D{$rnum}", $row->outstanding);

            if ($row->start_date) $sheet->setCellValue("E{$rnum}", XlsDate::PHPToExcel(strtotime($row->start_date)));
            if ($row->end_date)   $sheet->setCellValue("F{$rnum}", XlsDate::PHPToExcel(strtotime($row->end_date)));

            $sheet->setCellValue("G{$rnum}", $row->branch);
            $sheet->setCellValue("H{$rnum}", $row->area);
            $sheet->setCellValue("I{$rnum}", $row->project);
            $sheet->setCellValue("J{$rnum}", $row->product);
            $sheet->setCellValue("K{$rnum}", $row->payer);

            $rnum++;
        }

        $lastRow = max(1, $rnum - 1);

        $sheet->getStyle("A1:K1")->getFont()->setBold(true);
        $sheet->getStyle("A1:K1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A1:K1")->getFill()->setFillType('solid')->getStartColor()->setRGB('F3F4F6');
        $sheet->getStyle("C2:D{$lastRow}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle("E2:F{$lastRow}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        $sheet->freezePane('A2');
        $sheet->setAutoFilter("A1:K1");
        foreach (range('A','K') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $monthPart = $filters['month'] ? '_'.str_pad($filters['month'],2,'0',STR_PAD_LEFT) : '';
        $filename  = 'report_outstanding_'.$filters['year'].$monthPart.'.xlsx';

        if (ob_get_length()) { ob_end_clean(); }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');
        (new Xlsx($spreadsheet))->save('php://output');
        exit;
    }

    /* ===================== MENUNGGAK ===================== */

    private function fetchArrears(array $filters)
    {
        $month   = $filters['month']   ?? null;
        $year    = (int)($filters['year'] ?? now()->year);
        $project = $filters['project'] ?? null;

        $asOf = $month
            ? Carbon::create($year, $month, 1)->endOfMonth()
            : Carbon::create($year, 12, 31)->endOfDay();

        $q = Debtor::query()
            ->with(['project','details' => function($q){ $q->latest('id'); }])
            ->where('status','approved');

        if ($project) $q->where('project_id', $project);

        $debtors = $q->get();

        $ids = $debtors->pluck('id')->all();
        $arrearsMap = collect();
        if (!empty($ids)) {
            $arrearsMap = Repayment::query()
                ->selectRaw('debtor_id, SUM(GREATEST(amount_due - amount_paid,0)) AS ar')
                ->whereIn('debtor_id', $ids)
                ->whereDate('period_date', '<=', $asOf->format('Y-m-d'))
                ->where('status','!=','PAID')
                ->groupBy('debtor_id')
                ->pluck('ar','debtor_id');
        }

        return $debtors->map(function($d) use ($arrearsMap) {
            $det = optional($d->details)->first();
            return (object)[
                'nopen'       => (string)$d->nopen,
                'name'        => $d->name,
                'plafond'     => (float)$d->plafond,
                'outstanding' => (float)$d->outstanding,
                'start_date'  => $det->start_credit_date ?? $d->akad_date,
                'end_date'    => $det->end_credit_date ?? null,
                'branch'      => $det->branch ?? '',
                'area'        => $det->area ?? '',
                'project'     => $d->project?->name ?? '',
                'product'     => $det->submission_type ?? '',
                'payer'       => $det->payer ?? '',
                'arrears'     => (float)($arrearsMap[$d->id] ?? 0.0),
            ];
        })->filter(fn($r) => $r->arrears > 0)->values();
    }

    public function arrears(Request $r)
    {
        $filters = [
            'month'   => $r->filled('month') ? (int)$r->month : null,
            'year'    => (int)$r->input('year', now()->year),
            'project' => $r->integer('project_id') ?: null,
        ];

        $rows     = $this->fetchArrears($filters);
        $projects = Project::orderBy('name')->get(['id','name']);

        return view('reporting.arrears', compact('rows','projects','filters'));
    }

    public function exportArrears(Request $r)
    {
        $filters = [
            'month'   => $r->filled('month') ? (int)$r->month : null,
            'year'    => (int)$r->input('year', now()->year),
            'project' => $r->integer('project_id') ?: null,
        ];
        $rows = $this->fetchArrears($filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Menunggak');

        $headers = [
            'Nopen','Nama Debitur','Plafond','Outstanding',
            'Tgl Kredit','Tgl Lunas','Cabang','Area','Project','Product','Taspen/Asabri','Tunggakan'
        ];
        $sheet->fromArray($headers, null, 'A1');

        $rnum = 2;
        foreach ($rows as $row) {
            $sheet->setCellValueExplicit("A{$rnum}", (string)$row->nopen, DataType::TYPE_STRING);
            $sheet->setCellValue("B{$rnum}", $row->name);
            $sheet->setCellValue("C{$rnum}", $row->plafond);
            $sheet->setCellValue("D{$rnum}", $row->outstanding);

            if ($row->start_date) $sheet->setCellValue("E{$rnum}", XlsDate::PHPToExcel(strtotime($row->start_date)));
            if ($row->end_date)   $sheet->setCellValue("F{$rnum}", XlsDate::PHPToExcel(strtotime($row->end_date)));

            $sheet->setCellValue("G{$rnum}", $row->branch);
            $sheet->setCellValue("H{$rnum}", $row->area);
            $sheet->setCellValue("I{$rnum}", $row->project);
            $sheet->setCellValue("J{$rnum}", $row->product);
            $sheet->setCellValue("K{$rnum}", $row->payer);
            $sheet->setCellValue("L{$rnum}", $row->arrears);

            $rnum++;
        }

        $lastRow = max(1, $rnum - 1);

        $sheet->getStyle("A1:L1")->getFont()->setBold(true);
        $sheet->getStyle("A1:L1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A1:L1")->getFill()->setFillType('solid')->getStartColor()->setRGB('F3F4F6');
        $sheet->getStyle("C2:D{$lastRow}")
              ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle("L2:L{$lastRow}")
              ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle("E2:F{$lastRow}")
              ->getNumberFormat()->setFormatCode('dd/mm/yyyy');
        $sheet->freezePane('A2');
        $sheet->setAutoFilter("A1:L1");
        foreach (range('A','L') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $monthPart = $filters['month'] ? '_'.str_pad($filters['month'],2,'0',STR_PAD_LEFT) : '';
        $filename  = 'report_menunggak_'.$filters['year'].$monthPart.'.xlsx';

        if (ob_get_length()) { ob_end_clean(); }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');
        (new Xlsx($spreadsheet))->save('php://output');
        exit;
    }
}
