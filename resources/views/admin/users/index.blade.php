<x-app-layout>
    <x-slot name="header">Manajemen Pengguna & Persetujuan (Approval)</x-slot>

    <div class="p-4 lg:p-8">
        
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3 animate-pulse-once">
                <i class="fa-solid fa-circle-check text-emerald-500"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200">
            <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-users-gear text-blue-600"></i> Daftar Akun Terdaftar
                </h3>
            </div>
            
            <div class="overflow-x-auto custom-scrollbar">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Identitas Pengguna</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tipe Akses (Role)</th>
                            <th class="px-5 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Status Akses Sistem</th>
                            <th class="px-5 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi Admin</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-5 py-4">
                                    <div class="text-sm font-extrabold text-slate-800">
                                        @if($user->role === 'mitra')
                                            {{ $user->profilMitra->nama ?? 'Belum isi profil' }}
                                        @else
                                            {{ $user->profilBpn->nama ?? 'Belum isi profil' }}
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-500 font-semibold mt-0.5"><i class="fa-solid fa-envelope mr-1"></i> {{ $user->email }}</div>
                                    
                                    @if($user->role === 'mitra')
                                        <div class="text-[10px] text-blue-600 mt-1 font-bold">Kode Mitra: {{ $user->profilMitra->kode_mitra ?? '-' }}</div>
                                    @endif
                                </td>
                                
                                <td class="px-5 py-4">
                                    @if($user->role === 'bpn')
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-blue-100 text-blue-700 border border-blue-200">
                                            Petugas BPN
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-100 text-amber-700 border border-amber-200">
                                            Mitra / Eksternal
                                        </span>
                                    @endif
                                </td>
                                
                                <td class="px-5 py-4 text-center">
                                    @if($user->is_approved)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-700">
                                            <i class="fa-solid fa-shield-check"></i> Akses Diizinkan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase bg-rose-100 text-rose-700">
                                            <i class="fa-solid fa-lock"></i> Menunggu Persetujuan
                                        </span>
                                    @endif
                                </td>
                                
                                <td class="px-5 py-4 text-right">
                                    <form action="{{ route('admin.users.toggle-approval', $user->id) }}" method="POST">
                                        @csrf
                                        @if($user->is_approved)
                                            <button type="submit" class="bg-rose-50 hover:bg-rose-500 text-rose-600 hover:text-white border border-rose-200 text-xs font-bold py-1.5 px-4 rounded-lg transition-colors">
                                                <i class="fa-solid fa-ban"></i> Cabut Akses
                                            </button>
                                        @else
                                            <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white shadow text-xs font-bold py-1.5 px-4 rounded-lg transition-colors">
                                                <i class="fa-solid fa-check"></i> Setujui Akun
                                            </button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-slate-500 font-medium">Belum ada pengguna terdaftar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>