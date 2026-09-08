@extends('layouts.dashboard')

@section('title', 'Kelola Produk Marketplace - Petani SINTESA')

@section('content')
<div class="space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Manajemen Produk Marketplace</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola katalog komoditas hasil panen yang Anda jual langsung ke pembeli.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('marketplace.index') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs shadow-sm transition">
                <i data-lucide="external-link" class="w-4 h-4 text-emerald-600"></i>
                <span>Lihat Marketplace</span>
            </a>
            <a href="{{ route('farmer.products.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition transform hover:-translate-y-0.5">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Tambah Produk Baru</span>
            </a>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Produk</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $totalProducts }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="package" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Produk Aktif</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $activeProducts }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Stok Tersedia</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalStockKg, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <i data-lucide="layers" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Stok Habis</p>
                <p class="text-2xl font-black text-rose-600 mt-1">{{ $soldOutCount }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Table Container & Filters -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <!-- Filter and Search Bar -->
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <form action="{{ route('farmer.products.index') }}" method="GET" class="w-full sm:w-auto flex-1 flex flex-col sm:flex-row items-center gap-3">
                <div class="relative w-full sm:w-80">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk, grade..." 
                           class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
                </div>

                <select name="status" onchange="this.form.submit()" class="w-full sm:w-auto px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif / Draft</option>
                    <option value="sold_out" {{ request('status') === 'sold_out' ? 'selected' : '' }}>Habis</option>
                </select>

                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('farmer.products.index') }}" class="text-xs font-bold text-slate-500 hover:text-rose-600">
                        Reset Filter
                    </a>
                @endif
            </form>
        </div>

        <!-- Products Table -->
        @if($products->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50/75 border-b border-slate-100 uppercase font-bold text-slate-500 text-[11px] tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5">Produk</th>
                            <th class="px-6 py-3.5">Komoditas & Kualitas</th>
                            <th class="px-6 py-3.5">Harga Satuan</th>
                            <th class="px-6 py-3.5">Stok Tersedia</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($products as $product)
                            <tr class="hover:bg-slate-50/50 transition">
                                <!-- Product Column -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl bg-slate-100 overflow-hidden flex-shrink-0 border border-slate-200">
                                            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <a href="{{ route('marketplace.show', $product->slug) }}" target="_blank" class="font-bold text-slate-900 hover:text-emerald-700 transition">
                                                {{ $product->name }}
                                            </a>
                                            <p class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1">
                                                <i data-lucide="map-pin" class="w-3 h-3"></i>
                                                <span>{{ Str::limit($product->location, 25) }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Commodity & Quality -->
                                <td class="px-6 py-4">
                                    <span class="inline-block font-semibold text-slate-800">{{ $product->commodity->name ?? 'Komoditas' }}</span>
                                    <div class="mt-1">
                                        <span class="px-2 py-0.5 rounded bg-slate-100 text-[10px] font-semibold text-slate-600">
                                            {{ $product->quality }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Price -->
                                <td class="px-6 py-4">
                                    <span class="font-bold text-emerald-600 text-sm">{{ $product->formatted_price }}</span>
                                    <span class="text-slate-400 text-[10px]">/ {{ $product->unit }}</span>
                                </td>

                                <!-- Stock -->
                                <td class="px-6 py-4">
                                    <span class="font-bold text-slate-900">{{ $product->formatted_stock }}</span>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border {{ $product->stock_status_color }}">
                                            {{ $product->stock_status }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Status & Toggle -->
                                <td class="px-6 py-4">
                                    <form action="{{ route('farmer.products.toggle', $product->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" title="Klik untuk mengubah status" 
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold transition {{ $product->status === 'active' ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $product->status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                            <span>{{ $product->status === 'active' ? 'Aktif' : 'Nonaktif' }}</span>
                                        </button>
                                    </form>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('marketplace.show', $product->slug) }}" target="_blank" title="Lihat di Marketplace" 
                                           class="p-2 rounded-lg bg-slate-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 transition">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>

                                        <a href="{{ route('farmer.products.edit', $product->id) }}" title="Edit Produk" 
                                           class="p-2 rounded-lg bg-slate-100 hover:bg-amber-50 text-slate-600 hover:text-amber-700 transition">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>

                                        <form action="{{ route('farmer.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus Produk" class="p-2 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-700 transition">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-100">
                {{ $products->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="package-plus" class="w-8 h-8"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Belum Ada Produk yang Dijual</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mb-6">
                    Mulai pasarkan hasil panen pertanian Anda ke pasar digital agar dapat dilihat dan dibeli oleh ribuan Pengepul dan Konsumen.
                </p>
                <a href="{{ route('farmer.products.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Tambah Produk Pertama Anda</span>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
