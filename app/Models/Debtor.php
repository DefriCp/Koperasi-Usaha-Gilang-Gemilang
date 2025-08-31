<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Debtor extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'nopen',
        'name',
        'plafond',
        'installment',
        'tenor',
        'installment_no',
        'akad_date',
        'outstanding',
        'arrears',
        'status',
        'approved_by',
        'approved_at',
        'import_batch', // untuk rollback import
    ];

    protected $casts = [
        'akad_date'   => 'date',
        'approved_at' => 'datetime',
        'plafond'     => 'decimal:2',
        'installment' => 'decimal:2',
        'outstanding' => 'decimal:2',
        'arrears'     => 'decimal:2',
    ];

    /** Relasi utama */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Cicilan (repayments) */
    public function repayments(): HasMany
    {
        return $this->hasMany(Repayment::class, 'debtor_id');
    }

    /** Semua detail (jika tabel debtor_details ada) */
    public function details(): HasMany
    {
        return $this->hasMany(\App\Models\DebtorDetail::class, 'debtor_id');
    }

    /** Satu detail terakhir (praktis dipakai kalau perlu yang terbaru saja) */
    public function latestDetail(): HasOne
    {
        // Ambil baris dengan id terbesar sebagai yang terbaru
        return $this->hasOne(\App\Models\DebtorDetail::class, 'debtor_id')->latestOfMany('id');
    }
}
