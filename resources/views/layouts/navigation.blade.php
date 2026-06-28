<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ url('/') }}" class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center text-white font-bold shadow-sm">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </div>
                        <span class="font-black text-slate-800 text-lg hidden sm:block tracking-tight">Sistem Nganjuk</span>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    
                    @if(Auth::check() && Auth::user()->role === 'admin')
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            <i class="fa-solid fa-users mr-2"></i> User & Approval
                        </x-nav-link>
                        <x-nav-link :href="route('admin.wilayah.index')" :active="request()->routeIs('admin.wilayah.*')"> 
                            <i class="fa-solid fa-map-location-dot mr-2"></i> Master Wilayah
                        </x-nav-link>
                    @endif

                    @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'bpn']))
                        <x-nav-link :href="route('bpn.dashboard')" :active="request()->routeIs('bpn.dashboard')">
                            <i class="fa-solid fa-gauge mr-2"></i> Dashboard BPN
                        </x-nav-link>
                        <x-nav-link :href="route('bpn.loket.index')" :active="request()->routeIs('bpn.loket.*')">
                            <i class="fa-solid fa-inbox mr-2"></i> Loket Penerimaan
                        </x-nav-link>
                        <x-nav-link :href="route('bpn.pembayaran.index')" :active="request()->routeIs('bpn.pembayaran.*')">
                            <i class="fa-solid fa-file-invoice-dollar mr-2"></i> Loket Pembayaran
                        </x-nav-link>
                        <x-nav-link :href="route('bpn.pelaksana.index')" :active="request()->routeIs('bpn.pelaksana.*')">
                            <i class="fa-solid fa-layer-group mr-2"></i> Pelaksana Kegiatan
                        </x-nav-link>
                        <x-nav-link :href="route('bpn.peta')" :active="request()->routeIs('bpn.peta')">
                            <i class="fa-solid fa-map mr-2"></i> Peta Utama Nganjuk
                        </x-nav-link>
                    @endif

                    @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'mitra']))
                        <x-nav-link :href="route('mitra.berkas.biasa')" :active="request()->routeIs('mitra.berkas.biasa')">
                            <i class="fa-solid fa-folder-open mr-2"></i> Berkas Fisik
                        </x-nav-link>
                        <x-nav-link :href="route('mitra.plotting')" :active="request()->routeIs('mitra.plotting')">
                            <i class="fa-solid fa-map-location-dot mr-2"></i> Plotting Spasial
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-slate-200 text-sm leading-4 font-bold rounded-lg text-slate-600 bg-slate-50 hover:text-slate-800 hover:bg-slate-100 focus:outline-none transition ease-in-out duration-150 shadow-sm">
                            <i class="fa-solid fa-circle-user mr-2 text-blue-600 text-lg"></i>
                            <div>{{ Auth::user()->email ?? 'Pengguna' }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="font-semibold text-slate-700">
                            <i class="fa-solid fa-user-pen mr-2 text-slate-400"></i> {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();" class="font-semibold text-rose-600 hover:text-rose-700">
                                <i class="fa-solid fa-right-from-bracket mr-2 text-rose-400"></i> {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-slate-50 border-b border-slate-200">
        <div class="pt-2 pb-3 space-y-1">
            
            @if(Auth::check() && Auth::user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    User & Approval
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.wilayah.index')" :active="request()->routeIs('admin.wilayah.*')">
                    Master Wilayah
                </x-responsive-nav-link>
            @endif

            @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'bpn']))
                <x-responsive-nav-link :href="route('bpn.dashboard')" :active="request()->routeIs('bpn.dashboard')">
                    Dashboard BPN
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('bpn.loket.index')" :active="request()->routeIs('bpn.loket.*')">
                    Loket Penerimaan
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('bpn.pembayaran.index')" :active="request()->routeIs('bpn.pembayaran.*')">
                    Loket Pembayaran
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('bpn.pelaksana.index')" :active="request()->routeIs('bpn.pelaksana.*')">
                    Pelaksana Kegiatan
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('bpn.peta')" :active="request()->routeIs('bpn.peta')">
                    Peta Utama Nganjuk
                </x-responsive-nav-link>
            @endif

            @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'mitra']))
                <x-responsive-nav-link :href="route('mitra.berkas.biasa')" :active="request()->routeIs('mitra.berkas.biasa')">
                    Berkas Fisik
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('mitra.plotting')" :active="request()->routeIs('mitra.plotting')">
                    Plotting Spasial
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-slate-200">
            <div class="px-4">
                <div class="font-bold text-base text-slate-800">{{ Auth::user()->email ?? '' }}</div>
                <div class="font-medium text-sm text-slate-500 capitalize">{{ Auth::user()->role ?? '' }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();" class="text-rose-600">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>