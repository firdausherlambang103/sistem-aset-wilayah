<div x-data="{ sidebarOpen: false }">

    <nav class="bg-white border-b border-slate-200 md:hidden flex justify-between h-16 px-4 items-center sticky top-0 z-40 shadow-sm">
        <a href="{{ url('/') }}" class="flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center text-white font-bold">
                <i class="fa-solid fa-map-location-dot"></i>
            </div>
            <span class="font-black text-slate-800 text-lg tracking-tight">Sistem Nganjuk</span>
        </a>
        <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-blue-600 focus:outline-none transition">
            <i class="fa-solid fa-bars text-2xl" x-show="!sidebarOpen"></i>
            <i class="fa-solid fa-xmark text-2xl" x-show="sidebarOpen" x-cloak></i>
        </button>
    </nav>

    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/50 z-40 md:hidden" @click="sidebarOpen = false" x-cloak></div>

    <aside :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 shadow-2xl md:shadow-none transform transition-transform duration-300 md:translate-x-0 flex flex-col h-screen">
        
        <div class="hidden md:flex h-16 shrink-0 items-center px-6 border-b border-slate-200 bg-white">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center text-white font-bold shadow-sm">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <span class="font-black text-slate-800 text-lg tracking-tight">Sistem Nganjuk</span>
            </a>
        </div>

        <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1 bg-white">
            
            @php
                $linkClass = function($active) {
                    return $active 
                        ? 'flex items-center px-3 py-2.5 rounded-lg bg-blue-50 text-blue-700 font-bold mb-1 transition-colors border-l-4 border-blue-600'
                        : 'flex items-center px-3 py-2.5 rounded-lg text-slate-600 font-medium hover:bg-slate-50 hover:text-slate-900 mb-1 transition-colors border-l-4 border-transparent';
                };
            @endphp

            @if(Auth::check() && Auth::user()->role === 'admin')
                <div class="px-3 py-2 text-[10px] font-black text-slate-400 uppercase tracking-widest mt-2 mb-1">Administrator</div>
                <a href="{{ route('admin.users.index') }}" class="{{ $linkClass(request()->routeIs('admin.users.*')) }}">
                    <i class="fa-solid fa-users w-6 text-center mr-2"></i> User & Approval
                </a>
                <a href="{{ route('admin.wilayah.index') }}" class="{{ $linkClass(request()->routeIs('admin.wilayah.*')) }}">
                    <i class="fa-solid fa-map-location-dot w-6 text-center mr-2"></i> Master Wilayah
                </a>
            @endif

            @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'bpn']))
                <div class="px-3 py-2 text-[10px] font-black text-slate-400 uppercase tracking-widest mt-4 mb-1">Layanan BPN</div>
                <a href="{{ route('bpn.dashboard') }}" class="{{ $linkClass(request()->routeIs('bpn.dashboard')) }}">
                    <i class="fa-solid fa-gauge w-6 text-center mr-2"></i> Dashboard BPN
                </a>
                <a href="{{ route('bpn.loket.index') }}" class="{{ $linkClass(request()->routeIs('bpn.loket.*')) }}">
                    <i class="fa-solid fa-inbox w-6 text-center mr-2"></i> Loket Penerimaan
                </a>
                <a href="{{ route('bpn.pembayaran.index') }}" class="{{ $linkClass(request()->routeIs('bpn.pembayaran.*')) }}">
                    <i class="fa-solid fa-file-invoice-dollar w-6 text-center mr-2"></i> Loket Pembayaran
                </a>
                <a href="{{ route('bpn.pelaksana.index') }}" class="{{ $linkClass(request()->routeIs('bpn.pelaksana.*')) }}">
                    <i class="fa-solid fa-layer-group w-6 text-center mr-2"></i> Pelaksana Kegiatan
                </a>
                <a href="{{ route('bpn.peta') }}" class="{{ $linkClass(request()->routeIs('bpn.peta')) }}">
                    <i class="fa-solid fa-map w-6 text-center mr-2"></i> Peta Utama
                </a>
            @endif

            @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'mitra']))
                <div class="px-3 py-2 text-[10px] font-black text-slate-400 uppercase tracking-widest mt-4 mb-1">Ruang Mitra</div>
                <a href="{{ route('mitra.berkas.biasa') }}" class="{{ $linkClass(request()->routeIs('mitra.berkas.biasa')) }}">
                    <i class="fa-solid fa-folder-open w-6 text-center mr-2"></i> Berkas Fisik
                </a>
                <a href="{{ route('mitra.plotting') }}" class="{{ $linkClass(request()->routeIs('mitra.plotting')) }}">
                    <i class="fa-solid fa-map-location-dot w-6 text-center mr-2"></i> Plotting Spasial
                </a>
            @endif
        </div>

        <div class="shrink-0 border-t border-slate-200 p-4 bg-slate-50">
            <div class="flex items-center gap-3 px-2 mb-4">
                <div class="w-10 h-10 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 font-bold shrink-0">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="overflow-hidden">
                    <div class="text-sm font-bold text-slate-800 truncate" title="{{ Auth::user()->email ?? 'Pengguna' }}">
                        {{ Auth::user()->email ?? 'Pengguna' }}
                    </div>
                    <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                        {{ Auth::user()->role ?? '' }}
                    </div>
                </div>
            </div>

            <div class="space-y-1">
                <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2 rounded-lg text-slate-600 text-sm font-semibold hover:bg-slate-200 hover:text-slate-900 transition-colors">
                    <i class="fa-solid fa-user-pen w-5 text-center mr-2 text-slate-400"></i> Edit Profil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-3 py-2 rounded-lg text-rose-600 text-sm font-semibold hover:bg-rose-100 transition-colors">
                        <i class="fa-solid fa-right-from-bracket w-5 text-center mr-2 text-rose-400"></i> Log Out
                    </button>
                </form>
            </div>
        </div>
    </aside>
</div>