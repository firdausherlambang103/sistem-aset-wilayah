<x-app-layout>
    <x-slot name="header">Monitoring Dashboard BPN</x-slot>

    <div class="p-4 lg:p-8">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm border-l-4 border-l-blue-500 flex items-center gap-5 hover:shadow-md transition">
                <div class="w-14 h-14 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-2xl shrink-0"><i class="fa-solid fa-inbox"></i></div>
                <div><p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Masuk Hari Ini</p><h2 class="text-3xl font-black text-slate-800">{{ $masukHariIni }}</h2></div>
            </div>
            
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm border-l-4 border-l-emerald-500 flex items-center gap-5 hover:shadow-md transition">
                <div class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-2xl shrink-0"><i class="fa-solid fa-check-double"></i></div>
                <div><p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Selesai (Hari Ini)</p><h2 class="text-3xl font-black text-slate-800">{{ $selesaiHariIni }}</h2></div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm border-l-4 border-l-rose-500 flex items-center gap-5 hover:shadow-md transition">
                <div class="w-14 h-14 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center text-2xl shrink-0"><i class="fa-solid fa-hourglass-half"></i></div>
                <div><p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Sisa Belum Selesai</p><h2 class="text-3xl font-black text-slate-800">{{ $sisaKemarin }}</h2></div>
            </div>
        </div>

        <div x-data="{ activeTab: 'proses' }" class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200">
            
            <div class="p-6 border-b border-slate-100 bg-slate-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h3 class="font-extrabold text-lg text-slate-800 flex items-center gap-2"><i class="fa-solid fa-list-check text-blue-600"></i> Detail Riwayat Monitoring</h3>
                    <p class="text-[11px] text-slate-500 font-medium mt-1">Daftar ini bersifat *Read-Only*. Eksekusi penerimaan dan koreksi dilakukan melalui menu Ruang Kerja.</p>
                </div>
            </div>

            <div class="flex border-b border-slate-200 px-6 pt-2 bg-slate-50 gap-6 overflow-x-auto custom-scrollbar">
                <button @click="activeTab = 'proses'" 
                        :class="activeTab === 'proses' ? 'border-amber-500 text-amber-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-medium'"
                        class="px-4 py-3 border-b-[3px] transition-all flex items-center gap-2 text-sm whitespace-nowrap outline-none">
                    <i class="fa-solid fa-spinner" :class="activeTab === 'proses' ? 'fa-spin' : ''"></i> 
                    Belum Selesai ({{ $semuaBerkas->where('status_berkas', '!=', 'selesai')->count() }})
                </button>
                <button @click="activeTab = 'selesai'" 
                        :class="activeTab === 'selesai' ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-medium'"
                        class="px-4 py-3 border-b-[3px] transition-all flex items-center gap-2 text-sm whitespace-nowrap outline-none">
                    <i class="fa-solid fa-check-double"></i> 
                    Sudah Selesai ({{ $semuaBerkas->where('status_berkas', 'selesai')->count() }})
                </button>
                <button @click="activeTab = 'rekap'" 
                        :class="activeTab === 'rekap' ? 'border-blue-500 text-blue-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-medium'"
                        class="px-4 py-3 border-b-[3px] transition-all flex items-center gap-2 text-sm whitespace-nowrap outline-none">
                    <i class="fa-solid fa-users-viewfinder"></i> 
                    Rekap Kinerja Mitra
                </button>
            </div>
            
            <div x-show="activeTab === 'proses'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="overflow-x-auto custom-scrollbar">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider w-1/3">Detail Berkas & Legalitas</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Lokasi Wilayah</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Waktu Masuk</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Status Berkas</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @forelse($semuaBerkas->where('status_berkas', '!=', 'selesai') as $berkas)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-5">
                                    <div class="text-sm font-bold text-slate-800 truncate max-w-xs">{{ $berkas->nama_pemohon }}</div>
                                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 mt-2 rounded bg-slate-100 border border-slate-200 text-xs font-bold text-slate-600">
                                        <i class="fa-solid fa-file-contract text-blue-500"></i> {{ $berkas->jenis_hak }} - {{ $berkas->nomer_hak }}
                                    </div>
                                    <div class="text-[10px] text-slate-500 mt-2 font-semibold">
                                        <i class="fa-solid fa-user-pen text-slate-400 mr-1"></i> Diinput oleh: <span class="text-blue-600">{{ $berkas->mitra->profilMitra->nama ?? 'Mitra BPN' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-start gap-2">
                                        <i class="fa-solid fa-location-dot text-rose-500 mt-0.5"></i>
                                        <div>
                                            <div class="text-sm font-bold text-slate-800">{{ $berkas->desa ?? 'Belum diset' }}</div>
                                            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mt-0.5">Kec. {{ $berkas->kecamatan ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="text-[11px] font-semibold text-slate-500"><i class="fa-regular fa-calendar text-slate-400 mr-1"></i> Dibuat:</div>
                                    <div class="text-xs font-bold text-slate-700">{{ \Carbon\Carbon::parse($berkas->created_at)->format('d F Y - H:i') }}</div>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-100 text-amber-700 border border-amber-200">
                                        <i class="fa-solid fa-spinner fa-spin"></i> PROSES
                                    </span>
                                    <p class="text-[10px] font-bold text-slate-400 mt-2 capitalize">{{ str_replace('_', ' ', $berkas->status_berkas) }}</p>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-slate-500 font-medium">Tidak ada berkas yang sedang menunggu proses (Kosong).</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div x-show="activeTab === 'selesai'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="overflow-x-auto custom-scrollbar">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider w-1/3">Detail Berkas & Legalitas</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Lokasi Wilayah</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Waktu Selesai</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Status Berkas</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @forelse($semuaBerkas->where('status_berkas', 'selesai') as $berkas)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-5">
                                    <div class="text-sm font-bold text-slate-800 truncate max-w-xs">{{ $berkas->nama_pemohon }}</div>
                                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 mt-2 rounded bg-slate-100 border border-slate-200 text-xs font-bold text-slate-600">
                                        <i class="fa-solid fa-file-contract text-blue-500"></i> {{ $berkas->jenis_hak }} - {{ $berkas->nomer_hak }}
                                    </div>
                                    <div class="text-[10px] text-slate-500 mt-2 font-semibold">
                                        <i class="fa-solid fa-user-pen text-slate-400 mr-1"></i> Diinput oleh: <span class="text-blue-600">{{ $berkas->mitra->profilMitra->nama ?? 'Mitra BPN' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-start gap-2">
                                        <i class="fa-solid fa-location-dot text-rose-500 mt-0.5"></i>
                                        <div>
                                            <div class="text-sm font-bold text-slate-800">{{ $berkas->desa ?? 'Belum diset' }}</div>
                                            <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mt-0.5">Kec. {{ $berkas->kecamatan ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="text-[11px] font-semibold text-emerald-600"><i class="fa-solid fa-check-double mr-1"></i> Diselesaikan:</div>
                                    <div class="text-xs font-bold text-slate-700">{{ \Carbon\Carbon::parse($berkas->updated_at)->format('d F Y - H:i') }}</div>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fa-solid fa-check"></i> Selesai
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-slate-500 font-medium">Belum ada riwayat berkas yang diselesaikan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div x-show="activeTab === 'rekap'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="overflow-x-auto custom-scrollbar">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Mitra / Petugas Input</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Total Entri Berkas</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Telah Selesai</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Input Hari Ini</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Sisa Kemarin</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @forelse($rekapPengisi as $rekap)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm shadow-sm"><i class="fa-solid fa-user-tie"></i></div>
                                        <span class="font-extrabold text-slate-800">{{ $rekap['nama'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center font-black text-slate-700 text-base">{{ $rekap['total_entri'] }}</td>
                                <td class="px-6 py-4 text-center font-black text-emerald-600 text-base">{{ $rekap['total_selesai'] }}</td>
                                <td class="px-6 py-4 text-center font-black text-blue-600 text-base">{{ $rekap['input_hari_ini'] }}</td>
                                <td class="px-6 py-4 text-center font-black text-rose-600 text-base">{{ $rekap['sisa_kemarin'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500 font-medium">Belum ada data rekapan mitra.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>