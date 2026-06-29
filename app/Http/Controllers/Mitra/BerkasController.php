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
        $berkas = Berkas::where('tipe_berkas', 'biasa')
                        ->where('mitra_id', auth()->id())
                        ->orderBy('created_at', 'desc')
                        ->get();
                        
        $kecamatans = Kecamatan::with('desa')->orderBy('nama_kecamatan')->get();
        $jenisHaks = JenisHak::orderBy('id')->get();
        
        // Daftar Jenis Permohonan Standar BPN
        $jenisPermohonans = [
            'Pendaftaran Tanah Pertama Kali',
            'Pemecahan Bidang Tanah',
            'Penggabungan Bidang Tanah',
            'Peralihan Hak (Balik Nama Jual Beli)',
            'Peralihan Hak (Balik Nama Waris)',
            'Pengecekan Sertipikat',
            'Penurunan Hak',
            'Peningkatan Hak (HGB ke HM)',
            'Pembaruan Hak',
            'Roya (Penghapusan Hak Tanggungan)'
        ];

        // Tambahkan variabel $jenisPermohonans ke dalam compact()
        return view('mitra.ruang_kerja_biasa', compact('berkas', 'kecamatans', 'jenisHaks', 'jenisPermohonans'));
    }

    // 1. Simpan Berkas Biasa + Generate No Berkas Unik
    public function storeBerkasBiasa(Request $request)
    {
        // Sesuaikan validasi dengan struktur form HTML yang baru (menggunakan ID)
        $request->validate([
            'nama_pemohon' => 'required|string',
            'jenis_permohonan' => 'required',
            'jenis_hak' => 'required',
            'nomer_hak' => 'required',
            'kecamatan_id' => 'required',
            'desa_id' => 'required',
        ]);

        // Generate 6 digit alphanumeric acak & pastikan belum ada di DB
        do {
            $nomerUrutAcak = strtoupper(Str::random(6));
        } while (Berkas::where('nomer_berkas', $nomerUrutAcak)->exists());

        // Ambil nama (string) Kecamatan dan Desa berdasarkan ID yang dipilih user
        $kecamatan = Kecamatan::find($request->kecamatan_id);
        $desa = Desa::find($request->desa_id);

        $berkas = Berkas::create([
            'nomer_berkas' => $nomerUrutAcak,
            'tahun_berkas' => now()->year,
            'nama_pemohon' => $request->nama_pemohon,
            'jenis_permohonan' => $request->jenis_permohonan,
            'jenis_hak' => $request->jenis_hak,
            'nomer_hak' => $request->nomer_hak,
            'kecamatan' => $kecamatan ? $kecamatan->nama_kecamatan : '',
            'desa' => $desa ? $desa->nama_desa : '',
            'tipe_berkas' => 'biasa',
            'status_berkas' => 'draft',
            'mitra_id' => auth()->id(),
        ]);

        // Karena form menggunakan mekanisme Submit tradisional, 
        // kita alihkan (redirect) kembali ke halaman sebelumnya dengan membawa Alert Pesan Sukses
        return redirect()->back()->with('success', 'Berkas Permohonan baru berhasil dibuat dengan Kode: ' . $berkas->nomer_berkas);
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