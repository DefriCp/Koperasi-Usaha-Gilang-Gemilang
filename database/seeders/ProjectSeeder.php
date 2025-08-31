<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'CHANNELING BANK BUKOPIN / KB BANK',
            'CHANNELING BANK MNC',
            'CHANNELING BPR ADHIERRESA / VIMA',
            'CHANNELING BPR DHAHA',
            'CHANNELING BPR HASAMITRA',
            'CHANNELING BPR HOSING',
            'CHANNELING BPR INDOMITRA',
            'CHANNELING BPR KS',
            'CHANNELING BPR NBP29',
            'CHANNELING BUKOPIN SYARIAH',
            'CHANNELING KOP SAM',
            'CHANNELING KSP SMS',
            'CHANNELING SSB BPR RIFI',
            'EXECUT EKS PLAT',
            'EXECUT EKS PLAT SSB',
            'EXECUT PLAT SSB',
            'EXECUT PLATINUM',
            'SUB CHANNELING GRAHADI',
            'SUB CHANNELING KOPJAS',
            'SUB CHANNELING KOSPPI BANK BANTEN',
            'SUB CHANNELING SSB BPR PERDANA',
        ];

        DB::transaction(function () use ($names) {
            Project::whereNotIn('name', $names)->delete();
            foreach ($names as $nm) {
                Project::firstOrCreate(['name' => $nm]);
            }
        });
    }
}
