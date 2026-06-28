<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Sistem Monitoring Berkas') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <style>
            /* Custom Scrollbar untuk Sidebar agar lebih cantik */
            .sidebar-scroll::-webkit-scrollbar { width: 4px; }
            .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
            .sidebar-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
            .sidebar-scroll:hover::-webkit-scrollbar-thumb { background: #94a3b8; }
        </style>
    </head>
    <body class="font-sans antialiased text-slate-800 bg-slate-50/50" x-data="{ mobileSidebarOpen: false, desktopSidebarCollapsed: false }">
        
        <div class="min-h-screen flex overflow-hidden bg-slate-50/50">
            
            @include('layouts.navigation')

            <div :class="desktopSidebarCollapsed ? 'md:ml-20' : 'md:ml-64'" class="flex-1 flex flex-col min-h-screen transition-all duration-300 ease-in-out md:ml-64 w-full">
                
                @if (isset($header))
                    <header class="bg-white/80 backdrop-blur-md shadow-sm border-b border-slate-200 sticky top-0 z-30 hidden md:block">
                        <div class="py-4 px-8 flex justify-between items-center">
                            <h2 class="font-black text-xl text-slate-800 leading-tight tracking-tight">
                                {{ $header }}
                            </h2>
                            <div class="text-sm font-semibold text-slate-500">
                                <i class="fa-regular fa-calendar mr-1"></i> {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                            </div>
                        </div>
                    </header>
                @endif

                <main class="flex-1 p-4 sm:p-6 lg:p-8 w-full overflow-x-hidden">
                    {{ $slot }}
                </main>

            </div>

        </div>
    </body>
</html>