<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Repayment;
use App\Models\Debtor;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('checker')) {
            return redirect()->route('dashboard.checker');
        }
        if ($user->hasRole('inputer')) {
            return redirect()->route('dashboard.inputer');
        }
        if ($user->hasRole('viewer')) {
            return redirect()->route('dashboard.viewer');
        }

        return view('dashboard.blank');
    }

    public function viewer()
    {
        $now = now();

        $totalDebitur = Debtor::count();

        // Outstanding = sum(amount_due - amount_paid) seluruh rows (yang belum lunas)
        $outstanding = (float) Repayment::query()
            ->selectRaw('SUM(amount_due - amount_paid) as os')
            ->value('os');

        // Pembayaran bulan ini = sum(amount_paid) dengan paid_date bulan berjalan & status PAID
        $pembayaranBulanIni = (float) Repayment::query()
            ->where('status','PAID')
            ->whereNotNull('paid_date')
            ->whereMonth('paid_date', $now->month)
            ->whereYear('paid_date', $now->year)
            ->sum('amount_paid');

        // Kewajiban bulan ini
        $kewajibanBulanIni = (float) Repayment::query()
            ->whereMonth('period_date', $now->month)
            ->whereYear('period_date', $now->year)
            ->sum('amount_due');

        // Tunggakan = kewajiban bulan ini - pembayaran bulan ini (tidak minus)
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

        // Outstanding 
        $outstanding = (float) Repayment::query()
            ->selectRaw('SUM(amount_due - amount_paid) as os')
            ->value('os');

        // Pembayaran 
        $pembayaranBulanIni = (float) Repayment::query()
            ->where('status','PAID')
            ->whereNotNull('paid_date')
            ->whereMonth('paid_date', $now->month)
            ->whereYear('paid_date', $now->year)
            ->sum('amount_paid');

        // Kewajiban bulan ini
        $kewajibanBulanIni = (float) Repayment::query()
            ->whereMonth('period_date', $now->month)
            ->whereYear('period_date', $now->year)
            ->sum('amount_due');

        // Tunggakan = kewajiban bulan ini
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
