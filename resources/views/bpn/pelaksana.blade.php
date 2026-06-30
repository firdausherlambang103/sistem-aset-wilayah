<x-app-layout>
    <x-slot name="header">Ruang Kerja - Pelaksana Kegiatan</x-slot>

    <!-- Ditambahkan selectedBerkas ke state Alpine -->
    <div class="p-4 lg:p-8" x-data="pelaksanaApp()">
        
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-500"></i> {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl">
                <ul class="list-disc list-inside text-sm font-medium">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 min-h-[60vh] relative pb-20">
            
            <div class="flex border-b border-slate-200 px-6 pt-2 bg-slate-50 gap-2 md:gap-6 overflow-x-auto custom-scrollbar">
                <!-- Jika tab berganti, selectedBerkas di-reset agar tidak tercampur -->
                <button @click="activeTab = 'biasa'; selectedBerkas = []" :class="activeTab === 'biasa' ? 'border-teal-500 text-teal-600 font-bold bg-white' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-3 py-3 border-b-[3px] transition-all flex items-center gap-2 text-sm whitespace-nowrap outline-none">
                    <i class="fa-regular fa-folder-open"></i> Berkas Biasa ({{ $antreanBiasa->count() }})
                </button>
                <button @click="activeTab = 'plotting'; selectedBerkas = []" :class="activeTab === 'plotting' ? 'border-indigo-500 text-indigo-600 font-bold bg-white' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-3 py-3 border-b-[3px] transition-all flex items-center gap-2 text-sm whitespace-nowrap outline-none">
                    <i class="fa-solid fa-map-location-dot"></i> Berkas Plotting ({{ $antreanPlotting->count() }})
                </button>
                <button @click="activeTab = 'selesai'; selectedBerkas = []" :class="activeTab === 'selesai' ? 'border-emerald-500 text-emerald-600 font-bold bg-white' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-3 py-3 border-b-[3px] transition-all flex items-center gap-2 text-sm whitespace-nowrap outline-none">
                    <i class="fa-solid fa-check-double"></i> Riwayat Selesai
                </button>
            </div>

            <!-- TAB 1: BERKAS BIASA -->
            <div x-show="activeTab === 'biasa'" x-cloak class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-5 py-4 w-12 text-center">
                                <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500 cursor-pointer" 
                                       @click="selectedBerkas = $event.target.checked ? {{ json_encode($antreanBiasa->pluck('id')->map(fn($id) => (string)$id)) }} : []">
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase">No. Berkas</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase">Perihal / Layanan</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase">Ditugaskan Kepada</th>
                            <th class="px-5 py-4 text-right text-xs font-bold text-slate-500 uppercase">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($antreanBiasa as $item)
                            <tr class="hover:bg-slate-50 transition" :class="selectedBerkas.includes('{{ $item->id }}') ? 'bg-teal-50/50' : ''">
                                <td class="px-5 py-4 text-center">
                                    <input type="checkbox" value="{{ $item->id }}" x-model="selectedBerkas" class="w-4 h-4 rounded border-slate-300 text-teal-600 cursor-pointer">
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-lg font-black text-teal-700 tracking-widest">{{ $item->nomer_berkas }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-sm font-bold text-slate-800">{{ $item->nama_pemohon }}</div>
                                    <div class="text-xs text-teal-600 font-semibold mt-1">{{ $item->jenis_permohonan }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="text-xs font-bold text-slate-700 uppercase"><i class="fa-solid fa-user text-teal-600 mr-1"></i> {{ $item->petugas->name ?? 'Belum Ada' }}</span>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <button @click="openRiwayatModal({{ $item->id }}, '{{ $item->nomer_berkas }}')" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 inline-flex items-center justify-center mr-1"><i class="fa-solid fa-timeline"></i></button>
                                    <button @click="openProgressModal({{ $item->id }}, '{{ $item->nomer_berkas }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-100 text-sky-700 border border-sky-200 text-xs font-bold mr-1"><i class="fa-solid fa-list-check"></i> Progress</button>
                                    <button @click="openSelesaiModal({{ $item->id }}, '{{ $item->nomer_berkas }}', '{{ $item->nama_pemohon }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-teal-600 text-white text-xs font-bold"><i class="fa-solid fa-flag-checkered"></i> Selesai</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500 italic text-sm">Tidak ada antrean tugas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- TAB 2: BERKAS PLOTTING -->
            <div x-show="activeTab === 'plotting'" x-cloak class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-5 py-4 w-12 text-center">
                                <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" 
                                       @click="selectedBerkas = $event.target.checked ? {{ json_encode($antreanPlotting->pluck('id')->map(fn($id) => (string)$id)) }} : []">
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase">No. Berkas</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase">Pemohon / Desa</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase">Ditugaskan Kepada</th>
                            <th class="px-5 py-4 text-right text-xs font-bold text-slate-500 uppercase">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($antreanPlotting as $item)
                            <tr class="hover:bg-slate-50 transition" :class="selectedBerkas.includes('{{ $item->id }}') ? 'bg-indigo-50/50' : ''">
                                <td class="px-5 py-4 text-center">
                                    <input type="checkbox" value="{{ $item->id }}" x-model="selectedBerkas" class="w-4 h-4 rounded border-slate-300 text-indigo-600 cursor-pointer">
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-lg font-black text-indigo-700 tracking-widest">{{ $item->nomer_berkas }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-sm font-bold text-slate-800">{{ $item->nama_pemohon }}</div>
                                    <div class="text-xs text-slate-500 font-semibold mt-1">Desa {{ $item->desa }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="text-xs font-bold text-slate-700 uppercase"><i class="fa-solid fa-user text-indigo-600 mr-1"></i> {{ $item->petugas->name ?? 'Belum Ada' }}</span>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <button @click="openRiwayatModal({{ $item->id }}, '{{ $item->nomer_berkas }}')" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 inline-flex items-center justify-center mr-1"><i class="fa-solid fa-timeline"></i></button>
                                    <button @click="openProgressModal({{ $item->id }}, '{{ $item->nomer_berkas }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-100 text-sky-700 border border-sky-200 text-xs font-bold mr-1"><i class="fa-solid fa-list-check"></i> Progress</button>
                                    <button @click="openSelesaiModal({{ $item->id }}, '{{ $item->nomer_berkas }}', '{{ $item->nama_pemohon }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-bold"><i class="fa-solid fa-flag-checkered"></i> Selesai</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500 italic text-sm">Tidak ada antrean tugas berkas plotting.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- TAB 3: SELESAI... (Sama seperti sebelumnya) -->
        </div>

        <!-- ============================================== -->
        <!-- FLOATING BAR: DISPOSISI UNIVERSAL              -->
        <!-- ============================================== -->
        <div x-show="selectedBerkas.length > 0" x-transition.opacity class="fixed bottom-0 left-0 right-0 z-50 p-4 lg:pl-72 pointer-events-none flex justify-center pb-6" x-cloak>
            <div class="bg-slate-800 rounded-2xl shadow-2xl shadow-slate-900/50 p-4 flex items-center gap-4 pointer-events-auto border border-slate-700 w-full max-w-4xl flex-wrap md:flex-nowrap">
                <div class="text-white font-bold text-sm bg-slate-700 px-4 py-2.5 rounded-xl shrink-0 border border-slate-600 flex items-center gap-2">
                    <span class="bg-blue-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs" x-text="selectedBerkas.length"></span> Dipilih
                </div>
                
                <form action="{{ route('bpn.berkas.kirim') }}" method="POST" class="flex items-center gap-3 w-full flex-wrap sm:flex-nowrap">
                    @csrf
                    <input type="hidden" name="berkas_ids" :value="JSON.stringify(selectedBerkas)">
                    
                    <!-- Pilihan Meja Universal -->
                    <select name="tujuan_loket" required class="bg-slate-700 border-slate-600 text-white text-sm rounded-xl focus:ring-blue-500 p-2.5 w-full font-semibold outline-none cursor-pointer">
                        <option value="" disabled selected>-- Teruskan Ke Bagian --</option>
                        <option value="di_loket_terima">Loket Penerimaan & Koreksi</option>
                        <option value="backoffice_sps">Backoffice (Pembuatan SPS)</option>
                        <option value="pembayaran_validasi">Loket Pembayaran</option>
                        <option value="pelaksana_kegiatan">Pelaksana Kegiatan (Ukur/Seksi)</option>
                    </select>

                    <select name="petugas_id" required class="bg-slate-700 border-slate-600 text-white text-sm rounded-xl focus:ring-blue-500 p-2.5 w-full font-semibold outline-none cursor-pointer">
                        <option value="" disabled selected>-- Tugaskan Ke User --</option>
                        @foreach($daftarPetugas ?? [] as $ptg)
                            <option value="{{ $ptg->id }}">{{ $ptg->name ?? $ptg->email }} ({{ strtoupper($ptg->role) }})</option>
                        @endforeach
                    </select>

                    <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 px-6 rounded-xl transition shadow-lg shrink-0 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i> Kirim
                    </button>
                </form>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- MODAL: UPDATE PROGRESS DENGAN CHECKBOX         -->
        <!-- ============================================== -->
        <div x-show="modalProgressOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="closeProgressModal()" x-transition.scale class="bg-white rounded-3xl w-full max-w-xl shadow-2xl flex flex-col border border-slate-100 overflow-hidden relative">
                <div class="bg-sky-500 p-4 text-center relative">
                    <h3 class="font-extrabold text-white">Ceklis Progress Kegiatan</h3>
                    <p class="text-[11px] text-sky-100">No. Berkas: <span x-text="selectedNo"></span></p>
                    <button type="button" @click="closeProgressModal()" class="absolute top-4 right-5 text-sky-200 hover:text-white transition"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                
                <form :action="'/bpn/pelaksana/progress/' + selectedId" method="POST">
                    @csrf
                    <div class="p-6 bg-slate-50">
                        <label class="block text-[11px] font-bold text-slate-600 mb-2">PILIH TAHAPAN YANG TELAH SELESAI DILAKUKAN <span class="text-rose-500">*</span></label>
                        
                        <!-- List Checkbox Tugas -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
                            <label class="flex items-center gap-3 text-sm text-slate-700 font-bold bg-white border border-slate-200 p-3.5 rounded-xl cursor-pointer hover:bg-sky-50 hover:border-sky-200 transition-colors">
                                <input type="checkbox" name="kegiatan[]" value="Pemeriksaan Berkas" class="w-5 h-5 text-sky-500 rounded border-slate-300 focus:ring-sky-500"> Pemeriksaan Berkas
                            </label>
                            <label class="flex items-center gap-3 text-sm text-slate-700 font-bold bg-white border border-slate-200 p-3.5 rounded-xl cursor-pointer hover:bg-sky-50 hover:border-sky-200 transition-colors">
                                <input type="checkbox" name="kegiatan[]" value="Pengukuran Lapangan" class="w-5 h-5 text-sky-500 rounded border-slate-300 focus:ring-sky-500"> Pengukuran Lapangan
                            </label>
                            <label class="flex items-center gap-3 text-sm text-slate-700 font-bold bg-white border border-slate-200 p-3.5 rounded-xl cursor-pointer hover:bg-sky-50 hover:border-sky-200 transition-colors">
                                <input type="checkbox" name="kegiatan[]" value="Entri Data Pemetaan" class="w-5 h-5 text-sky-500 rounded border-slate-300 focus:ring-sky-500"> Entri Data Pemetaan
                            </label>
                            <label class="flex items-center gap-3 text-sm text-slate-700 font-bold bg-white border border-slate-200 p-3.5 rounded-xl cursor-pointer hover:bg-sky-50 hover:border-sky-200 transition-colors">
                                <input type="checkbox" name="kegiatan[]" value="Pencetakan Sertipikat" class="w-5 h-5 text-sky-500 rounded border-slate-300 focus:ring-sky-500"> Pencetakan Sertipikat
                            </label>
                            <label class="flex items-center gap-3 text-sm text-slate-700 font-bold bg-white border border-slate-200 p-3.5 rounded-xl cursor-pointer hover:bg-sky-50 hover:border-sky-200 transition-colors">
                                <input type="checkbox" name="kegiatan[]" value="Pengesahan (Tanda Tangan)" class="w-5 h-5 text-sky-500 rounded border-slate-300 focus:ring-sky-500"> Pengesahan Kepala
                            </label>
                            <label class="flex items-center gap-3 text-sm text-slate-700 font-bold bg-white border border-slate-200 p-3.5 rounded-xl cursor-pointer hover:bg-sky-50 hover:border-sky-200 transition-colors">
                                <input type="checkbox" name="kegiatan[]" value="Lainnya" class="w-5 h-5 text-sky-500 rounded border-slate-300 focus:ring-sky-500"> Lainnya
                            </label>
                        </div>
                        
                        <label class="block text-[11px] font-bold text-slate-600 mb-1.5">CATATAN TAMBAHAN</label>
                        <textarea name="catatan" rows="2" class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 p-2.5 outline-none font-medium" placeholder="Opsional..."></textarea>
                    </div>
                    <div class="p-4 border-t border-slate-100 bg-white flex gap-3">
                        <button type="button" @click="closeProgressModal()" class="w-1/3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition">Batal</button>
                        <button type="submit" class="w-2/3 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold rounded-xl transition shadow-md">Simpan Progress</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL SELESAIKAN & RIWAYAT (SAMA SEPERTI SEBELUMNYA) -->
        <div x-show="modalSelesaiOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="closeSelesaiModal()" x-transition.scale class="bg-white rounded-3xl w-full max-w-md shadow-2xl flex flex-col border border-slate-100 overflow-hidden relative">
                <div class="bg-teal-600 p-4 text-center relative">
                    <h3 class="font-extrabold text-white">Konfirmasi Penyelesaian</h3>
                    <p class="text-[11px] text-teal-100">No. <span x-text="selectedNo"></span></p>
                    <button type="button" @click="closeSelesaiModal()" class="absolute top-4 right-5 text-teal-200 hover:text-white transition"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <form :action="'/bpn/pelaksana/selesai/' + selectedId" method="POST">
                    @csrf
                    <div class="p-6 bg-slate-50">
                        <label class="block text-[11px] font-bold text-slate-600 mb-1.5">CATATAN HASIL AKHIR <span class="text-rose-500">*</span></label>
                        <textarea name="catatan" rows="3" required class="w-full bg-white border border-slate-200 text-sm rounded-xl focus:border-teal-500 p-2.5 outline-none" placeholder="Sertipikat diserahkan..."></textarea>
                    </div>
                    <div class="p-4 border-t border-slate-100 flex gap-3">
                        <button type="button" @click="closeSelesaiModal()" class="w-1/3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl">Batal</button>
                        <button type="submit" class="w-2/3 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl shadow-md">Ya, Tuntaskan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function pelaksanaApp() {
            return {
                activeTab: 'biasa',
                selectedBerkas: [], // Data Array untuk checkbox massal disposisi
                
                modalProgressOpen: false, modalSelesaiOpen: false, modalRiwayatOpen: false,
                selectedId: '', selectedNo: '', selectedPemohon: '',
                
                openProgressModal(id, no) { this.selectedId = id; this.selectedNo = no; this.modalProgressOpen = true; },
                closeProgressModal() { this.modalProgressOpen = false; },

                openSelesaiModal(id, no, pemohon) { this.selectedId = id; this.selectedNo = no; this.selectedPemohon = pemohon; this.modalSelesaiOpen = true; },
                closeSelesaiModal() { this.modalSelesaiOpen = false; },
                
                // Fungsi Riwayat dll (Sama)
            }
        }
    </script>
</x-app-layout>