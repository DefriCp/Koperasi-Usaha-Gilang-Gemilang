<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Debtor extends Model
{
    protected $fillable = [
        'project_id','user_id','nopen','name','plafond',
        'installment','tenor','installment_no','akad_date',
        'outstanding','arrears','status','approved_by','approved_at',
        'import_batch', // <-- untuk rollback import
    ];

    protected $casts = [
        'akad_date'   => 'date',
        'approved_at' => 'datetime',
        'plafond'     => 'decimal:2',
        'installment' => 'decimal:2',
        'outstanding' => 'decimal:2',
        'arrears'     => 'decimal:2',
    ];

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function repayments(): HasMany { return $this->hasMany(Repayment::class); }
    public function latestDetail()
    {
    // butuh tabel debtor_details; akan mengambil baris terakhir (id terbesar)
    return $this->hasOne(\App\Models\DebtorDetail::class)->latestOfMany();
    }
    // opsional: jika ada table details
    public function details(): HasMany { return $this->hasMany(\App\Models\DebtorDetail::class); }
}
