<x-app-layout>
    <x-slot name="header">Ruang Kerja - Pelaksana Kegiatan</x-slot>

    <div class="p-4 lg:p-8" x-data="pelaksanaApp()">
        
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-500"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 min-h-[60vh]">
            
            <div class="flex border-b border-slate-200 px-6 pt-2 bg-slate-50 gap-2 md:gap-6 overflow-x-auto custom-scrollbar">
                <button @click="activeTab = 'antrean'" :class="activeTab === 'antrean' ? 'border-teal-500 text-teal-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-3 py-3 border-b-[3px] transition-all flex items-center gap-2 text-sm whitespace-nowrap outline-none">
                    <i class="fa-solid fa-layer-group"></i> Antrean Tugas ({{ $antrean->count() }})
                </button>
                <button @click="activeTab = 'selesai'" :class="activeTab === 'selesai' ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-3 py-3 border-b-[3px] transition-all flex items-center gap-2 text-sm whitespace-nowrap outline-none">
                    <i class="fa-solid fa-check-double"></i> Riwayat Selesai
                </button>
            </div>

            <div x-show="activeTab === 'antrean'" x-cloak class="overflow-x-auto pb-6">
                <div class="bg-teal-50 border-b border-teal-100 p-4">
                    <p class="text-xs font-medium text-teal-800 flex items-center gap-2">
                        <i class="fa-solid fa-info-circle"></i> Ini adalah daftar berkas yang telah lunas dibayar dan masuk ke tahap akhir (pemeriksaan kelengkapan akhir / pengukuran). Silakan klik "Selesaikan" jika pekerjaan fisik berkas telah tuntas.
                    </p>
                </div>
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase">No. Berkas</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase">Perihal / Layanan</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase">Ditugaskan Kepada</th>
                            <th class="px-5 py-4 text-right text-xs font-bold text-slate-500 uppercase">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($antrean as $item)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-5 py-4">
                                    <div class="text-lg font-black text-teal-700 tracking-widest">{{ $item->nomer_berkas }}</div>
                                    <div class="text-[10px] text-slate-500 mt-1 font-semibold uppercase bg-slate-100 inline-block px-2 py-0.5 rounded border border-slate-200">TIPE: {{ $item->tipe_berkas }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-sm font-bold text-slate-800">{{ $item->nama_pemohon }}</div>
                                    <div class="text-xs text-teal-600 font-semibold mt-1">{{ $item->jenis_permohonan }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    @if($item->petugas)
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center text-[10px] font-bold"><i class="fa-solid fa-user"></i></div>
                                            <span class="text-xs font-bold text-slate-700 uppercase">{{ $item->petugas->name ?? $item->petugas->email }}</span>
                                        </div>
                                    @else
                                        <span class="text-[10px] font-bold text-rose-500 italic">Belum Ditugaskan / Umum</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openRiwayatModal({{ $item->id }}, '{{ $item->nomer_berkas }}', '{{ $item->nama_pemohon }}')" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors flex items-center justify-center" title="Riwayat Berkas">
                                            <i class="fa-solid fa-timeline"></i>
                                        </button>
                                        
                                        <button @click="openSelesaiModal({{ $item->id }}, '{{ $item->nomer_berkas }}', '{{ $item->nama_pemohon }}')" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-white transition-colors text-xs font-bold shadow-sm">
                                            <i class="fa-solid fa-flag-checkered"></i> Selesaikan
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-slate-500 italic text-sm">Tidak ada antrean tugas pelaksana saat ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div x-show="activeTab === 'selesai'" x-cloak class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-emerald-50/30">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase">No. Berkas</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase">Pemohon</th>
                            <th class="px-5 py-4 text-center text-xs font-bold text-slate-500 uppercase">Waktu Selesai</th>
                            <th class="px-5 py-4 text-center text-xs font-bold text-slate-500 uppercase">Status Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($selesai as $item)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 font-black text-emerald-700">{{ $item->nomer_berkas }}</td>
                                <td class="px-5 py-4 font-bold text-slate-800">{{ $item->nama_pemohon }}</td>
                                <td class="px-5 py-4 text-center text-xs font-bold text-slate-600">
                                    {{ $item->updated_at->format('d M Y - H:i') }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-700 border border-emerald-200"><i class="fa-solid fa-check mr-1"></i> TUNTAS</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-slate-500 italic text-sm">Belum ada riwayat penyelesaian tugas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        <div x-show="modalSelesaiOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="closeSelesaiModal()" x-transition.scale class="bg-white rounded-3xl w-full max-w-md shadow-2xl flex flex-col border border-slate-100 overflow-hidden relative">
                <div class="bg-teal-600 p-4 text-center relative">
                    <h3 class="font-extrabold text-white">Konfirmasi Penyelesaian</h3>
                    <p class="text-[11px] text-teal-100">No. <span x-text="selectedNo"></span> - <span x-text="selectedPemohon"></span></p>
                    <button type="button" @click="closeSelesaiModal()" class="absolute top-4 right-5 text-teal-200 hover:text-white transition">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                
                <form :action="'/bpn/pelaksana/selesai/' + selectedId" method="POST">
                    @csrf
                    <div class="p-6 bg-slate-50">
                        <div class="bg-amber-50 border border-amber-200 text-amber-800 text-[11px] p-3 rounded-xl mb-5 font-medium leading-relaxed">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i> <b>Perhatian:</b> Aksi ini akan mengubah status berkas menjadi "Selesai" dan menandakan bahwa seluruh proses di BPN (termasuk sertipikasi/pemetaan) telah rampung.
                        </div>
                        
                        <label class="block text-[11px] font-bold text-slate-600 mb-1.5">CATATAN HASIL AKHIR <span class="text-rose-500">*</span></label>
                        <textarea name="catatan" rows="3" required class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 p-2.5 outline-none font-medium" placeholder="Misal: Sertipikat telah dicetak dan diserahkan..."></textarea>
                    </div>
                    
                    <div class="p-4 border-t border-slate-100 bg-white flex gap-3">
                        <button type="button" @click="closeSelesaiModal()" class="w-1/3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition">Batal</button>
                        <button type="submit" class="w-2/3 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition shadow-md flex items-center justify-center gap-2">
                            <i class="fa-solid fa-check-double"></i> Ya, Selesaikan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="modalRiwayatOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="closeRiwayatModal()" x-transition.scale class="bg-white rounded-3xl w-full max-w-lg shadow-2xl flex flex-col border border-slate-100 overflow-hidden">
                <div class="bg-slate-800 p-4 text-center relative shadow-sm z-10">
                    <h3 class="font-extrabold text-white">Riwayat Perjalanan Berkas</h3>
                    <p class="text-[11px] text-slate-300">No. <span x-text="selectedNo"></span></p>
                    <button type="button" @click="closeRiwayatModal()" class="absolute top-4 right-5 text-slate-400 hover:text-white transition"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                
                <div class="p-6 bg-slate-50 max-h-[65vh] overflow-y-auto custom-scrollbar relative">
                    <div x-show="isLoadingRiwayat" class="text-center py-8">
                        <i class="fa-solid fa-circle-notch fa-spin text-3xl text-teal-500 mb-3"></i>
                    </div>
                    <div x-show="!isLoadingRiwayat && riwayatData.length === 0" class="text-center py-8 text-slate-500 text-sm font-medium">Belum ada riwayat.</div>
                    
                    <div x-show="!isLoadingRiwayat && riwayatData.length > 0" class="relative border-l-2 border-teal-200 ml-3 md:ml-4 space-y-6 pb-2">
                        <template x-for="(riwayat, index) in riwayatData" :key="index">
                            <div class="relative pl-6">
                                <div class="absolute -left-[9px] top-1.5 w-4 h-4 rounded-full bg-white border-4 border-teal-500 shadow-sm"></div>
                                <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 relative">
                                    <span x-show="index === 0" class="absolute -top-2.5 -right-2 bg-rose-500 text-white text-[9px] font-black px-2 py-0.5 rounded-full uppercase">Terbaru</span>
                                    <p class="text-[10px] font-bold text-slate-400 mb-1" x-text="riwayat.tanggal"></p>
                                    <h4 class="text-sm font-black text-slate-800 mb-1.5" x-text="riwayat.aksi"></h4>
                                    <p class="text-[11px] leading-relaxed text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100 font-medium" x-text="riwayat.catatan"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function pelaksanaApp() {
            return {
                activeTab: 'antrean',
                
                modalSelesaiOpen: false,
                modalRiwayatOpen: false,
                
                selectedId: '',
                selectedNo: '',
                selectedPemohon: '',
                
                riwayatData: [],
                isLoadingRiwayat: false,

                openSelesaiModal(id, no, pemohon) {
                    this.selectedId = id;
                    this.selectedNo = no;
                    this.selectedPemohon = pemohon;
                    this.modalSelesaiOpen = true;
                },
                closeSelesaiModal() {
                    this.modalSelesaiOpen = false;
                },

                openRiwayatModal(id, no, pemohon) {
                    this.selectedNo = no;
                    this.selectedPemohon = pemohon;
                    this.modalRiwayatOpen = true;
                    this.isLoadingRiwayat = true;
                    this.riwayatData = []; 
                    fetch(`/api/berkas/${id}/riwayat`).then(res => res.json()).then(data => {
                        this.riwayatData = data; this.isLoadingRiwayat = false;
                    });
                },
                closeRiwayatModal() { this.modalRiwayatOpen = false; }
            }
        }
    </script>
</x-app-layout>