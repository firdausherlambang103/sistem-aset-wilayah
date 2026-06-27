<x-app-layout>
    <x-slot name="header">Loket Pembayaran & Validasi</x-slot>

    <div class="p-6">
        <table class="min-w-full bg-white border border-slate-200 rounded-2xl">
            <thead>
                <tr class="bg-slate-50">
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">No. Berkas</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">Pemohon</th>
                    <th class="px-6 py-4 text-center text-xs font-bold uppercase text-slate-500">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($berkasSPS as $item)
                <tr class="border-t">
                    <td class="px-6 py-4 font-black text-blue-700">{{ $item->nomer_berkas }}</td>
                    <td class="px-6 py-4">{{ $item->nama_pemohon }}</td>
                    <td class="px-6 py-4 text-center">
                        <button onclick="openBayarModal({{ $item->id }})" class="bg-emerald-500 text-white px-4 py-2 rounded-lg text-xs font-bold">Validasi Bayar</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div id="modalBayar" class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center">
        <div class="bg-white p-6 rounded-2xl w-96">
            <h3 class="font-bold mb-4">Input Data Kwitansi</h3>
            <form id="formBayar" method="POST">
                @csrf
                <input type="text" name="penerima_kwitansi" class="w-full border p-2 rounded-lg mb-4" placeholder="Nama Penerima Kwitansi" required>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-bold">Simpan & Validasi</button>
            </form>
        </div>
    </div>
</x-app-layout>