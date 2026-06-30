<?php

namespace App\Http\Controllers;

use App\Models\Berkas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RuangKerjaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $jabatan = $user->jabatan; // Nilai bisa berupa: pelaksana, loket, backoffice, mitra, admin

        // Query dasar dengan memuat relasi (Eager Loading) agar performa optimal
        $query = Berkas::with(['kecamatan', 'desa', 'jenisHak']);

        // --- FILTER DATA BERDASARKAN JABATAN (Seperti sistem_berkas) ---
        if ($jabatan === 'pelaksana') {
            // Pelaksana hanya melihat berkas/aset yang ditugaskan kepadanya
            $query->where('petugas_id', $user->id);
            
        } elseif ($jabatan === 'mitra') {
            // Mitra hanya melihat aset milik instansi/entitas mereka sendiri
            $query->where('mitra_id', $user->mitra_id);
            
        } elseif ($jabatan === 'loket') {
            // Loket biasanya melihat semua berkas masuk yang berstatus 'pendaftaran' atau 'pembayaran'
            $query->whereIn('status', ['pendaftaran', 'pembayaran']);
            
        } elseif ($jabatan === 'backoffice') {
            // Backoffice melihat berkas yang sudah lolos tahap awal dan siap divalidasi/SK-kan
            $query->where('status', 'verifikasi_lanjutan');
        }
        // Jika admin, biarkan tanpa filter agar bisa memonitor semua data berkas/aset

        // --- FITUR PENCARIAN GLOBAL ---
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_berkas', 'LIKE', "%{$search}%")
                  ->orWhere('nama_pemohon', 'LIKE', "%{$search}%");
            });
        }

        // Ambil data dengan pagination (10 data per halaman)
        $berkas = $query->latest()->paginate(10);

        return view('ruang_kerja.index', compact('berkas'));
    }
}