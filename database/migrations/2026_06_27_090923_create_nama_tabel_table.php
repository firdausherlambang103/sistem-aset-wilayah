<?php
// database/migrations/2026_01_01_000001_create_system_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pastikan Ekstensi PostGIS Aktif
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis;');

        // 2. Tabel Users
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'bpn', 'mitra']);
            $table->boolean('is_approved')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        // 3. Tabel Profil BPN
        Schema::create('profil_bpn', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama');
            $table->string('nomer_wa');
            $table->string('jabatan');
            $table->string('seksi');
            $table->timestamps();
        });

        // 4. Tabel Profil Mitra
        Schema::create('profil_mitra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama');
            $table->string('nomer_wa');
            $table->string('kode_mitra')->unique();
            $table->string('jabatan');
            $table->timestamps();
        });

        // 5. Tabel Berkas
        Schema::create('berkas', function (Blueprint $table) {
            $table->id();
            $table->string('nomer_berkas', 6)->unique(); // 6 digit acak angka/huruf
            $table->year('tahun_berkas');
            $table->string('nama_pemohon');
            $table->string('jenis_permohonan');
            $table->string('jenis_hak');
            $table->string('nomer_hak');
            $table->string('kecamatan');
            $table->string('desa');
            $table->enum('tipe_berkas', ['biasa', 'plotting']);
            $table->enum('status_berkas', [
                'draft', 'dikirim_mitra', 'di_loket_terima', 'di_loket_koreksi', 
                'backoffice_sps', 'pembayaran_validasi', 'pelaksana_kegiatan', 
                'selesai', 'dikembalikan'
            ])->default('draft');
            $table->foreignId('mitra_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // 6. Tabel Data Plotting (Spasial)
        Schema::create('data_plotting', function (Blueprint $table) {
            $table->id();
            $table->foreignId('berkas_id')->constrained('berkas')->onDelete('cascade');
            $table->string('foto_lokasi')->nullable();
            $table->timestamps();
        });

        // Menambahkan kolom geometri PostGIS secara manual (Mendukung Point/Polygon)
        DB::statement('ALTER TABLE data_plotting ADD COLUMN geom geometry(Geometry, 4326);');

        // 7. Tabel Dokumen SPS & Pembayaran
        Schema::create('dokumen_sps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('berkas_id')->constrained('berkas')->onDelete('cascade');
            $table->string('file_sps')->nullable();
            $table->boolean('is_sps_validated')->default(false);
            $table->boolean('is_payment_validated')->default(false);
            $table->date('tanggal_bayar')->nullable();
            $table->string('penerima_kwitansi')->nullable();
            $table->timestamps();
        });

        // 8. Tabel Riwayat Berkas (Tracking & Logs)
        Schema::create('riwayat_berkas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('berkas_id')->constrained('berkas')->onDelete('cascade');
            $table->foreignId('dari_user_id')->nullable()->constrained('users');
            $table->foreignId('ke_user_id')->nullable()->constrained('users');
            $table->text('catatan')->nullable();
            $table->string('aksi'); // misal: "Diterima Loket", "Dikoreksi", "Di-SPS"
            $table->timestamps();
        });

        // 9. Tabel Template Notifikasi WA
        Schema::create('template_notifikasi_wa', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pemicu')->unique(); // misal: 'berkas_baru', 'pembayaran_lunas'
            $table->text('template_pesan'); // Menggunakan placeholder fleksibel {{nama_pemohon}}
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_notifikasi_wa');
        Schema::dropIfExists('riwayat_berkas');
        Schema::dropIfExists('dokumen_sps');
        DB::statement('DROP TABLE IF EXISTS data_plotting;');
        Schema::dropIfExists('berkas');
        Schema::dropIfExists('profil_mitra');
        Schema::dropIfExists('profil_bpn');
        Schema::dropIfExists('users');
    }
};