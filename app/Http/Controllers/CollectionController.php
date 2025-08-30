<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Repayment;
use App\Models\DebtorDetail; 

class CollectionController extends Controller
{

    public function obligations(Request $r)
    {
        
        $month = (int) ($r->input('month') ?: now()->month);
        $year  = (int) ($r->input('year')  ?: now()->year);
        $month = max(1, min(12, $month));

       
        $repays = Repayment::query()
            ->with(['debtor.project'])
            ->whereYear('period_date', $year)
            ->whereMonth('period_date', $month)
            ->orderBy('period_date')
            ->get(['id','debtor_id','period_date','amount_due','amount_paid','status']);

        
        $debtorIds = $repays->pluck('debtor_id')->filter()->unique()->values();
        $detailMap = DebtorDetail::query()
            ->select('debtor_id','loan_number','account_number')
            ->whereIn('debtor_id', $debtorIds)
            ->orderBy('id')
            ->get()
            ->groupBy('debtor_id')
            ->map(fn($c) => $c->last()); 

       
        $rows = $repays->map(function ($rp) use ($detailMap) {
            $d   = $rp->debtor; 
            $det = $d ? $detailMap->get($d->id) : null;

            return (object) [
                'project_name'   => optional($d?->project)->name ?? '—',
                'loan_number'    => $det->loan_number    ?? '—', 
                'account_number' => $det->account_number ?? '—', 
                'debtor_name'    => $d->name  ?? '—',
                'installment'    => (float) ($rp->amount_due ?? 0),
                'nopen'          => $d->nopen ?? '—',
            ];
        })
     
        ->sortBy('debtor_name', SORT_NATURAL | SORT_FLAG_CASE)
        ->sortBy('project_name', SORT_NATURAL | SORT_FLAG_CASE)
        ->values();

        return view('collections.obligations', [
            'month' => $month,
            'year'  => $year,
            'rows'  => $rows,
        ]);
    }
}
