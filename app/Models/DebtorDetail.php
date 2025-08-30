<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebtorDetail extends Model
{
    protected $fillable = [
        'debtor_id','input_date','loan_number','project_text','payer','pension','area','branch','submission_type',
        'address','kelurahan','kecamatan','kabupaten','provinsi',
        'interest_rate','baa_percent','agreement_date','start_credit_date','end_credit_date','disbursement_date',
        'provisi','administrasi','asuransi','tata_kelola','angsuran_dimuka',
        'birth_date','baa_value','total_installment','account_number','bank_alias',
    ];

    protected $casts = [
        'input_date'         => 'date',
        'agreement_date'     => 'date',
        'start_credit_date'  => 'date',
        'end_credit_date'    => 'date',
        'disbursement_date'  => 'date',
        'birth_date'         => 'date',
        'interest_rate'      => 'decimal:4',
        'baa_percent'        => 'decimal:4',
        'provisi'            => 'decimal:2',
        'administrasi'       => 'decimal:2',
        'asuransi'           => 'decimal:2',
        'tata_kelola'        => 'decimal:2',
        'angsuran_dimuka'    => 'decimal:2',
        'baa_value'          => 'decimal:2',
        'total_installment'  => 'decimal:2',
    ];

    public function debtor(): BelongsTo
    {
        return $this->belongsTo(Debtor::class);
    }
}
