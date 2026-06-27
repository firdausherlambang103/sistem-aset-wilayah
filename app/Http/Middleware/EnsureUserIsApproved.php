<?php

// app/Http/Middleware/EnsureUserIsApproved.php
namespace App\Http/Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->is_approved) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Akun Anda belum disetujui oleh Administrator BPN.');
        }

        return $next($request);
    }
}