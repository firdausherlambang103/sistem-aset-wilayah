<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Controllers Mitra
use App\Http\Controllers\Mitra\BerkasController;

// Controllers BPN
use App\Http\Controllers\Bpn\LoketController;
use App\Http\Controllers\Bpn\PelaksanaController;

// Controllers Admin
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\WilayahController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Jika ingin langsung ke halaman Login
Route::get('/', function () {
    return redirect('/login');
});

// --- RUTE PROFIL (Bawaan Laravel Breeze) ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// --- RUTE ADMINISTRATOR ---
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Manajemen User dan Approval
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/toggle-approval/{id}', [UserController::class, 'toggleApproval'])->name('users.toggle-approval');
    
    // Manajemen Master Wilayah
    Route::get('/wilayah', [WilayahController::class, 'index'])->name('wilayah.index');
    Route::post('/wilayah/kecamatan', [WilayahController::class, 'storeKecamatan'])->name('wilayah.kecamatan.store');
    Route::post('/wilayah/desa', [WilayahController::class, 'storeDesa'])->name('wilayah.desa.store');

});


// --- RUTE BPN ---
Route::middleware(['auth'])->prefix('bpn')->name('bpn.')->group(function () {
    
    // Dashboard BPN
    Route::get('/dashboard', [LoketController::class, 'dashboard'])->name('dashboard');

    // Ruang Kerja Loket (Terima & Koreksi)
    Route::get('/loket-terima', [LoketController::class, 'index'])->name('loket.index');
    Route::post('/loket-terima/scan', [LoketController::class, 'terimaDariScan'])->name('loket.scan');
    Route::post('/loket-terima/koreksi/{id}', [LoketController::class, 'prosesKoreksi'])->name('loket.koreksi');

    // Ruang Kerja Loket Pembayaran & Validasi SPS
    Route::get('/loket-pembayaran', [LoketController::class, 'indexPembayaran'])->name('pembayaran.index');
    Route::post('/loket-pembayaran/proses/{id}', [LoketController::class, 'prosesPembayaran'])->name('pembayaran.proses');

    // Ruang Kerja Pelaksana Kegiatan (Plotting)
    Route::get('/pelaksana', [PelaksanaController::class, 'index'])->name('pelaksana.index');
    Route::post('/pelaksana/selesai/{id}', [PelaksanaController::class, 'selesaikan'])->name('pelaksana.selesaikan');

    // Peta Utama / WebGIS Nganjuk
    Route::get('/peta-utama', function () {
        return view('map');
    })->name('peta');

});


// --- RUTE MITRA ---
Route::middleware(['auth'])->prefix('mitra')->group(function () {
    
    // Menampilkan Halaman Dashboard / Berkas Biasa
    Route::get('/berkas-biasa', [BerkasController::class, 'indexBiasa'])->name('mitra.berkas.biasa');
    
    // Menampilkan Halaman Plotting
    Route::get('/plotting', function () {
        return view('plotting');
    })->name('mitra.plotting');

    // Endpoint untuk memproses form pengajuan
    Route::post('/berkas/biasa', [BerkasController::class, 'storeBerkasBiasa'])->name('berkas.biasa.store');
    Route::post('/berkas/plotting', [BerkasController::class, 'storeBerkasPlotting'])->name('berkas.plotting.store');
    
});

require __DIR__.'/auth.php';