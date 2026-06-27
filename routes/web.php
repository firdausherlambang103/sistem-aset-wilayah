<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mitra\BerkasController;

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
    
});