<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Menampilkan daftar user
    public function index()
    {
        // Ambil semua user kecuali akun admin itu sendiri
        $users = User::with(['profilMitra', 'profilBpn'])
                     ->where('role', '!=', 'admin')
                     ->orderBy('created_at', 'desc')
                     ->get();

        return view('admin.users.index', compact('users'));
    }

    // Mengaktifkan / Menonaktifkan status approval
    public function toggleApproval($id)
    {
        $user = User::findOrFail($id);
        
        // Membalikkan status (true jadi false, false jadi true)
        $user->is_approved = !$user->is_approved;
        $user->save();

        $statusText = $user->is_approved ? 'Disetujui dan Diaktifkan' : 'Dinonaktifkan';

        return back()->with('success', "Akun dengan email {$user->email} berhasil {$statusText}.");
    }
}