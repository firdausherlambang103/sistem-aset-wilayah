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
        $antreanBiasa = \App\Models\Berkas::where('status_berkas', 'pelaksana_kegiatan')->where('tipe_berkas', 'biasa')->with('petugas')->orderBy('updated_at', 'desc')->get();
        $antreanPlotting = \App\Models\Berkas::where('status_berkas', 'pelaksana_kegiatan')->where('tipe_berkas', 'plotting')->with('petugas')->orderBy('updated_at', 'desc')->get();
        $selesai = \App\Models\Berkas::where('status_berkas', 'selesai')->orderBy('updated_at', 'desc')->limit(100)->get();
        
        $daftarPetugas = \App\Models\User::whereIn('role', ['bpn', 'admin'])->get();

        return view('bpn.pelaksana', compact('antreanBiasa', 'antreanPlotting', 'selesai', 'daftarPetugas'));
    }

    public function updateProgress(Request $request, $id)
    {
        // Validasi kini mengharapkan array (karena dari checkbox)
        $request->validate([
            'kegiatan' => 'required|array',
            'catatan' => 'nullable|string'
        ]);

        $berkas = \App\Models\Berkas::findOrFail($id);
        // Gabungkan array checkbox menjadi string (Contoh: "Pengukuran, Pemeriksaan Tanah")
        $kegiatan = implode(', ', $request->kegiatan);

        \App\Models\RiwayatBerkas::create([
            'berkas_id' => $berkas->id,
            'dari_user_id' => auth()->id() ?? 1,
            'aksi' => 'Progress: ' . $kegiatan,
            'catatan' => $request->catatan ?? 'Menyelesaikan tahapan tugas yang dipilih.'
        ]);

        return back()->with('success', 'Progress kegiatan berhasil diperbarui.');
    }

    public function selesaikan(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string'
        ]);

        $berkas = Berkas::findOrFail($id);

        // Update status menjadi selesai
        $berkas->update(['status_berkas' => 'selesai']);

        // Catat di timeline riwayat
        RiwayatBerkas::create([
            'berkas_id' => $berkas->id,
            'dari_user_id' => auth()->id() ?? 1,
            'aksi' => 'Kegiatan Selesai (Finalisasi Pelaksana)',
            'catatan' => $request->catatan
        ]);

        return back()->with('success', 'Pekerjaan selesai! Berkas ' . $berkas->nomer_berkas . ' telah ditutup.');
    }
}