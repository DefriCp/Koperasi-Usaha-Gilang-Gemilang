<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['inputer','checker','viewer'] as $r) {
            Role::firstOrCreate(['name'=>$r,'guard_name'=>'web']);
        }
    }
}