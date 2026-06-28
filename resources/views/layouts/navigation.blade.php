<nav class="bg-white border-b border-slate-200 md:hidden flex justify-between items-center h-16 px-4 sticky top-0 z-40 w-full shadow-sm">
    <a href="{{ url('/') }}" class="flex items-center gap-3 shrink-0">
        <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold shadow-md">
            <i class="fa-solid fa-map-location-dot"></i>
        </div>
        <span class="font-black text-slate-800 text-lg tracking-tight">Sistem Nganjuk</span>
    </a>
    <button @click="mobileSidebarOpen = !mobileSidebarOpen" class="text-slate-500 hover:text-blue-700 bg-slate-100 p-2 rounded-lg transition-colors focus:outline-none shrink-0">
        <i class="fa-solid fa-bars w-5 h-5 flex items-center justify-center" x-show="!mobileSidebarOpen"></i>
        <i class="fa-solid fa-xmark w-5 h-5 flex items-center justify-center text-xl" x-show="mobileSidebarOpen" x-cloak></i>
    </button>
</nav>

<div x-show="mobileSidebarOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 md:hidden" @click="mobileSidebarOpen = false" x-cloak></div>

<aside :class="[
        mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full',
        desktopSidebarCollapsed ? 'md:w-20' : 'md:w-64'
    ]" 
    class="fixed inset-y-0 left-0 z-50 bg-white border-r border-slate-200 shadow-2xl md:shadow-none transform transition-all duration-300 ease-in-out md:translate-x-0 flex flex-col h-screen">
    
    <div class="h-20 shrink-0 flex items-center justify-start border-b border-slate-100 px-4 relative overflow-hidden">
        <a href="{{ url('/') }}" class="flex items-center gap-3 w-full">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold shadow-lg shadow-blue-500/30 shrink-0 mx-auto md:mx-0">
                <i class="fa-solid fa-map-location-dot text-lg"></i>
            </div>
            <div class="flex flex-col transition-opacity duration-300 whitespace-nowrap" x-show="!desktopSidebarCollapsed">
                <span class="font-black text-slate-800 text-lg leading-tight">Sistem Nganjuk</span>
                <span class="text-[10px] font-bold text-blue-500 uppercase tracking-widest leading-tight">Aset Wilayah</span>
            </div>
        </a>
        
        <button @click="desktopSidebarCollapsed = !desktopSidebarCollapsed" class="hidden md:flex absolute -right-3 top-7 w-7 h-7 bg-white border border-slate-200 rounded-full items-center justify-center text-slate-400 hover:text-blue-600 hover:border-blue-300 shadow-sm transition-all z-50 focus:outline-none">
            <i class="fa-solid fa-chevron-left text-xs transition-transform duration-300" :class="desktopSidebarCollapsed ? 'rotate-180' : ''"></i>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto overflow-x-hidden py-5 px-3 space-y-1 sidebar-scroll bg-slate-50/50">
        
        @php
            // Fungsi untuk menentukan class warna menu yang sedang aktif
            $linkClass = function($active) {
                return $active 
                    ? 'flex items-center rounded-xl bg-blue-50 text-blue-700 font-bold mb-2 transition-all group relative border border-blue-100 shadow-sm overflow-hidden p-2'
                    : 'flex items-center rounded-xl text-slate-500 font-semibold hover:bg-white hover:text-slate-900 mb-2 transition-all group relative border border-transparent hover:border-slate-200 hover:shadow-sm overflow-hidden p-2';
            };
        @endphp

        @if(Auth::check() && Auth::user()->role === 'admin')
            <div x-show="!desktopSidebarCollapsed" class="px-2 py-1 text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1 mb-2 whitespace-nowrap">Administrator</div>
            <div x-show="desktopSidebarCollapsed" class="h-4 mt-1 mb-2 border-b border-slate-200 mx-2" x-cloak></div>
            
            <a href="{{ route('admin.users.index') }}" class="{{ $linkClass(request()->routeIs('admin.users.*')) }}">
                <div class="w-8 shrink-0 flex justify-center items-center">
                    <i class="fa-solid fa-users-gear text-lg transition-colors group-hover:text-blue-600"></i>
                </div>
                <span x-show="!desktopSidebarCollapsed" class="ml-2 whitespace-nowrap">User & Approval</span>
                <div x-show="desktopSidebarCollapsed" class="absolute left-14 bg-slate-800 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-50 shadow-xl" x-cloak>User & Approval</div>
            </a>
            
            <a href="{{ route('admin.wilayah.index') }}" class="{{ $linkClass(request()->routeIs('admin.wilayah.*')) }}">
                <div class="w-8 shrink-0 flex justify-center items-center">
                    <i class="fa-solid fa-map-location-dot text-lg transition-colors group-hover:text-blue-600"></i>
                </div>
                <span x-show="!desktopSidebarCollapsed" class="ml-2 whitespace-nowrap">Master Wilayah</span>
                <div x-show="desktopSidebarCollapsed" class="absolute left-14 bg-slate-800 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-50 shadow-xl" x-cloak>Master Wilayah</div>
            </a>
        @endif

        @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'bpn']))
            <div x-show="!desktopSidebarCollapsed" class="px-2 py-1 text-[10px] font-black text-slate-400 uppercase tracking-widest mt-4 mb-2 whitespace-nowrap">Layanan BPN</div>
            <div x-show="desktopSidebarCollapsed" class="h-4 mt-4 mb-2 border-b border-slate-200 mx-2" x-cloak></div>

            <a href="{{ route('bpn.dashboard') }}" class="{{ $linkClass(request()->routeIs('bpn.dashboard')) }}">
                <div class="w-8 shrink-0 flex justify-center items-center">
                    <i class="fa-solid fa-gauge-high text-lg transition-colors group-hover:text-blue-600"></i>
                </div>
                <span x-show="!desktopSidebarCollapsed" class="ml-2 whitespace-nowrap">Dashboard BPN</span>
                <div x-show="desktopSidebarCollapsed" class="absolute left-14 bg-slate-800 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-50 shadow-xl" x-cloak>Dashboard BPN</div>
            </a>
            
            <a href="{{ route('bpn.loket.index') }}" class="{{ $linkClass(request()->routeIs('bpn.loket.*')) }}">
                <div class="w-8 shrink-0 flex justify-center items-center">
                    <i class="fa-solid fa-inbox text-lg transition-colors group-hover:text-blue-600"></i>
                </div>
                <span x-show="!desktopSidebarCollapsed" class="ml-2 whitespace-nowrap">Loket Penerimaan</span>
                <div x-show="desktopSidebarCollapsed" class="absolute left-14 bg-slate-800 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-50 shadow-xl" x-cloak>Loket Penerimaan</div>
            </a>

            <a href="{{ route('bpn.pembayaran.index') }}" class="{{ $linkClass(request()->routeIs('bpn.pembayaran.*')) }}">
                <div class="w-8 shrink-0 flex justify-center items-center">
                    <i class="fa-solid fa-file-invoice-dollar text-lg transition-colors group-hover:text-blue-600"></i>
                </div>
                <span x-show="!desktopSidebarCollapsed" class="ml-2 whitespace-nowrap">Loket Pembayaran</span>
                <div x-show="desktopSidebarCollapsed" class="absolute left-14 bg-slate-800 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-50 shadow-xl" x-cloak>Loket Pembayaran</div>
            </a>

            <a href="{{ route('bpn.pelaksana.index') }}" class="{{ $linkClass(request()->routeIs('bpn.pelaksana.*')) }}">
                <div class="w-8 shrink-0 flex justify-center items-center">
                    <i class="fa-solid fa-layer-group text-lg transition-colors group-hover:text-blue-600"></i>
                </div>
                <span x-show="!desktopSidebarCollapsed" class="ml-2 whitespace-nowrap">Pelaksana Kegiatan</span>
                <div x-show="desktopSidebarCollapsed" class="absolute left-14 bg-slate-800 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-50 shadow-xl" x-cloak>Pelaksana Kegiatan</div>
            </a>

            <a href="{{ route('bpn.peta') }}" class="{{ $linkClass(request()->routeIs('bpn.peta')) }}">
                <div class="w-8 shrink-0 flex justify-center items-center">
                    <i class="fa-solid fa-map text-lg transition-colors group-hover:text-blue-600"></i>
                </div>
                <span x-show="!desktopSidebarCollapsed" class="ml-2 whitespace-nowrap">Peta Utama Nganjuk</span>
                <div x-show="desktopSidebarCollapsed" class="absolute left-14 bg-slate-800 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-50 shadow-xl" x-cloak>Peta Utama Nganjuk</div>
            </a>
        @endif

        @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'mitra']))
            <div x-show="!desktopSidebarCollapsed" class="px-2 py-1 text-[10px] font-black text-slate-400 uppercase tracking-widest mt-4 mb-2 whitespace-nowrap">Ruang Kerja Mitra</div>
            <div x-show="desktopSidebarCollapsed" class="h-4 mt-4 mb-2 border-b border-slate-200 mx-2" x-cloak></div>

            <a href="{{ route('mitra.berkas.biasa') }}" class="{{ $linkClass(request()->routeIs('mitra.berkas.biasa')) }}">
                <div class="w-8 shrink-0 flex justify-center items-center">
                    <i class="fa-solid fa-folder-open text-lg transition-colors group-hover:text-blue-600"></i>
                </div>
                <span x-show="!desktopSidebarCollapsed" class="ml-2 whitespace-nowrap">Berkas Fisik</span>
                <div x-show="desktopSidebarCollapsed" class="absolute left-14 bg-slate-800 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-50 shadow-xl" x-cloak>Berkas Fisik</div>
            </a>

            <a href="{{ route('mitra.plotting') }}" class="{{ $linkClass(request()->routeIs('mitra.plotting')) }}">
                <div class="w-8 shrink-0 flex justify-center items-center">
                    <i class="fa-solid fa-map-pin text-lg transition-colors group-hover:text-blue-600"></i>
                </div>
                <span x-show="!desktopSidebarCollapsed" class="ml-2 whitespace-nowrap">Plotting Spasial</span>
                <div x-show="desktopSidebarCollapsed" class="absolute left-14 bg-slate-800 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-50 shadow-xl" x-cloak>Plotting Spasial</div>
            </a>
        @endif
    </div>

    <div class="shrink-0 border-t border-slate-200 p-3 bg-white overflow-hidden">
        
        <div class="flex items-center px-1 mb-3">
            <div class="w-10 h-10 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 font-bold shrink-0 mx-auto md:mx-0">
                <i class="fa-solid fa-user-astronaut"></i>
            </div>
            <div x-show="!desktopSidebarCollapsed" class="ml-3 overflow-hidden transition-opacity">
                <div class="text-sm font-bold text-slate-800 truncate" title="{{ Auth::user()->email ?? 'Pengguna' }}">
                    {{ Auth::user()->email ?? 'Pengguna' }}
                </div>
                <div class="text-[10px] font-black text-blue-600 uppercase tracking-widest">
                    {{ Auth::user()->role ?? '' }}
                </div>
            </div>
        </div>

        <div class="space-y-2">
            <a href="{{ route('profile.edit') }}" class="flex items-center rounded-lg text-slate-500 text-sm font-bold hover:bg-slate-100 hover:text-slate-900 transition-colors group relative border border-transparent p-2">
                <div class="w-8 shrink-0 flex justify-center items-center">
                    <i class="fa-solid fa-user-pen text-lg transition-colors"></i>
                </div>
                <span x-show="!desktopSidebarCollapsed" class="ml-2 whitespace-nowrap">Edit Profil</span>
                <div x-show="desktopSidebarCollapsed" class="absolute left-14 bg-slate-800 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-50 shadow-xl" x-cloak>Edit Profil</div>
            </a>
            
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full flex items-center rounded-lg text-rose-500 text-sm font-bold hover:bg-rose-50 hover:text-rose-700 transition-colors group relative border border-transparent p-2">
                    <div class="w-8 shrink-0 flex justify-center items-center">
                        <i class="fa-solid fa-power-off text-lg transition-colors"></i>
                    </div>
                    <span x-show="!desktopSidebarCollapsed" class="ml-2 whitespace-nowrap">Log Out</span>
                    <div x-show="desktopSidebarCollapsed" class="absolute left-14 bg-rose-600 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-50 shadow-xl" x-cloak>Keluar / Log Out</div>
                </button>
            </form>
        </div>
    </div>
</aside>