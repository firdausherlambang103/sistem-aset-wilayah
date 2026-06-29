<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <h2 class="font-black text-xl text-slate-800 leading-tight flex items-center tracking-tight">
                <i class="fa-solid fa-desktop mr-3 text-indigo-600"></i>
                {{ __('Ruang Kerja Saya (Berkas Fisik)') }}
            </h2>

            <div class="flex items-center gap-3">
                <a href="{{ route('mitra.plotting') }}" class="inline-flex items-center px-4 py-2 bg-white text-indigo-600 border border-indigo-200 rounded-lg font-bold text-xs uppercase tracking-widest hover:bg-indigo-50 hover:border-indigo-300 active:bg-indigo-100 focus:outline-none transition-all shadow-sm">
                    <i class="fa-solid fa-map-location-dot mr-2 text-lg"></i>
                    <span>Beralih ke Plotting</span>
                </a>
            </div>
        </div>
    </x-slot>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <div class="py-8 bg-slate-50/50 min-h-screen" x-data="ruangKerja({{ Js::from($kecamatans ?? []) }})">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="p-4 mb-4 text-sm font-bold text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 shadow-sm flex items-center">
                    <i class="fa-solid fa-check-circle mr-3 text-emerald-500 text-lg"></i> {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200">
                <div class="flex flex-wrap bg-slate-50 border-b border-slate-200">
                    <button @click="switchTab('daftar')" :class="activeTab === 'daftar' ? 'border-blue-500 text-blue-700 bg-white shadow-sm' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-100'" class="px-6 py-4 border-b-4 font-bold text-sm transition-all outline-none flex items-center gap-2 w-full sm:w-auto justify-center">
                        <i class="fa-solid fa-file-signature"></i> Berkas di Meja Saya
                    </button>
                    <button @click="switchTab('buat')" :class="activeTab === 'buat' ? 'border-amber-500 text-amber-700 bg-white shadow-sm' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-100'" class="px-6 py-4 border-b-4 font-bold text-sm transition-all outline-none flex items-center gap-2 w-full sm:w-auto justify-center">
                        <i class="fa-solid fa-plus-circle"></i> Input Berkas Baru
                    </button>
                    <button @click="switchTab('scan')" :class="activeTab === 'scan' ? 'border-purple-500 text-purple-700 bg-white shadow-sm' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-100'" class="px-6 py-4 border-b-4 font-bold text-sm transition-all outline-none flex items-center gap-2 w-full sm:w-auto justify-center">
                        <i class="fa-solid fa-qrcode"></i> Scan Pengembalian BPN
                    </button>
                </div>
            </div>

            <div x-show="activeTab === 'daftar'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white shadow-sm rounded-2xl border-l-4 border-blue-500 border-y border-r border-slate-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-lg font-black text-slate-800 flex items-center tracking-tight">
                            <span class="bg-blue-100 text-blue-600 w-10 h-10 flex items-center justify-center rounded-xl mr-3"><i class="fa-solid fa-file-signature"></i></span>
                            Daftar Berkas Fisik Anda
                        </h3>
                        
                        <div class="relative w-full sm:w-auto">
                            <input type="text" placeholder="Cari No. Berkas..." class="pl-10 pr-4 py-2.5 border-slate-200 bg-slate-50 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm text-sm font-semibold w-full lg:w-72 transition-colors">
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-slate-400"></i>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-black text-slate-500 uppercase tracking-wider">No. Berkas</th>
                                    <th class="px-6 py-4 text-left text-xs font-black text-slate-500 uppercase tracking-wider">Perihal & Pemohon</th>
                                    <th class="px-6 py-4 text-left text-xs font-black text-slate-500 uppercase tracking-wider">Hak & Lokasi</th>
                                    <th class="px-6 py-4 text-center text-xs font-black text-slate-500 uppercase tracking-wider">Status Berkas</th>
                                    <th class="px-6 py-4 text-right text-xs font-black text-slate-500 uppercase tracking-wider">Aksi Verifikasi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @forelse($berkas ?? [] as $item)
                                    <tr class="hover:bg-blue-50/50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-slate-800">{{ $item->nomer_berkas ?? '-' }}</div>
                                            <div class="text-[11px] font-bold text-slate-500 mt-1 uppercase tracking-wider"><i class="fa-regular fa-clock mr-1"></i> Tahun {{ $item->tahun_berkas ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex px-2.5 py-1 text-[10px] font-black uppercase tracking-wider leading-5 text-blue-700 bg-blue-100 rounded-md mb-1.5 border border-blue-200">
                                                {{ Str::limit($item->jenis_permohonan ?? '-', 20) }}
                                            </span>
                                            <div class="text-sm font-bold text-slate-800">{{ $item->nama_pemohon ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-slate-700">
                                                {{ $item->jenis_hak ?? '-' }} <span class="font-mono bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded border border-slate-200 ml-1">{{ $item->nomer_hak ?? '-' }}</span>
                                            </div>
                                            <div class="text-xs font-semibold text-slate-500 mt-1.5 flex items-center">
                                                <i class="fa-solid fa-map-location-dot text-rose-400 mr-1.5"></i> {{ $item->desa ?? '-' }}, Kec. {{ $item->kecamatan ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                            @if(($item->status_berkas ?? '') === 'draft')
                                                <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold border border-amber-200">DRAFT</span>
                                            @elseif(($item->status_berkas ?? '') === 'dikembalikan')
                                                <span class="px-3 py-1 rounded-full bg-rose-100 text-rose-800 text-xs font-bold border border-rose-200">DIKEMBALIKAN</span>
                                            @else
                                                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold border border-emerald-200">PROSES BPN</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <button type="button" @click="showQrModal('{{ $item->nomer_berkas ?? '' }}', '{{ $item->nama_pemohon ?? '' }}')" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg shadow-sm hover:bg-indigo-700 transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1" title="Tampilkan QR Code">
                                                <i class="fa-solid fa-qrcode mr-1.5"></i> Tampilkan QR
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                                <i class="fa-solid fa-folder-open text-2xl text-slate-300"></i>
                                            </div>
                                            <p class="text-sm font-bold text-slate-500">Belum ada berkas di meja Anda.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'buat'" x-cloak class="bg-white shadow-sm rounded-2xl border-l-4 border-amber-500 border-y border-r border-slate-200 overflow-hidden">
                <div class="p-6 md:p-8">
                    <h3 class="text-xl font-black text-slate-800 mb-6 flex items-center tracking-tight border-b border-slate-100 pb-4">
                        <span class="bg-amber-100 text-amber-600 w-10 h-10 flex items-center justify-center rounded-xl mr-3"><i class="fa-solid fa-file-circle-plus"></i></span>
                        Formulir Registrasi Berkas Baru
                    </h3>
                    
                    <form action="{{ route('berkas.biasa.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="bg-slate-50/50 p-5 md:p-6 rounded-2xl border border-slate-200 shadow-sm">
                            <h4 class="font-black text-xs text-slate-500 mb-5 uppercase tracking-widest flex items-center">
                                <i class="fa-solid fa-user-pen mr-2"></i> Identitas Pemohon & Layanan
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-600 mb-1.5 uppercase tracking-wider">NAMA PEMOHON <span class="text-rose-500">*</span></label>
                                    <input type="text" name="nama_pemohon" required class="w-full bg-white border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-sm p-3 font-semibold transition-colors" placeholder="Nama lengkap sesuai KTP">
                                </div>
                                <!-- KODE BARU UNTUK KOLOM JENIS PERMOHONAN -->
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-600 mb-1.5 uppercase tracking-wider">JENIS PERMOHONAN <span class="text-rose-500">*</span></label>
                                    <select name="jenis_permohonan" required class="w-full bg-white border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-sm p-3 font-semibold cursor-pointer appearance-none transition-colors">
                                        <option value="" disabled selected>-- Pilih Layanan / Permohonan --</option>
                                        @foreach($jenisPermohonans ?? [] as $permohonan)
                                            <option value="{{ $permohonan }}">{{ $permohonan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50/50 p-5 md:p-6 rounded-2xl border border-slate-200 shadow-sm">
                            <h4 class="font-black text-xs text-slate-500 mb-5 uppercase tracking-widest flex items-center">
                                <i class="fa-solid fa-map-location-dot mr-2"></i> Data Alas Hak & Wilayah
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-600 mb-1.5 uppercase tracking-wider">JENIS HAK <span class="text-rose-500">*</span></label>
                                    <select name="jenis_hak" required class="w-full bg-white border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-sm p-3 font-semibold cursor-pointer appearance-none transition-colors">
                                        <option value="" disabled selected>-- Pilih Jenis Hak --</option>
                                        @foreach($jenisHaks ?? [] as $hak)
                                            <option value="{{ $hak->nama_hak }}">{{ $hak->nama_hak }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-600 mb-1.5 uppercase tracking-wider">NOMOR HAK <span class="text-rose-500">*</span></label>
                                    <input type="text" name="nomer_hak" required class="w-full bg-white border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-sm p-3 font-semibold transition-colors" placeholder="Nomor Sertipikat/Hak">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-600 mb-1.5 uppercase tracking-wider">KECAMATAN <span class="text-rose-500">*</span></label>
                                    <select name="kecamatan_id" x-model="selectedKecamatan" @change="updateDesa()" required class="w-full bg-white border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-sm p-3 font-semibold cursor-pointer appearance-none transition-colors">
                                        <option value="" disabled>-- Pilih Kecamatan --</option>
                                        <template x-for="kec in kecamatans" :key="kec.id">
                                            <option :value="kec.id" x-text="kec.nama_kecamatan"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-600 mb-1.5 uppercase tracking-wider">DESA / KELURAHAN <span class="text-rose-500">*</span></label>
                                    <select name="desa_id" required :disabled="desas.length === 0" class="w-full bg-white border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-sm p-3 font-semibold cursor-pointer appearance-none transition-colors disabled:opacity-50 disabled:bg-slate-100 disabled:cursor-not-allowed">
                                        <option value="" disabled selected>-- Pilih Desa --</option>
                                        <template x-for="d in desas" :key="d.id">
                                            <option :value="d.id" x-text="d.nama_desa"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="inline-flex items-center px-6 py-3.5 bg-indigo-600 border border-transparent rounded-xl font-black text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all shadow-md hover:shadow-lg">
                                <i class="fa-solid fa-save mr-2 text-lg"></i> Simpan & Generate Berkas
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div x-show="activeTab === 'scan'" x-cloak class="bg-white shadow-sm rounded-2xl border-l-4 border-purple-500 border-y border-r border-slate-200 overflow-hidden">
                <div class="p-6 md:p-8">
                    <h3 class="text-xl font-black text-slate-800 mb-6 flex items-center tracking-tight border-b border-slate-100 pb-4">
                        <span class="bg-purple-100 text-purple-600 w-10 h-10 flex items-center justify-center rounded-xl mr-3"><i class="fa-solid fa-qrcode"></i></span>
                        Verifikasi Penerimaan Berkas (Scan QR)
                    </h3>
                    
                    <div class="max-w-md mx-auto">
                        <div class="bg-purple-50 border border-purple-200 text-purple-800 p-4 rounded-xl mb-6 shadow-sm">
                            <h4 class="font-bold text-sm mb-1"><i class="fa-solid fa-camera mr-1"></i> Arahkan Kamera ke QR Code</h4>
                            <p class="text-xs">Gunakan pemindai ini untuk memverifikasi fisik berkas yang dikembalikan oleh Loket BPN ke tangan Anda.</p>
                        </div>
                        
                        <div id="reader" class="rounded-xl overflow-hidden border-2 border-dashed border-slate-300 w-full bg-slate-50 mb-6"></div>
                        
                        <div x-show="scanResult" class="bg-emerald-50 text-emerald-800 p-5 rounded-xl border border-emerald-200 shadow-sm text-center">
                            <p class="text-[10px] font-black uppercase tracking-widest mb-2 text-emerald-600">Terdeteksi Nomor Berkas:</p>
                            <p class="font-black text-2xl tracking-widest mb-5 font-mono bg-white inline-block px-4 py-2 rounded-lg border border-emerald-100" x-text="scanResult"></p>
                            <form action="#" method="POST">
                                @csrf
                                <input type="hidden" name="nomer_berkas" x-model="scanResult">
                                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 bg-emerald-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 transition shadow-md">
                                    <i class="fa-solid fa-check-double mr-2"></i> Konfirmasi Terima
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div x-show="qrModalOpen" x-cloak class="fixed z-[100] inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                
                <div x-show="qrModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeQrModal()"></div>
                
                <div x-show="qrModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full border border-slate-100">
                    
                    <div class="bg-indigo-600 p-5 text-center relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 w-16 h-16 bg-indigo-500 rounded-full opacity-50"></div>
                        <div class="absolute -left-4 -bottom-4 w-12 h-12 bg-indigo-700 rounded-full opacity-50"></div>
                        
                        <h3 class="text-lg leading-6 font-black text-white relative z-10" id="modal-title">QR Code Penyerahan</h3>
                        <p class="text-[11px] text-indigo-200 mt-1 font-bold uppercase tracking-widest relative z-10">Tunjukkan ke Petugas Loket BPN</p>
                    </div>
                    
                    <div class="bg-white px-4 pt-6 pb-4 sm:p-6 sm:pb-5 flex flex-col items-center relative z-10">
                        <div id="qrcode-container" class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 mb-5 inline-block"></div>
                        
                        <div class="text-center w-full bg-slate-50 py-3 rounded-xl border border-slate-200">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Kode Berkas</p>
                            <p class="text-2xl font-black text-indigo-700 tracking-widest font-mono" x-text="selectedNoBerkas"></p>
                        </div>
                        
                        <div class="mt-5 w-full">
                            <p class="text-xs text-slate-700 font-bold bg-slate-100 border border-slate-200 px-4 py-2.5 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-user-astronaut mr-2.5 text-slate-400 text-lg"></i> <span x-text="selectedPemohon"></span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-200">
                        <button type="button" @click="closeQrModal()" class="w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-2.5 bg-white text-sm font-bold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto transition-colors">
                            Tutup Jendela
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function ruangKerja(kecamatansData = []) {
            return {
                activeTab: 'daftar',
                
                // Data untuk Modal QR
                qrModalOpen: false,
                selectedNoBerkas: '',
                selectedPemohon: '',
                qrCodeInstance: null,
                
                // Data untuk Scanner
                html5QrcodeScanner: null,
                scanResult: '',

                // Data untuk Dropdown Form (Input Berkas Baru)
                kecamatans: kecamatansData,
                selectedKecamatan: '',
                desas: [],

                // Fungsi Dropdown Dinamis
                updateDesa() {
                    let kec = this.kecamatans.find(k => k.id == this.selectedKecamatan);
                    this.desas = kec && kec.desa ? kec.desa : [];
                },

                // Logika Tab & Scanner
                switchTab(tabName) {
                    this.activeTab = tabName;
                    if (tabName === 'scan') {
                        this.startScanner();
                    } else {
                        this.stopScanner();
                    }
                },

                // Logika Menampilkan Modal QR
                showQrModal(noBerkas, pemohon) {
                    this.selectedNoBerkas = noBerkas;
                    this.selectedPemohon = pemohon;
                    this.qrModalOpen = true;
                    
                    // Generate QR (diberi delay sedikit agar Alpine selesai merender div-nya)
                    setTimeout(() => {
                        const container = document.getElementById("qrcode-container");
                        container.innerHTML = ""; 
                        
                        this.qrCodeInstance = new QRCode(container, {
                            text: noBerkas,
                            width: 200,
                            height: 200,
                            colorDark : "#4338ca", // Warna Indigo 700
                            colorLight : "#ffffff",
                            correctLevel : QRCode.CorrectLevel.H
                        });
                    }, 150);
                },

                closeQrModal() {
                    this.qrModalOpen = false;
                },

                // Logika Inisiasi Kamera (Scanner)
                startScanner() {
                    this.scanResult = '';
                    if (!this.html5QrcodeScanner) {
                        this.html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: {width: 250, height: 250} }, false);
                    }
                    this.html5QrcodeScanner.render((decodedText, decodedResult) => {
                        this.scanResult = decodedText;
                        this.html5QrcodeScanner.clear();
                    }, (errorMessage) => {});
                },

                stopScanner() {
                    if (this.html5QrcodeScanner) {
                        this.html5QrcodeScanner.clear().catch(error => {
                            console.error("Failed to clear scanner.", error);
                        });
                    }
                }
            }
        }
    </script>
</x-app-layout>