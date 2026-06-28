@php
    $isResponsive = $responsive ?? false;
    $component = $isResponsive ? 'responsive-nav-link' : 'nav-link';
@endphp

@if(Auth::check() && Auth::user()->role === 'admin')
    <x-{{$component}} :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">User & Approval</x-{{$component}}>
    <x-{{$component}} :href="route('admin.wilayah.index')" :active="request()->routeIs('admin.wilayah.*')">Master Wilayah</x-{{$component}}>
@endif

{{-- Tambahkan menu lainnya dengan pola yang sama ... --}}