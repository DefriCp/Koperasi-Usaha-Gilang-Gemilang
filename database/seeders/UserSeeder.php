<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $checker = User::firstOrCreate(
            ['email'=>'checker@kugg.com'],
            ['name'=>'Checker Admin','password'=>Hash::make('password')]
        );
        $checker->syncRoles(['checker']);

        $inputer = User::firstOrCreate(
            ['email'=>'inputer@kugg.com'],
            ['name'=>'Inputer','password'=>Hash::make('password')]
        );
        $inputer->syncRoles(['inputer']);

        $viewer = User::firstOrCreate(
            ['email'=>'viewer@kugg.com'],
            ['name'=>'Viewer','password'=>Hash::make('password')]
        );
        $viewer->syncRoles(['viewer']);
    }
}