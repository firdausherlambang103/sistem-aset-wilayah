<x-app-layout>
    <x-slot name="header">Ruang Kerja Biasa - Mitra</x-slot>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <div class="p-4 lg:p-8" x-data="ruangKerja()">
        
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3 animate-pulse-once">
                <i class="fa-solid fa-circle-check text-emerald-500"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200">
            
            <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 flex items-center gap-2"><i class="fa-solid fa-folder-open text-blue-600"></i> Manajemen Berkas Fisik</h3>
            </div>

            <div class="flex border-b border-slate-200 px-6 pt-2 bg-slate-50 gap-6 overflow-x-auto custom-scrollbar">
                <button @click="activeTab = 'daftar'" :class="activeTab === 'daftar' ? 'border-blue-500 text-blue-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-medium'" class="px-4 py-3 border-b-[3px] transition-all flex items-center gap-2 text-sm whitespace-nowrap outline-none">
                    <i class="fa-solid fa-list-ul"></i> Daftar Berkas
                </button>
                <button @click="activeTab = 'buat'" :class="activeTab === 'buat' ? 'border-amber-500 text-amber-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-medium'" class="px-4 py-3 border-b-[3px] transition-all flex items-center gap-2 text-sm whitespace-nowrap outline-none">
                    <i class="fa-solid fa-plus"></i> Input Berkas Baru
                </button>
                <button @click="switchTab('scan')" :class="activeTab === 'scan' ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-medium'" class="px-4 py-3 border-b-[3px] transition-all flex items-center gap-2 text-sm whitespace-nowrap outline-none">
                    <i class="fa-solid fa-qrcode"></i> Scan Pengembalian BPN
                </button>
            </div>
            
            <div x-show="activeTab === 'daftar'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="overflow-x-auto custom-scrollbar">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">No. Berkas</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Pemohon & Hak</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Wilayah</th>
                            <th class="px-5 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi (QR)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @forelse($berkas as $item)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-5 py-4">
                                    <div class="text-lg font-black text-blue-700 tracking-widest">{{ $item->nomer_berkas }}</div>
                                    <div class="text-[10px] text-slate-500 mt-1 font-semibold">Tahun: {{ $item->tahun_berkas }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-sm font-bold text-slate-800">{{ $item->nama_pemohon }}</div>
                                    <div class="text-xs text-blue-600 font-semibold mt-1">{{ $item->jenis_hak }} - {{ $item->nomer_hak }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-sm font-medium text-slate-800">{{ $item->desa }}</div>
                                    <div class="text-xs text-slate-500">Kec. {{ $item->kecamatan }}</div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if($item->status_berkas === 'draft')
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-100 text-amber-700 border border-amber-200">Menunggu Diserahkan</span>
                                    @elseif($item->status_berkas === 'dikembalikan')
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-rose-100 text-rose-700 border border-rose-200">Dikembalikan</span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-700 border border-emerald-200">Proses BPN</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <button @click="showQrModal('{{ $item->nomer_berkas }}', '{{ $item->nama_pemohon }}')" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white transition-colors border border-blue-200 hover:border-blue-600 text-xs font-bold shadow-sm">
                                        <i class="fa-solid fa-qrcode"></i> Tampilkan QR
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500 italic font-medium">Belum ada berkas biasa yang Anda buat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div x-show="activeTab === 'buat'" x-cloak class="p-6">
                <form action="{{ route('berkas.biasa.store') }}" method="POST" class="max-w-2xl mx-auto space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1.5">NAMA PEMOHON <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_pemohon" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-2.5 outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1.5">JENIS PERMOHONAN <span class="text-rose-500">*</span></label>
                        <input type="text" name="jenis_permohonan" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-2.5 outline-none" placeholder="Contoh: Pemecahan, Balik Nama...">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1.5">JENIS HAK <span class="text-rose-500">*</span></label>
                            <input type="text" name="jenis_hak" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl p-2.5 outline-none">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1.5">NOMOR HAK <span class="text-rose-500">*</span></label>
                            <input type="text" name="nomer_hak" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl p-2.5 outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1.5">KECAMATAN <span class="text-rose-500">*</span></label>
                            <input type="text" name="kecamatan" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl p-2.5 outline-none">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1.5">DESA <span class="text-rose-500">*</span></label>
                            <input type="text" name="desa" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl p-2.5 outline-none">
                        </div>
                    </div>
                    <div class="pt-4">
                        <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-3 rounded-2xl shadow-lg transition-colors flex justify-center items-center gap-2">
                            <i class="fa-solid fa-save"></i> Simpan & Generate No. Berkas
                        </button>
                    </div>
                </form>
            </div>

            <div x-show="activeTab === 'scan'" x-cloak class="p-6">
                <div class="max-w-md mx-auto text-center">
                    <div class="bg-blue-50 text-blue-800 text-xs font-bold p-3 rounded-xl mb-4 border border-blue-200">
                        <i class="fa-solid fa-info-circle mr-1"></i> Gunakan kamera untuk memindai QR Code berkas yang dikembalikan oleh Petugas BPN.
                    </div>
                    
                    <div id="reader" class="rounded-2xl overflow-hidden border-2 border-dashed border-slate-300 w-full bg-slate-50 mb-4"></div>
                    
                    <div x-show="scanResult" class="bg-emerald-100 text-emerald-800 p-4 rounded-xl border border-emerald-300">
                        <p class="text-xs font-bold uppercase mb-1">Hasil Scan:</p>
                        <p class="font-black text-xl tracking-widest" x-text="scanResult"></p>
                        <form action="#" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="nomer_berkas" x-model="scanResult">
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2 px-4 rounded-lg shadow transition">Terima Berkas Kembali</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <div x-show="qrModalOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="closeQrModal()" x-transition.scale class="bg-white rounded-3xl w-full max-w-sm shadow-2xl flex flex-col border border-slate-100 overflow-hidden">
                <div class="bg-blue-600 p-4 text-center">
                    <h3 class="font-extrabold text-white">QR Code Berkas</h3>
                    <p class="text-[11px] text-blue-100">Scan QR ini di Loket Penerimaan BPN</p>
                </div>
                <div class="p-8 flex flex-col items-center bg-slate-50">
                    <div id="qrcode-container" class="bg-white p-3 rounded-xl shadow-sm border border-slate-200 mb-4"></div>
                    
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Nomor Berkas (6 Digit):</p>
                    <p class="text-2xl font-black text-slate-800 tracking-widest mb-4" x-text="selectedNoBerkas"></p>
                    
                    <p class="text-xs font-bold text-slate-600 bg-slate-200 px-3 py-1 rounded-full"><i class="fa-solid fa-user mr-1"></i> <span x-text="selectedPemohon"></span></p>
                </div>
                <div class="p-4 border-t border-slate-100 bg-white">
                    <button @click="closeQrModal()" class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">Tutup</button>
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
                    // Jika pindah ke tab scan, nyalakan kamera
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
                    
                    // Render QR Code (Delay sedikit agar modal render dulu)
                    setTimeout(() => {
                        const container = document.getElementById("qrcode-container");
                        container.innerHTML = ""; // Bersihkan QR sebelumnya
                        
                        this.qrCodeInstance = new QRCode(container, {
                            text: noBerkas, // Ini isi dari QR-nya (6 Digit nomor berkas)
                            width: 200,
                            height: 200,
                            colorDark : "#0f172a", // Warna gelap slate
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
                        // Ketika QR berhasil di-scan
                        this.scanResult = decodedText;
                        // Matikan scanner secara otomatis setelah terbaca
                        this.html5QrcodeScanner.clear();
                    }, (errorMessage) => {
                        // Hindari mencetak console log saat proses scanning mencari fokus
                    });
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