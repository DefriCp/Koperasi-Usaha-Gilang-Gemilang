<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Repayment;
use App\Models\Debtor;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('checker')) return redirect()->route('dashboard.checker');
        if ($user->hasRole('inputer')) return redirect()->route('dashboard.inputer');
        if ($user->hasRole('viewer'))  return redirect()->route('dashboard.viewer');

        return view('dashboard.blank');
    }

    public function viewer()
    {
        $now = now();

        $totalDebitur = Debtor::count();

        $outstanding = (float) Repayment::query()
            ->selectRaw('SUM(GREATEST(COALESCE(amount_due,0) - COALESCE(amount_paid,0),0)) as os')
            ->value('os');

        // Pembayaran bulan ini: status PAID atau LUNAS
        $pembayaranBulanIni = (float) Repayment::query()
            ->whereNotNull('paid_date')
            ->whereMonth('paid_date', $now->month)
            ->whereYear('paid_date', $now->year)
            ->where(function($q){
                $q->where('status','PAID')->orWhere('status','LUNAS');
            })
            ->sum('amount_paid');

        $kewajibanBulanIni = (float) Repayment::query()
            ->whereMonth('period_date', $now->month)
            ->whereYear('period_date', $now->year)
            ->sum('amount_due');

        $tunggakan = max(0, $kewajibanBulanIni - $pembayaranBulanIni);

        $stats = [
            'debitors'    => $totalDebitur,
            'outstanding' => $outstanding,
            'paid_month'  => $pembayaranBulanIni,
            'arrears'     => $tunggakan,
        ];

        return view('dashboard.viewer', compact('stats'));
    }

    public function checker()
    {
        $now = now();

        $totalDebitur = Debtor::count();

        $outstanding = (float) Repayment::query()
            ->selectRaw('SUM(GREATEST(COALESCE(amount_due,0) - COALESCE(amount_paid,0),0)) as os')
            ->value('os');

        $pembayaranBulanIni = (float) Repayment::query()
            ->whereNotNull('paid_date')
            ->whereMonth('paid_date', $now->month)
            ->whereYear('paid_date', $now->year)
            ->where(function($q){
                $q->where('status','PAID')->orWhere('status','LUNAS');
            })
            ->sum('amount_paid');

        $kewajibanBulanIni = (float) Repayment::query()
            ->whereMonth('period_date', $now->month)
            ->whereYear('period_date', $now->year)
            ->sum('amount_due');

        $tunggakan = max(0, $kewajibanBulanIni - $pembayaranBulanIni);

        $stats = [
            'debitors'    => $totalDebitur,
            'outstanding' => $outstanding,
            'paid_month'  => $pembayaranBulanIni,
            'arrears'     => $tunggakan,
        ];

        return view('dashboard.checker', compact('stats'));
    }

    public function inputer()
    {
        $activities = [];
        return view('dashboard.inputer', compact('activities'));
    }
}
