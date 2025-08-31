<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Repayment;
use App\Models\DebtorDetail;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentsController extends Controller
{
    public function index(Request $r)
    {
        $month = (int) ($r->input('month') ?: now()->month);
        $year  = (int) ($r->input('year')  ?: now()->year);

        $repays = Repayment::with(['debtor.project'])
            ->whereYear('period_date', $year)
            ->whereMonth('period_date', $month)
            ->orderBy('period_date')
            ->get();

        $detailMap = DebtorDetail::select('debtor_id', 'loan_number', 'account_number')
            ->whereIn('debtor_id', $repays->pluck('debtor_id')->unique())
            ->get()
            ->groupBy('debtor_id')
            ->map(fn ($c) => $c->last());

        $rows = $repays->map(function ($rp) use ($detailMap) {
            $d   = $rp->debtor;
            $det = $detailMap->get($d->id);

            return (object)[
                'id'          => $rp->id,
                'tgl_efekt'   => $rp->period_date?->format('d/m/Y'),
                'batch'       => 'BATCH-'.now()->format('YmdHis'),
                'kode_mitra'  => $det->loan_number    ?? '',
                'nama_mitra'  => optional($d->project)->name ?? '',
                'no_rekening' => $det->account_number ?? '',
                'nama'        => $d->name,
                'nopen'       => $d->nopen,
                'nominal'     => (float)$rp->amount_due,
                'status'      => $rp->status,
                'tgl_debet'   => optional($rp->paid_date)?->format('d/m/Y'),
                'keterangan'  => $rp->rejected_reason ?? '',
            ];
        })->values();

        return view('payments.index', [
            'month' => $month,
            'year'  => $year,
            'rows'  => $rows,
        ]);
    }

    /** APPROVE pembayaran oleh CHECKER */
    public function approve(Repayment $repayment, Request $request)
    {
        $amount = (float) ($request->input('amount_paid') ?: $repayment->amount_due);

        $repayment->update([
            'status'       => 'PAID',
            'amount_paid'  => $amount,
            'paid_date'    => now()->toDateString(),
            'approved_by'  => $request->user()->id,
            'approved_at'  => now(),
            'rejected_by'  => null,
            'rejected_at'  => null,
            'rejected_reason' => null,
        ]);

        return back()->with('ok','Pembayaran disetujui.');
    }

    /** REJECT pembayaran oleh CHECKER */
    public function reject(Repayment $repayment, Request $request)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);

        $repayment->update([
            'status'          => 'REJECTED',
            'rejected_by'     => $request->user()->id,
            'rejected_at'     => now(),
            'rejected_reason' => $request->reason,
            'approved_by'     => null,
            'approved_at'     => null,
        ]);

        return back()->with('ok','Pembayaran ditolak.');
    }

    /** Download Excel dengan header rapi */
    public function export(Request $r): StreamedResponse
    {
        $month = (int) ($r->input('month') ?: now()->month);
        $year  = (int) ($r->input('year')  ?: now()->year);

        $repays = Repayment::with(['debtor.project'])
            ->whereYear('period_date', $year)
            ->whereMonth('period_date', $month)
            ->orderBy('period_date')
            ->get();

        $detailMap = DebtorDetail::select('debtor_id', 'loan_number', 'account_number')
            ->whereIn('debtor_id', $repays->pluck('debtor_id')->unique())
            ->get()
            ->groupBy('debtor_id')
            ->map(fn ($c) => $c->last());

        $rows = $repays->map(function ($rp) use ($detailMap) {
            $d   = $rp->debtor;
            $det = $detailMap->get($d->id);

            return [
                'id'          => $rp->id,
                'tgl_efekt'   => $rp->period_date?->format('d/m/Y'),
                'batch'       => 'BATCH-'.now()->format('YmdHis'),
                'kode_mitra'  => $det->loan_number    ?? '',
                'nama_mitra'  => optional($d->project)->name ?? '',
                'no_rekening' => $det->account_number ?? '',
                'nama'        => $d->name,
                'nopen'       => $d->nopen,
                'nominal'     => (float)$rp->amount_due,
                'status'      => $rp->status,
                'tgl_debet'   => optional($rp->paid_date)?->format('d/m/Y'),
                'keterangan'  => $rp->rejected_reason ?? '',
            ];
        })->values();

        $ss    = new Spreadsheet();
        $sheet = $ss->getActiveSheet();
        $headers = ['id','tgl_efekt','batch','kode_mitra','nama_mitra','no_rekening','nama','nopen','nominal','status','tgl_debet','keterangan'];
        $sheet->fromArray([$headers], null, 'A1');

        // header style
        $sheet->getStyle('A1:L1')->getFont()->setBold(true);
        $sheet->getStyle('A1:L1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE5EEF7');
        $sheet->getStyle('A1:L1')->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $row = 2;
        foreach ($rows as $rdata) {
            $sheet->fromArray(array_values($rdata), '', 'A'.$row);
            $row++;
        }

        // format nominal
        $sheet->getStyle("I2:I".($row-1))->getNumberFormat()->setFormatCode('#,##0');
        foreach (range('A','L') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $filename = "DATA_PEMBAYARAN_{$year}_{$month}.xlsx";

        return new StreamedResponse(function () use ($ss) {
            (new Xlsx($ss))->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
