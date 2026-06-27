<?php
// app/Http/Controllers/Mitra/BerkasController.php
namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Berkas;
use App\Models\DataPlotting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BerkasController extends Controller
{
    // 1. Simpan Berkas Biasa + Generate No Berkas Unik
    public function storeBerkasBiasa(Request $request)
    {
        $request->validate([
            'nama_pemohon' => 'required|string',
            'jenis_permohonan' => 'required',
            'jenis_hak' => 'required',
            'nomer_hak' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
        ]);

        // Generate 6 digit alphanumeric acak & pastikan belum ada di DB
        do {
            $nomerUrutAcak = strtoupper(Str::random(6));
        } while (Berkas::where('nomer_berkas', $nomerUrutAcak)->exists());

        $berkas = Berkas::create([
            'nomer_berkas' => $nomerUrutAcak,
            'tahun_berkas' => now()->year,
            'nama_pemohon' => $request->nama_pemohon,
            'jenis_permohonan' => $request->jenis_permohonan,
            'jenis_hak' => $request->jenis_hak,
            'nomer_hak' => $request->nomer_hak,
            'kecamatan' => $request->kecamatan,
            'desa' => $request->desa,
            'tipe_berkas' => 'biasa',
            'status_berkas' => 'draft',
            'mitra_id' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Berkas berhasil dibuat',
            'nomer_berkas' => $berkas->nomer_berkas,
            // Frontend bisa menggunakan parameter ini untuk render QR Code dengan SimpleQRCode
            'qr_content' => route('bpn.loket.terima', ['nomer_berkas' => $berkas->nomer_berkas]) 
        ]);
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

        return response()->json(['message' => 'Berkas Plotting Spasial berhasil disimpan.']);
    }
}