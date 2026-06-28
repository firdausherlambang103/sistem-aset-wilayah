<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat akun Administrator
        User::create([
            'name' => 'Administrator BPN', // Jika di tabel users Anda ada kolom name
            'email' => 'admin@nganjuk.go.id',
            'password' => Hash::make('Nganjuk@1226'), // Silakan ganti passwordnya
            'role' => 'admin',
            'is_approved' => true, // Admin otomatis langsung disetujui
        ]);

        $this->command->info('Akun Admin berhasil dibuat! Email: admin@nganjuk.go.id | Password: Nganjuk@1226');
    }
}