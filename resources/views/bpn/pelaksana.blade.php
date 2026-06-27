<x-app-layout>
    <x-slot name="header">Ruang Kerja - Pelaksana Kegiatan Plotting</x-slot>

    <div class="p-4 lg:p-8">
        
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3 animate-pulse-once">
                <i class="fa-solid fa-circle-check text-emerald-500"></i> {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xl"><i class="fa-solid fa-layer-group"></i></div>
                <div><p class="text-[11px] font-bold text-slate-500 uppercase">Batas Antrean Harian</p><h3 class="text-2xl font-black text-slate-800">{{ $limitHarian }} <span class="text-sm font-medium text-slate-500">Berkas</span></h3></div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-xl"><i class="fa-solid fa-map-check"></i></div>
                <div><p class="text-[11px] font-bold text-slate-500 uppercase">Telah Diselesaikan Hari Ini</p><h3 class="text-2xl font-black text-slate-800">{{ $jumlahSelesaiHariIni }} <span class="text-sm font-medium text-slate-500">Berkas</span></h3></div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200">
            <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 flex items-center gap-2"><i class="fa-solid fa-map text-indigo-600"></i> Antrean Eksekusi Plotting</h3>
            </div>
            
            <div class="overflow-x-auto custom-scrollbar">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Berkas & Pemohon</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Identitas Wilayah</th>
                            <th class="px-5 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi Pelaksana</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @forelse($antrean as $item)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-5 py-4">
                                    <div class="text-lg font-black text-indigo-700 tracking-widest">{{ $item->nomer_berkas }}</div>
                                    <div class="text-sm font-bold text-slate-800 mt-1">{{ $item->nama_pemohon }}</div>
                                    <div class="text-[10px] text-slate-500 font-semibold">{{ $item->jenis_hak }} - {{ $item->nomer_hak }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-sm font-bold text-slate-800"><i class="fa-solid fa-location-dot text-rose-500 mr-1"></i> {{ $item->desa }}</div>
                                    <div class="text-xs text-slate-500 ml-3">Kec. {{ $item->kecamatan }}</div>
                                </td>
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    <form action="{{ route('bpn.pelaksana.selesaikan', $item->id) }}" method="POST" onsubmit="return confirm('Selesaikan plotting untuk berkas ini?')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white transition-colors text-xs font-bold shadow-md">
                                            <i class="fa-solid fa-check-double"></i> Verifikasi & Selesai
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-12 text-center text-slate-500 italic font-medium">Antrean plotting kosong atau sudah mencapai limit.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>