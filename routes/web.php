<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mitra\BerkasController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Bpn\LoketController;
Route::get('/', function () {
    return view('welcome');
});

// Ruang Kerja Mitra
Route::prefix('mitra')->group(function () {
    
    // Menampilkan Halaman Plotting
    Route::get('/plotting', function () {
        return view('plotting');
    })->name('mitra.plotting');

    // Endpoint untuk memproses form pengajuan
    Route::post('/berkas/biasa', [BerkasController::class, 'storeBerkasBiasa'])->name('berkas.biasa.store');
    Route::post('/berkas/plotting', [BerkasController::class, 'storeBerkasPlotting'])->name('berkas.plotting.store');
    Route::get('/mitra/berkas-biasa', [App\Http\Controllers\Mitra\BerkasController::class, 'indexBiasa'])->name('mitra.berkas.biasa');
    
});

// --- Rute BPN ---
Route::prefix('bpn')->group(function () {
    
    // Dashboard BPN
    Route::get('/dashboard', [LoketController::class, 'dashboard'])->name('bpn.dashboard');

    // Ruang Kerja Loket (Terima & Koreksi)
    Route::get('/loket-terima', [LoketController::class, 'index'])->name('bpn.loket.index');
    Route::post('/loket-terima/scan', [LoketController::class, 'terimaDariScan'])->name('bpn.loket.scan');
    Route::post('/loket-terima/koreksi/{id}', [LoketController::class, 'prosesKoreksi'])->name('bpn.loket.koreksi');

});
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Manajemen User dan Approval
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/toggle-approval/{id}', [UserController::class, 'toggleApproval'])->name('users.toggle-approval');
});