<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatBerkas extends Model
{
    use HasFactory;

    // Pastikan nama tabel di database sesuai (Laravel defaultnya 'riwayat_berkas')
    protected $table = 'riwayat_berkas'; 

    protected $guarded = [];

    // Relasi ke Berkas
    public function berkas()
    {
        return $this->belongsTo(Berkas::class);
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'ke_user_id');
    }
}