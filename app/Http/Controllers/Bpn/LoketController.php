<?php
namespace App\Http\Controllers\Bpn;

use App\Http\Controllers\Controller;
use App\Models\Berkas;
use App\Models\RiwayatBerkas;
use App\Models\User;
use App\Models\Kecamatan;
use App\Models\Desa;
use App\Models\JenisHak;
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
        $antrean = Berkas::whereIn('status_berkas', ['di_loket_terima', 'di_loket_koreksi'])
                         ->orderBy('updated_at', 'desc')
                         ->get();

        // Ambil daftar nama petugas (BPN) untuk dimunculkan di Dropdown
        $daftarPetugas = User::whereIn('role', ['bpn', 'admin'])->get();

        return view('bpn.loket_terima', compact('antrean', 'daftarPetugas'));
    }

    // ========================================================================
    // FITUR BARU: CREATE & STORE BERKAS
    // ========================================================================

    public function create()
    {
        // Mengambil data referensi untuk form pembuatan berkas
        $kecamatans = Kecamatan::all();
        $desas = Desa::all();
        $jenis_haks = JenisHak::all();
        
        return view('bpn.create_berkas', compact('kecamatans', 'desas', 'jenis_haks'));
    }

    public function store(Request $request)
    {
        // Validasi disesuaikan dengan skema database
        $validated = $request->validate([
            'nomer_berkas'     => 'required|unique:berkas,nomer_berkas',
            'tahun_berkas'     => 'required|integer',
            'nama_pemohon'     => 'required|string|max:255',
            'tipe_berkas'      => 'required|in:biasa,plotting',
            'jenis_permohonan' => 'required|string',
            'jenis_hak'        => 'required|string',
            'nomer_hak'        => 'required|string',
            'kecamatan'        => 'required|string',
            'desa'             => 'required|string',
        ]);

        $validated['status_berkas'] = 'di_loket_terima';
        
        // PENTING: Karena di database kolom 'mitra_id' sifatnya wajib (constrained),
        // dan yang menginput ini adalah BPN (bukan mitra), maka kita simpan ID akun BPN sebagai penginput.
        $validated['mitra_id'] = auth()->id(); 

        $berkas = Berkas::create($validated);

        RiwayatBerkas::create([
            'berkas_id' => $berkas->id,
            'ke_user_id' => auth()->id() ?? 1,
            'aksi' => 'Dibuat Loket',
            'catatan' => 'Berkas baru diinput langsung melalui Loket Penerimaan.'
        ]);

        return redirect()->route('bpn.loket.index')->with('success', 'Berkas baru ' . $berkas->nomer_berkas . ' berhasil ditambahkan!');
    }

    // ========================================================================

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
        $antrean = \App\Models\Berkas::where('status_berkas', 'pembayaran_validasi')->with('sps')->orderBy('updated_at', 'desc')->get();
        $kwitansi = \App\Models\Berkas::whereHas('sps', function ($query) {
            $query->where('is_payment_validated', true);
        })->with('sps')->orderBy('updated_at', 'desc')->get();
        
        $daftarPetugas = \App\Models\User::whereIn('role', ['bpn', 'admin'])->get();

        return view('bpn.loket_pembayaran', compact('antrean', 'kwitansi', 'daftarPetugas'));
    }
    
    public function prosesPembayaran(Request $request, $id)
    {
        $request->validate([
            'penerima_kwitansi' => 'required|string|max:255',
        ]);

        $berkas = Berkas::findOrFail($id);
        
        // Pastikan relasi sps sudah ada di database
        if ($berkas->sps) {
            $berkas->sps->update([
                'is_payment_validated' => true,
                'tanggal_bayar' => now(),
                'penerima_kwitansi' => $request->penerima_kwitansi
            ]);
        }

        // Update Status Berkas ke Pelaksana Kegiatan
        $berkas->update(['status_berkas' => 'pelaksana_kegiatan']);

        // Catat riwayat serah terima kwitansi
        RiwayatBerkas::create([
            'berkas_id' => $berkas->id,
            'dari_user_id' => auth()->id() ?? 1,
            'aksi' => 'Pembayaran Divalidasi',
            'catatan' => 'SPS telah dibayar lunas. Bukti Kwitansi fisik diserahkan kepada: ' . strtoupper($request->penerima_kwitansi)
        ]);

        return back()->with('success', 'Pembayaran divalidasi dan Kwitansi diserahkan kepada ' . $request->penerima_kwitansi . '. Berkas diteruskan ke Pelaksana Kegiatan.');
    }
    
    public function kirimBatch(Request $request)
    {
        $request->validate([
            'berkas_ids' => 'required|string',
            'tujuan_loket' => 'required|string',
            'petugas_id' => 'required|exists:users,id'
        ]);

        $ids = json_decode($request->berkas_ids, true);
        if (empty($ids)) return back()->with('error', 'Pilih minimal satu berkas untuk dikirim.');

        $petugas = User::find($request->petugas_id);

        foreach ($ids as $id) {
            $berkas = Berkas::find($id);
            if ($berkas) {
                // Update status dan pindahkan kepemilikan berkas ke petugas yg ditunjuk
                $berkas->update([
                    'status_berkas' => $request->tujuan_loket,
                    'petugas_id' => $petugas->id
                ]);

                RiwayatBerkas::create([
                    'berkas_id' => $berkas->id,
                    'dari_user_id' => auth()->id() ?? 1,
                    'ke_user_id' => $petugas->id,
                    'aksi' => 'Disposisi Berkas',
                    'catatan' => 'Berkas diteruskan ke ' . strtoupper($petugas->name ?? $petugas->email) . ' pada bagian ' . str_replace('_', ' ', $request->tujuan_loket)
                ]);
            }
        }

        return back()->with('success', count($ids) . ' Berkas berhasil dikirim ke ' . ($petugas->name ?? $petugas->email) . '!');
    }
}