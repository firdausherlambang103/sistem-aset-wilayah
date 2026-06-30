<x-app-layout>
    <x-slot name="header">Ruang Kerja - Loket Pembayaran</x-slot>

    <div class="p-4 lg:p-8" x-data="pembayaranApp()">
        
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-500"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 min-h-[60vh]">
            
            <div class="flex border-b border-slate-200 px-6 pt-2 bg-slate-50 gap-2 md:gap-6 overflow-x-auto custom-scrollbar">
                <button @click="activeTab = 'antrean'" :class="activeTab === 'antrean' ? 'border-indigo-500 text-indigo-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-3 py-3 border-b-[3px] transition-all flex items-center gap-2 text-sm whitespace-nowrap outline-none">
                    <i class="fa-solid fa-wallet"></i> Antrean Pembayaran ({{ $antrean->count() }})
                </button>
                <button @click="activeTab = 'kwitansi'" :class="activeTab === 'kwitansi' ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-3 py-3 border-b-[3px] transition-all flex items-center gap-2 text-sm whitespace-nowrap outline-none">
                    <i class="fa-solid fa-receipt"></i> Riwayat Kwitansi Tercetak ({{ $kwitansi->count() }})
                </button>
            </div>

            <div x-show="activeTab === 'antrean'" x-cloak class="overflow-x-auto">
                <div class="bg-indigo-50 text-indigo-800 text-xs p-3 font-medium border-b border-indigo-100 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info"></i> Daftar di bawah ini adalah berkas yang dokumen SPS-nya sudah diterbitkan Backoffice. Petugas menerima pembayaran lalu menyerahkan kwitansi fisik.
                </div>
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase">No. Berkas</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase">Pemohon / Layanan</th>
                            <th class="px-5 py-4 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                            <th class="px-5 py-4 text-right text-xs font-bold text-slate-500 uppercase">Dokumen & Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($antrean as $item)
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
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-100 text-amber-700 border border-amber-200">MENUNGGU PEMBAYARAN</span>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($item->sps && $item->sps->file_sps)
                                            <a href="{{ asset('storage/' . $item->sps->file_sps) }}" target="_blank" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors flex items-center justify-center" title="Lihat PDF SPS">
                                                <i class="fa-solid fa-file-pdf"></i>
                                            </a>
                                        @endif
                                        
                                        <button @click="openProsesModal({{ $item->id }}, '{{ $item->nomer_berkas }}', '{{ $item->nama_pemohon }}')" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors text-xs font-bold shadow-sm">
                                            <i class="fa-solid fa-hand-holding-dollar"></i> Serahkan Kwitansi
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-slate-500 italic text-sm">Tidak ada antrean pembayaran saat ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div x-show="activeTab === 'kwitansi'" x-cloak class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-emerald-50/30">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase">No. Berkas</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase">Tanggal Bayar</th>
                            <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase">Diserahkan Kepada</th>
                            <th class="px-5 py-4 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($kwitansi as $item)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 font-black text-emerald-700">{{ $item->nomer_berkas }}</td>
                                <td class="px-5 py-4">
                                    <div class="text-sm font-bold text-slate-800">
                                        {{ \Carbon\Carbon::parse($item->sps->tanggal_bayar)->format('d M Y') }}
                                    </div>
                                    <div class="text-xs text-slate-500 font-medium">
                                        Pukul: {{ \Carbon\Carbon::parse($item->sps->tanggal_bayar)->format('H:i') }} WIB
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs"><i class="fa-solid fa-user-check"></i></div>
                                        <div>
                                            <div class="text-sm font-bold text-slate-800 uppercase">{{ $item->sps->penerima_kwitansi }}</div>
                                            <div class="text-[10px] text-slate-500 uppercase">Penerima Kwitansi Fisik</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-700"><i class="fa-solid fa-check mr-1"></i> LUNAS</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-slate-500 italic text-sm">Belum ada riwayat kwitansi pembayaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        <div x-show="modalProsesOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="closeProsesModal()" x-transition.scale class="bg-white rounded-3xl w-full max-w-md shadow-2xl flex flex-col border border-slate-100 overflow-hidden">
                <div class="bg-indigo-600 p-4 text-center relative">
                    <h3 class="font-extrabold text-white">Validasi & Serah Terima</h3>
                    <p class="text-[11px] text-indigo-100">No. <span x-text="selectedNo"></span> - <span x-text="selectedPemohon"></span></p>
                </div>
                
                <form :action="'/bpn/loket-pembayaran/proses/' + selectedId" method="POST">
                    @csrf
                    <div class="p-6 bg-slate-50">
                        <div class="bg-blue-50 border border-blue-200 text-blue-800 text-[11px] p-3 rounded-xl mb-5 font-medium leading-relaxed">
                            <i class="fa-solid fa-info-circle mr-1"></i> <b>Petunjuk:</b> Pastikan uang pembayaran telah diterima lunas sesuai nominal SPS. Inputkan nama orang yang menerima lembar kwitansi fisik di loket saat ini (bisa pemohon langsung, mitra, atau perwakilan).
                        </div>
                        
                        <label class="block text-[11px] font-bold text-slate-600 mb-1.5">NAMA PENERIMA KWITANSI <span class="text-rose-500">*</span></label>
                        <input type="text" name="penerima_kwitansi" required autocomplete="off" 
                               class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 p-2.5 outline-none font-bold uppercase" 
                               placeholder="Nama Orang di Loket...">
                    </div>
                    
                    <div class="p-4 border-t border-slate-100 bg-white flex gap-3">
                        <button type="button" @click="closeProsesModal()" class="w-1/3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition">Batal</button>
                        <button type="submit" class="w-2/3 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition shadow-md">Serahkan Kwitansi</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function pembayaranApp() {
            return {
                activeTab: 'antrean',
                modalProsesOpen: false,
                selectedId: '',
                selectedNo: '',
                selectedPemohon: '',

                openProsesModal(id, no, pemohon) {
                    this.selectedId = id;
                    this.selectedNo = no;
                    this.selectedPemohon = pemohon;
                    this.modalProsesOpen = true;
                },
                closeProsesModal() {
                    this.modalProsesOpen = false;
                }
            }
        }
    </script>
</x-app-layout>