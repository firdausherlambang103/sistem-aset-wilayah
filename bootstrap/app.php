<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // Mengatur arah redirect untuk user yang SUDAH LOGIN
        $middleware->redirectUsersTo(function (Request $request) {
            $role = auth()->user()->role ?? 'mitra';
            return match ($role) {
                'admin' => route('admin.users.index'),
                'bpn'   => route('bpn.dashboard'),
                'mitra' => route('mitra.berkas.biasa'),
                default => '/',
            };
        });

        // Mendaftarkan alias middleware agar mudah dipanggil di rute
        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();