<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Controllers Mitra
use App\Http\Controllers\Mitra\BerkasController;

// Controllers BPN
use App\Http\Controllers\Bpn\LoketController;
use App\Http\Controllers\Bpn\PelaksanaController;
use App\Http\Controllers\Bpn\BackofficeController; // <-- Pastikan ini ada

// Controllers Admin
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\WilayahController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/login');
});

// --- API TRACKING RIWAYAT BERKAS ---
Route::middleware('auth')->get('/api/berkas/{id}/riwayat', function($id) {
    $riwayats = \App\Models\RiwayatBerkas::where('berkas_id', $id)
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function($r) {
                        return [
                            'tanggal' => $r->created_at->format('d M Y, H:i'),
                            'aksi' => $r->aksi,
                            'catatan' => $r->catatan
                        ];
                    });
    return response()->json($riwayats);
})->name('api.berkas.riwayat');

// --- RUTE PROFIL (Bawaan Laravel Breeze) ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// --- RUTE ADMINISTRATOR ---
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/toggle-approval/{id}', [UserController::class, 'toggleApproval'])->name('users.toggle-approval');
    Route::get('/wilayah', [WilayahController::class, 'index'])->name('wilayah.index');
    Route::post('/wilayah/kecamatan', [WilayahController::class, 'storeKecamatan'])->name('wilayah.kecamatan.store');
    Route::post('/wilayah/desa', [WilayahController::class, 'storeDesa'])->name('wilayah.desa.store');
});


// --- RUTE BPN ---
Route::middleware(['auth'])->prefix('bpn')->name('bpn.')->group(function () {
    // Dashboard BPN
    Route::get('/dashboard', [LoketController::class, 'dashboard'])->name('dashboard');

    // Loket Terima & Koreksi
    Route::get('/loket-terima', [LoketController::class, 'index'])->name('loket.index');
    Route::post('/loket-terima/berkas', [LoketController::class, 'store'])->name('loket.berkas.store');
    Route::post('/loket-terima/scan', [LoketController::class, 'terimaDariScan'])->name('loket.scan');
    Route::post('/loket-terima/koreksi/{id}', [LoketController::class, 'prosesKoreksi'])->name('loket.koreksi');

    // --- FITUR BACKOFFICE ---
    Route::get('/backoffice', [BackofficeController::class, 'index'])->name('backoffice.index');
    Route::post('/backoffice/proses/{id}', [BackofficeController::class, 'proses'])->name('backoffice.proses');
    
    // Rute Baru: Tolak dan Edit Berkas (Mengatasi error 404)
    Route::post('/backoffice/tolak/{id}', [BackofficeController::class, 'tolak'])->name('backoffice.tolak');
    Route::put('/backoffice/update/{id}', [BackofficeController::class, 'update'])->name('backoffice.update');
    // ------------------------------
    // Rute untuk Disposisi/Kirim Massal Berkas
    Route::post('/berkas/kirim-batch', [LoketController::class, 'kirimBatch'])->name('berkas.kirim');
    // Loket Pembayaran & Validasi SPS
    Route::get('/loket-pembayaran', [LoketController::class, 'indexPembayaran'])->name('pembayaran.index');
    Route::post('/loket-pembayaran/proses/{id}', [LoketController::class, 'prosesPembayaran'])->name('pembayaran.proses');

    // Pelaksana Kegiatan (Plotting)
    Route::get('/pelaksana', [PelaksanaController::class, 'index'])->name('pelaksana.index');
    Route::post('/pelaksana/selesai/{id}', [PelaksanaController::class, 'selesaikan'])->name('pelaksana.selesaikan');

    // Peta Utama / WebGIS Nganjuk
    Route::get('/peta-utama', function () { return view('map'); })->name('peta');
});


// --- RUTE MITRA ---
Route::middleware(['auth'])->prefix('mitra')->group(function () {
    Route::get('/berkas-biasa', [BerkasController::class, 'indexBiasa'])->name('mitra.berkas.biasa');
    Route::get('/plotting', function () { return view('plotting'); })->name('mitra.plotting');
    Route::post('/berkas/biasa', [BerkasController::class, 'storeBerkasBiasa'])->name('berkas.biasa.store');
    Route::post('/berkas/plotting', [BerkasController::class, 'storeBerkasPlotting'])->name('berkas.plotting.store');
});

require __DIR__.'/auth.php';