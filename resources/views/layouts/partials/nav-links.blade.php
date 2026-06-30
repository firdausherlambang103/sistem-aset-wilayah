<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
    <x-nav-link :href="route('ruang-kerja')" :active="request()->routeIs('ruang-kerja')">
        {{ __('Ruang Kerja') }}
    </x-nav-link>

    @if(auth()->user()->jabatan === 'admin')
        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
            {{ __('Manajemen User') }}
        </x-nav-link>
        
        <x-nav-link :href="route('admin.wilayah.index')" :active="request()->routeIs('admin.wilayah.*')">
            {{ __('Data Wilayah') }}
        </x-nav-link>
    @endif

    <x-nav-link :href="url('/map')" :active="request()->is('map')">
        {{ __('Peta Aset') }}
    </x-nav-link>
</div>