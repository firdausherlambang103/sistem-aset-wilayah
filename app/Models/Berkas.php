<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berkas extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara eksplisit (karena jamak dari berkas tetap berkas, bukan berkass)
    protected $table = 'berkas';

    // Mengizinkan semua kolom diisi secara massal (Mass Assignment)
    protected $guarded = [];

    /**
     * Relasi ke Mitra (User yang membuat berkas)
     */
    public function mitra()
    {
        return $this->belongsTo(User::class, 'mitra_id');
    }

    /**
     * Relasi ke Data Plotting (jika ada data spasialnya)
     */
    public function dataPlotting()
    {
        return $this->hasOne(DataPlotting::class, 'berkas_id');
    }

    // Di dalam class Berkas
    public function sps()
    {
        return $this->hasOne(DokumenSps::class, 'berkas_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}