<?php

namespace App\Http\Controllers\Bpn;

use App\Http\Controllers\Controller;
use App\Models\Berkas;
use App\Models\RiwayatBerkas;
use Illuminate\Http\Request;

class PelaksanaController extends Controller
{
    public function index()
    {
        // 1. Ambil berkas yang berstatus pelaksana_kegiatan
        // Anda bisa menambahkan ->where('petugas_id', auth()->id()) jika ingin 
        // pelaksana HANYA melihat berkas yang ditugaskan spesifik ke akunnya.
        $antrean = Berkas::where('status_berkas', 'pelaksana_kegiatan')
                         ->with('petugas')
                         ->orderBy('updated_at', 'desc')
                         ->get();

        // 2. Ambil berkas yang sudah diselesaikan (Untuk Tab Riwayat)
        $selesai = Berkas::where('status_berkas', 'selesai')
                         ->orderBy('updated_at', 'desc')
                         ->limit(100) // Dibatasi agar tidak terlalu berat memuat halaman
                         ->get();

        return view('bpn.pelaksana', compact('antrean', 'selesai'));
    }

    public function selesaikan(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string'
        ]);

        $berkas = Berkas::findOrFail($id);

        // Update status menjadi selesai
        $berkas->update([
            'status_berkas' => 'selesai'
        ]);

        // Catat di timeline riwayat
        RiwayatBerkas::create([
            'berkas_id' => $berkas->id,
            'dari_user_id' => auth()->id() ?? 1,
            'aksi' => 'Kegiatan Selesai (Pelaksana)',
            'catatan' => $request->catatan
        ]);

        return back()->with('success', 'Pekerjaan selesai! Berkas ' . $berkas->nomer_berkas . ' telah ditutup.');
    }
}