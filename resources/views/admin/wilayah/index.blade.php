<x-app-layout>
    <x-slot name="header">Master Data Wilayah Nganjuk</x-slot>

    <div class="p-4 lg:p-8" x-data="{ activeTab: 'kecamatan' }">
        
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3 animate-pulse-once">
                <i class="fa-solid fa-circle-check text-emerald-500"></i> {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation text-rose-500"></i> Terjadi kesalahan input data.
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200">
            
            <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 flex items-center gap-2"><i class="fa-solid fa-map-location-dot text-blue-600"></i> Pengelolaan Wilayah Teritorial</h3>
            </div>

            <div class="flex border-b border-slate-200 px-6 pt-2 bg-slate-50 gap-6 overflow-x-auto custom-scrollbar">
                <button @click="activeTab = 'kecamatan'" :class="activeTab === 'kecamatan' ? 'border-blue-500 text-blue-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-medium'" class="px-4 py-3 border-b-[3px] transition-all flex items-center gap-2 text-sm whitespace-nowrap outline-none">
                    <i class="fa-solid fa-map"></i> Data Kecamatan
                </button>
                <button @click="activeTab = 'desa'" :class="activeTab === 'desa' ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-medium'" class="px-4 py-3 border-b-[3px] transition-all flex items-center gap-2 text-sm whitespace-nowrap outline-none">
                    <i class="fa-solid fa-location-dot"></i> Data Desa / Kelurahan
                </button>
            </div>
            
            <div x-show="activeTab === 'kecamatan'" x-cloak class="p-6">
                <div class="flex flex-col md:flex-row gap-8">
                    <div class="w-full md:w-1/3">
                        <div class="bg-blue-50/50 border border-blue-100 p-5 rounded-2xl">
                            <h4 class="font-bold text-blue-800 mb-4 text-sm uppercase tracking-wider"><i class="fa-solid fa-plus-circle mr-1"></i> Tambah Kecamatan Baru</h4>
                            <form action="{{ route('admin.wilayah.kecamatan.store') }}" method="POST">
                                @csrf
                                <label class="block text-[11px] font-bold text-slate-600 mb-1.5">NAMA KECAMATAN <span class="text-rose-500">*</span></label>
                                <input type="text" name="nama_kecamatan" required class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-2.5 outline-none mb-4" placeholder="Cth: NGANJUK">
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl transition shadow-md">Simpan Kecamatan</button>
                            </form>
                        </div>
                    </div>
                    <div class="w-full md:w-2/3 border border-slate-100 rounded-2xl overflow-hidden">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Nama Kecamatan</th>
                                    <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Jumlah Desa Terdaftar</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100">
                                @forelse($kecamatans as $kecamatan)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-5 py-3 font-extrabold text-slate-800">{{ $kecamatan->nama_kecamatan }}</td>
                                        <td class="px-5 py-3 text-center">
                                            <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-bold border border-slate-200">{{ $kecamatan->desa_count }} Desa</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="px-5 py-8 text-center text-slate-500">Belum ada data kecamatan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'desa'" x-cloak class="p-6">
                <div class="flex flex-col md:flex-row gap-8">
                    <div class="w-full md:w-1/3">
                        <div class="bg-emerald-50/50 border border-emerald-100 p-5 rounded-2xl">
                            <h4 class="font-bold text-emerald-800 mb-4 text-sm uppercase tracking-wider"><i class="fa-solid fa-plus-circle mr-1"></i> Tambah Desa Baru</h4>
                            <form action="{{ route('admin.wilayah.desa.store') }}" method="POST">
                                @csrf
                                <label class="block text-[11px] font-bold text-slate-600 mb-1.5">INDUK KECAMATAN <span class="text-rose-500">*</span></label>
                                <select name="kecamatan_id" required class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 p-2.5 outline-none mb-3">
                                    <option value="">-- Pilih Kecamatan --</option>
                                    @foreach($kecamatans as $kecamatan)
                                        <option value="{{ $kecamatan->id }}">{{ $kecamatan->nama_kecamatan }}</option>
                                    @endforeach
                                </select>

                                <label class="block text-[11px] font-bold text-slate-600 mb-1.5">NAMA DESA / KEL. <span class="text-rose-500">*</span></label>
                                <input type="text" name="nama_desa" required class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 p-2.5 outline-none mb-4" placeholder="Cth: MANGUNDIKARAN">
                                
                                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl transition shadow-md">Simpan Desa</button>
                            </form>
                        </div>
                    </div>
                    <div class="w-full md:w-2/3 border border-slate-100 rounded-2xl overflow-hidden max-h-[500px] overflow-y-auto custom-scrollbar">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50 sticky top-0">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Nama Desa / Kelurahan</th>
                                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Kecamatan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100">
                                @forelse($desas as $desa)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-5 py-3 font-extrabold text-slate-800">{{ $desa->nama_desa }}</td>
                                        <td class="px-5 py-3 text-xs font-bold text-blue-600">{{ $desa->kecamatan->nama_kecamatan }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="px-5 py-8 text-center text-slate-500">Belum ada data desa.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>