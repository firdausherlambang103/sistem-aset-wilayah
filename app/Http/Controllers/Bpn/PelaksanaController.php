<?php

namespace App\Http\Controllers\Bpn;

use App\Http\Controllers\Controller;
use App\Models\Berkas;
use App\Models\RiwayatBerkas;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PelaksanaController extends Controller
{
    public function index()
    {
        $limitHarian = 15; // Kuota plotting harian yang Anda minta
        
        // Ambil berkas khusus plotting yang sudah sampai di tahap pelaksana
        $antrean = Berkas::where('tipe_berkas', 'plotting')
                         ->where('status_berkas', 'pelaksana_kegiatan')
                         ->orderBy('updated_at', 'asc')
                         ->take($limitHarian)
                         ->get();

        $jumlahSelesaiHariIni = Berkas::where('tipe_berkas', 'plotting')
                                      ->where('status_berkas', 'selesai')
                                      ->whereDate('updated_at', Carbon::today())
                                      ->count();

        return view('bpn.pelaksana', compact('antrean', 'limitHarian', 'jumlahSelesaiHariIni'));
    }

    public function selesaikan($id)
    {
        $berkas = Berkas::findOrFail($id);

        $berkas->update([
            'status_berkas' => 'selesai'
        ]);

        RiwayatBerkas::create([
            'berkas_id' => $berkas->id,
            'dari_user_id' => auth()->id(),
            'aksi' => 'Plotting Diselesaikan',
            'catatan' => 'Berkas spasial telah diproses dan dipublikasikan ke Peta Utama.'
        ]);

        return back()->with('success', 'Berkas plotting ' . $berkas->nomer_berkas . ' berhasil diselesaikan!');
    }
}