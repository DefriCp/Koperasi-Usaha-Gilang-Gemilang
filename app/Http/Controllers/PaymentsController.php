<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Repayment;
use App\Models\DebtorDetail;
use App\Models\Debtor;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class PaymentsController extends Controller
{
    /** LIST + FILTER + SEARCH */
    public function index(Request $r)
    {
        $monthParam = $r->input('month', now()->month);   // 1..12 atau 'all'
        $year  = (int) ($r->input('year') ?: now()->year);
        $q     = trim((string) $r->input('q', ''));

        $repays = Repayment::with(['debtor.project'])
            ->when(!in_array(strtolower($monthParam), ['all','semua'], true), function ($w) use ($year, $monthParam) {
                $m = max(1, min(12, (int) $monthParam));
                $w->whereYear('period_date', $year)->whereMonth('period_date', $m);
            }, function ($w) use ($year) {
                $w->whereYear('period_date', $year);
            })
            ->when($q !== '', function ($w) use ($q) {
                $w->whereHas('debtor', function ($x) use ($q) {
                    $x->where('name', 'ILIKE', "%{$q}%")
                      ->orWhere('nopen','ILIKE', "%{$q}%");
                });
            })
            ->orderBy('period_date')
            ->get();

        $detailMap = DebtorDetail::select('debtor_id', 'loan_number', 'account_number')
            ->whereIn('debtor_id', $repays->pluck('debtor_id')->unique())
            ->orderBy('id')
            ->get()
            ->groupBy('debtor_id')
            ->map(fn ($c) => $c->last());

        $rows = $repays->map(function ($rp) use ($detailMap) {
            $d   = $rp->debtor;
            $det = $detailMap->get(optional($d)->id);
            $period = $rp->period_date instanceof \DateTimeInterface ? $rp->period_date : Carbon::parse($rp->period_date);

            $s = strtolower((string)$rp->status);
            if ($s === 'paid') $s = 'lunas';
            if (!in_array($s, ['lunas','dalam_proses','menunggak'])) $s = 'menunggak';

            return (object) [
                'id'             => $rp->id,
                'tgl_efekt'      => $period?->format('d/m/Y'),
                'batch'          => 'BATCH-'.$period?->format('Ymd').'1003',
                'kode_mitra'     => $det->loan_number    ?? '',
                'nama_mitra'     => optional(optional($d)->project)->name ?? '',
                'no_rekening'    => $det->account_number ?? '',
                'nama'           => $d->name ?? '—',
                'nopen'          => $d->nopen ?? '—',
                'nominal'        => (float)($rp->amount_due ?? 0),
                'status_key'     => $s,
                'tgl_debet_inp'  => $rp->paid_date ? Carbon::parse($rp->paid_date)->format('Y-m-d') : '',
                'keterangan_val' => (string)($rp->rejected_reason ?? ''),
            ];
        })->values();

        return view('payments.index', [
            'month'    => $monthParam,
            'year'     => $year,
            'q'        => $q,
            'rows'     => $rows,
        ]);
    }

    /** UPDATE manual oleh inputer/checker (status, tgl_debet, keterangan) */
    public function update(Request $r, Repayment $repayment)
    {
        // Kita sudah lindungi route dengan middleware role:inputer|checker.
        // Tambahan pengaman ringan:
        if (!$r->user()->hasAnyRole(['inputer','checker'])) {
            abort(403);
        }

        $data = $r->validate([
            'status'      => 'required|in:lunas,dalam_proses,menunggak',
            'paid_date'   => 'nullable|date',
            'keterangan'  => 'nullable|string|max:500',
        ]);

        $status = strtolower($data['status']);
        $save = [
            'status'          => $status === 'lunas' ? 'LUNAS' : ($status === 'dalam_proses' ? 'DALAM_PROSES' : 'MENUNGGAK'),
            'paid_date'       => $data['paid_date'] ?? null,
            'rejected_reason' => $data['keterangan'] ?? null, // dipakai sbg keterangan
        ];

        if ($status === 'lunas') {
            $save['amount_paid'] = $repayment->amount_due;
            if (empty($save['paid_date'])) $save['paid_date'] = now()->toDateString();
        }

        $repayment->update($save);

        $this->recalcDebtorFromRepayments($repayment->debtor_id);

        return back()->with('ok','Data pembayaran diperbarui.');
    }

    /** APPROVE pembayaran oleh CHECKER (fitur lama) */
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

        $this->recalcDebtorFromRepayments($repayment->debtor_id);

        return back()->with('ok','Pembayaran disetujui.');
    }

    /** REJECT pembayaran oleh CHECKER (fitur lama) */
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

        $this->recalcDebtorFromRepayments($repayment->debtor_id);

        return back()->with('ok','Pembayaran ditolak.');
    }

    /** Download Excel */
    public function export(Request $r): StreamedResponse
    {
        $monthParam = $r->input('month', now()->month);
        $year  = (int) ($r->input('year') ?: now()->year);
        $q     = trim((string) $r->input('q', ''));

        $repays = Repayment::with(['debtor.project'])
            ->when(!in_array(strtolower($monthParam), ['all','semua'], true), function ($w) use ($year, $monthParam) {
                $m = max(1, min(12, (int) $monthParam));
                $w->whereYear('period_date', $year)->whereMonth('period_date', $m);
            }, function ($w) use ($year) {
                $w->whereYear('period_date', $year);
            })
            ->when($q !== '', function ($w) use ($q) {
                $w->whereHas('debtor', function ($x) use ($q) {
                    $x->where('name', 'ILIKE', "%{$q}%")
                      ->orWhere('nopen','ILIKE', "%{$q}%");
                });
            })
            ->orderBy('period_date')
            ->get();

        $detailMap = DebtorDetail::select('debtor_id', 'loan_number', 'account_number')
            ->whereIn('debtor_id', $repays->pluck('debtor_id')->unique())
            ->orderBy('id')
            ->get()
            ->groupBy('debtor_id')
            ->map(fn ($c) => $c->last());

        $rows = $repays->map(function ($rp) use ($detailMap) {
            $d   = $rp->debtor;
            $det = $detailMap->get(optional($d)->id);
            $period = $rp->period_date instanceof \DateTimeInterface ? $rp->period_date : Carbon::parse($rp->period_date);

            return [
                'id'          => $rp->id,
                'tgl_efekt'   => $period?->format('d/m/Y'),
                'batch'       => 'BATCH-'.$period?->format('Ymd').'1003',
                'kode_mitra'  => $det->loan_number    ?? '',
                'nama_mitra'  => optional(optional($d)->project)->name ?? '',
                'no_rekening' => $det->account_number ?? '',
                'nama'        => $d->name ?? '—',
                'nopen'       => $d->nopen ?? '—',
                'nominal'     => (float)($rp->amount_due ?? 0),
                'status'      => (string)$rp->status,
                'tgl_debet'   => $rp->paid_date ? Carbon::parse($rp->paid_date)->format('d/m/Y') : '',
                'keterangan'  => (string)($rp->rejected_reason ?? ''),
            ];
        })->values();

        $ss    = new Spreadsheet();
        $sheet = $ss->getActiveSheet();
        $headers = ['id','tgl_efekt','batch','kode_mitra','nama_mitra','no_rekening','nama','nopen','nominal','status','tgl_debet','keterangan'];
        $sheet->fromArray([$headers], null, 'A1');

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

        $sheet->getStyle("I2:I".($row-1))->getNumberFormat()->setFormatCode('#,##0');
        foreach (range('A','L') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        $filename = "DATA_PEMBAYARAN_{$year}_".(in_array(strtolower($monthParam),['all','semua'])?'ALL':$monthParam).".xlsx";

        return new StreamedResponse(function () use ($ss) {
            (new Xlsx($ss))->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /** Recalc ringkasan Debtor dari Repayment (anggap LUNAS/PAID sebagai terbayar) */
    private function recalcDebtorFromRepayments(int $debtorId): void
    {
        $today = Carbon::now()->startOfDay();
        $rows = Repayment::where('debtor_id', $debtorId)->get(['period_date','amount_due','amount_paid','status']);

        if ($rows->isEmpty()) return;

        $outstanding = 0.0; $arrears = 0.0;
        foreach ($rows as $r) {
            $remain = max(0, (float)$r->amount_due - (float)$r->amount_paid);
            $outstanding += $remain;

            $st = strtolower((string)$r->status);
            $isPaid = ($st === 'lunas') || (strtoupper((string)$r->status) === 'PAID');
            if (!$isPaid && Carbon::parse($r->period_date)->lt($today)) {
                $arrears += $remain;
            }
        }

        Debtor::whereKey($debtorId)->update([
            'outstanding' => $outstanding,
            'arrears'     => $arrears,
        ]);
    }
}
