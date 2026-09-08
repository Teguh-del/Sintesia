@extends('layouts.app')

@section('title', 'Marketplace Hasil Pertanian - SINTESA')

@section('content')
<div class="bg-slate-50 min-h-screen pb-16">
    <!-- Marketplace Header Banner -->
    <div class="bg-gradient-to-r from-emerald-900 via-emerald-800 to-teal-900 text-white py-12 px-4 sm:px-6 lg:px-8 border-b border-emerald-950/20 shadow-inner">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-700/60 border border-emerald-500/30 text-emerald-200 text-xs font-semibold mb-3">
                        <i data-lucide="shopping-bag" class="w-3.5 h-3.5"></i>
                        <span>Katalog Niaga Hasil Panen Langsung Petani</span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-white">Marketplace Pertanian</h1>
                    <p class="text-emerald-100/90 text-sm sm:text-base mt-2 max-w-2xl leading-relaxed">
                        Temukan pasokan komoditas pangan segar, harga transparan langsung dari produsen, dan jaminan ketersediaan stok riil.
                    </p>
                </div>

                <!-- Quick Action Buttons for Authenticated Roles -->
                <div class="flex flex-wrap items-center gap-3">
                    @auth
                        @if(auth()->user()->isPetani())
                            <a href="{{ route('farmer.products.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-sm shadow-md transition transform hover:-translate-y-0.5">
                                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                                <span>Jual Produk Baru</span>
                            </a>
                        @endif
                        <a href="{{ auth()->user()->getDashboardRoute() }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-sm transition">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            <span>Dashboard Saya</span>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-white font-bold text-sm shadow-md transition">
                            <i data-lucide="user-plus" class="w-4 h-4"></i>
                            <span>Daftar & Mulai Beli</span>
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Quick Commodity Filter Bar -->
            <div class="mt-8 pt-6 border-t border-emerald-700/40 flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                <a href="{{ route('marketplace.index', array_merge(request()->except('commodity', 'page'))) }}" 
                   class="px-4 py-1.5 rounded-full text-xs font-bold whitespace-nowrap transition {{ !request('commodity') ? 'bg-white text-emerald-950 shadow-sm' : 'bg-emerald-800/60 text-emerald-100 hover:bg-emerald-700/80 border border-emerald-600/40' }}">
                    Semua Komoditas
                </a>
                @foreach($commodities as $com)
                    <a href="{{ route('marketplace.index', array_merge(request()->except('commodity', 'page'), ['commodity' => $com->slug])) }}" 
                       class="px-4 py-1.5 rounded-full text-xs font-bold whitespace-nowrap transition flex items-center gap-1.5 {{ request('commodity') == $com->slug ? 'bg-white text-emerald-950 shadow-sm' : 'bg-emerald-800/60 text-emerald-100 hover:bg-emerald-700/80 border border-emerald-600/40' }}">
                        <span>{{ $com->name }}</span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded-full {{ request('commodity') == $com->slug ? 'bg-emerald-100 text-emerald-900' : 'bg-emerald-900/60 text-emerald-300' }}">
                            {{ $com->products_count ?? 0 }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Main Marketplace Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- Left Sidebar Filter -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sticky top-24">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                        <div class="flex items-center gap-2 text-slate-900 font-bold">
                            <i data-lucide="sliders-horizontal" class="w-5 h-5 text-emerald-600"></i>
                            <span>Filter Pencarian</span>
                        </div>
                        @if(request()->anyFilled(['search', 'commodity', 'location', 'min_price', 'max_price', 'quality', 'stock_status', 'sort']))
                            <a href="{{ route('marketplace.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700 hover:underline">
                                Reset
                            </a>
                        @endif
                    </div>

                    <form action="{{ route('marketplace.index') }}" method="GET" class="space-y-6">
                        <!-- Search Keyword -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kata Kunci</label>
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk, kebun..." 
                                       class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-3"></i>
                            </div>
                        </div>

                        <!-- Commodity Select -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Komoditas</label>
                            <select name="commodity" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                                <option value="">Semua Komoditas</option>
                                @foreach($commodities as $com)
                                    <option value="{{ $com->slug }}" {{ request('commodity') == $com->slug ? 'selected' : '' }}>
                                        {{ $com->name }} ({{ $com->products_count ?? 0 }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Location Select / Search -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Lokasi Pengiriman</label>
                            <div class="relative">
                                <input type="text" name="location" value="{{ request('location') }}" placeholder="Contoh: Malang, Cianjur..." list="location-list"
                                       class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                                <i data-lucide="map-pin" class="w-4 h-4 text-slate-400 absolute left-3 top-3"></i>
                                <datalist id="location-list">
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc }}">
                                    @endforeach
                                </datalist>
                            </div>
                        </div>

                        <!-- Quality Filter -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kualitas Panen</label>
                            <select name="quality" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                                <option value="">Semua Kualitas</option>
                                @foreach($qualityOptions as $qOpt)
                                    <option value="{{ $qOpt }}" {{ request('quality') == $qOpt ? 'selected' : '' }}>
                                        {{ $qOpt }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Rentang Harga (Rp)</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" 
                                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Maks" 
                                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                            </div>
                        </div>

                        <!-- Stock Status -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Status Ketersediaan</label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                                    <input type="radio" name="stock_status" value="" {{ !request('stock_status') ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                    <span>Semua Stok</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                                    <input type="radio" name="stock_status" value="available" {{ request('stock_status') == 'available' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                    <span>Tersedia (&gt; 10 unit)</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                                    <input type="radio" name="stock_status" value="limited" {{ request('stock_status') == 'limited' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                    <span>Stok Terbatas (1 - 10 unit)</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2">
                            <i data-lucide="filter" class="w-4 h-4"></i>
                            <span>Terapkan Filter</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Content: Product Grid -->
            <div class="lg:col-span-3">
                <!-- Sorting & Result Summary Bar -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 mb-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-slate-600">
                        Menampilkan <span class="font-bold text-slate-900">{{ $products->total() }}</span> produk pertanian aktif
                    </div>

                    <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                        <label for="sort-select" class="text-xs font-bold text-slate-500 uppercase whitespace-nowrap">Urutkan:</label>
                        <form id="sort-form" action="{{ route('marketplace.index') }}" method="GET">
                            @foreach(request()->except('sort', 'page') as $key => $val)
                                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                            @endforeach
                            <select id="sort-select" name="sort" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                                <option value="stock_high" {{ request('sort') == 'stock_high' ? 'selected' : '' }}>Stok Terbanyak</option>
                            </select>
                        </form>
                    </div>
                </div>

                <!-- Products Grid -->
                @if($products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            <div class="bg-white rounded-2xl border border-slate-200/80 hover:border-emerald-500/50 shadow-sm hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300 flex flex-col overflow-hidden group">
                                <!-- Product Image Container -->
                                <div class="relative h-48 w-full overflow-hidden bg-slate-100">
                                    <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    
                                    <!-- Commodity Badge -->
                                    <div class="absolute top-3 left-3">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-white/90 backdrop-blur-md text-emerald-800 shadow-sm">
                                            {{ $product->commodity->name ?? 'Komoditas' }}
                                        </span>
                                    </div>

                                    <!-- Stock Status Badge -->
                                    <div class="absolute top-3 right-3">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold border shadow-sm {{ $product->stock_status_color }}">
                                            {{ $product->stock_status }}
                                        </span>
                                    </div>

                                    <!-- Negotiation Badge if enabled -->
                                    @if($product->allow_negotiation)
                                        <div class="absolute bottom-3 right-3">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-500/90 text-slate-950 backdrop-blur-md shadow-sm">
                                                <i data-lucide="handshake" class="w-3 h-3"></i>
                                                <span>Bisa Nego</span>
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Card Body -->
                                <div class="p-5 flex-1 flex flex-col">
                                    <!-- Farmer & Location info -->
                                    <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                                        <div class="flex items-center gap-1 truncate max-w-[60%]">
                                            <i data-lucide="user" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0"></i>
                                            <span class="truncate font-semibold text-slate-700">{{ $product->user->farmerProfile->farm_name ?? $product->user->name }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 truncate text-slate-500">
                                            <i data-lucide="map-pin" class="w-3 h-3 flex-shrink-0"></i>
                                            <span class="truncate">{{ Str::limit($product->location, 18) }}</span>
                                        </div>
                                    </div>

                                    <!-- Product Title -->
                                    <a href="{{ route('marketplace.show', $product->slug) }}" class="block mb-2">
                                        <h3 class="text-base font-bold text-slate-900 group-hover:text-emerald-700 transition line-clamp-1">
                                            {{ $product->name }}
                                        </h3>
                                    </a>

                                    <!-- Quality Grade & Harvest Date -->
                                    <div class="flex flex-wrap items-center gap-2 mb-3 text-xs">
                                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-medium">
                                            {{ $product->quality }}
                                        </span>
                                        @if($product->harvest_date)
                                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-medium flex items-center gap-1">
                                                <i data-lucide="calendar" class="w-3 h-3"></i>
                                                {{ $product->harvest_date->format('d M Y') }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Price & Stock Info -->
                                    <div class="mt-auto pt-3 border-t border-slate-100 flex items-end justify-between">
                                        <div>
                                            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Harga per {{ $product->unit }}</p>
                                            <div class="flex items-baseline gap-1">
                                                <span class="text-lg font-black text-emerald-600">{{ $product->formatted_price }}</span>
                                                <span class="text-xs text-slate-500 font-medium">/ {{ $product->unit }}</span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Sisa Stok</p>
                                            <p class="text-xs font-bold text-slate-700">{{ $product->formatted_stock }}</p>
                                        </div>
                                    </div>

                                    <!-- Action Button -->
                                    <div class="mt-4 pt-2">
                                        <a href="{{ route('marketplace.show', $product->slug) }}" 
                                           class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-emerald-600 text-slate-700 hover:text-white font-bold text-xs flex items-center justify-center gap-2 transition duration-200 group/btn">
                                            <span>Lihat Detail Produk</span>
                                            <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover/btn:translate-x-1 transition-transform"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center shadow-sm">
                        <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="package-search" class="w-8 h-8"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-1">Tidak Ada Produk yang Ditemukan</h3>
                        <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">
                            Tidak ditemukan hasil pertanian yang cocok dengan filter yang Anda tentukan. Cobalah mengatur ulang kata kunci atau filter pencarian Anda.
                        </p>
                        <a href="{{ route('marketplace.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md transition">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            <span>Reset Semua Filter</span>
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
