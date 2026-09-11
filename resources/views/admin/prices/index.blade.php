@extends('layouts.dashboard')

@section('title', 'Kelola Harga Komoditas Pasar — Admin SINTESA')

@section('content')
<div class="space-y-6">
    <!-- Header Area -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold mb-2">
                <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                <span>Admin Panel — Manajemen Referensi Pasar</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Kelola Data Harga Komoditas</h1>
            <p class="text-xs text-slate-500 mt-0.5">Catat dan perbarui harga pasar acuan daerah untuk transparansi data niaga SINTESA</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('prices.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5">
                <i data-lucide="line-chart" class="w-4 h-4"></i>
                <span>Lihat Dashboard Tren</span>
            </a>
            <button onclick="document.getElementById('create-modal').classList.remove('hidden')" 
                    class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Catat Harga Baru</span>
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.prices.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Filter Komoditas</label>
                <select name="commodity_id" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">Semua Komoditas</option>
                    @foreach($commodities as $c)
                        <option value="{{ $c->id }}" {{ $selectedCommodity == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Lokasi / Pasar</label>
                <input type="text" name="location" value="{{ $selectedLocation }}" placeholder="Cari wilayah/pasar..." 
                       class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition">
                    Filter
                </button>
                <a href="{{ route('admin.prices.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table of Recorded Prices -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Daftar Rekaman Harga Pasar</h3>
            <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-lg">
                Total {{ $prices->total() }} Catatan
            </span>
        </div>

        @if($prices->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-slate-400 font-bold uppercase tracking-wider">
                            <th class="py-3 px-4">Tanggal Catat</th>
                            <th class="py-3 px-4">Komoditas</th>
                            <th class="py-3 px-4">Harga Acuan</th>
                            <th class="py-3 px-4">Wilayah / Pasar</th>
                            <th class="py-3 px-4">Sumber Referensi</th>
                            <th class="py-3 px-4">Catatan</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($prices as $price)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ \Carbon\Carbon::parse($price->recorded_date)->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-800">
                                {{ $price->commodity->name ?? 'Komoditas' }}
                            </td>
                            <td class="py-3.5 px-4 font-black text-emerald-700">
                                Rp {{ number_format($price->price, 0, ',', '.') }} / {{ $price->unit }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $price->location }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                    {{ $price->source ?? 'Survei Pasar' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-400 max-w-xs truncate">
                                {{ $price->notes ?: '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <form action="{{ route('admin.prices.destroy', $price) }}" method="POST" onsubmit="return confirm('Hapus catatan harga ini?')" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50 transition" title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $prices->links() }}
            </div>
        @else
            <div class="py-12 text-center text-slate-400">
                <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                <p class="text-xs">Belum ada catatan harga yang sesuai.</p>
            </div>
        @endif
    </div>
</div>

<!-- Create Modal -->
<div id="create-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-md w-full p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Catat Harga Acuan Komoditas</h3>
            <button onclick="document.getElementById('create-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('admin.prices.store') }}" method="POST" class="space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Komoditas <span class="text-rose-500">*</span></label>
                <select name="commodity_id" required class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    @foreach($commodities as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->unit }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Harga Acuan (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" min="100" name="price" required placeholder="Contoh: 28000" 
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Satuan <span class="text-rose-500">*</span></label>
                    <input type="text" name="unit" value="kg" required 
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Wilayah / Pasar <span class="text-rose-500">*</span></label>
                <input type="text" name="location" required placeholder="Contoh: Pasar Beringharjo, Sleman" 
                       class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Catat <span class="text-rose-500">*</span></label>
                    <input type="date" name="recorded_date" value="{{ date('Y-m-d') }}" required 
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Sumber Referensi</label>
                    <input type="text" name="source" value="Survei Pasar SINTESA" 
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan</label>
                <textarea name="notes" rows="2" placeholder="Kondisi pasokan, cuaca, atau keterangan lainnya..." 
                          class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('create-modal').classList.add('hidden')" 
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md transition">
                    Simpan Catatan Harga
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
