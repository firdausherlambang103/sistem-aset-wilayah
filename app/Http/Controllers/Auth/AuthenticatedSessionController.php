<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Memproses request otentikasi (Login).
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // 1. Cek apakah akun sudah di-approve oleh Admin (Kecuali Admin itu sendiri)
        if ($user->role !== 'admin' && !$user->is_approved) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'Akun Anda sedang ditinjau. Silakan tunggu persetujuan dari Administrator.');
        }

        // 2. Redirect berdasarkan Role
        return match ($user->role) {
            'admin' => redirect()->route('admin.users.index'), 
            'bpn'   => redirect()->route('bpn.dashboard'),
            'mitra' => redirect()->route('mitra.berkas.biasa'),
            default => redirect('/'),
        };
    }

    /**
     * Proses Logout.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}