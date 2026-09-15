@extends('layouts.dashboard')

@section('title', 'Tambah Produk Baru - Petani SINTESA')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    #location-picker-map { min-height: 280px; z-index: 1; }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <a href="{{ route('farmer.products.index') }}" class="hover:text-emerald-600 transition">Produk Saya</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-slate-800">Tambah Baru</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Tambah Produk Marketplace</h1>
        </div>
        <a href="{{ route('farmer.products.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 font-bold text-xs shadow-xs transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Produk Saya</span>
        </a>
    </div>

    <!-- Main Form Container -->
    <form action="{{ route('farmer.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        @if(isset($selectedStock) && $selectedStock)
            <input type="hidden" name="stock_id" value="{{ $selectedStock->id }}">
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="link-2" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">Terhubung ke Inventaris Stok Riil</p>
                        <p class="text-sm font-black text-slate-900">Batch: {{ $selectedStock->batch_code }} ({{ $selectedStock->formatted_available }} siap jual)</p>
                    </div>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800">
                    {{ $selectedStock->quality }}
                </span>
            </div>
        @endif

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
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required 
                           placeholder="Contoh: Cabai Rawit Merah Segar Dataran Tinggi" 
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
                        <option value="">Pilih Komoditas Pertanian</option>
                        @foreach($commodities as $com)
                            <option value="{{ $com->id }}" data-unit="{{ $com->unit }}" {{ old('commodity_id', $selectedStock->commodity_id ?? '') == $com->id ? 'selected' : '' }}>
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
                        <option value="Grade A (Super)" {{ old('quality', $selectedStock->quality ?? 'Grade A (Super)') == 'Grade A (Super)' ? 'selected' : '' }}>Grade A (Super / Ekspor)</option>
                        <option value="Grade B (Standar)" {{ old('quality', $selectedStock->quality ?? 'Grade B (Standar)') == 'Grade B (Standar)' ? 'selected' : '' }}>Grade B (Standar Pasar)</option>
                        <option value="Organik Premium" {{ old('quality', $selectedStock->quality ?? '') == 'Organik Premium' ? 'selected' : '' }}>Organik Premium (Bebas Pestisida Kimia)</option>
                        <option value="Campuran / Curah" {{ old('quality', $selectedStock->quality ?? '') == 'Campuran / Curah' ? 'selected' : '' }}>Campuran / Curah Standar</option>
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
                    <input type="date" id="harvest_date" name="harvest_date" value="{{ old('harvest_date', date('Y-m-d')) }}" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition">
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Status Publikasi <span class="text-rose-500">*</span>
                    </label>
                    <select id="status" name="status" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition">
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Aktif (Tampil di Marketplace)</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Nonaktif / Draft (Disembunyikan)</option>
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
                        <input type="number" id="price" name="price" value="{{ old('price') }}" required min="100" step="100" 
                               placeholder="15000" 
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
                    <input type="text" id="unit" name="unit" value="{{ old('unit', $selectedStock->unit ?? 'kg') }}" required 
                           placeholder="kg, ton, butir..." 
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
                    <input type="number" id="stock" name="stock" value="{{ old('stock', isset($selectedStock) ? (float)$selectedStock->available_quantity : 100) }}" required min="0" step="any" 
                           placeholder="500" 
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
                    <input type="number" id="min_order" name="min_order" value="{{ old('min_order', 1) }}" required min="0.01" step="any" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition">
                </div>

                <!-- Allow Negotiation Checkbox -->
                <div class="sm:col-span-2 flex items-center pt-6">
                    <label class="relative flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="allow_negotiation" value="1" {{ old('allow_negotiation', '1') ? 'checked' : '' }} 
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
                    <input type="text" id="location" name="location" value="{{ old('location', $farmerProfile->address ?? '') }}" required 
                           placeholder="Contoh: Desa Tulungrejo, Kec. Bumiaji, Kota Batu, Jawa Timur" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition @error('location') border-rose-500 @enderror">
                    @error('location')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Leaflet Location Picker -->
                <div class="md:col-span-2 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Titik Lokasi Kebun pada Peta
                            </label>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Klik langsung pada peta untuk memindahkan pin lokasi kebun Anda, atau gunakan deteksi GPS otomatis.
                            </p>
                        </div>
                        <button type="button" onclick="detectGPSLocation()" 
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-bold transition shadow-xs">
                            <i data-lucide="crosshair" class="w-4 h-4 text-emerald-600"></i>
                            <span>📍 Deteksi Lokasi Saya (GPS)</span>
                        </button>
                    </div>

                    <!-- Interactive Map Container -->
                    <div id="location-picker-map" class="w-full h-72 rounded-2xl border border-slate-200 shadow-inner relative z-10"></div>

                    <!-- Friendly Status Card (Tanpa Angka Desimal Koordinat) -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 rounded-xl bg-emerald-50/70 border border-emerald-200/80 text-xs gap-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse flex-shrink-0"></span>
                            <span class="font-bold text-emerald-950">Titik Kebun Ditandai:</span>
                            <span id="coord-display" class="text-emerald-800 font-medium">
                                {{ $selectedStock->harvest->location ?? $farmerProfile->address ?? 'Pin lokasi lahan terpasang di peta' }}
                            </span>
                        </div>
                        <span class="text-[11px] text-emerald-700 font-semibold bg-white/80 border border-emerald-200 px-2.5 py-0.5 rounded-lg flex-shrink-0">
                            ✓ Otomatis terhubung ke SINTESA Match
                        </span>
                    </div>

                    <!-- Auto-populated hidden coordinates -->
                    <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $selectedStock->harvest->latitude ?? $farmerProfile->latitude ?? '-7.8712') }}">
                    <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $selectedStock->harvest->longitude ?? $farmerProfile->longitude ?? '112.5273') }}">
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Deskripsi Produk & Hasil Panen
                    </label>
                    <textarea id="description" name="description" rows="4" 
                              placeholder="Jelaskan karakteristik komoditas Anda, cara budidaya, waktu simpan, kondisi kesegaran, dll..." 
                              class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Card: Foto Produk -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-6">
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100">
                <i data-lucide="image" class="w-5 h-5 text-emerald-600"></i>
                <span>Foto Produk Hasil Panen</span>
            </h2>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Unggah Gambar (Maksimal 5 foto, format: JPG, PNG, WEBP, maks. 3MB per file)
                </label>
                <div class="border-2 border-dashed border-slate-300 rounded-2xl p-6 text-center hover:border-emerald-500 transition cursor-pointer bg-slate-50/50">
                    <input type="file" name="images[]" id="product-images-input" multiple accept="image/jpeg,image/png,image/webp" class="hidden" onchange="previewImages(event)">
                    <label for="product-images-input" class="cursor-pointer flex flex-col items-center">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3">
                            <i data-lucide="upload-cloud" class="w-6 h-6"></i>
                        </div>
                        <span class="text-sm font-bold text-slate-800">Klik untuk memilih foto produk</span>
                        <span class="text-xs text-slate-400 mt-1">Foto pertama akan dijadikan foto utama produk di marketplace.</span>
                    </label>
                </div>

                <div id="image-preview-strip" class="grid grid-cols-2 sm:grid-cols-5 gap-3 mt-4 hidden"></div>
                @error('images.*')
                    <p class="text-xs text-rose-600 mt-2 font-semibold">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Submit Button Bar -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('farmer.products.index') }}" class="px-6 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                Batal
            </a>
            <button type="submit" class="px-8 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 transition flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Simpan & Tayangkan ke Marketplace</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    // Automatic unit synchronization from chosen commodity
    const commoditySelect = document.getElementById('commodity_id');
    if (commoditySelect) {
        commoditySelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const defaultUnit = selected.getAttribute('data-unit');
            if (defaultUnit) {
                document.getElementById('unit').value = defaultUnit;
            }
        });
    }

    function previewImages(event) {
        const strip = document.getElementById('image-preview-strip');
        strip.innerHTML = '';
        const files = event.target.files;

        if (files.length > 0) {
            strip.classList.remove('hidden');
            for (let i = 0; i < files.length; i++) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative aspect-square rounded-xl overflow-hidden border border-slate-200 bg-slate-100';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-full object-cover">
                        <span class="absolute top-1 left-1 px-1.5 py-0.5 rounded text-[9px] font-bold ${i === 0 ? 'bg-emerald-600 text-white' : 'bg-slate-800/80 text-white'}">
                            ${i === 0 ? 'Utama' : '#' + (i + 1)}
                        </span>
                    `;
                    strip.appendChild(div);
                };
                reader.readAsDataURL(files[i]);
            }
        } else {
            strip.classList.add('hidden');
        }
    }

    // Leaflet Interactive Map Picker for Farmer
    let map, marker;
    let geocodeTimeout = null;

    document.addEventListener('DOMContentLoaded', function() {
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const locationInput = document.getElementById('location');
        const coordDisplay = document.getElementById('coord-display');

        let initialLat = parseFloat(latInput.value) || -7.8712;
        let initialLng = parseFloat(lngInput.value) || 112.5273;

        // Initialize Map
        map = L.map('location-picker-map', {
            center: [initialLat, initialLng],
            zoom: 14,
            scrollWheelZoom: false
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Custom Leaflet marker icon
        const farmIcon = L.divIcon({
            className: 'custom-farm-marker',
            html: `
                <div style="background-color: #059669; width: 34px; height: 34px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); display: flex; align-items: center; justify-content: center; border: 3px solid #ffffff; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3);">
                    <div style="transform: rotate(45deg); color: #ffffff; font-size: 14px; font-weight: bold;">🌱</div>
                </div>
            `,
            iconSize: [34, 34],
            iconAnchor: [17, 34],
            popupAnchor: [0, -34]
        });

        marker = L.marker([initialLat, initialLng], {
            draggable: true,
            icon: farmIcon
        }).addTo(map);

        const currentLocText = locationInput ? (locationInput.value.trim() || 'Lokasi Kebun Anda') : 'Lokasi Kebun Anda';
        marker.bindPopup(`<b>Lokasi Kebun Produk</b><br><span style="font-size:11px;color:#334155;">${currentLocText}</span>`).openPopup();

        function updatePosition(lat, lng, autoUpdateInput = true) {
            const fixedLat = parseFloat(lat).toFixed(7);
            const fixedLng = parseFloat(lng).toFixed(7);
            latInput.value = fixedLat;
            lngInput.value = fixedLng;

            if (coordDisplay) {
                coordDisplay.innerHTML = '<span class="text-amber-700 animate-pulse font-semibold">📍 Mendeteksi alamat dari pin peta...</span>';
            }

            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${fixedLat}&lon=${fixedLng}&zoom=18&addressdetails=1`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.display_name) {
                        if (coordDisplay) {
                            coordDisplay.innerHTML = '<strong>' + data.display_name + '</strong>';
                        }
                        if (autoUpdateInput && locationInput) {
                            locationInput.value = data.display_name;
                        }
                        marker.bindPopup(`<b>Lokasi Kebun Produk</b><br><span style="font-size:11px;color:#334155;">${data.display_name}</span>`).openPopup();
                    } else if (coordDisplay) {
                        coordDisplay.textContent = 'Pin lokasi lahan terpasang di peta (' + fixedLat + ', ' + fixedLng + ')';
                    }
                })
                .catch(() => {
                    if (coordDisplay) {
                        coordDisplay.textContent = 'Pin lokasi lahan terpasang di peta';
                    }
                });
        }

        // Marker drag handler
        marker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            updatePosition(pos.lat, pos.lng, true);
        });

        // Map click handler
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updatePosition(e.latlng.lat, e.latlng.lng, true);
        });

        // Two-way sync: Forward Geocode when user types into location input
        if (locationInput) {
            locationInput.addEventListener('input', function() {
                clearTimeout(geocodeTimeout);
                const query = this.value.trim();
                if (query.length < 4) return;

                if (coordDisplay) {
                    coordDisplay.innerHTML = '<span class="text-blue-700 animate-pulse font-semibold">🔍 Menyesuaikan titik pin dengan alamat...</span>';
                }

                geocodeTimeout = setTimeout(function() {
                    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`)
                        .then(res => res.json())
                        .then(results => {
                            if (results && results.length > 0) {
                                const place = results[0];
                                const lat = parseFloat(place.lat);
                                const lon = parseFloat(place.lon);

                                latInput.value = lat.toFixed(7);
                                lngInput.value = lon.toFixed(7);

                                map.flyTo([lat, lon], 15);
                                marker.setLatLng([lat, lon]);
                                marker.bindPopup(`<b>Lokasi Kebun Disesuaikan</b><br><span style="font-size:11px;color:#334155;">${place.display_name}</span>`).openPopup();

                                if (coordDisplay) {
                                    coordDisplay.innerHTML = '<strong>' + place.display_name + '</strong>';
                                }
                            }
                        })
                        .catch(() => {});
                }, 800);
            });
        }

        // Ensure tiles load correctly on container render
        setTimeout(function() {
            map.invalidateSize();
        }, 300);
    });

    // Detect GPS location with browser Geolocation API
    window.detectGPSLocation = function() {
        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung deteksi lokasi GPS.');
            return;
        }

        const coordDisplay = document.getElementById('coord-display');
        if (coordDisplay) {
            coordDisplay.innerHTML = '<span class="text-amber-700 animate-pulse font-semibold">Mencari sinyal GPS perangkat...</span>';
        }

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                if (map && marker) {
                    map.flyTo([lat, lng], 16);
                    marker.setLatLng([lat, lng]);
                }

                document.getElementById('latitude').value = parseFloat(lat).toFixed(7);
                document.getElementById('longitude').value = parseFloat(lng).toFixed(7);

                // Reverse geocode to fill location address
                const locationInput = document.getElementById('location');
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.display_name) {
                            if (locationInput) {
                                locationInput.value = data.display_name;
                            }
                            if (coordDisplay) {
                                coordDisplay.innerHTML = '<strong>' + data.display_name + '</strong>';
                            }
                            if (marker) {
                                marker.bindPopup(`<b>Lokasi Kebun (GPS)</b><br><span style="font-size:11px;color:#334155;">${data.display_name}</span>`).openPopup();
                            }
                        }
                    })
                    .catch(() => {
                        if (coordDisplay) {
                            coordDisplay.textContent = 'Lokasi GPS lahan berhasil ditandai di peta';
                        }
                    });
            },
            function(error) {
                alert('Gagal mendeteksi lokasi GPS: ' + error.message);
                if (coordDisplay) {
                    coordDisplay.textContent = 'Silakan geser pin pada peta untuk menentukan lokasi';
                }
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    };
</script>
@endpush
@endsection
