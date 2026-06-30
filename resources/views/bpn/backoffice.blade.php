<x-app-layout>
    <x-slot name="header">Ruang Kerja - Backoffice (Penerbitan SPS)</x-slot>

    <div class="p-4 lg:p-8" x-data="backofficeApp()">
        
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-500"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-rose-500"></i> {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-sm font-bold">
                <ul>@foreach ($errors->all() as $error) <li>- {{ $error }}</li> @endforeach</ul>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200">
            <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice-dollar text-indigo-600"></i> Antrean Backoffice
                </h3>
            </div>
            
            <div class="overflow-x-auto custom-scrollbar min-h-[50vh]">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase">No. Berkas</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase">Pemohon / Layanan</th>
                            <th class="px-5 py-4 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                            <th class="px-5 py-4 text-right text-xs font-bold text-slate-500 uppercase">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @forelse($berkas as $item)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-5 py-4">
                                    <div class="text-lg font-black text-indigo-700 tracking-widest">{{ $item->nomer_berkas }}</div>
                                    <div class="text-[10px] text-slate-500 mt-1 font-semibold uppercase">{{ $item->tipe_berkas }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-sm font-bold text-slate-800">{{ $item->nama_pemohon }}</div>
                                    <div class="text-xs text-indigo-600 font-semibold mt-1">{{ $item->jenis_permohonan }}</div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-100 text-amber-700 border border-amber-200">PROSES BACKOFFICE</span>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openRiwayatModal({{ $item->id }}, '{{ $item->nomer_berkas }}', '{{ $item->nama_pemohon }}')" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors flex items-center justify-center" title="Riwayat">
                                            <i class="fa-solid fa-timeline"></i>
                                        </button>

                                        <button @click="openEditModal({{ json_encode($item) }})" class="w-8 h-8 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-600 transition-colors flex items-center justify-center" title="Edit Data">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <button @click="openTolakModal({{ $item->id }}, '{{ $item->nomer_berkas }}', '{{ $item->nama_pemohon }}')" class="w-8 h-8 rounded-lg bg-rose-100 hover:bg-rose-200 text-rose-600 transition-colors flex items-center justify-center" title="Tolak ke Loket">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                        
                                        <button @click="openProsesModal({{ $item->id }}, '{{ $item->nomer_berkas }}', '{{ $item->nama_pemohon }}')" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors text-xs font-bold shadow-sm ml-2">
                                            <i class="fa-solid fa-file-invoice"></i> Upload SPS
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-slate-500 italic font-medium">Antrean backoffice saat ini kosong.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="modalProsesOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="closeProsesModal()" x-transition.scale class="bg-white rounded-3xl w-full max-w-md shadow-2xl flex flex-col border border-slate-100 overflow-hidden">
                <div class="bg-indigo-600 p-4 text-center relative">
                    <h3 class="font-extrabold text-white">Upload & Terbitkan SPS</h3>
                    <p class="text-[11px] text-indigo-100">No. <span x-text="selectedNo"></span> - <span x-text="selectedPemohon"></span></p>
                </div>
                
                <form :action="'/bpn/backoffice/proses/' + selectedId" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6 bg-slate-50">
                        <label class="block text-[11px] font-bold text-slate-600 mb-1.5">FILE DOKUMEN SPS (PDF) <span class="text-rose-500">*</span></label>
                        <input type="file" name="file_sps" accept=".pdf" required class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 p-2 outline-none mb-4 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">

                        <label class="block text-[11px] font-bold text-slate-600 mb-1.5">NOMOR SPS / CATATAN <span class="text-rose-500">*</span></label>
                        <textarea name="catatan" rows="3" required class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 p-2.5 outline-none" placeholder="Misal: SPS No. 99827 telah diterbitkan."></textarea>
                    </div>
                    
                    <div class="p-4 border-t border-slate-100 bg-white flex gap-3">
                        <button type="button" @click="closeProsesModal()" class="w-1/3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition">Batal</button>
                        <button type="submit" class="w-2/3 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition shadow-md">Simpan & Teruskan</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="modalTolakOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="closeTolakModal()" x-transition.scale class="bg-white rounded-3xl w-full max-w-md shadow-2xl flex flex-col border border-slate-100 overflow-hidden">
                <div class="bg-rose-600 p-4 text-center relative">
                    <h3 class="font-extrabold text-white">Tolak / Kembalikan Berkas</h3>
                    <p class="text-[11px] text-rose-100">No. <span x-text="selectedNo"></span> - <span x-text="selectedPemohon"></span></p>
                </div>
                
                <form :action="'/bpn/backoffice/tolak/' + selectedId" method="POST">
                    @csrf
                    <div class="p-6 bg-slate-50">
                        <label class="block text-[11px] font-bold text-slate-600 mb-1.5">ALASAN PENOLAKAN <span class="text-rose-500">*</span></label>
                        <textarea name="catatan" rows="3" required class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 p-2.5 outline-none mb-4" placeholder="Misal: Data NIK pemohon tidak sesuai dengan KTP..."></textarea>
                    </div>
                    
                    <div class="p-4 border-t border-slate-100 bg-white flex gap-3">
                        <button type="button" @click="closeTolakModal()" class="w-1/3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition">Batal</button>
                        <button type="submit" class="w-2/3 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl transition shadow-md">Kembalikan ke Loket</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="modalEditOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="closeEditModal()" x-transition.scale class="bg-white rounded-3xl w-full max-w-2xl shadow-2xl flex flex-col border border-slate-100 overflow-hidden relative">
                <div class="bg-blue-600 p-4 text-center relative">
                    <h3 class="font-extrabold text-white">Edit Data Berkas</h3>
                </div>
                
                <form :action="'/bpn/backoffice/update/' + editData.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6 bg-slate-50 grid grid-cols-2 gap-4 max-h-[60vh] overflow-y-auto custom-scrollbar">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1.5">NOMOR BERKAS</label>
                            <input type="text" name="nomer_berkas" x-model="editData.nomer_berkas" required class="w-full bg-white border border-slate-200 text-sm rounded-xl p-2.5 outline-none font-bold uppercase">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1.5">TAHUN BERKAS</label>
                            <input type="number" name="tahun_berkas" x-model="editData.tahun_berkas" required class="w-full bg-white border border-slate-200 text-sm rounded-xl p-2.5 outline-none font-bold">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-[11px] font-bold text-slate-600 mb-1.5">NAMA PEMOHON</label>
                            <input type="text" name="nama_pemohon" x-model="editData.nama_pemohon" required class="w-full bg-white border border-slate-200 text-sm rounded-xl p-2.5 outline-none font-semibold">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1.5">TIPE BERKAS</label>
                            <select name="tipe_berkas" x-model="editData.tipe_berkas" required class="w-full bg-white border border-slate-200 text-sm rounded-xl p-2.5 outline-none font-semibold">
                                <option value="biasa">Biasa</option>
                                <option value="plotting">Plotting</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1.5">JENIS PERMOHONAN</label>
                            <input type="text" name="jenis_permohonan" x-model="editData.jenis_permohonan" required class="w-full bg-white border border-slate-200 text-sm rounded-xl p-2.5 outline-none font-semibold">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1.5">JENIS HAK</label>
                            <input type="text" name="jenis_hak" x-model="editData.jenis_hak" required class="w-full bg-white border border-slate-200 text-sm rounded-xl p-2.5 outline-none">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1.5">NOMER HAK</label>
                            <input type="text" name="nomer_hak" x-model="editData.nomer_hak" required class="w-full bg-white border border-slate-200 text-sm rounded-xl p-2.5 outline-none">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1.5">KECAMATAN</label>
                            <input type="text" name="kecamatan" x-model="editData.kecamatan" required class="w-full bg-white border border-slate-200 text-sm rounded-xl p-2.5 outline-none">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1.5">DESA / KELURAHAN</label>
                            <input type="text" name="desa" x-model="editData.desa" required class="w-full bg-white border border-slate-200 text-sm rounded-xl p-2.5 outline-none">
                        </div>
                    </div>
                    
                    <div class="p-4 border-t border-slate-100 bg-white flex gap-3">
                        <button type="button" @click="closeEditModal()" class="w-1/3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition">Batal</button>
                        <button type="submit" class="w-2/3 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition shadow-md">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="modalRiwayatOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="closeRiwayatModal()" x-transition.scale class="bg-white rounded-3xl w-full max-w-lg shadow-2xl flex flex-col border border-slate-100 overflow-hidden">
                <div class="bg-slate-800 p-4 text-center relative shadow-sm z-10">
                    <h3 class="font-extrabold text-white">Riwayat Perjalanan Berkas</h3>
                    <p class="text-[11px] text-slate-300">No. <span x-text="selectedNo"></span> - <span x-text="selectedPemohon"></span></p>
                    <button type="button" @click="closeRiwayatModal()" class="absolute top-4 right-5 text-slate-400 hover:text-white transition"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                
                <div class="p-6 bg-slate-50 max-h-[65vh] overflow-y-auto custom-scrollbar relative">
                    <div x-show="isLoadingRiwayat" class="text-center py-8">
                        <i class="fa-solid fa-circle-notch fa-spin text-3xl text-indigo-500 mb-3"></i>
                    </div>
                    <div x-show="!isLoadingRiwayat && riwayatData.length === 0" class="text-center py-8 text-slate-500 text-sm font-medium">Belum ada riwayat.</div>
                    
                    <div x-show="!isLoadingRiwayat && riwayatData.length > 0" class="relative border-l-2 border-indigo-200 ml-3 md:ml-4 space-y-6 pb-2">
                        <template x-for="(riwayat, index) in riwayatData" :key="index">
                            <div class="relative pl-6">
                                <div class="absolute -left-[9px] top-1.5 w-4 h-4 rounded-full bg-white border-4 border-indigo-500 shadow-sm"></div>
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
        function backofficeApp() {
            return {
                modalProsesOpen: false,
                modalTolakOpen: false,
                modalEditOpen: false,
                modalRiwayatOpen: false,
                
                selectedId: '',
                selectedNo: '',
                selectedPemohon: '',
                editData: {}, // Objek untuk menampung data form Edit
                
                riwayatData: [],
                isLoadingRiwayat: false,

                openProsesModal(id, no, pemohon) {
                    this.selectedId = id; this.selectedNo = no; this.selectedPemohon = pemohon;
                    this.modalProsesOpen = true;
                },
                closeProsesModal() { this.modalProsesOpen = false; },

                openTolakModal(id, no, pemohon) {
                    this.selectedId = id; this.selectedNo = no; this.selectedPemohon = pemohon;
                    this.modalTolakOpen = true;
                },
                closeTolakModal() { this.modalTolakOpen = false; },

                openEditModal(item) {
                    this.editData = { ...item }; // Salin data ke state alpine
                    this.modalEditOpen = true;
                },
                closeEditModal() { this.modalEditOpen = false; },

                openRiwayatModal(id, no, pemohon) {
                    this.selectedNo = no; this.selectedPemohon = pemohon;
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