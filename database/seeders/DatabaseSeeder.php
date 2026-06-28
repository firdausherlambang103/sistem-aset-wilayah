<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            WilayahNganjukSeeder::class,
            // Jika Anda sudah punya seeder wilayah, bisa ditambahkan di sini:
            // WilayahSeeder::class, 
        ]);
    }
}