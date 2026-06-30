<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ruang Kerja Terpadu Aset Wilayah') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
                    <form method="GET" action="{{ route('ruang-kerja') }}" class="flex w-full md:w-auto gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari nomor berkas atau nama pemohon..." 
                               class="w-full md:w-80 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md text-sm transition shadow">
                            Cari
                        </button>
                    </form>
                    <div class="text-sm text-gray-600">
                        Login sebagai: <span class="font-bold uppercase text-blue-600">{{ auth()->user()->jabatan }}</span>
                    </div>
                </div>

                <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">No. Berkas</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Pemohon / Instansi</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Jenis Hak</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Letak Wilayah</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Status saat ini</th>
                                <th class="px-6 py-3 text-center font-semibold text-gray-600 uppercase tracking-wider">Aksi Operasional</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($berkas as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $item->nomor_berkas }}</td>
                                    <td class="px-6 py-4 text-gray-700">{{ $item->nama_pemohon }}</td>
                                    <td class="px-6 py-4 text-gray-600 whitespace-nowrap">{{ $item->jenisHak?->nama ?? '-' }}</td>
                                    <td class="px-6 py-4 text-gray-600">
                                        Kec. {{ $item->kecamatan?->nama ?? '-' }}, Desa {{ $item->desa?->nama ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ str_replace('_', ' ', $item->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="{{ route('berkas.show', $item->id) }}" class="text-blue-600 hover:text-blue-900 font-semibold">Detail</a>

                                            @if(auth()->user()->jabatan === 'loket')
                                                @if($item->status === 'pendaftaran')
                                                    <form action="{{ route('loket.verifikasi', $item->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-900 font-semibold" onclick="return confirm('Verifikasi berkas ini?')">
                                                            Terima & Verifikasi
                                                        </button>
                                                    </form>
                                                @endif

                                            @elseif(auth()->user()->jabatan === 'pelaksana')
                                                <a href="{{ route('plotting', ['berkas_id' => $item->id]) }}" class="text-purple-600 hover:text-purple-900 font-semibold">
                                                    Plotting Peta
                                                </a>
                                                <form action="{{ route('pelaksana.proses', $item->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                                        Selesaikan Tugas
                                                    </button>
                                                </form>

                                            @elseif(auth()->user()->jabatan === 'backoffice')
                                                <form action="{{ route('backoffice.approve', $item->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-emerald-600 hover:text-emerald-900 font-semibold" onclick="return confirm('Setujui dan terbitkan sertipikat aset?')">
                                                        Approve & Terbitkan
                                                    </button>
                                                </form>

                                            @elseif(auth()->user()->jabatan === 'mitra')
                                                @if($item->status === 'perbaikan')
                                                    <a href="{{ route('mitra.berkas.edit', $item->id) }}" class="text-amber-600 hover:text-amber-900 font-semibold">
                                                        Perbaiki Dokumen
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-gray-500 text-center font-medium">
                                        Tidak ada data aset/berkas yang membutuhkan penanganan Anda saat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $berkas->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>