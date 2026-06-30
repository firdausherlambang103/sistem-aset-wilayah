<?php

namespace App\Http\Controllers\Bpn;

use App\Http\Controllers\Controller;
use App\Models\Berkas;
use App\Models\RiwayatBerkas;
use App\Models\DokumenSps; // <-- Import model DokumenSps
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BackofficeController extends Controller
{
    public function index()
    {
        // Ambil antrean berkas yang diteruskan ke Backoffice (SPS)
        $berkas = Berkas::where('status_berkas', 'backoffice_sps')
                        ->orderBy('updated_at', 'desc')
                        ->get();
                        
        return view('bpn.backoffice', compact('berkas'));
    }

    // 1. FUNGSI TERBITKAN SPS (SIMPAN KE TABEL dokumen_sps)
    public function proses(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string',
            'file_sps' => 'required|mimes:pdf|max:5120' // Wajib PDF, max 5MB
        ]);

        $berkas = Berkas::findOrFail($id);
        
        // Proses Upload File
        $path = null;
        if ($request->hasFile('file_sps')) {
            $path = $request->file('file_sps')->store('sps_dokumen', 'public');
        }

        // Simpan ke tabel dokumen_sps (Sesuai skema database Anda)
        DokumenSps::updateOrCreate(
            ['berkas_id' => $berkas->id],
            [
                'file_sps' => $path,
                'is_sps_validated' => true
            ]
        );

        // Teruskan ke Loket Pembayaran (Menggunakan enum database yang benar)
        $berkas->update(['status_berkas' => 'pembayaran_validasi']);

        // Catat riwayat
        RiwayatBerkas::create([
            'berkas_id' => $berkas->id,
            'dari_user_id' => auth()->id() ?? 1,
            'aksi' => 'SPS Diterbitkan (Backoffice)',
            'catatan' => $request->catatan
        ]);

        return back()->with('success', 'Dokumen SPS berhasil diunggah. Berkas diteruskan ke Loket Pembayaran.');
    }

    // 2. FUNGSI TOLAK KEMBALI KE LOKET
    public function tolak(Request $request, $id)
    {
        $request->validate(['catatan' => 'required|string']);

        $berkas = Berkas::findOrFail($id);
        
        // Kembalikan statusnya ke koreksi loket
        $berkas->update(['status_berkas' => 'di_loket_koreksi']);

        RiwayatBerkas::create([
            'berkas_id' => $berkas->id,
            'dari_user_id' => auth()->id() ?? 1,
            'aksi' => 'Ditolak Backoffice (Dikembalikan ke Loket)',
            'catatan' => $request->catatan
        ]);

        return back()->with('error', 'Berkas dikembalikan ke Loket Penerimaan.');
    }

    // 3. FUNGSI EDIT BERKAS
    public function update(Request $request, $id)
    {
        $berkas = Berkas::findOrFail($id);

        $validated = $request->validate([
            'nomer_berkas'     => 'required|unique:berkas,nomer_berkas,' . $id,
            'tahun_berkas'     => 'required|integer',
            'nama_pemohon'     => 'required|string|max:255',
            'tipe_berkas'      => 'required|string',
            'jenis_permohonan' => 'required|string',
            'jenis_hak'        => 'required|string',
            'nomer_hak'        => 'required|string',
            'kecamatan'        => 'required|string',
            'desa'             => 'required|string',
        ]);

        $berkas->update($validated);

        RiwayatBerkas::create([
            'berkas_id' => $berkas->id,
            'dari_user_id' => auth()->id() ?? 1,
            'aksi' => 'Data Berkas Diubah',
            'catatan' => 'Data detail berkas diperbarui oleh Backoffice.'
        ]);

        return back()->with('success', 'Data berkas berhasil diperbarui.');
    }
}