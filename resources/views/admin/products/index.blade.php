@extends('layouts.dashboard')

@section('title', 'Moderasi Produk Marketplace')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-stone-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-bold text-stone-900 tracking-tight">Moderasi Produk Marketplace</h1>
            <p class="text-sm text-stone-500 mt-1">Pantau, audit kelayakan, dan moderasi penayangan produk hasil tani dari mitra Petani.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <i data-lucide="package" class="w-3.5 h-3.5 mr-1.5"></i> Total: {{ $products->total() }} Produk
            </span>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3">
        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Filter & Search Bar -->
    <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
        <form action="{{ route('admin.products.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-6 relative">
                <i data-lucide="search" class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul produk, lokasi, atau nama petani..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-stone-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
            </div>
            <div class="md:col-span-3">
                <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                    <option value="">Semua Status Moderasi</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif (Tayang)</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif (Ditangguhkan)</option>
                    <option value="sold_out" {{ request('status') == 'sold_out' ? 'selected' : '' }}>Stok Habis</option>
                </select>
            </div>
            <div class="md:col-span-3 flex gap-2">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold py-2.5 px-4 rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'commodity_id']))
                <a href="{{ route('admin.products.index') }}" class="px-3.5 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-600 rounded-xl transition flex items-center justify-center">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200 text-xs font-semibold text-stone-600 uppercase tracking-wider">
                        <th class="py-3.5 px-4">Produk</th>
                        <th class="py-3.5 px-4">Petani Produsen</th>
                        <th class="py-3.5 px-4">Harga & Satuan</th>
                        <th class="py-3.5 px-4">Kapasitas Stok</th>
                        <th class="py-3.5 px-4">Status Moderasi</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-sm">
                    @forelse($products as $product)
                    <tr class="hover:bg-stone-50/70 transition">
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-3">
                                @if($product->primaryImage)
                                    <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded-xl border border-stone-200">
                                @elseif($product->images->isNotEmpty())
                                    <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded-xl border border-stone-200">
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center shrink-0">
                                        <i data-lucide="sprout" class="w-6 h-6"></i>
                                    </div>
                                @endif
                                <div>
                                    <a href="{{ route('marketplace.show', $product->slug ?? $product->id) }}" target="_blank" class="font-bold text-stone-900 hover:text-emerald-600 transition line-clamp-1">
                                        {{ $product->name }}
                                    </a>
                                    <div class="text-xs text-stone-500 mt-0.5 flex items-center gap-1.5">
                                        <span class="px-1.5 py-0.5 rounded bg-stone-100 text-stone-600 font-medium">{{ $product->commodity->name ?? 'Komoditas' }}</span>
                                        <span>•</span>
                                        <span>Kualitas {{ $product->quality ?? 'Standar' }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-medium text-stone-900">{{ $product->farmer->name ?? ($product->user->name ?? 'Petani Anonim') }}</div>
                            <div class="text-xs text-stone-400">{{ $product->location ?? ($product->farmer->address ?? '-') }}</div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-bold text-emerald-700">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                            <div class="text-xs text-stone-400">per {{ $product->unit ?? 'kg' }}</div>
                        </td>
                        <td class="py-4 px-4">
                            @if($product->stock <= 0)
                                <span class="text-rose-600 font-bold text-xs bg-rose-50 px-2 py-1 rounded-lg border border-rose-200">Habis</span>
                            @else
                                <span class="font-bold text-stone-800">{{ number_format($product->stock) }}</span>
                                <span class="text-xs text-stone-500">{{ $product->unit ?? 'kg' }}</span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            @if($product->status === 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Tayang Aktif
                                </span>
                            @elseif($product->status === 'sold_out')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span> Stok Habis
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span> Nonaktif / Ditangguhkan
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('marketplace.show', $product->slug ?? $product->id) }}" target="_blank" class="p-1.5 text-stone-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Lihat di Marketplace">
                                    <i data-lucide="external-link" class="w-4 h-4"></i>
                                </a>

                                <form action="{{ route('admin.products.toggle-status', $product->id) }}" method="POST" onsubmit="return confirm('Ubah status moderasi produk ini?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-1.5 {{ $product->status === 'active' ? 'text-rose-600 hover:bg-rose-50' : 'text-emerald-600 hover:bg-emerald-50' }} rounded-lg transition" title="{{ $product->status === 'active' ? 'Tangguhkan Produk' : 'Aktifkan Produk' }}">
                                        <i data-lucide="{{ $product->status === 'active' ? 'ban' : 'check-circle' }}" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-stone-500">
                            <i data-lucide="package-open" class="w-12 h-12 mx-auto text-stone-300 mb-3"></i>
                            <p class="font-medium text-stone-700">Tidak ada produk ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="p-4 border-t border-stone-200">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
