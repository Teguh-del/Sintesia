@extends('layouts.dashboard')

@section('title', 'Catat Panen & Tambah Stok - Petani SINTESA')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    #harvest-map-picker { min-height: 320px; z-index: 1; }
    .custom-farm-pin {
        background-color: #059669;
        width: 36px;
        height: 36px;
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid #ffffff;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3);
    }
    .custom-farm-pin span {
        transform: rotate(45deg);
        font-size: 16px;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <a href="{{ route('farmer.stocks.index') }}" class="hover:text-emerald-600 transition">Manajemen Stok</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <a href="{{ route('farmer.harvests.index') }}" class="hover:text-emerald-600 transition">Catatan Panen</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-slate-800 font-bold">Tambah Stok Baru</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Catat Hasil Panen & Tambah Stok Riil</h1>
            <p class="text-sm text-slate-500 mt-1">Hasil panen yang Anda catat akan otomatis diterbitkan sebagai batch stok siap jual di SINTESA.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('farmer.stocks.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 font-bold text-xs shadow-xs transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Stok</span>
            </a>
        </div>
    </div>

    <!-- Educational Banner: Alur Terintegrasi -->
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start gap-3 shadow-xs">
        <div class="p-2 rounded-xl bg-emerald-100 text-emerald-700 flex-shrink-0 mt-0.5">
            <i data-lucide="info" class="w-5 h-5"></i>
        </div>
        <div class="text-xs space-y-1">
            <p class="font-bold">Alur Otomatis: Panen &rarr; Stok Riil</p>
            <p class="text-emerald-700 leading-relaxed">
                Setiap hasil panen yang Anda simpan akan otomatis membuat satu batch <strong>Stok Riil</strong>. Lokasi kebun yang Anda tentukan di peta akan digunakan oleh <strong>SINTESA Match</strong> untuk mencocokkan panen Anda dengan pengepul dan konsumen terdekat secara otomatis.
            </p>
        </div>
    </div>

    <!-- Main Form Container -->
    <form action="{{ route('farmer.harvests.store') }}" method="POST" class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-8">
        @csrf

        <!-- Bagian 1: Data Komoditas & Hasil Produksi -->
        <div class="space-y-4">
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100">
                <i data-lucide="sprout" class="w-5 h-5 text-emerald-600"></i>
                <span>Informasi Hasil Panen</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Commodity Select -->
                <div class="sm:col-span-2">
                    <label for="commodity_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Komoditas Pertanian <span class="text-rose-500">*</span>
                    </label>
                    <select id="commodity_id" name="commodity_id" required 
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition @error('commodity_id') border-rose-500 @enderror">
                        <option value="">Pilih Komoditas Hasil Panen</option>
                        @foreach($commodities as $com)
                            <option value="{{ $com->id }}" data-unit="{{ $com->unit }}" {{ old('commodity_id') == $com->id ? 'selected' : '' }}>
                                {{ $com->name }} (Satuan standar: {{ $com->unit }})
                            </option>
                        @endforeach
                    </select>
                    @error('commodity_id')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quantity -->
                <div>
                    <label for="quantity" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Jumlah / Volume Hasil Panen <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="quantity" name="quantity" value="{{ old('quantity') }}" required min="0.01" step="any" 
                           placeholder="Contoh: 500" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition @error('quantity') border-rose-500 @enderror">
                    @error('quantity')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Unit -->
                <div>
                    <label for="unit" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Satuan Komoditas <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="unit" name="unit" value="{{ old('unit', 'kg') }}" required 
                           placeholder="kg, ton, butir..." 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition @error('unit') border-rose-500 @enderror">
                    @error('unit')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harvest Date -->
                <div>
                    <label for="harvest_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tanggal Petik / Panen <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" id="harvest_date" name="harvest_date" value="{{ old('harvest_date', date('Y-m-d')) }}" required 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition @error('harvest_date') border-rose-500 @enderror">
                    @error('harvest_date')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quality Grade -->
                <div>
                    <label for="quality" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Mutu / Grade Kualitas <span class="text-rose-500">*</span>
                    </label>
                    <select id="quality" name="quality" required 
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition @error('quality') border-rose-500 @enderror">
                        <option value="Grade A (Super)" {{ old('quality') == 'Grade A (Super)' ? 'selected' : '' }}>Grade A (Super / Ekspor)</option>
                        <option value="Grade B (Standar)" {{ old('quality', 'Grade B (Standar)') == 'Grade B (Standar)' ? 'selected' : '' }}>Grade B (Standar Pasar)</option>
                        <option value="Organik Premium" {{ old('quality') == 'Organik Premium' ? 'selected' : '' }}>Organik Premium</option>
                        <option value="Campuran / Curah" {{ old('quality') == 'Campuran / Curah' ? 'selected' : '' }}>Campuran / Curah Standar</option>
                    </select>
                    @error('quality')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Bagian 2: Lokasi Kebun & Pengaturan Peta Interaktif -->
        <div class="space-y-4 pt-2">
            <div class="pb-3 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-5 h-5 text-emerald-600"></i>
                        <span>Lokasi Lahan & Peta Kebun Petani</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Tentukan titik kebun Anda langsung pada peta. Cukup geser pin penanda atau klik lokasi lahan Anda.</p>
                </div>
                <button type="button" onclick="detectFarmerGPS()" 
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-bold transition shadow-xs">
                    <i data-lucide="crosshair" class="w-4 h-4 text-emerald-600"></i>
                    <span>📍 Gunakan Lokasi Saya (GPS)</span>
                </button>
            </div>

            <!-- Kolom Teks Alamat Lahan -->
            <div>
                <label for="location" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Lokasi / Alamat Lahan Kebun <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <input type="text" id="location" name="location" value="{{ old('location', $farmerProfile->address ?? '') }}" required 
                           placeholder="Contoh: Kebun Blok Barat, Desa Tulungrejo, Kec. Bumiaji, Kota Batu" 
                           class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition @error('location') border-rose-500 @enderror">
                    <button type="button" id="btn-sync-pin-address" onclick="applyDetectedAddress()" title="Terapkan alamat dari pin peta" 
                            class="absolute right-2 top-2 px-2 py-1 rounded-lg text-[11px] font-bold bg-emerald-100 text-emerald-800 hover:bg-emerald-200 transition">
                        Salin dari Peta
                    </button>
                </div>
                @error('location')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
                <p class="text-[11px] text-slate-400 mt-1">Alamat dapat Anda ketik manual atau diambil otomatis saat Anda menggeser pin di peta.</p>
            </div>

            <!-- Kontrol Pencarian Lokasi Peta (Ramah Petani) -->
            <div class="space-y-2">
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <input type="text" id="map-search-query" 
                               placeholder="Cari nama desa, kecamatan, atau kota kebun Anda (misal: Bumiaji Batu, Pujon Malang)..." 
                               class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition"
                               onkeydown="if(event.key === 'Enter'){ event.preventDefault(); searchLocationOnMap(); }">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
                    </div>
                    <button type="button" onclick="searchLocationOnMap()" 
                            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs border border-slate-200 transition flex items-center gap-1.5">
                        <i data-lucide="map" class="w-3.5 h-3.5 text-emerald-600"></i>
                        <span>Cari di Peta</span>
                    </button>
                </div>
                <div id="search-status-message" class="text-[11px] text-slate-500 hidden"></div>
            </div>

            <!-- Leaflet Interactive Map Container -->
            <div class="relative rounded-2xl overflow-hidden border border-slate-200 shadow-inner">
                <div id="harvest-map-picker" class="w-full h-80 relative z-10"></div>
            </div>

            <!-- Status Lokasi Ramah Petani (Tanpa Angka Lat/Long Rumit) -->
            <div class="p-4 rounded-xl bg-emerald-50/70 border border-emerald-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-emerald-500 ring-4 ring-emerald-200 animate-pulse flex-shrink-0"></div>
                    <div>
                        <span class="font-bold text-emerald-950 block">📍 Titik Lokasi Kebun Berhasil Ditandai</span>
                        <p id="map-readable-status" class="text-emerald-800 text-[11px] mt-0.5 font-medium">
                            {{ $farmerProfile->address ?? 'Pin lokasi terpasang pada peta kebun Anda.' }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="text-[11px] font-semibold text-emerald-700 bg-white/80 px-2.5 py-1 rounded-lg border border-emerald-200 shadow-xs">
                        ✓ Terhubung ke SINTESA Match
                    </span>
                </div>
            </div>

            <!-- Opsi Sinkronisasi ke Profil Petani -->
            <div class="pt-1">
                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="update_profile_location" value="1" 
                           {{ empty($farmerProfile->latitude) ? 'checked' : '' }} 
                           class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                    <span class="text-xs text-slate-700 font-medium">Jadikan titik ini sebagai lokasi kebun utama pada profil petani saya</span>
                </label>
            </div>

            <!-- Kolom Koordinat Tersembunyi (Disimpan Senyap untuk Backend & Database) -->
            <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $farmerProfile->latitude ?? '-7.8712') }}">
            <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $farmerProfile->longitude ?? '112.5273') }}">
        </div>

        <!-- Bagian 3: Catatan Tambahan -->
        <div class="space-y-4 pt-2">
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100">
                <i data-lucide="file-text" class="w-5 h-5 text-emerald-600"></i>
                <span>Catatan Tambahan (Opsional)</span>
            </h2>

            <div>
                <textarea id="notes" name="notes" rows="3" 
                          placeholder="Catatan kondisi cuaca saat panen, metode perawatan lahan, perlakuan pasca panen, atau pesan untuk calon pembeli..." 
                          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition">{{ old('notes') }}</textarea>
            </div>
        </div>

        <!-- Submit Bar -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('farmer.stocks.index') }}" class="px-6 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                Batal
            </a>
            <button type="submit" class="px-8 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 transition flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                <span>Simpan Panen & Terbitkan Stok Riil</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    let harvestMap, harvestMarker;
    let latestAddressString = '';

    document.addEventListener('DOMContentLoaded', function() {
        // Auto update default unit when commodity is changed
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

        // Initialize Map
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        let initialLat = parseFloat(latInput.value) || -7.8712;
        let initialLng = parseFloat(lngInput.value) || 112.5273;

        harvestMap = L.map('harvest-map-picker', {
            center: [initialLat, initialLng],
            zoom: 13,
            scrollWheelZoom: false
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(harvestMap);

        // Custom Farm Marker Icon
        const farmIcon = L.divIcon({
            className: 'custom-farm-marker',
            html: `
                <div class="custom-farm-pin">
                    <span>🌱</span>
                </div>
            `,
            iconSize: [36, 36],
            iconAnchor: [18, 36],
            popupAnchor: [0, -36]
        });

        harvestMarker = L.marker([initialLat, initialLng], {
            draggable: true,
            icon: farmIcon
        }).addTo(harvestMap);

        harvestMarker.bindPopup('<b>Lokasi Kebun Panen Anda</b><br><span style="font-size:11px;color:#64748b;">Geser pin atau klik peta untuk menyesuaikan posisi lahan.</span>').openPopup();

        // Update coordinates and reverse geocode
        function onMarkerMoved(lat, lng, autoFill = false) {
            latInput.value = parseFloat(lat).toFixed(7);
            lngInput.value = parseFloat(lng).toFixed(7);

            const statusEl = document.getElementById('map-readable-status');
            if (statusEl) {
                statusEl.textContent = 'Mendeteksi nama wilayah kebun...';
            }

            // Debounced reverse geocoding to human-readable address
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=16&addressdetails=1`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.display_name) {
                        latestAddressString = data.display_name;
                        if (statusEl) {
                            statusEl.textContent = 'Area kebun: ' + data.display_name;
                        }
                        const locationInput = document.getElementById('location');
                        if (autoFill && locationInput && !locationInput.value.trim()) {
                            locationInput.value = data.display_name;
                        }
                    } else if (statusEl) {
                        statusEl.textContent = 'Pin lokasi lahan terpasang di peta.';
                    }
                })
                .catch(() => {
                    if (statusEl) {
                        statusEl.textContent = 'Pin lokasi lahan terpasang di peta.';
                    }
                });
        }

        // Marker dragend event
        harvestMarker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            onMarkerMoved(pos.lat, pos.lng);
        });

        // Map click event
        harvestMap.on('click', function(e) {
            harvestMarker.setLatLng(e.latlng);
            onMarkerMoved(e.latlng.lat, e.latlng.lng);
        });

        // Invalidate map size after rendering
        setTimeout(function() {
            harvestMap.invalidateSize();
        }, 350);

        // Initial reverse geocode if location string not present
        if (!document.getElementById('location').value.trim()) {
            onMarkerMoved(initialLat, initialLng, true);
        }
    });

    // Detect GPS location
    window.detectFarmerGPS = function() {
        if (!navigator.geolocation) {
            alert('Browser perangkat Anda belum mendukung deteksi lokasi GPS.');
            return;
        }

        const statusEl = document.getElementById('map-readable-status');
        if (statusEl) {
            statusEl.textContent = 'Mencari sinyal GPS perangkat Anda...';
        }

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                if (harvestMap && harvestMarker) {
                    harvestMap.setView([lat, lng], 15);
                    harvestMarker.setLatLng([lat, lng]);
                    harvestMarker.bindPopup('<b>Lokasi Kebun Berhasil Ditemukan!</b>').openPopup();
                }

                document.getElementById('latitude').value = parseFloat(lat).toFixed(7);
                document.getElementById('longitude').value = parseFloat(lng).toFixed(7);

                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=16&addressdetails=1`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.display_name) {
                            latestAddressString = data.display_name;
                            if (statusEl) {
                                statusEl.textContent = 'Area kebun (GPS): ' + data.display_name;
                            }
                            const locationInput = document.getElementById('location');
                            if (locationInput) {
                                locationInput.value = data.display_name;
                            }
                        }
                    })
                    .catch(() => {
                        if (statusEl) {
                            statusEl.textContent = 'Lokasi GPS berhasil ditandai di peta.';
                        }
                    });
            },
            function(error) {
                alert('Gagal mendeteksi lokasi GPS: ' + error.message + '. Silakan pilih lokasi secara langsung pada peta atau gunakan kolom pencarian desa.');
                if (statusEl) {
                    statusEl.textContent = 'Silakan klik pada peta untuk menentukan lokasi kebun.';
                }
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    };

    // Search Location on Map by Name
    window.searchLocationOnMap = function() {
        const query = document.getElementById('map-search-query').value.trim();
        const msgEl = document.getElementById('search-status-message');
        if (!query) {
            alert('Silakan masukkan nama desa, kecamatan, atau kota kebun Anda terlebih dahulu.');
            return;
        }

        if (msgEl) {
            msgEl.classList.remove('hidden');
            msgEl.textContent = 'Sedang mencari lokasi "' + query + '"...';
        }

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`)
            .then(res => res.json())
            .then(results => {
                if (results && results.length > 0) {
                    const place = results[0];
                    const lat = parseFloat(place.lat);
                    const lon = parseFloat(place.lon);

                    if (harvestMap && harvestMarker) {
                        harvestMap.setView([lat, lon], 14);
                        harvestMarker.setLatLng([lat, lon]);
                        harvestMarker.bindPopup(`<b>${place.display_name}</b>`).openPopup();
                    }

                    document.getElementById('latitude').value = lat.toFixed(7);
                    document.getElementById('longitude').value = lon.toFixed(7);

                    latestAddressString = place.display_name;
                    const statusEl = document.getElementById('map-readable-status');
                    if (statusEl) {
                        statusEl.textContent = 'Area terpilih: ' + place.display_name;
                    }

                    const locationInput = document.getElementById('location');
                    if (locationInput && (!locationInput.value.trim() || confirm('Perbarui alamat kebun dengan hasil pencarian: "' + place.display_name + '"?'))) {
                        locationInput.value = place.display_name;
                    }

                    if (msgEl) {
                        msgEl.textContent = 'Lokasi ditemukan: ' + place.display_name;
                    }
                } else {
                    if (msgEl) {
                        msgEl.textContent = 'Lokasi "' + query + '" tidak ditemukan. Silakan gunakan nama kecamatan atau kota terdekat, lalu geser pin ke posisi kebun Anda.';
                    }
                }
            })
            .catch(() => {
                if (msgEl) {
                    msgEl.textContent = 'Gagal menghubungi layanan pencarian peta. Anda tetap dapat menggeser pin secara manual.';
                }
            });
    };

    // Apply detected address to location input
    window.applyDetectedAddress = function() {
        const locationInput = document.getElementById('location');
        if (latestAddressString && locationInput) {
            locationInput.value = latestAddressString;
        } else {
            alert('Silakan geser pin pada peta atau gunakan tombol GPS untuk mendeteksi alamat kebun terlebih dahulu.');
        }
    };
</script>
@endpush
