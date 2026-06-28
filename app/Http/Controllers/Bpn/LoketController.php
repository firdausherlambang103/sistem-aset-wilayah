<?php
namespace App\Http\Controllers\Bpn;

use App\Http\Controllers\Controller;
use App\Models\Berkas;
use App\Models\RiwayatBerkas;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoketController extends Controller
{
    public function dashboard()
    {
        // 1. Statistik Kinerja (Kartu Atas)
        $masukHariIni = Berkas::whereDate('created_at', Carbon::today())->count();
        $selesaiHariIni = Berkas::where('status_berkas', 'selesai')
                                ->whereDate('updated_at', Carbon::today())
                                ->count();
        $sisaKemarin = Berkas::whereDate('created_at', '<', Carbon::today())
                            ->where('status_berkas', '!=', 'selesai')
                            ->count();

        // 2. Data Riwayat Detail (Digunakan untuk Tab 'Belum Dikerjakan' & 'Sudah Dikerjakan')
        // Memuat relasi mitra untuk mendapatkan nama pemohon/pengisi
        $semuaBerkas = Berkas::with(['mitra.profilMitra'])->orderBy('updated_at', 'desc')->get();

        // 3. Data Rekap Kinerja Petugas / Mitra (Tab ke-3)
        // Menghitung jumlah berkas yang dimasukkan oleh masing-masing Mitra
        $mitras = User::where('role', 'mitra')->with('profilMitra')->get();
        $rekapPengisi = [];

        foreach ($mitras as $mitra) {
            $berkasMitra = Berkas::where('mitra_id', $mitra->id)->get();
            
            if ($berkasMitra->count() > 0) {
                $rekapPengisi[] = [
                    'nama' => $mitra->profilMitra->nama ?? $mitra->email,
                    'total_entri' => $berkasMitra->count(),
                    'total_selesai' => $berkasMitra->where('status_berkas', 'selesai')->count(),
                    'input_hari_ini' => $berkasMitra->where('created_at', '>=', Carbon::today())->count(),
                    'sisa_kemarin' => $berkasMitra->where('created_at', '<', Carbon::today())
                                                ->where('status_berkas', '!=', 'selesai')
                                                ->count(),
                ];
            }
        }

        // Mengurutkan rekap berdasarkan total entri terbanyak
        usort($rekapPengisi, function($a, $b) {
            return $b['total_entri'] <=> $a['total_entri'];
        });

        return view('bpn.dashboard', compact(
            'masukHariIni', 'selesaiHariIni', 'sisaKemarin', 'semuaBerkas', 'rekapPengisi'
        ));
    }

    public function index()
    {
        // Mengambil antrean berkas yang saat ini berada di Loket Terima / Koreksi
        $antrean = Berkas::whereIn('status_berkas', ['di_loket_terima', 'di_loket_koreksi'])
                         ->orderBy('updated_at', 'desc')
                         ->get();

        return view('bpn.loket_terima', compact('antrean'));
    }

    public function terimaDariScan(Request $request)
    {
        $request->validate(['nomer_berkas' => 'required']);

        $berkas = Berkas::where('nomer_berkas', $request->nomer_berkas)->first();

        if (!$berkas) {
            return back()->with('error', 'Berkas tidak ditemukan!');
        }

        if ($berkas->status_berkas === 'draft') {
            $berkas->update(['status_berkas' => 'di_loket_terima']);
            
            RiwayatBerkas::create([
                'berkas_id' => $berkas->id,
                'ke_user_id' => auth()->id() ?? 1, // Anggap 1 jika auth belum aktif sempurna
                'aksi' => 'Diterima Loket',
                'catatan' => 'Berkas fisik telah diterima oleh loket.'
            ]);

            return back()->with('success', 'Berkas ' . $berkas->nomer_berkas . ' berhasil diterima!');
        }

        return back()->with('error', 'Status berkas saat ini tidak bisa diterima loket.');
    }

    public function prosesKoreksi(Request $request, $id)
    {
        $berkas = Berkas::findOrFail($id);
        
        $aksi = $request->aksi; // 'kembalikan' atau 'teruskan_backoffice'
        $catatan = $request->catatan;

        if ($aksi === 'kembalikan') {
            $berkas->update(['status_berkas' => 'dikembalikan']);
            $namaAksi = 'Dikembalikan ke Mitra';
        } else {
            $berkas->update(['status_berkas' => 'backoffice_sps']);
            $namaAksi = 'Diteruskan ke Backoffice (SPS)';
        }

        RiwayatBerkas::create([
            'berkas_id' => $berkas->id,
            'dari_user_id' => auth()->id() ?? 1,
            'aksi' => $namaAksi,
            'catatan' => $catatan
        ]);

        return back()->with('success', 'Berkas berhasil diproses: ' . $namaAksi);
    }

    public function indexPembayaran()
    {
        // Mengambil berkas yang statusnya sudah diupload SPS
        $berkasSPS = Berkas::where('status_berkas', 'backoffice_sps')->get();
        return view('bpn.loket_pembayaran', compact('berkasSPS'));
    }

    public function prosesPembayaran(Request $request, $id)
    {
        $request->validate([
            'penerima_kwitansi' => 'required|string',
        ]);

        $berkas = Berkas::findOrFail($id);
        
        // Update Dokumen SPS
        $berkas->sps()->update([
            'is_payment_validated' => true,
            'tanggal_bayar' => now(),
            'penerima_kwitansi' => $request->penerima_kwitansi
        ]);

        // Update Status Berkas ke Pelaksana Kegiatan
        $berkas->update(['status_berkas' => 'pelaksana_kegiatan']);

        return back()->with('success', 'Pembayaran divalidasi. Berkas diteruskan ke Pelaksana.');
    }
}