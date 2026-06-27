<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                <i class="fa-solid fa-desktop mr-3 text-indigo-600"></i>
                {{ __('Ruang Kerja Saya (Berkas Fisik)') }}
            </h2>

            <div class="flex items-center gap-3">
                <a href="{{ route('mitra.plotting') }}" class="inline-flex items-center px-4 py-2 bg-white text-indigo-600 border border-indigo-600 rounded-md font-bold text-xs uppercase tracking-widest hover:bg-indigo-50 active:bg-indigo-100 focus:outline-none transition shadow-sm">
                    <i class="fa-solid fa-map-location-dot mr-2 text-lg"></i>
                    <span>Beralih ke Plotting</span>
                </a>
            </div>
        </div>
    </x-slot>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <div class="py-12 bg-gray-100" x-data="ruangKerja()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">
                    <i class="fa-solid fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-b border-gray-200">
                <div class="flex flex-wrap border-b border-gray-200 bg-gray-50">
                    <button @click="activeTab = 'daftar'" :class="activeTab === 'daftar' ? 'border-indigo-500 text-indigo-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'" class="px-6 py-4 border-b-4 font-bold text-sm transition-all outline-none flex items-center gap-2 w-full sm:w-auto justify-center">
                        <i class="fa-solid fa-file-signature"></i> Berkas di Meja Saya
                    </button>
                    <button @click="activeTab = 'buat'" :class="activeTab === 'buat' ? 'border-yellow-500 text-yellow-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'" class="px-6 py-4 border-b-4 font-bold text-sm transition-all outline-none flex items-center gap-2 w-full sm:w-auto justify-center">
                        <i class="fa-solid fa-plus-circle"></i> Input Berkas Baru
                    </button>
                    <button @click="switchTab('scan')" :class="activeTab === 'scan' ? 'border-purple-500 text-purple-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'" class="px-6 py-4 border-b-4 font-bold text-sm transition-all outline-none flex items-center gap-2 w-full sm:w-auto justify-center">
                        <i class="fa-solid fa-qrcode"></i> Scan Pengembalian BPN
                    </button>
                </div>
            </div>

            <div x-show="activeTab === 'daftar'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-500">
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center">
                            <span class="bg-blue-100 text-blue-600 p-2 rounded-full mr-3"><i class="fa-solid fa-file-signature"></i></span>
                            Daftar Berkas Fisik Anda
                        </h3>
                        
                        <div class="relative w-full sm:w-auto">
                            <input type="text" placeholder="Cari No. Berkas..." class="pl-10 pr-4 py-2 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-full shadow-sm text-sm w-full lg:w-72">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. Berkas</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Perihal & Pemohon</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Hak & Lokasi</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status Berkas</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi Verifikasi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($berkas as $item)
                                    <tr class="hover:bg-blue-50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-800">{{ $item->nomer_berkas }}</div>
                                            <div class="text-xs text-gray-500 mt-1"><i class="fa-regular fa-clock mr-1"></i> Tahun {{ $item->tahun_berkas }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex px-2 text-xs font-semibold leading-5 text-blue-800 bg-blue-100 rounded-full mb-1">
                                                {{ Str::limit($item->jenis_permohonan, 20) }}
                                            </span>
                                            <div class="text-sm font-medium text-gray-900">{{ $item->nama_pemohon }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-semibold text-gray-700">
                                                {{ $item->jenis_hak }} <span class="font-mono bg-blue-50 text-blue-700 px-1 rounded border border-blue-100">{{ $item->nomer_hak }}</span>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1 flex items-center">
                                                <i class="fa-solid fa-map-location-dot text-red-400 mr-1.5"></i> {{ $item->desa }}, Kec. {{ $item->kecamatan }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            @if($item->status_berkas === 'draft')
                                                <span class="px-3 py-1 rounded bg-yellow-100 text-yellow-800 text-xs font-bold border border-yellow-200">DRAFT (Belum Diserahkan)</span>
                                            @elseif($item->status_berkas === 'dikembalikan')
                                                <span class="px-3 py-1 rounded bg-red-100 text-red-800 text-xs font-bold border border-red-200">DIKEMBALIKAN</span>
                                            @else
                                                <span class="px-3 py-1 rounded bg-emerald-100 text-emerald-800 text-xs font-bold border border-emerald-200">PROSES BPN</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button type="button" @click="showQrModal('{{ $item->nomer_berkas }}', '{{ $item->nama_pemohon }}')" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-medium rounded shadow hover:bg-indigo-700 transition" title="Tampilkan QR Code">
                                                <i class="fa-solid fa-qrcode mr-1.5"></i> Tampilkan QR
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">Belum ada berkas di meja Anda.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'buat'" x-cloak class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-yellow-500">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                        <span class="bg-yellow-100 text-yellow-600 p-2 rounded-full mr-3"><i class="fa-solid fa-file-circle-plus"></i></span>
                        Formulir Registrasi Berkas Baru
                    </h3>
                    
                    <form action="{{ route('berkas.biasa.store') }}" method="POST" class="max-w-3xl space-y-5">
                        @csrf
                        
                        <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                            <h4 class="font-bold text-sm text-gray-700 mb-4 border-b border-gray-200 pb-2 uppercase">Identitas Pemohon & Layanan</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">NAMA PEMOHON <span class="text-red-500">*</span></label>
                                    <input type="text" name="nama_pemohon" required class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">JENIS PERMOHONAN <span class="text-red-500">*</span></label>
                                    <input type="text" name="jenis_permohonan" required class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" placeholder="Contoh: Pemecahan, Balik Nama...">
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-5 rounded-lg border border-gray-200" 
                             x-data="{ 
                                kecamatan_id: '', 
                                semuaDesa: {{ Js::from($desas ?? []) }}, 
                                desaTerfilter: [] 
                             }" 
                             x-effect="desaTerfilter = semuaDesa.filter(d => d.kecamatan_id == kecamatan_id)">
                            
                            <h4 class="font-bold text-sm text-gray-700 mb-4 border-b border-gray-200 pb-2 uppercase">Data Alas Hak & Wilayah</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">JENIS HAK <span class="text-red-500">*</span></label>
                                    <select name="jenis_hak" required class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                        <option value="">-- Pilih Jenis Hak --</option>
                                        @foreach($jenisHaks ?? [] as $hak)
                                            <option value="{{ $hak->nama_hak }}">{{ $hak->nama_hak }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">NOMOR HAK <span class="text-red-500">*</span></label>
                                    <input type="text" name="nomer_hak" required class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">KECAMATAN <span class="text-red-500">*</span></label>
                                    <select name="kecamatan" x-model="kecamatan_id" required class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                        <option value="">-- Pilih Kecamatan --</option>
                                        @foreach($kecamatans ?? [] as $kec)
                                            <option value="{{ $kec->id }}">{{ $kec->nama_kecamatan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">DESA / KELURAHAN <span class="text-red-500">*</span></label>
                                    <select name="desa" required class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm disabled:bg-gray-200" :disabled="!kecamatan_id">
                                        <option value="">-- Pilih Desa --</option>
                                        <template x-for="desa in desaTerfilter" :key="desa.id">
                                            <option :value="desa.nama_desa" x-text="desa.nama_desa"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 transition ease-in-out duration-150 shadow-md">
                                <i class="fa-solid fa-save mr-2"></i> Simpan & Generate Berkas
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div x-show="activeTab === 'scan'" x-cloak class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-purple-500">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                        <span class="bg-purple-100 text-purple-600 p-2 rounded-full mr-3"><i class="fa-solid fa-qrcode"></i></span>
                        Verifikasi Penerimaan Berkas (Scan QR)
                    </h3>
                    
                    <div class="max-w-md mx-auto">
                        <div class="bg-purple-50 border border-purple-200 text-purple-800 p-4 rounded-lg mb-6 shadow-sm">
                            <h4 class="font-bold text-sm mb-1"><i class="fa-solid fa-camera mr-1"></i> Arahkan Kamera ke QR Code</h4>
                            <p class="text-xs">Gunakan pemindai ini untuk memverifikasi fisik berkas yang dikembalikan oleh Loket BPN ke tangan Anda.</p>
                        </div>
                        
                        <div id="reader" class="rounded-lg overflow-hidden border-2 border-dashed border-gray-300 w-full bg-gray-50 mb-6"></div>
                        
                        <div x-show="scanResult" class="bg-green-50 text-green-800 p-5 rounded-lg border border-green-200 shadow-sm text-center">
                            <p class="text-xs font-bold uppercase mb-2 text-green-600">Terdeteksi Nomor Berkas:</p>
                            <p class="font-black text-2xl tracking-widest mb-4" x-text="scanResult"></p>
                            <form action="#" method="POST">
                                @csrf
                                <input type="hidden" name="nomer_berkas" x-model="scanResult">
                                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">
                                    <i class="fa-solid fa-check-double mr-2"></i> Konfirmasi Terima
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div x-show="qrModalOpen" x-cloak class="fixed z-[100] inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <div x-show="qrModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeQrModal()"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div x-show="qrModalOpen" x-transition.scale class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full">
                    <div class="bg-indigo-600 p-4 text-center">
                        <h3 class="text-lg leading-6 font-black text-white" id="modal-title">QR Code Penyerahan Berkas</h3>
                        <p class="text-xs text-indigo-200 mt-1 font-semibold">Tunjukkan QR ini ke Petugas Loket BPN</p>
                    </div>
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 flex flex-col items-center">
                        <div id="qrcode-container" class="bg-white p-3 rounded-lg shadow-sm border border-gray-200 mb-4 inline-block"></div>
                        
                        <div class="text-center w-full bg-gray-50 py-3 rounded-lg border border-gray-100">
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Kode Berkas</p>
                            <p class="text-3xl font-black text-indigo-700 tracking-widest font-mono" x-text="selectedNoBerkas"></p>
                        </div>
                        
                        <div class="mt-4 w-full">
                            <p class="text-xs text-gray-600 font-semibold bg-gray-100 px-3 py-2 rounded flex items-center justify-center">
                                <i class="fa-solid fa-user-circle mr-2 text-gray-400"></i> <span x-text="selectedPemohon"></span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                        <button type="button" @click="closeQrModal()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition">
                            Tutup Jendela
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function ruangKerja() {
            return {
                activeTab: 'daftar',
                qrModalOpen: false,
                selectedNoBerkas: '',
                selectedPemohon: '',
                html5QrcodeScanner: null,
                scanResult: '',
                qrCodeInstance: null,

                switchTab(tabName) {
                    this.activeTab = tabName;
                    if (tabName === 'scan') {
                        this.startScanner();
                    } else {
                        this.stopScanner();
                    }
                },

                showQrModal(noBerkas, pemohon) {
                    this.selectedNoBerkas = noBerkas;
                    this.selectedPemohon = pemohon;
                    this.qrModalOpen = true;
                    
                    setTimeout(() => {
                        const container = document.getElementById("qrcode-container");
                        container.innerHTML = ""; 
                        
                        this.qrCodeInstance = new QRCode(container, {
                            text: noBerkas,
                            width: 220,
                            height: 220,
                            colorDark : "#4338ca", // Indigo-700 dari Tailwind
                            colorLight : "#ffffff",
                            correctLevel : QRCode.CorrectLevel.H
                        });
                    }, 150);
                },

                closeQrModal() {
                    this.qrModalOpen = false;
                },

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