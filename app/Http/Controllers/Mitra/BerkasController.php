<?php
// app/Http/Controllers/Mitra/BerkasController.php
namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Berkas;
use App\Models\DataPlotting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Kecamatan;
use App\Models\Desa;
use App\Models\JenisHak;

class BerkasController extends Controller
{
    public function indexBiasa()
    {
        $mitraId = auth()->id();

        // 1. Berkas di Mitra (Status: Draft atau Dikembalikan)
        $berkasMitra = Berkas::with('sps')
            ->where('mitra_id', $mitraId)
            ->where('tipe_berkas', 'biasa')
            ->whereIn('status_berkas', ['draft', 'dikembalikan'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // 2. Berkas di BPN (Status: Diproses di Loket, Backoffice, Pembayaran, Pelaksana)
        $berkasBpn = Berkas::with('sps')
            ->where('mitra_id', $mitraId)
            ->where('tipe_berkas', 'biasa')
            ->whereNotIn('status_berkas', ['draft', 'dikembalikan', 'selesai'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // 3. Berkas Selesai
        $berkasSelesai = Berkas::with('sps')
            ->where('mitra_id', $mitraId)
            ->where('tipe_berkas', 'biasa')
            ->where('status_berkas', 'selesai')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('mitra.ruang_kerja_biasa', compact('berkasMitra', 'berkasBpn', 'berkasSelesai'));
    }

    public function storeBerkasBiasa(Request $request)
    {
        $validated = $request->validate([
            'nomer_berkas'     => 'required|string',
            'tahun_berkas'     => 'required|integer',
            'nama_pemohon'     => 'required|string|max:255',
            'jenis_permohonan' => 'required|string',
            'jenis_hak'        => 'required|string',
            'nomer_hak'        => 'required|string',
            'kecamatan'        => 'required|string',
            'desa'             => 'required|string',
        ]);

        $validated['tipe_berkas'] = 'biasa';
        $validated['status_berkas'] = 'draft'; 
        $validated['mitra_id'] = auth()->id();

        $berkas = Berkas::create($validated);

        RiwayatBerkas::create([
            'berkas_id' => $berkas->id,
            'dari_user_id' => auth()->id(),
            'aksi' => 'Dibuat oleh Mitra',
            'catatan' => 'Berkas baru didaftarkan melalui Ruang Kerja Mitra.'
        ]);

        return back()->with('success', 'Berkas berhasil dibuat.');
    }

    // 2. Simpan Berkas Plotting Spasial (PostGIS GeoJSON)
    public function storeBerkasPlotting(Request $request)
    {
        $request->validate([
            'nama_pemohon' => 'required',
            'foto_lokasi' => 'required|image|mimes:jpeg,jpg,png|max:5120',
            'geo_json_data' => 'required', // Stringified GeoJSON dari Leaflet Draw/Polygon/Point
        ]);

        // Pengkondisian nomor acak
        do {
            $nomerUrutAcak = strtoupper(Str::random(6));
        } while (Berkas::where('nomer_berkas', $nomerUrutAcak)->exists());

        // Upload Foto
        $pathFoto = $request->file('foto_lokasi')->store('foto_lokasi_berkas', 'public');

        DB::transaction(function () use ($request, $nomerUrutAcak, $pathFoto) {
            
            // Catatan: Jika halaman Plotting nanti form kecamatannya juga menggunakan dropdown ID, 
            // maka logika pencarian nama kecamatan/desa harus disamakan dengan fungsi storeBerkasBiasa di atas.
            
            $berkas = Berkas::create([
                'nomer_berkas' => $nomerUrutAcak,
                'tahun_berkas' => now()->year,
                'nama_pemohon' => $request->nama_pemohon,
                'jenis_permohonan' => $request->jenis_permohonan,
                'jenis_hak' => $request->jenis_hak,
                'nomer_hak' => $request->nomer_hak,
                'kecamatan' => $request->kecamatan,
                'desa' => $request->desa,
                'tipe_berkas' => 'plotting',
                'status_berkas' => 'draft',
                'mitra_id' => auth()->id(),
            ]);

            // Menyimpan koordinat spasial menggunakan fungsi bawaan PostGIS
            // Mengubah GeoJSON string langsung menjadi struktur geometri spasial di database
            DB::statement("
                INSERT INTO data_plotting (berkas_id, foto_lokasi, geom, created_at, updated_at) 
                VALUES (?, ?, ST_GeomFromGeoJSON(?), NOW(), NOW())
            ", [
                $berkas->id, 
                $pathFoto, 
                $request->geo_json_data // Berupa format JSON objek Poligon/Point dari Leaflet
            ]);
        });

        // Halaman plotting biasanya di-submit melalui AJAX Fetch (Javascript bawaan peta Leaflet), 
        // sehingga response json dipertahankan.
        return response()->json(['message' => 'Berkas Plotting Spasial berhasil disimpan.']);
    }
}