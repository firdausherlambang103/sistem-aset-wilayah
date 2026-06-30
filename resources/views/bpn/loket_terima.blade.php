<x-app-layout>
    <x-slot name="header">Ruang Kerja - Loket Terima & Koreksi</x-slot>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <div class="p-4 lg:p-8" x-data="loketApp()">
        
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3 animate-pulse-once">
                <i class="fa-solid fa-circle-check text-emerald-500"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center gap-3 animate-pulse-once">
                <i class="fa-solid fa-circle-exclamation text-rose-500"></i> {{ session('error') }}
            </div>
        @endif
        
        @if ($errors->any())
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl">
                <ul class="list-disc list-inside text-sm font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 relative">
            <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center flex-wrap gap-4">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-inbox text-blue-600"></i> Penerimaan Berkas Fisik
                </h3>
                
                <button @click="openBuatBerkasModal()" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2.5 px-4 rounded-lg shadow-sm transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Buat Berkas Baru
                </button>
            </div>

            <div class="flex border-b border-slate-200 px-6 pt-2 bg-slate-50 gap-6 overflow-x-auto custom-scrollbar">
                <button @click="switchTab('antrean')" :class="activeTab === 'antrean' ? 'border-amber-500 text-amber-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-medium'" class="px-4 py-3 border-b-[3px] transition-all flex items-center gap-2 text-sm whitespace-nowrap outline-none">
                    <i class="fa-solid fa-list-check"></i> Antrean Koreksi ({{ $antrean->count() }})
                </button>
                <button @click="switchTab('scan')" :class="activeTab === 'scan' ? 'border-blue-500 text-blue-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-medium'" class="px-4 py-3 border-b-[3px] transition-all flex items-center gap-2 text-sm whitespace-nowrap outline-none">
                    <i class="fa-solid fa-qrcode"></i> Scan Terima Berkas
                </button>
            </div>
            
            <div x-show="activeTab === 'antrean'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="overflow-x-auto custom-scrollbar pb-24">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-5 py-4 w-12 text-center">
                                <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer" 
                                       @click="selectedBerkas = $event.target.checked ? {{ json_encode($antrean->pluck('id')->map(fn($id) => (string)$id)) }} : []">
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">No. Berkas</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Pemohon / Layanan</th>
                            <th class="px-5 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Status Loket</th>
                            <th class="px-5 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @forelse($antrean as $item)
                            <tr class="hover:bg-blue-50/30 transition" :class="selectedBerkas.includes('{{ $item->id }}') ? 'bg-blue-50/70' : ''">
                                <td class="px-5 py-4 text-center">
                                    <input type="checkbox" value="{{ $item->id }}" x-model="selectedBerkas" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-lg font-black text-blue-700 tracking-widest">{{ $item->nomer_berkas }}</div>
                                    <div class="text-[10px] text-slate-500 mt-1 font-semibold uppercase">{{ $item->tipe_berkas }} - THN {{ $item->tahun_berkas }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-sm font-bold text-slate-800">{{ $item->nama_pemohon }}</div>
                                    <div class="text-xs text-blue-600 font-semibold mt-1">{{ $item->jenis_permohonan }}</div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-700 border border-emerald-200">DITERIMA LOKET</span>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <button @click="openKoreksiModal({{ $item->id }}, '{{ $item->nomer_berkas }}', '{{ $item->nama_pemohon }}')" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white transition-colors text-xs font-bold shadow-sm">
                                        <i class="fa-solid fa-pen-to-square"></i> Cek Koreksi
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500 italic font-medium">Antrean loket kosong.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div x-show="activeTab === 'scan'" x-cloak class="p-6">
                <div class="max-w-md mx-auto text-center">
                    <div class="bg-blue-50 text-blue-800 text-xs font-bold p-3 rounded-xl mb-4 border border-blue-200">
                        <i class="fa-solid fa-info-circle mr-1"></i> Scan QR dari Mitra untuk memverifikasi penerimaan fisik berkas.
                    </div>
                    
                    <div id="reader" class="rounded-2xl overflow-hidden border-2 border-dashed border-slate-300 w-full bg-slate-50 mb-4"></div>
                    
                    <div x-show="scanResult" class="bg-emerald-100 text-emerald-800 p-4 rounded-xl border border-emerald-300">
                        <p class="text-xs font-bold uppercase mb-1">Nomor Berkas Terbaca:</p>
                        <p class="font-black text-xl tracking-widest" x-text="scanResult"></p>
                        
                        <form action="{{ route('bpn.loket.scan') }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="nomer_berkas" x-model="scanResult">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 px-4 rounded-lg shadow transition w-full">
                                <i class="fa-solid fa-check"></i> Konfirmasi Terima Berkas
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <div x-show="selectedBerkas.length > 0" x-transition.opacity class="fixed bottom-0 left-0 right-0 z-50 p-4 lg:pl-72 pointer-events-none flex justify-center pb-6" x-cloak>
            <div class="bg-slate-800 rounded-2xl shadow-2xl shadow-slate-900/50 p-4 flex items-center gap-4 pointer-events-auto border border-slate-700 w-full max-w-4xl flex-wrap md:flex-nowrap">
                
                <div class="text-white font-bold text-sm bg-slate-700 px-4 py-2.5 rounded-xl shrink-0 border border-slate-600 flex items-center gap-2">
                    <span class="bg-blue-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs" x-text="selectedBerkas.length"></span> Dipilih
                </div>
                
                <form action="{{ route('bpn.berkas.kirim') }}" method="POST" class="flex items-center gap-3 w-full flex-wrap sm:flex-nowrap">
                    @csrf
                    <input type="hidden" name="berkas_ids" :value="JSON.stringify(selectedBerkas)">
                    
                    <select name="tujuan_loket" required class="bg-slate-700 border-slate-600 text-white text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full font-semibold outline-none cursor-pointer">
                        <option value="" disabled selected>-- Teruskan Ke Bagian --</option>
                        <option value="backoffice_sps">Backoffice (Pembuatan SPS)</option>
                        <option value="pembayaran_validasi">Loket Pembayaran</option>
                        <option value="pelaksana_kegiatan">Pelaksana Kegiatan (Plotting/Ukur)</option>
                    </select>

                    <select name="petugas_id" required class="bg-slate-700 border-slate-600 text-white text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full font-semibold outline-none cursor-pointer">
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
        <div x-show="modalBuatBerkasOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="closeBuatBerkasModal()" x-transition.scale class="bg-white rounded-3xl w-full max-w-lg shadow-2xl flex flex-col border border-slate-100 overflow-hidden relative">
                
                <div class="bg-blue-600 p-4 text-center relative">
                    <h3 class="font-extrabold text-white">Input Berkas Baru</h3>
                    <p class="text-[11px] text-blue-100">Entri data berkas fisik langsung dari loket</p>
                    <button type="button" @click="closeBuatBerkasModal()" class="absolute top-4 right-5 text-blue-200 hover:text-white transition">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                
                <form action="{{ route('bpn.loket.berkas.store') }}" method="POST">
                    @csrf
                    <div class="p-6 bg-slate-50 flex flex-col gap-4 max-h-[70vh] overflow-y-auto custom-scrollbar">
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1.5">NOMOR BERKAS <span class="text-rose-500">*</span></label>
                                <input type="text" name="nomer_berkas" required autocomplete="off" 
                                    value="{{ strtoupper(\Illuminate\Support\Str::random(6)) }}"
                                    class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-2.5 outline-none font-bold uppercase" 
                                    placeholder="Misal: 123456">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1.5">TAHUN BERKAS <span class="text-rose-500">*</span></label>
                                <input type="number" name="tahun_berkas" required value="{{ date('Y') }}"
                                       class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-2.5 outline-none font-bold">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1.5">NAMA PEMOHON <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_pemohon" required autocomplete="off" 
                                   class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-2.5 outline-none font-semibold" 
                                   placeholder="Nama Lengkap Pemohon">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1.5">TIPE BERKAS <span class="text-rose-500">*</span></label>
                                <select name="tipe_berkas" required class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-2.5 outline-none font-semibold">
                                    <option value="" disabled selected>Pilih Tipe...</option>
                                    <option value="biasa">Biasa</option>
                                    <option value="plotting">Plotting</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1.5">JENIS PERMOHONAN <span class="text-rose-500">*</span></label>
                                <select name="jenis_permohonan" required class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-2.5 outline-none font-semibold">
                                    <option value="" disabled selected>Pilih Jenis...</option>
                                    <option value="Peralihan Hak (Jual Beli)">Peralihan Hak (Jual Beli)</option>
                                    <option value="Pendaftaran SK">Pendaftaran SK</option>
                                    <option value="Pemecahan / Penggabungan">Pemecahan / Penggabungan</option>
                                    <option value="Roya / Hak Tanggungan">Roya / Hak Tanggungan</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1.5">JENIS HAK <span class="text-rose-500">*</span></label>
                                <select name="jenis_hak" required class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-2.5 outline-none font-semibold">
                                    <option value="" disabled selected>Pilih Hak...</option>
                                    <option value="Hak Milik">Hak Milik (HM)</option>
                                    <option value="Hak Guna Bangunan">Hak Guna Bangunan (HGB)</option>
                                    <option value="Hak Pakai">Hak Pakai (HP)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1.5">NOMER HAK <span class="text-rose-500">*</span></label>
                                <input type="text" name="nomer_hak" required autocomplete="off" 
                                       class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-2.5 outline-none font-semibold" 
                                       placeholder="Misal: 01234">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1.5">KECAMATAN <span class="text-rose-500">*</span></label>
                                <input type="text" name="kecamatan" required autocomplete="off" 
                                       class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-2.5 outline-none font-semibold" 
                                       placeholder="Nama Kecamatan">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1.5">DESA / KELURAHAN <span class="text-rose-500">*</span></label>
                                <input type="text" name="desa" required autocomplete="off" 
                                       class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-2.5 outline-none font-semibold" 
                                       placeholder="Nama Desa">
                            </div>
                        </div>

                    </div>
                    
                    <div class="p-4 border-t border-slate-100 bg-white flex gap-3">
                        <button type="button" @click="closeBuatBerkasModal()" class="w-1/3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition">Batal</button>
                        <button type="submit" class="w-2/3 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition shadow-md">
                            <i class="fa-solid fa-save mr-1"></i> Simpan Berkas
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="modalKoreksiOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="closeKoreksiModal()" x-transition.scale class="bg-white rounded-3xl w-full max-w-md shadow-2xl flex flex-col border border-slate-100 overflow-hidden relative">
                <div class="bg-amber-500 p-4 text-center relative">
                    <h3 class="font-extrabold text-white">Tindakan Koreksi Berkas</h3>
                    <p class="text-[11px] text-amber-100">No. <span x-text="selectedNo"></span> - <span x-text="selectedPemohon"></span></p>
                    <button type="button" @click="closeKoreksiModal()" class="absolute top-4 right-5 text-amber-200 hover:text-white transition">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                
                <form :action="'/bpn/loket-terima/koreksi/' + selectedId" method="POST">
                    @csrf
                    <div class="p-6 bg-slate-50">
                        <label class="block text-[11px] font-bold text-slate-600 mb-1.5">CATATAN LOKET <span class="text-rose-500">*</span></label>
                        <textarea name="catatan" rows="3" required class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 p-2.5 outline-none mb-4" placeholder="Misal: Berkas lengkap. Siap dikirim..."></textarea>
                        
                        <label class="block text-[11px] font-bold text-slate-600 mb-1.5">TINDAKAN KOREKSI <span class="text-rose-500">*</span></label>
                        <select name="aksi" class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 p-2.5 outline-none font-bold">
                            <option value="siap_kirim">Koreksi Selesai (Simpan Catatan & Siap Dikirim)</option>
                            <option value="kembalikan">Salah/Kurang - Tolak & Kembalikan ke Mitra</option>
                        </select>
                    </div>
                    
                    <div class="p-4 border-t border-slate-100 bg-white flex gap-3">
                        <button type="button" @click="closeKoreksiModal()" class="w-1/3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition">Batal</button>
                        <button type="submit" class="w-2/3 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl transition shadow-md">Simpan Tindakan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function loketApp() {
            return {
                activeTab: 'antrean',
                selectedBerkas: [], // Data Array untuk checkbox massal
                
                scanner: null,
                scanResult: '',
                
                modalBuatBerkasOpen: false,
                modalKoreksiOpen: false,
                selectedId: '',
                selectedNo: '',
                selectedPemohon: '',

                switchTab(tab) {
                    this.activeTab = tab;
                    if (tab === 'scan') { this.startScanner(); } 
                    else { this.stopScanner(); }
                },

                startScanner() {
                    this.scanResult = '';
                    if (!this.scanner) {
                        this.scanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: {width: 250, height: 250} }, false);
                    }
                    this.scanner.render((text) => {
                        this.scanResult = text;
                        this.scanner.clear(); // Berhenti otomatis setelah scan sukses
                    });
                },

                stopScanner() {
                    if (this.scanner) { this.scanner.clear(); }
                },

                openBuatBerkasModal() { this.modalBuatBerkasOpen = true; },
                closeBuatBerkasModal() { this.modalBuatBerkasOpen = false; },

                openKoreksiModal(id, no, pemohon) {
                    this.selectedId = id;
                    this.selectedNo = no;
                    this.selectedPemohon = pemohon;
                    this.modalKoreksiOpen = true;
                },
                closeKoreksiModal() { this.modalKoreksiOpen = false; }
            }
        }
    </script>
</x-app-layout>