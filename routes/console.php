<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Debtor;
use App\Models\Repayment;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('repayments:rebuild {--id=*}', function () {
    $ids  = $this->option('id');
    $q    = Debtor::query()->where('status','approved');
    if (!empty($ids)) $q->whereIn('id', $ids);

    $done = 0;

    $q->orderBy('id')->chunk(200, function ($chunk) use (&$done) {
        foreach ($chunk as $d) {
            if (!$d->tenor || !$d->installment) continue;

            // anchor date: start_credit_date > disbursement_date > agreement_date > akad_date
            $anchor = $d->akad_date;
            if (method_exists($d,'details')) {
                $det = $d->details()->latest('id')->first();
                if ($det) $anchor = $det->start_credit_date ?? $det->disbursement_date ?? $det->agreement_date ?? $anchor;
            }
            if (!$anchor) continue;

            $anchor = Carbon::parse($anchor);
            $tenor  = (int) $d->tenor;
            $paidNo = max(0, (int) $d->installment_no);
            $amount = (float) $d->installment;

            for ($i=$paidNo; $i<$tenor; $i++) {
                $period = $anchor->copy()->addMonthsNoOverflow($i)->format('Y-m-d');
                $ex = Repayment::where('debtor_id',$d->id)->whereDate('period_date',$period)->first();
                if (!$ex) {
                    Repayment::create([
                        'debtor_id'=>$d->id,'period_date'=>$period,'amount_due'=>$amount,'amount_paid'=>0,'status'=>'UNPAID',
                    ]);
                } elseif ($ex->amount_due != $amount) {
                    $ex->update(['amount_due'=>$amount]);
                }
            }

            // Recalc summary
            $today = Carbon::now()->startOfDay();
            $rows = Repayment::where('debtor_id',$d->id)->get(['period_date','amount_due','amount_paid','status']);
            $out=0.0; $arr=0.0;
            foreach ($rows as $r) {
                $remain = max(0, (float)$r->amount_due - (float)$r->amount_paid);
                $out += $remain;
                if ($r->status !== 'PAID' && Carbon::parse($r->period_date)->lt($today)) $arr += $remain;
            }
            $d->update(['outstanding'=>$out,'arrears'=>$arr]);

            $done++;
        }
    });

    $this->info("Done. Processed {$done} debtor(s).");
})->purpose('Bangun ulang repayment schedule untuk debitur approved (optional: --id=1 --id=2).');
