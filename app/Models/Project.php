<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    // Sesuaikan dengan kolom di tabel projects
    protected $fillable = ['name', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function debtors(): HasMany
    {
        return $this->hasMany(Debtor::class);
    }
}
