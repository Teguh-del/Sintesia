@extends('layouts.dashboard')

@section('title', 'Manajemen Stok Riil - Petani SINTESA')

@section('content')
<div class="space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Manajemen Stok Riil</h1>
            <p class="text-sm text-slate-500 mt-0.5">Pantau ketersediaan pasokan komoditas per batch hasil panen secara akurat tanpa risiko stok negatif.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('farmer.harvests.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs shadow-sm transition">
                <i data-lucide="plus" class="w-4 h-4 text-emerald-600"></i>
                <span>Catat Panen Baru</span>
            </a>
            <a href="{{ route('farmer.products.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition">
                <i data-lucide="store" class="w-4 h-4"></i>
                <span>Kelola Produk Marketplace</span>
            </a>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Stok Tersedia</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($totalAvailable, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sedang Dipesan</p>
                <p class="text-2xl font-black text-amber-600 mt-1">{{ number_format($totalOrdered, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <i data-lucide="clock" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Terjual</p>
                <p class="text-2xl font-black text-blue-600 mt-1">{{ number_format($totalSold, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <i data-lucide="shopping-bag" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Stok Menipis / Habis</p>
                <p class="text-2xl font-black text-rose-600 mt-1">{{ $lowStockCount }} <span class="text-xs font-semibold text-slate-400">batch</span></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Table Container & Filters -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <!-- Filter Form -->
        <div class="p-4 sm:p-5 border-b border-slate-100">
            <form action="{{ route('farmer.stocks.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Search Keyword -->
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode batch, mutu..." 
                           class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
                </div>

                <!-- Commodity Select -->
                <div>
                    <select name="commodity_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="">Semua Komoditas</option>
                        @foreach($commodities as $com)
                            <option value="{{ $com->id }}" {{ request('commodity_id') == $com->id ? 'selected' : '' }}>
                                {{ $com->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Select -->
                <div>
                    <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="">Semua Status Stok</option>
                        @foreach($statusOptions as $sOpt)
                            <option value="{{ $sOpt }}" {{ request('status') == $sOpt ? 'selected' : '' }}>
                                {{ $sOpt }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition">
                        Filter
                    </button>
                    @if(request()->anyFilled(['search', 'commodity_id', 'status']))
                        <a href="{{ route('farmer.stocks.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition" title="Reset Filter">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Stocks Table -->
        @if($stocks->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50/75 border-b border-slate-100 uppercase font-bold text-slate-500 text-[11px] tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5">Batch & Komoditas</th>
                            <th class="px-6 py-3.5">Kualitas Mutu</th>
                            <th class="px-6 py-3.5">Rincian Stok (Tersedia / Pesan / Terjual)</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5">Katalog Marketplace</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($stocks as $stock)
                            <tr class="hover:bg-slate-50/50 transition">
                                <!-- Batch & Commodity -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center flex-shrink-0">
                                            <i data-lucide="boxes" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('farmer.stocks.show', $stock->id) }}" class="font-bold text-slate-900 hover:text-emerald-700 transition">
                                                {{ $stock->batch_code }}
                                            </a>
                                            <p class="text-[11px] font-semibold text-slate-500 mt-0.5">
                                                {{ $stock->commodity->name ?? 'Komoditas' }}
                                                @if($stock->harvest)
                                                    <span class="text-slate-400 font-normal">• Panen: {{ $stock->harvest->harvest_date->format('d/m/Y') }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Quality -->
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-semibold text-[11px]">
                                        {{ $stock->quality }}
                                    </span>
                                </td>

                                <!-- Quantities breakdown -->
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        <div class="flex items-baseline gap-2">
                                            <span class="font-black text-emerald-600 text-sm">{{ $stock->formatted_available }}</span>
                                            <span class="text-[10px] uppercase font-bold text-slate-400">tersedia</span>
                                        </div>
                                        <div class="flex items-center gap-3 text-[11px] text-slate-500">
                                            <span>Dipesan: <strong class="text-amber-600">{{ $stock->formatted_ordered }}</strong></span>
                                            <span>•</span>
                                            <span>Terjual: <strong class="text-blue-600">{{ $stock->formatted_sold }}</strong></span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Status Badge -->
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $stock->status_badge_color }}">
                                        {{ $stock->status }}
                                    </span>
                                </td>

                                <!-- Linked Marketplace Product -->
                                <td class="px-6 py-4">
                                    @if($stock->products->count() > 0)
                                        <div class="space-y-1">
                                            @foreach($stock->products as $p)
                                                <a href="{{ route('marketplace.show', $p->slug) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 hover:text-emerald-800">
                                                    <i data-lucide="store" class="w-3 h-3"></i>
                                                    <span class="truncate max-w-[140px]">{{ $p->name }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        @if($stock->available_quantity > 0)
                                            <a href="{{ route('farmer.products.create', ['stock_id' => $stock->id, 'commodity_id' => $stock->commodity_id, 'quality' => $stock->quality, 'stock' => $stock->available_quantity, 'unit' => $stock->unit]) }}" 
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-[11px] shadow-sm transition">
                                                <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                                                <span>Jual ke Marketplace</span>
                                            </a>
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">Stok Habis</span>
                                        @endif
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('farmer.stocks.show', $stock->id) }}" title="Lihat Detail Batch" 
                                           class="p-2 rounded-lg bg-slate-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 transition">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>

                                        <button type="button" onclick="openAdjustModal('{{ $stock->id }}', '{{ $stock->batch_code }}', '{{ (float)$stock->available_quantity }}', '{{ $stock->unit }}')" 
                                                title="Sesuaikan Kuantitas Fisik" 
                                                class="p-2 rounded-lg bg-slate-100 hover:bg-amber-50 text-slate-600 hover:text-amber-700 transition">
                                            <i data-lucide="sliders" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-100">
                {{ $stocks->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="boxes" class="w-8 h-8"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Belum Ada Inventaris Stok</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mb-6">
                    Stok riil diterbitkan secara otomatis dari pencatatan hasil panen. Mulai catat hasil panen kebun Anda.
                </p>
                <a href="{{ route('farmer.harvests.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Catat Panen Sekarang</span>
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Modal Penyesuaian Stok Fisik -->
<div id="adjust-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                <i data-lucide="sliders" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900">Penyesuaian Stok Fisik</h3>
                <p id="modal-batch-code" class="text-xs text-slate-500">Batch: -</p>
            </div>
        </div>

        <form id="adjust-form" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label for="modal-qty-input" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Kuantitas Stok Tersedia Baru (<span id="modal-unit-label">kg</span>) <span class="text-rose-500">*</span>
                </label>
                <input type="number" id="modal-qty-input" name="available_quantity" required min="0" step="any" 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div>
                <label for="modal-reason-input" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                    Alasan Penyesuaian (Audit / Penyusutan / Sortasi)
                </label>
                <input type="text" id="modal-reason-input" name="reason" placeholder="Contoh: Penyusutan alami pasca simpan..." 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div class="p-3 bg-amber-50 rounded-xl text-[11px] text-amber-800 border border-amber-200">
                <strong>Catatan Integritas:</strong> Stok tidak boleh bernilai negatif. Perubahan akan langsung disinkronkan ke status ketersediaan komoditas.
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeAdjustModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition">
                    Simpan Penyesuaian
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAdjustModal(stockId, batchCode, currentQty, unit) {
        const form = document.getElementById('adjust-form');
        form.action = `/farmer/stocks/${stockId}/adjust`;
        document.getElementById('modal-batch-code').textContent = 'Batch: ' + batchCode;
        document.getElementById('modal-qty-input').value = currentQty;
        document.getElementById('modal-unit-label').textContent = unit;
        document.getElementById('adjust-modal').classList.remove('hidden');
    }

    function closeAdjustModal() {
        document.getElementById('adjust-modal').classList.add('hidden');
    }
</script>
@endsection
