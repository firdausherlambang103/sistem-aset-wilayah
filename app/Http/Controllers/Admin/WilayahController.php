<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\Desa;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function index()
    {
        // Ambil data kecamatan beserta jumlah desa di dalamnya
        $kecamatans = Kecamatan::withCount('desa')->orderBy('nama_kecamatan')->get();
        
        // Ambil data desa beserta nama kecamatannya (Eager Loading)
        $desas = Desa::with('kecamatan')->orderBy('nama_desa')->get();

        return view('admin.wilayah.index', compact('kecamatans', 'desas'));
    }

    public function storeKecamatan(Request $request)
    {
        $request->validate([
            'nama_kecamatan' => 'required|string|unique:kecamatans,nama_kecamatan|max:255',
        ]);

        Kecamatan::create([
            'nama_kecamatan' => strtoupper($request->nama_kecamatan)
        ]);

        return back()->with('success', 'Data Kecamatan berhasil ditambahkan!');
    }

    public function storeDesa(Request $request)
    {
        $request->validate([
            'kecamatan_id' => 'required|exists:kecamatans,id',
            'nama_desa' => 'required|string|max:255',
        ]);

        Desa::create([
            'kecamatan_id' => $request->kecamatan_id,
            'nama_desa' => strtoupper($request->nama_desa)
        ]);

        return back()->with('success', 'Data Desa berhasil ditambahkan!');
    }
}