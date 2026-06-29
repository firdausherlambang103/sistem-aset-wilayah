<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisHak;

class JenisHakSeeder extends Seeder
{
    public function run(): void
    {
        $haks = [
            'Hak Milik (HM)',
            'Hak Guna Bangunan (HGB)',
            'Hak Pakai (HP)',
            'Hak Guna Usaha (HGU)',
            'Hak Pengelolaan (HPL)',
            'Tanah Wakaf',
            'Letter C / Petok D (Belum Sertipikat)',
        ];

        foreach ($haks as $hak) {
            JenisHak::create([
                'nama_hak' => $hak
            ]);
        }
    }
}