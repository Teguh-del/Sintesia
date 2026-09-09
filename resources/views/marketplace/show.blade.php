@extends('layouts.app')

@section('title', $product->name . ' - Marketplace SINTESA')

@section('content')
<div class="bg-slate-50 min-h-screen pb-16">
    <!-- Breadcrumbs -->
    <div class="bg-white border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-emerald-600 transition">Beranda</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <a href="{{ route('marketplace.index') }}" class="hover:text-emerald-600 transition">Marketplace</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <a href="{{ route('marketplace.index', ['commodity' => $product->commodity->slug ?? '']) }}" class="hover:text-emerald-600 transition">
                    {{ $product->commodity->name ?? 'Komoditas' }}
                </a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-slate-900 truncate max-w-xs">{{ $product->name }}</span>
            </nav>
        </div>
    </div>

    <!-- Main Detail Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left Side: Image Gallery & Description (7 Cols) -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Gallery Container -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm">
                    <!-- Main Display Image -->
                    <div class="relative w-full h-80 sm:h-96 rounded-xl overflow-hidden bg-slate-100 mb-4">
                        <img id="main-product-image" src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" 
                             class="w-full h-full object-cover transition duration-300">
                        
                        <!-- Badges -->
                        <div class="absolute top-4 left-4 flex flex-wrap gap-2">
                            <span class="px-3 py-1 rounded-lg text-xs font-bold bg-white/95 backdrop-blur-md text-emerald-800 shadow-sm">
                                {{ $product->commodity->name ?? 'Komoditas' }}
                            </span>
                            <span class="px-3 py-1 rounded-lg text-xs font-bold border shadow-sm {{ $product->stock_status_color }}">
                                {{ $product->stock_status }}
                            </span>
                        </div>

                        @if($product->allow_negotiation)
                            <div class="absolute bottom-4 left-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-amber-500 text-slate-950 shadow-md">
                                    <i data-lucide="handshake" class="w-4 h-4"></i>
                                    <span>Menerima Penawaran Harga (Nego)</span>
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Thumbnails Strip -->
                    @if($product->images->count() > 1)
                        <div class="flex items-center gap-3 overflow-x-auto pb-2">
                            @foreach($product->images as $img)
                                <button type="button" onclick="switchProductImage('{{ Str::startsWith($img->image_path, ['http://', 'https://']) ? $img->image_path : asset('storage/' . $img->image_path) }}', this)" 
                                        class="w-20 h-20 rounded-xl overflow-hidden border-2 {{ $loop->first ? 'border-emerald-600 ring-2 ring-emerald-500/20' : 'border-transparent hover:border-slate-300' }} flex-shrink-0 transition thumbnail-btn">
                                    <img src="{{ Str::startsWith($img->image_path, ['http://', 'https://']) ? $img->image_path : asset('storage/' . $img->image_path) }}" 
                                         alt="Thumbnail" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Product Description & Story -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <i data-lucide="file-text" class="w-5 h-5 text-emerald-600"></i>
                        <span>Deskripsi & Informasi Panen</span>
                    </h2>
                    <div class="text-slate-600 text-sm leading-relaxed space-y-4">
                        @if($product->description)
                            <p class="whitespace-pre-line">{{ $product->description }}</p>
                        @else
                            <p class="text-slate-400 italic">Petani belum menyertakan deskripsi tambahan untuk produk ini.</p>
                        @endif
                    </div>

                    <!-- Specifications Table -->
                    <div class="mt-8 pt-6 border-t border-slate-100">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Spesifikasi Komoditas</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                <p class="text-xs text-slate-400 font-semibold">Kualitas / Grade</p>
                                <p class="text-sm font-bold text-slate-800 mt-0.5">{{ $product->quality }}</p>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                <p class="text-xs text-slate-400 font-semibold">Tanggal Panen</p>
                                <p class="text-sm font-bold text-slate-800 mt-0.5">
                                    {{ $product->harvest_date ? $product->harvest_date->translatedFormat('d F Y') : 'Panen Segar Berkelanjutan' }}
                                </p>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                <p class="text-xs text-slate-400 font-semibold">Minimal Pembelian</p>
                                <p class="text-sm font-bold text-slate-800 mt-0.5">{{ number_format($product->min_order, 0) }} {{ $product->unit }}</p>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                <p class="text-xs text-slate-400 font-semibold">Lokasi Pengiriman / Kebun</p>
                                <p class="text-sm font-bold text-slate-800 mt-0.5 flex items-center gap-1">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0"></i>
                                    <span>{{ $product->location }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Farmer Profile Card -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <i data-lucide="user-check" class="w-5 h-5 text-emerald-600"></i>
                            <span>Profil Petani Produsen</span>
                        </h2>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                            <span>Terverifikasi SINTESA</span>
                        </span>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 pt-2">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white font-bold text-xl shadow-md">
                            {{ substr($product->user->name, 0, 2) }}
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-slate-900">{{ $product->user->name }}</h3>
                            <p class="text-xs font-semibold text-emerald-700">
                                {{ $product->user->farmerProfile->farm_name ?? 'Kelompok Tani SINTESA' }}
                            </p>
                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>{{ $product->user->farmerProfile->address ?? $product->location }}</span>
                            </p>
                        </div>
                        @if($product->user->phone)
                            <div>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $product->user->phone) }}" target="_blank" 
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold border border-emerald-200 transition">
                                    <i data-lucide="message-circle" class="w-4 h-4 text-emerald-600"></i>
                                    <span>Hubungi Petani</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Side: Order Calculator & Sticky CTAs (5 Cols) -->
            <div class="lg:col-span-5">
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm sticky top-24 space-y-6">
                    
                    <!-- Header Info -->
                    <div>
                        <div class="flex items-center gap-2 text-xs font-semibold text-emerald-700 mb-2">
                            <span>{{ $product->commodity->name ?? 'Komoditas' }}</span>
                            <span>•</span>
                            <span>{{ $product->quality }}</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 leading-tight">
                            {{ $product->name }}
                        </h1>
                    </div>

                    <!-- Price Box -->
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50/50 p-4 rounded-xl border border-emerald-100">
                        <p class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Harga Satuan</p>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-3xl font-black text-emerald-700">{{ $product->formatted_price }}</span>
                            <span class="text-sm font-semibold text-slate-500">/ {{ $product->unit }}</span>
                        </div>
                        <div class="mt-2 pt-2 border-t border-emerald-200/40 flex items-center justify-between text-xs text-slate-600">
                            <span>Ketersediaan Stok:</span>
                            <span class="font-bold text-slate-900">{{ $product->formatted_stock }}</span>
                        </div>
                    </div>

                    <!-- Interactive Quantity & Subtotal Calculator -->
                    <div class="space-y-4 pt-2">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="calc-quantity" class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Tentukan Jumlah Pembelian ({{ $product->unit }})
                                </label>
                                <span class="text-xs text-slate-500 font-medium">Min: {{ number_format($product->min_order, 0) }} {{ $product->unit }}</span>
                            </div>

                            <div class="flex items-center gap-3">
                                <button type="button" onclick="adjustQuantity(-1)" 
                                        class="w-11 h-11 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center transition focus:outline-none">
                                    <i data-lucide="minus" class="w-4 h-4"></i>
                                </button>
                                
                                <input type="number" id="calc-quantity" 
                                       value="{{ max(1, (int)$product->min_order) }}" 
                                       min="{{ $product->min_order }}" 
                                       max="{{ $product->stock }}" 
                                       step="1"
                                       oninput="recalculateSubtotal()"
                                       class="flex-1 text-center font-bold text-lg py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                                
                                <button type="button" onclick="adjustQuantity(1)" 
                                        class="w-11 h-11 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center transition focus:outline-none">
                                    <i data-lucide="plus" class="w-4 h-4"></i>
                                </button>
                            </div>

                            <p id="calc-error-msg" class="text-xs font-semibold text-rose-600 mt-2 hidden"></p>
                        </div>

                        <!-- Subtotal Preview Box -->
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 flex items-center justify-between">
                            <div>
                                <p class="text-xs text-slate-500 font-semibold">Estimasi Subtotal</p>
                                <p class="text-[11px] text-slate-400">Belum termasuk ongkir</p>
                            </div>
                            <div class="text-right">
                                <span id="calc-subtotal-display" class="text-xl font-black text-slate-900">
                                    Rp {{ number_format($product->price * max(1, (int)$product->min_order), 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons Based on Role -->
                    <div class="space-y-3 pt-4 border-t border-slate-100">
                        @auth
                            @if(auth()->id() === $product->user_id)
                                <!-- Farmer Owner Actions -->
                                <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-800 flex items-center gap-2 mb-2">
                                    <i data-lucide="info" class="w-4 h-4 flex-shrink-0"></i>
                                    <span>Ini adalah produk yang Anda jual di marketplace.</span>
                                </div>
                                <a href="{{ route('farmer.products.edit', $product->id) }}" 
                                   class="w-full py-3.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-sm shadow-md transition flex items-center justify-center gap-2">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                    <span>Edit Produk Ini</span>
                                </a>
                                <a href="{{ route('farmer.products.index') }}" 
                                   class="w-full py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition flex items-center justify-center gap-2">
                                    <span>Kembali ke Manajemen Produk</span>
                                </a>
                            @else
                                <!-- Buyer (Pengepul / Konsumen) Actions -->
                                @if($product->stock > 0 && $product->status === 'active')
                                    <button type="button" onclick="openOrderModal()" 
                                            class="w-full py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-lg shadow-emerald-600/20 transition flex items-center justify-center gap-2 transform hover:-translate-y-0.5">
                                        <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                                        <span>Beli Langsung Sekarang</span>
                                    </button>

                                    @if($product->allow_negotiation)
                                        <button type="button" onclick="openNegotiationModal()" 
                                                class="w-full py-3 rounded-xl bg-white hover:bg-amber-50 text-amber-900 font-bold text-xs border-2 border-amber-400 hover:border-amber-500 transition flex items-center justify-center gap-2 shadow-sm">
                                            <i data-lucide="handshake" class="w-4 h-4 text-amber-600"></i>
                                            <span>Ajukan Penawaran Harga (Nego)</span>
                                        </button>
                                    @endif
                                @else
                                    <button disabled class="w-full py-3.5 rounded-xl bg-slate-200 text-slate-500 font-bold text-sm cursor-not-allowed flex items-center justify-center gap-2">
                                        <i data-lucide="ban" class="w-4 h-4"></i>
                                        <span>Stok Saat Ini Habis</span>
                                    </button>
                                @endif
                            @endif
                        @else
                            <!-- Guest Prompt -->
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-center space-y-3">
                                <p class="text-xs text-slate-600 font-medium">
                                    Masuk sebagai <strong class="text-slate-900">Pengepul</strong> atau <strong class="text-slate-900">Konsumen</strong> untuk melakukan pembelian langsung atau negosiasi harga.
                                </p>
                                <div class="flex gap-2">
                                    <a href="{{ route('login') }}" class="flex-1 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition">
                                        Masuk
                                    </a>
                                    <a href="{{ route('register') }}" class="flex-1 py-2.5 rounded-xl bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 font-bold text-xs transition">
                                        Daftar Akun
                                    </a>
                                </div>
                            </div>
                        @endauth
                    </div>

                    <!-- Trust & Guarantee Guarantee -->
                    <div class="pt-4 border-t border-slate-100 space-y-2 text-xs text-slate-500">
                        <div class="flex items-center gap-2">
                            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                            <span>Jaminan stok riil dari kebun terdata</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="shield" class="w-4 h-4 text-emerald-600"></i>
                            <span>Transaksi aman & transparan</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Related Products in Same Commodity -->
        @if($relatedProducts->count() > 0)
            <div class="mt-16 pt-12 border-t border-slate-200">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">Produk Serupa Lainnya</h2>
                        <p class="text-sm text-slate-500 mt-1">Komoditas {{ $product->commodity->name ?? '' }} dari petani lainnya</p>
                    </div>
                    <a href="{{ route('marketplace.index', ['commodity' => $product->commodity->slug ?? '']) }}" 
                       class="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                        <span>Lihat Semua</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $rel)
                        <div class="bg-white rounded-2xl border border-slate-200 hover:border-emerald-500/50 shadow-sm hover:shadow-md transition flex flex-col overflow-hidden group">
                            <div class="h-40 w-full overflow-hidden bg-slate-100">
                                <img src="{{ $rel->primary_image_url }}" alt="{{ $rel->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>
                            <div class="p-4 flex-1 flex flex-col">
                                <span class="text-[10px] font-bold text-emerald-700 uppercase">{{ $rel->commodity->name ?? '' }}</span>
                                <a href="{{ route('marketplace.show', $rel->slug) }}" class="text-sm font-bold text-slate-900 hover:text-emerald-700 transition line-clamp-1 mt-1">
                                    {{ $rel->name }}
                                </a>
                                <p class="text-xs text-slate-500 mt-1">{{ Str::limit($rel->location, 20) }}</p>
                                <div class="mt-auto pt-3 border-t border-slate-100 flex items-baseline justify-between">
                                    <span class="text-sm font-black text-emerald-600">{{ $rel->formatted_price }}</span>
                                    <span class="text-[11px] text-slate-400">/ {{ $rel->unit }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Beli Langsung (Phase 4 Real Checkout) -->
<div id="order-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-6 my-8">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="shopping-cart" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Konfirmasi Pemesanan Komoditas</h3>
                    <p class="text-xs text-slate-500">Transaksi langsung dengan petani terdaftar SINTESA</p>
                </div>
            </div>
            <button type="button" onclick="closeOrderModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('orders.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">

            <!-- Product Summary -->
            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80 flex items-center gap-3">
                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="w-14 h-14 rounded-xl object-cover border border-slate-200">
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-bold text-slate-900 truncate">{{ $product->name }}</h4>
                    <p class="text-xs text-slate-500">Petani: <span class="font-semibold text-slate-700">{{ $product->user->name }}</span></p>
                    <p class="text-xs font-bold text-emerald-600">{{ $product->formatted_price }} <span class="text-[10px] text-slate-400 font-normal">/ {{ $product->unit }}</span></p>
                </div>
            </div>

            <!-- Quantity Input -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Jumlah Pesanan ({{ $product->unit }}) <span class="text-rose-500">*</span>
                </label>
                <div class="flex items-center gap-2">
                    <input type="number" id="form-order-qty" name="quantity" 
                           step="any"
                           min="{{ $product->min_order }}" 
                           max="{{ $product->stock }}"
                           value="{{ max(1, $product->min_order) }}"
                           oninput="updateModalSubtotal()"
                           required
                           class="flex-1 px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <span class="text-xs font-semibold text-slate-500">{{ $product->unit }}</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">
                    Min: {{ number_format($product->min_order, 0, ',', '.') }} {{ $product->unit }} | Tersedia: {{ number_format($product->stock, 0, ',', '.') }} {{ $product->unit }}
                </p>
            </div>

            <!-- Shipping Method -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Metode Pengambilan / Pengiriman <span class="text-rose-500">*</span>
                </label>
                <select name="shipping_method" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="Ambil di Lokasi Petani">Ambil Sendiri di Lokasi Petani ({{ $product->location }})</option>
                    <option value="Pengiriman / Kurir">Kirim ke Alamat Pembeli (Kurir / Ekspedisi Lokal)</option>
                </select>
            </div>

            <!-- Payment Method -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Metode Pembayaran <span class="text-rose-500">*</span>
                </label>
                <select name="payment_method" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="Transfer Bank / Rekber SINTESA">Transfer Bank (Rekening Bersama SINTESA)</option>
                    <option value="Tunai / COD saat Timbang">Tunai / Bayar di Tempat (Saat Timbang & Serah Terima)</option>
                </select>
            </div>

            <!-- Shipping Address -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Alamat Pengiriman / Domisili Pembeli <span class="text-rose-500">*</span>
                </label>
                <textarea name="shipping_address" rows="2" required 
                          class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                          placeholder="Masukkan alamat lengkap pengiriman">{{ Auth::user()?->consumerProfile?->address ?? (Auth::user()?->collectorProfile?->address ?? '') }}</textarea>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Catatan untuk Petani <span class="text-slate-400 text-[10px] font-normal">(Opsional)</span>
                </label>
                <input type="text" name="notes" 
                       class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="Contoh: Kemasan karung bersih, tiba pagi hari">
            </div>

            <!-- Price Breakdown Summary -->
            <div class="p-4 bg-emerald-50/70 border border-emerald-200 rounded-2xl space-y-2">
                <div class="flex justify-between text-xs text-slate-600">
                    <span>Estimasi Subtotal Komoditas:</span>
                    <span id="modal-subtotal-text" class="font-bold text-slate-900">Rp 0</span>
                </div>
                <div class="flex justify-between text-xs text-slate-600">
                    <span>Biaya Layanan SINTESA:</span>
                    <span class="font-bold text-emerald-700">Gratis (Phase 4)</span>
                </div>
                <div class="pt-2 border-t border-emerald-200/80 flex justify-between items-baseline">
                    <span class="text-sm font-bold text-slate-900">Total Pembayaran:</span>
                    <span id="modal-total-text" class="text-lg font-black text-emerald-700">Rp 0</span>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-3">
                <button type="button" onclick="closeOrderModal()" class="flex-1 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span>Kirim Pesanan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Negosiasi (Phase 5 Real Negotiation Engine) -->
<div id="nego-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-6 my-8">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="handshake" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Ajukan Negosiasi Harga</h3>
                    <p class="text-xs text-slate-500">Tawar harga komoditas langsung dengan Petani</p>
                </div>
            </div>
            <button type="button" onclick="closeNegotiationModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('offers.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">

            <!-- Product Summary -->
            <div class="p-3.5 bg-amber-50/50 rounded-2xl border border-amber-200/60 flex items-center gap-3">
                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="w-14 h-14 rounded-xl object-cover border border-amber-200">
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-bold text-slate-900 truncate">{{ $product->name }}</h4>
                    <p class="text-xs text-slate-500">Petani: <span class="font-semibold text-slate-700">{{ $product->user->name }}</span></p>
                    <p class="text-xs text-slate-500">Harga Katalog: <span class="font-bold text-slate-800">{{ $product->formatted_price }} / {{ $product->unit }}</span></p>
                </div>
            </div>

            <!-- Quantity Input -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Jumlah Volume Pembelian ({{ $product->unit }}) <span class="text-rose-500">*</span>
                </label>
                <div class="flex items-center gap-2">
                    <input type="number" id="nego-quantity-input" name="quantity" 
                           step="any"
                           min="{{ $product->min_order }}" 
                           max="{{ $product->stock }}"
                           value="{{ max(1, $product->min_order) }}"
                           required
                           class="flex-1 px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <span class="text-xs font-semibold text-slate-500">{{ $product->unit }}</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">
                    Min order: {{ number_format($product->min_order, 0, ',', '.') }} {{ $product->unit }} | Stok: {{ number_format($product->stock, 0, ',', '.') }} {{ $product->unit }}
                </p>
            </div>

            <!-- Offered Price -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Tawaran Harga Anda (Rp / {{ $product->unit }}) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">Rp</span>
                    <input type="number" name="offered_price" 
                           min="1"
                           placeholder="Contoh: {{ (int)($product->price * 0.9) }}"
                           required
                           class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <p class="text-[11px] text-amber-700 mt-1">
                    Tips: Ajukan tawaran yang wajar untuk memperbesar peluang disepakati petani.
                </p>
            </div>

            <!-- Shipping Method -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Rencana Pengambilan / Pengiriman <span class="text-rose-500">*</span>
                </label>
                <select name="shipping_method" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="Ambil di Lokasi Petani">Ambil Sendiri di Lokasi Petani ({{ $product->location }})</option>
                    <option value="Pengiriman / Kurir">Kirim ke Alamat Pembeli (Kurir / Ekspedisi Lokal)</option>
                </select>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Catatan untuk Petani <span class="text-slate-400 text-[10px] font-normal">(Opsional)</span>
                </label>
                <textarea name="notes" rows="2" 
                          class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500"
                          placeholder="Jelaskan kesiapan armada pengangkutan, jadwal pickup, atau spesifikasi khusus"></textarea>
            </div>

            <div class="flex items-center gap-3 pt-3">
                <button type="button" onclick="closeNegotiationModal()" class="flex-1 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-md shadow-amber-500/20 transition flex items-center justify-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span>Kirim Penawaran</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const unitPrice = {{ (float)$product->price }};
    const minOrder = {{ (float)$product->min_order }};
    const maxStock = {{ (float)$product->stock }};

    function switchProductImage(src, btn) {
        document.getElementById('main-product-image').src = src;
        document.querySelectorAll('.thumbnail-btn').forEach(b => {
            b.classList.remove('border-emerald-600', 'ring-2', 'ring-emerald-500/20');
            b.classList.add('border-transparent');
        });
        btn.classList.remove('border-transparent');
        btn.classList.add('border-emerald-600', 'ring-2', 'ring-emerald-500/20');
    }

    function adjustQuantity(delta) {
        const input = document.getElementById('calc-quantity');
        let current = parseFloat(input.value) || minOrder;
        let next = current + delta;
        if (next < minOrder) next = minOrder;
        if (next > maxStock) next = maxStock;
        input.value = next;
        recalculateSubtotal();
    }

    function recalculateSubtotal() {
        const input = document.getElementById('calc-quantity');
        const errorMsg = document.getElementById('calc-error-msg');
        const display = document.getElementById('calc-subtotal-display');
        let qty = parseFloat(input.value) || 0;

        if (qty < minOrder) {
            errorMsg.textContent = 'Jumlah pemesanan minimal adalah ' + minOrder + ' {{ $product->unit }}';
            errorMsg.classList.remove('hidden');
        } else if (qty > maxStock) {
            errorMsg.textContent = 'Jumlah melebihi stok yang tersedia (' + maxStock + ' {{ $product->unit }})';
            errorMsg.classList.remove('hidden');
        } else {
            errorMsg.classList.add('hidden');
        }

        const subtotal = Math.max(0, qty) * unitPrice;
        display.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal);
    }

    function openOrderModal() {
        const qty = parseFloat(document.getElementById('calc-quantity').value) || minOrder;
        document.getElementById('form-order-qty').value = qty;
        updateModalSubtotal();
        document.getElementById('order-modal').classList.remove('hidden');
    }

    function updateModalSubtotal() {
        const formQty = parseFloat(document.getElementById('form-order-qty').value) || 0;
        const subtotal = Math.max(0, formQty) * unitPrice;
        const formatted = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal);
        const subtotalEl = document.getElementById('modal-subtotal-text');
        const totalEl = document.getElementById('modal-total-text');
        if (subtotalEl) subtotalEl.textContent = formatted;
        if (totalEl) totalEl.textContent = formatted;
    }

    function closeOrderModal() {
        document.getElementById('order-modal').classList.add('hidden');
    }

    function openNegotiationModal() {
        const qty = parseFloat(document.getElementById('calc-quantity').value) || minOrder;
        const negoQty = document.getElementById('nego-quantity-input');
        if (negoQty) negoQty.value = qty;
        document.getElementById('nego-modal').classList.remove('hidden');
    }

    function closeNegotiationModal() {
        document.getElementById('nego-modal').classList.add('hidden');
    }
</script>
@endsection
