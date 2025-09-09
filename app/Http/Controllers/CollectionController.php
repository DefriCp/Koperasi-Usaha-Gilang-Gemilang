<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Repayment;
use App\Models\DebtorDetail;
use Carbon\Carbon;

class CollectionController extends Controller
{
    public function obligations(Request $r)
    {
        // Ambil input; dukung angka 1..12 atau string 'all'
        $year  = (int) ($r->input('year') ?: now()->year);
        $raw   = $r->input('month', now()->month); // bisa 'all' atau 1..12

        $month = null; // null = semua bulan
        if ($raw !== 'all') {
            $m = (int) $raw;
            $month = max(1, min(12, $m));
        }

        // Query repayment (filter tahun; bulan hanya kalau dipilih)
        $q = Repayment::query()
            ->with(['debtor.project'])
            ->whereYear('period_date', $year);

        if ($month !== null) {
            $q->whereMonth('period_date', $month);
        }

        $repays = $q->orderBy('period_date')->get([
            'id','debtor_id','period_date','amount_due','amount_paid','status'
        ]);

        // Ambil latest detail per debitur (rekening & loan number)
        $debtorIds = $repays->pluck('debtor_id')->filter()->unique()->values();
        $detailMap = DebtorDetail::query()
            ->select('debtor_id','loan_number','account_number')
            ->whereIn('debtor_id', $debtorIds)
            ->orderBy('id')
            ->get()
            ->groupBy('debtor_id')
            ->map(fn ($c) => $c->last()); // ambil baris terakhir

        // Bentuk rows untuk tabel
        $rows = $repays->map(function ($rp) use ($detailMap) {
            $d     = $rp->debtor;
            $det   = $d ? $detailMap->get($d->id) : null;
            $pDate = $rp->period_date ? Carbon::parse($rp->period_date) : null;

            return (object) [
                'period_date'    => $rp->period_date,
                'month_num'      => $pDate ? (int)$pDate->format('n') : 0,
                'month_name'     => $pDate ? $pDate->translatedFormat('F') : '—',
                'project_name'   => optional($d?->project)->name ?? '—',
                'loan_number'    => $det->loan_number    ?? '—',
                'account_number' => $det->account_number ?? '—',
                'debtor_name'    => $d->name  ?? '—',
                'installment'    => (float) ($rp->amount_due ?? 0),
                'nopen'          => $d->nopen ?? '—',
            ];
        });

        // Sorting: jika semua bulan → urut bulan → mitra → nama; jika 1 bulan → mitra → nama
        $rows = ($month === null)
            ? $rows->sortBy(fn($x) => sprintf('%02d|%s|%s', $x->month_num, $x->project_name, $x->debtor_name), SORT_NATURAL | SORT_FLAG_CASE)->values()
            : $rows->sortBy(fn($x) => sprintf('%s|%s', $x->project_name, $x->debtor_name), SORT_NATURAL | SORT_FLAG_CASE)->values();

        // Kirim ke view
        return view('collections.obligations', [
            'month' => $month === null ? 'all' : $month, // untuk state dropdown
            'year'  => $year,
            'rows'  => $rows,
        ]);
    }
}
