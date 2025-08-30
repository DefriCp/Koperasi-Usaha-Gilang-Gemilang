<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Repayment extends Model
{
    protected $fillable = [
        'debtor_id',
        'period_date',
        'amount_due',
        'amount_paid',
        'paid_date',
        'status',            // UNPAID|PAID|REJECTED
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejected_reason',
    ];

    protected $casts = [
        'period_date' => 'date',
        'paid_date'   => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'amount_due'  => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    public function debtor(): BelongsTo
    {
        return $this->belongsTo(Debtor::class);
    }
}
