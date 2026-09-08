@extends('layouts.dashboard')

@section('title', 'Edit Produk - ' . $product->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <a href="{{ route('farmer.products.index') }}" class="hover:text-emerald-600 transition">Produk Saya</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-slate-800">Edit Produk</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Edit Produk: {{ $product->name }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('marketplace.show', $product->slug) }}" target="_blank" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs shadow-sm transition flex items-center gap-1.5">
                <i data-lucide="external-link" class="w-4 h-4 text-emerald-600"></i>
                <span>Lihat di Marketplace</span>
            </a>
            <a href="{{ route('farmer.products.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                Kembali
            </a>
        </div>
    </div>

    <!-- Main Form Container -->
    <form action="{{ route('farmer.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Card: Informasi Utama Produk -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-6">
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100">
                <i data-lucide="package" class="w-5 h-5 text-emerald-600"></i>
                <span>Informasi Dasar Produk</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Product Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Produk <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition @error('name') border-rose-500 @enderror">
                    @error('name')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Commodity Select -->
                <div>
                    <label for="commodity_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Komoditas <span class="text-rose-500">*</span>
                    </label>
                    <select id="commodity_id" name="commodity_id" required 
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition @error('commodity_id') border-rose-500 @enderror">
                        @foreach($commodities as $com)
                            <option value="{{ $com->id }}" data-unit="{{ $com->unit }}" {{ old('commodity_id', $product->commodity_id) == $com->id ? 'selected' : '' }}>
                                {{ $com->name }} (Satuan standar: {{ $com->unit }})
                            </option>
                        @endforeach
                    </select>
                    @error('commodity_id')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quality Grade -->
                <div>
                    <label for="quality" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kualitas / Grade Panen <span class="text-rose-500">*</span>
                    </label>
                    <select id="quality" name="quality" required 
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition @error('quality') border-rose-500 @enderror">
                        <option value="Grade A (Super)" {{ old('quality', $product->quality) == 'Grade A (Super)' ? 'selected' : '' }}>Grade A (Super / Ekspor)</option>
                        <option value="Grade B (Standar)" {{ old('quality', $product->quality) == 'Grade B (Standar)' ? 'selected' : '' }}>Grade B (Standar Pasar)</option>
                        <option value="Organik Premium" {{ old('quality', $product->quality) == 'Organik Premium' ? 'selected' : '' }}>Organik Premium (Bebas Pestisida Kimia)</option>
                        <option value="Campuran / Curah" {{ old('quality', $product->quality) == 'Campuran / Curah' ? 'selected' : '' }}>Campuran / Curah Standar</option>
                    </select>
                    @error('quality')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harvest Date -->
                <div>
                    <label for="harvest_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tanggal Panen
                    </label>
                    <input type="date" id="harvest_date" name="harvest_date" value="{{ old('harvest_date', $product->harvest_date?->format('Y-m-d')) }}" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition">
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Status Publikasi <span class="text-rose-500">*</span>
                    </label>
                    <select id="status" name="status" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition">
                        <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Aktif (Tampil di Marketplace)</option>
                        <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Nonaktif / Draft (Disembunyikan)</option>
                        <option value="sold_out" {{ old('status', $product->status) == 'sold_out' ? 'selected' : '' }}>Habis (Sold Out)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Card: Harga & Stok -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-6">
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100">
                <i data-lucide="tag" class="w-5 h-5 text-emerald-600"></i>
                <span>Harga Satuan & Manajemen Stok</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Price -->
                <div>
                    <label for="price" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Harga Satuan (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-400 font-bold text-xs">Rp</span>
                        <input type="number" id="price" name="price" value="{{ old('price', (int)$product->price) }}" required min="100" step="100" 
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition @error('price') border-rose-500 @enderror">
                    </div>
                    @error('price')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Unit -->
                <div>
                    <label for="unit" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Satuan Penjualan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="unit" name="unit" value="{{ old('unit', $product->unit) }}" required 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition @error('unit') border-rose-500 @enderror">
                    @error('unit')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stock Available -->
                <div>
                    <label for="stock" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Stok Tersedia <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" required min="0" step="any" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition @error('stock') border-rose-500 @enderror">
                    @error('stock')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Min Order -->
                <div>
                    <label for="min_order" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Minimal Pemesanan <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="min_order" name="min_order" value="{{ old('min_order', $product->min_order) }}" required min="0.01" step="any" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition">
                </div>

                <!-- Allow Negotiation Checkbox -->
                <div class="sm:col-span-2 flex items-center pt-6">
                    <label class="relative flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="allow_negotiation" value="1" {{ old('allow_negotiation', $product->allow_negotiation) ? 'checked' : '' }} 
                               class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                        <div>
                            <span class="text-xs font-bold text-slate-800">Izinkan Penawaran Harga (Fitur Nego)</span>
                            <p class="text-[11px] text-slate-500">Pengepul atau konsumen dapat mengajukan negosiasi harga jika membeli dalam jumlah tertentu.</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Card: Lokasi & Deskripsi -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-6">
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100">
                <i data-lucide="map-pin" class="w-5 h-5 text-emerald-600"></i>
                <span>Lokasi Kebun & Deskripsi</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Location text -->
                <div class="md:col-span-2">
                    <label for="location" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Lokasi Pengiriman / Kebun <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="location" name="location" value="{{ old('location', $product->location) }}" required 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition @error('location') border-rose-500 @enderror">
                    @error('location')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Coordinates -->
                <div>
                    <label for="latitude" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Latitude (Opsional - Koordinat Peta)
                    </label>
                    <input type="text" id="latitude" name="latitude" value="{{ old('latitude', $product->latitude) }}" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition">
                </div>

                <div>
                    <label for="longitude" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Longitude (Opsional - Koordinat Peta)
                    </label>
                    <input type="text" id="longitude" name="longitude" value="{{ old('longitude', $product->longitude) }}" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition">
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Deskripsi Produk & Hasil Panen
                    </label>
                    <textarea id="description" name="description" rows="4" 
                              class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Card: Kelola Foto Produk -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-6">
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100">
                <i data-lucide="image" class="w-5 h-5 text-emerald-600"></i>
                <span>Kelola Foto Produk</span>
            </h2>

            <!-- Existing Images -->
            @if($product->images->count() > 0)
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Foto Saat Ini (Centang untuk menghapus foto)
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @foreach($product->images as $img)
                            <div class="relative rounded-xl overflow-hidden border border-slate-200 bg-slate-100 group">
                                <img src="{{ Str::startsWith($img->image_path, ['http://', 'https://']) ? $img->image_path : asset('storage/' . $img->image_path) }}" 
                                     alt="Foto Produk" class="w-full h-32 object-cover">
                                <label class="absolute inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition cursor-pointer">
                                    <input type="checkbox" name="delete_images[]" value="{{ $img->id }}" class="w-5 h-5 rounded text-rose-600 focus:ring-rose-500 border-white">
                                    <span class="text-white text-xs font-bold ml-2">Hapus</span>
                                </label>
                                @if($img->is_primary)
                                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-600 text-white shadow-sm">
                                        Utama
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Upload New Images -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Tambah Foto Baru (Format: JPG, PNG, WEBP, maks. 3MB)
                </label>
                <div class="border-2 border-dashed border-slate-300 rounded-2xl p-6 text-center hover:border-emerald-500 transition cursor-pointer bg-slate-50/50">
                    <input type="file" name="images[]" id="product-images-input" multiple accept="image/jpeg,image/png,image/webp" class="hidden">
                    <label for="product-images-input" class="cursor-pointer flex flex-col items-center">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3">
                            <i data-lucide="upload-cloud" class="w-6 h-6"></i>
                        </div>
                        <span class="text-sm font-bold text-slate-800">Klik untuk menambah foto baru</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Submit Button Bar -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('farmer.products.index') }}" class="px-6 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                Batal
            </a>
            <button type="submit" class="px-8 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 transition flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Simpan Perubahan Produk</span>
            </button>
        </div>
    </form>
</div>
@endsection
