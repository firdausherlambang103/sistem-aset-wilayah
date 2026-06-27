<?php
namespace App\Http\Controllers\Bpn;

use App\Http\Controllers\Controller;
use App\Models\Berkas;
use App\Models\RiwayatBerkas;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoketController extends Controller
{
    public function dashboard()
    {
        $hariIni = Berkas::whereDate('created_at', Carbon::today())->count();
        $sisaKemarin = Berkas::whereDate('created_at', '<', Carbon::today())
                             ->where('status_berkas', '!=', 'selesai')->count();
        $selesai = Berkas::where('status_berkas', 'selesai')->count();

        return view('bpn.dashboard', compact('hariIni', 'sisaKemarin', 'selesai'));
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