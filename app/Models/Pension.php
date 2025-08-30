<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pension extends Model
{
    protected $fillable = [
        'nip','name',
        'address_line1','address_line2','address_line3',
        'phone','phone_alt','ktp','birth_date','npwp',
        'branch_code','branch_name',
        'jenis_pensiun_code','jenis_pensiun_name',
        'kode_jiwa','nomor_skep','tmt_pensiun','tanggal_skep',
        'payer_code','payer_name','account_number',
        'penpok','tunj_istri','tunj_anak','tunj_beras',
        'penyesuaian','tunj_bulat','total_kotor','bersih',
        'extras',
    ];

    protected $casts = [
        'birth_date'   => 'date',
        'tmt_pensiun'  => 'date',
        'tanggal_skep' => 'date',
        'penpok'       => 'decimal:2',
        'tunj_istri'   => 'decimal:2',
        'tunj_anak'    => 'decimal:2',
        'tunj_beras'   => 'decimal:2',
        'penyesuaian'  => 'decimal:2',
        'tunj_bulat'   => 'decimal:2',
        'total_kotor'  => 'decimal:2',
        'bersih'       => 'decimal:2',
        'extras'       => 'array',
    ];
}
