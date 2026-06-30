<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berkas extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara eksplisit
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

    /**
     * Relasi ke Dokumen SPS
     */
    public function sps()
    {
        return $this->hasOne(DokumenSps::class, 'berkas_id');
    }

    /**
     * Relasi ke Petugas Pelaksana
     */
    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    // =================================================================
    // TAMBAHAN RELASI BARU UNTUK RUANG KERJA
    // =================================================================

    /**
     * Relasi ke Master Kecamatan
     */
    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }

    /**
     * Relasi ke Master Desa
     */
    public function desa()
    {
        return $this->belongsTo(Desa::class, 'desa_id');
    }

    /**
     * Relasi ke Master Jenis Hak
     */
    public function jenisHak()
    {
        return $this->belongsTo(JenisHak::class, 'jenis_hak_id');
    }
}