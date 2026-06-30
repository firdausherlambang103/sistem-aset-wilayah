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

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 min-h-[60vh]">
                
                <div class="flex border-b border-slate-200 px-6 pt-2 bg-slate-50 gap-2 md:gap-6 overflow-x-auto custom-scrollbar">
                    <button @click="switchTab('di_mitra')" :class="activeTab === 'di_mitra' ? 'border-rose-500 text-rose-600 font-bold bg-white' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-3 py-3 border-b-[3px] transition-all flex items-center gap-2 text-sm whitespace-nowrap outline-none">
                        <i class="fa-solid fa-file-pen"></i> Draft & Revisi ({{ collect($berkasMitra ?? [])->count() }})
                    </button>
                    <button @click="switchTab('di_bpn')" :class="activeTab === 'di_bpn' ? 'border-blue-500 text-blue-600 font-bold bg-white' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-3 py-3 border-b-[3px] transition-all flex items-center gap-2 text-sm whitespace-nowrap outline-none">
                        <i class="fa-solid fa-building-flag"></i> Sedang di BPN ({{ collect($berkasBpn ?? [])->count() }})
                    </button>
                    <button @click="switchTab('selesai')" :class="activeTab === 'selesai' ? 'border-emerald-500 text-emerald-600 font-bold bg-white' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-3 py-3 border-b-[3px] transition-all flex items-center gap-2 text-sm whitespace-nowrap outline-none">
                        <i class="fa-solid fa-check-double"></i> Selesai ({{ collect($berkasSelesai ?? [])->count() }})
                    </button>
                    <button @click="switchTab('scan')" :class="activeTab === 'scan' ? 'border-purple-500 text-purple-600 font-bold bg-white' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-3 py-3 border-b-[3px] transition-all flex items-center gap-2 text-sm whitespace-nowrap outline-none">
                        <i class="fa-solid fa-qrcode"></i> Scan BPN
                    </button>
                    
                    <div class="flex-1"></div> <button @click="switchTab('buat')" :class="activeTab === 'buat' ? 'border-indigo-500 text-indigo-600 font-bold bg-indigo-50/50 rounded-t-lg' : 'border-transparent text-indigo-600 hover:text-indigo-800'" class="px-4 py-3 border-b-[3px] transition-all flex items-center gap-2 text-sm whitespace-nowrap outline-none">
                        <i class="fa-solid fa-plus"></i> Buat Berkas Baru
                    </button>
                </div>

                <div x-show="activeTab === 'di_mitra'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="overflow-x-auto">
                    <div class="p-6 pb-2">
                        <h3 class="text-lg font-black text-slate-800 flex items-center tracking-tight mb-4">
                            <span class="bg-rose-100 text-rose-600 w-10 h-10 flex items-center justify-center rounded-xl mr-3"><i class="fa-solid fa-file-pen"></i></span>
                            Berkas yang Harus Anda Kerjakan
                        </h3>
                    </div>
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50 border-y border-slate-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-black text-slate-500 uppercase tracking-wider">No. Berkas</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-slate-500 uppercase tracking-wider">Perihal & Pemohon</th>
                                <th class="px-6 py-4 text-center text-xs font-black text-slate-500 uppercase tracking-wider">Status Berkas</th>
                                <th class="px-6 py-4 text-right text-xs font-black text-slate-500 uppercase tracking-wider">Aksi Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse($berkasMitra ?? [] as $item)
                                <tr class="hover:bg-rose-50/30 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-rose-700">{{ $item->nomer_berkas ?? '-' }}</div>
                                        <div class="text-[11px] font-bold text-slate-500 mt-1 uppercase tracking-wider"><i class="fa-regular fa-clock mr-1"></i> Tahun {{ $item->tahun_berkas ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-2.5 py-1 text-[10px] font-black uppercase tracking-wider leading-5 text-slate-700 bg-slate-100 rounded-md mb-1.5 border border-slate-200">
                                            {{ Str::limit($item->jenis_permohonan ?? '-', 20) }}
                                        </span>
                                        <div class="text-sm font-bold text-slate-800">{{ $item->nama_pemohon ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if(($item->status_berkas ?? '') === 'dikembalikan')
                                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-rose-100 text-rose-700 border border-rose-200">DIKEMBALIKAN BPN</span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-amber-100 text-amber-700 border border-amber-200">DRAFT (BELUM DIKIRIM)</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <button type="button" @click="showQrModal('{{ $item->nomer_berkas ?? '' }}', '{{ $item->nama_pemohon ?? '' }}')" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg shadow-sm hover:bg-indigo-700 transition" title="Tampilkan QR Code untuk Loket">
                                            <i class="fa-solid fa-qrcode mr-1.5"></i> Tampilkan QR ke Loket
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fa-solid fa-folder-open text-2xl text-slate-300"></i></div>
                                        <p class="text-sm font-bold text-slate-500">Tidak ada berkas draft atau revisi di meja Anda.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div x-show="activeTab === 'di_bpn'" x-cloak class="overflow-x-auto">
                    <div class="p-6 pb-2">
                        <h3 class="text-lg font-black text-slate-800 flex items-center tracking-tight mb-4">
                            <span class="bg-blue-100 text-blue-600 w-10 h-10 flex items-center justify-center rounded-xl mr-3"><i class="fa-solid fa-building-flag"></i></span>
                            Berkas Sedang Diproses BPN
                        </h3>
                    </div>
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50 border-y border-slate-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-black text-slate-500 uppercase tracking-wider">No. Berkas</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-slate-500 uppercase tracking-wider">Perihal & Pemohon</th>
                                <th class="px-6 py-4 text-center text-xs font-black text-slate-500 uppercase tracking-wider">Posisi / Status</th>
                                <th class="px-6 py-4 text-right text-xs font-black text-slate-500 uppercase tracking-wider">Dokumen SPS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($berkasBpn ?? [] as $item)
                                <tr class="hover:bg-blue-50/30 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-blue-700 tracking-widest">{{ $item->nomer_berkas ?? '-' }}</div>
                                        <div class="text-[11px] font-bold text-slate-500 mt-1 uppercase tracking-wider"><i class="fa-regular fa-clock mr-1"></i> Tahun {{ $item->tahun_berkas ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-2.5 py-1 text-[10px] font-black uppercase tracking-wider leading-5 text-blue-700 bg-blue-100 rounded-md mb-1.5 border border-blue-200">
                                            {{ Str::limit($item->jenis_permohonan ?? '-', 20) }}
                                        </span>
                                        <div class="text-sm font-bold text-slate-800">{{ $item->nama_pemohon ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $badgeColor = 'bg-slate-100 text-slate-700 border-slate-200';
                                            $statusText = strtoupper(str_replace('_', ' ', $item->status_berkas ?? ''));
                                            
                                            if(in_array($item->status_berkas, ['di_loket_terima', 'di_loket_koreksi'])) $badgeColor = 'bg-amber-100 text-amber-700 border-amber-200';
                                            if($item->status_berkas === 'backoffice_sps') $badgeColor = 'bg-indigo-100 text-indigo-700 border-indigo-200';
                                            if($item->status_berkas === 'pembayaran_validasi') $badgeColor = 'bg-purple-100 text-purple-700 border-purple-200';
                                            if($item->status_berkas === 'pelaksana_kegiatan') $badgeColor = 'bg-teal-100 text-teal-700 border-teal-200';
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black tracking-widest border {{ $badgeColor }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($item->sps && $item->sps->file_sps)
                                            <a href="{{ asset('storage/' . $item->sps->file_sps) }}" target="_blank" class="inline-flex items-center gap-1.5 text-white bg-rose-600 hover:bg-rose-700 px-3 py-2 rounded-lg font-bold text-xs shadow-sm transition">
                                                <i class="fa-solid fa-file-pdf"></i> Unduh SPS
                                            </a>
                                        @else
                                            <span class="text-slate-400 text-[11px] font-bold italic border border-slate-200 px-3 py-1.5 rounded-md bg-slate-50 inline-block">Belum Terbit</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <p class="text-sm font-bold text-slate-500">Tidak ada berkas Anda yang sedang diproses di BPN.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div x-show="activeTab === 'selesai'" x-cloak class="overflow-x-auto">
                    <div class="p-6 pb-2">
                        <h3 class="text-lg font-black text-slate-800 flex items-center tracking-tight mb-4">
                            <span class="bg-emerald-100 text-emerald-600 w-10 h-10 flex items-center justify-center rounded-xl mr-3"><i class="fa-solid fa-check-double"></i></span>
                            Berkas Selesai
                        </h3>
                    </div>
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50 border-y border-slate-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-black text-slate-500 uppercase tracking-wider">No. Berkas</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-slate-500 uppercase tracking-wider">Pemohon</th>
                                <th class="px-6 py-4 text-center text-xs font-black text-slate-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($berkasSelesai ?? [] as $item)
                                <tr class="hover:bg-emerald-50/30">
                                    <td class="px-6 py-4 font-black text-emerald-700">{{ $item->nomer_berkas }}</td>
                                    <td class="px-6 py-4 font-bold text-slate-800">{{ $item->nama_pemohon }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-700 border border-emerald-200"><i class="fa-solid fa-check mr-1"></i> SELESAI</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-6 py-12 text-center text-slate-500 italic text-sm">Belum ada berkas yang selesai.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div x-show="activeTab === 'buat'" x-cloak class="p-6 md:p-8 bg-indigo-50/30">
                    <h3 class="text-xl font-black text-slate-800 mb-6 flex items-center tracking-tight border-b border-slate-200 pb-4 max-w-3xl mx-auto">
                        <span class="bg-indigo-100 text-indigo-600 w-10 h-10 flex items-center justify-center rounded-xl mr-3"><i class="fa-solid fa-folder-plus"></i></span>
                        Formulir Registrasi Berkas Baru
                    </h3>
                    
                    <form action="{{ route('berkas.biasa.store') }}" method="POST" class="space-y-6 max-w-3xl mx-auto bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                        @csrf
                        
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1.5 uppercase tracking-wider">Nomor Berkas <span class="text-rose-500">*</span></label>
                                <input type="text" name="nomer_berkas" required 
                                    value="{{ strtoupper(\Illuminate\Support\Str::random(6)) }}"
                                    class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:border-indigo-500 focus:ring-indigo-500 uppercase font-black tracking-widest" 
                                    placeholder="Misal: 123456">
                                <p class="text-[10px] text-slate-400 mt-1">Otomatis acak. Ganti dengan no fisik jika ada.</p>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1.5 uppercase tracking-wider">Tahun Berkas <span class="text-rose-500">*</span></label>
                                <input type="number" name="tahun_berkas" required value="{{ date('Y') }}"
                                    class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:border-indigo-500 focus:ring-indigo-500 font-bold">
                            </div>
                        </div>

                        <div class="bg-slate-50/50 p-5 md:p-6 rounded-2xl border border-slate-200 shadow-sm">
                            <h4 class="font-black text-xs text-slate-500 mb-5 uppercase tracking-widest flex items-center">
                                <i class="fa-solid fa-user-pen mr-2"></i> Identitas Pemohon & Layanan
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-600 mb-1.5 uppercase tracking-wider">NAMA PEMOHON <span class="text-rose-500">*</span></label>
                                    <input type="text" name="nama_pemohon" required class="w-full bg-white border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-sm p-3 font-semibold transition-colors" placeholder="Nama lengkap sesuai KTP">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-600 mb-1.5 uppercase tracking-wider">JENIS PERMOHONAN <span class="text-rose-500">*</span></label>
                                    <select name="jenis_permohonan" required class="w-full bg-white border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-sm p-3 font-semibold cursor-pointer appearance-none transition-colors">
                                        <option value="" disabled selected>-- Pilih Layanan --</option>
                                        <option value="Peralihan Hak (Jual Beli)">Peralihan Hak (Jual Beli)</option>
                                        <option value="Pendaftaran SK">Pendaftaran SK</option>
                                        <option value="Pemecahan / Penggabungan">Pemecahan / Penggabungan</option>
                                        <option value="Roya / Hak Tanggungan">Roya / Hak Tanggungan</option>
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
                                        <option value="Hak Milik">Hak Milik (HM)</option>
                                        <option value="Hak Guna Bangunan">Hak Guna Bangunan (HGB)</option>
                                        <option value="Hak Pakai">Hak Pakai (HP)</option>
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
                                    <select name="kecamatan" x-model="selectedKecamatan" @change="updateDesa()" required class="w-full bg-white border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-sm p-3 font-semibold cursor-pointer appearance-none transition-colors">
                                        <option value="" disabled>-- Pilih Kecamatan --</option>
                                        <template x-for="kec in kecamatans" :key="kec.id">
                                            <option :value="kec.nama_kecamatan" x-text="kec.nama_kecamatan"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-600 mb-1.5 uppercase tracking-wider">DESA / KELURAHAN <span class="text-rose-500">*</span></label>
                                    <select name="desa" required :disabled="desas.length === 0" class="w-full bg-white border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-sm p-3 font-semibold cursor-pointer appearance-none transition-colors disabled:opacity-50 disabled:bg-slate-100 disabled:cursor-not-allowed">
                                        <option value="" disabled selected>-- Pilih Desa --</option>
                                        <template x-for="d in desas" :key="d.id">
                                            <option :value="d.nama_desa" x-text="d.nama_desa"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="inline-flex items-center px-6 py-3.5 bg-indigo-600 border border-transparent rounded-xl font-black text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all shadow-md hover:shadow-lg w-full md:w-auto justify-center">
                                <i class="fa-solid fa-save mr-2 text-lg"></i> Simpan & Daftarkan Berkas
                            </button>
                        </div>
                    </form>
                </div>

                <div x-show="activeTab === 'scan'" x-cloak class="p-6 md:p-8 bg-slate-50 border-t border-slate-200">
                    <h3 class="text-xl font-black text-slate-800 mb-6 flex items-center tracking-tight border-b border-slate-200 pb-4 max-w-md mx-auto">
                        <span class="bg-purple-100 text-purple-600 w-10 h-10 flex items-center justify-center rounded-xl mr-3"><i class="fa-solid fa-qrcode"></i></span>
                        Scan Bukti Pengembalian
                    </h3>
                    
                    <div class="max-w-md mx-auto">
                        <div class="bg-white border border-slate-200 p-4 rounded-xl mb-6 shadow-sm">
                            <h4 class="font-bold text-sm text-slate-700 mb-1"><i class="fa-solid fa-camera mr-1 text-slate-400"></i> Arahkan Kamera ke QR</h4>
                            <p class="text-xs text-slate-500">Gunakan fitur ini untuk memverifikasi bahwa berkas fisik benar-benar sudah dikembalikan BPN ke tangan Anda.</p>
                        </div>
                        
                        <div id="reader" class="rounded-xl overflow-hidden border-2 border-dashed border-slate-300 w-full bg-white mb-6"></div>
                        
                        <div x-show="scanResult" class="bg-emerald-50 text-emerald-800 p-5 rounded-xl border border-emerald-200 shadow-sm text-center">
                            <p class="text-[10px] font-black uppercase tracking-widest mb-2 text-emerald-600">Terdeteksi Nomor Berkas:</p>
                            <p class="font-black text-2xl tracking-widest mb-5 font-mono bg-white inline-block px-4 py-2 rounded-lg border border-emerald-100" x-text="scanResult"></p>
                            </div>
                    </div>
                </div>

            </div>
        </div>

        <div x-show="qrModalOpen" x-cloak class="fixed z-[100] inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                <div x-show="qrModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeQrModal()"></div>
                
                <div x-show="qrModalOpen" 
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95" 
                     class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full border border-slate-100">
                    
                    <div class="bg-indigo-600 p-5 text-center relative overflow-hidden">
                        <h3 class="text-lg leading-6 font-black text-white relative z-10">QR Code Penyerahan</h3>
                        <p class="text-[11px] text-indigo-200 mt-1 font-bold uppercase tracking-widest relative z-10">Tunjukkan ke Petugas Loket BPN</p>
                    </div>
                    
                    <div class="bg-white px-4 pt-6 pb-4 sm:p-6 sm:pb-5 flex flex-col items-center">
                        <div id="qrcode-container" class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 mb-5 inline-block"></div>
                        
                        <div class="text-center w-full bg-slate-50 py-3 rounded-xl border border-slate-200 mb-4">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Kode Berkas</p>
                            <p class="text-2xl font-black text-indigo-700 tracking-widest font-mono" x-text="selectedNoBerkas"></p>
                        </div>
                        
                        <div class="w-full">
                            <p class="text-xs text-slate-700 font-bold bg-slate-100 border border-slate-200 px-4 py-2.5 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-user mr-2 text-slate-400"></i> <span x-text="selectedPemohon"></span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-200">
                        <button type="button" @click="closeQrModal()" class="w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-2.5 bg-white text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors">
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
                activeTab: 'di_bpn', // Default Tab yang terbuka
                
                qrModalOpen: false,
                selectedNoBerkas: '',
                selectedPemohon: '',
                qrCodeInstance: null,
                
                html5QrcodeScanner: null,
                scanResult: '',

                kecamatans: kecamatansData,
                selectedKecamatan: '',
                desas: [],

                // Update Array Desa berdasarkan text Kecamatan (Bukan ID karena schema menyimpan string)
                updateDesa() {
                    let kec = this.kecamatans.find(k => k.nama_kecamatan == this.selectedKecamatan);
                    this.desas = kec && kec.desa ? kec.desa : [];
                },

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
                            text: noBerkas, width: 200, height: 200,
                            colorDark : "#4338ca", colorLight : "#ffffff", correctLevel : QRCode.CorrectLevel.H
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
                    this.html5QrcodeScanner.render((decodedText) => {
                        this.scanResult = decodedText;
                        this.html5QrcodeScanner.clear();
                    }, () => {});
                },

                stopScanner() {
                    if (this.html5QrcodeScanner) {
                        this.html5QrcodeScanner.clear().catch(err => console.error(err));
                    }
                }
            }
        }
    </script>
</x-app-layout>