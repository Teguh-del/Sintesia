@extends('layouts.dashboard')

@section('title', 'Edit Hasil Panen - Petani SINTESA')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    #harvest-edit-map { min-height: 320px; z-index: 1; }
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
                <a href="{{ route('farmer.harvests.index') }}" class="hover:text-emerald-600 transition">Hasil Panen</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-slate-800 font-bold">Edit Panen</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Edit Catatan Panen & Lokasi Lahan</h1>
            <p class="text-sm text-slate-500 mt-1">Perubahan kuantitas atau lokasi kebun akan otomatis disinkronkan ke batch inventaris stok riil.</p>
        </div>
        <a href="{{ route('farmer.harvests.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 font-bold text-xs shadow-xs transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Catatan Panen</span>
        </a>
    </div>

    <!-- Main Form Container -->
    <form action="{{ route('farmer.harvests.update', $harvest->id) }}" method="POST" class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-8">
        @csrf
        @method('PUT')

        <!-- Bagian 1: Data Panen -->
        <div class="space-y-4">
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100">
                <i data-lucide="sprout" class="w-5 h-5 text-emerald-600"></i>
                <span>Rincian Hasil Panen</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Commodity Select -->
                <div class="sm:col-span-2">
                    <label for="commodity_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Komoditas Pertanian <span class="text-rose-500">*</span>
                    </label>
                    <select id="commodity_id" name="commodity_id" required 
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition @error('commodity_id') border-rose-500 @enderror">
                        @foreach($commodities as $com)
                            <option value="{{ $com->id }}" data-unit="{{ $com->unit }}" {{ old('commodity_id', $harvest->commodity_id) == $com->id ? 'selected' : '' }}>
                                {{ $com->name }} (Satuan: {{ $com->unit }})
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
                    <input type="number" id="quantity" name="quantity" value="{{ old('quantity', $harvest->quantity) }}" required min="0.01" step="any" 
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
                    <input type="text" id="unit" name="unit" value="{{ old('unit', $harvest->unit) }}" required 
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
                    <input type="date" id="harvest_date" name="harvest_date" value="{{ old('harvest_date', $harvest->harvest_date->format('Y-m-d')) }}" required 
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
                        <option value="Grade A (Super)" {{ old('quality', $harvest->quality) == 'Grade A (Super)' ? 'selected' : '' }}>Grade A (Super / Ekspor)</option>
                        <option value="Grade B (Standar)" {{ old('quality', $harvest->quality) == 'Grade B (Standar)' ? 'selected' : '' }}>Grade B (Standar Pasar)</option>
                        <option value="Organik Premium" {{ old('quality', $harvest->quality) == 'Organik Premium' ? 'selected' : '' }}>Organik Premium</option>
                        <option value="Campuran / Curah" {{ old('quality', $harvest->quality) == 'Campuran / Curah' ? 'selected' : '' }}>Campuran / Curah Standar</option>
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
                    <p class="text-xs text-slate-500 mt-0.5">Sesuaikan titik kebun pada peta dengan menggeser pin atau mencari nama desa.</p>
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
                    <input type="text" id="location" name="location" value="{{ old('location', $harvest->location) }}" required 
                           class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition @error('location') border-rose-500 @enderror">
                    <button type="button" onclick="applyDetectedAddress()" title="Terapkan alamat dari pin peta" 
                            class="absolute right-2 top-2 px-2 py-1 rounded-lg text-[11px] font-bold bg-emerald-100 text-emerald-800 hover:bg-emerald-200 transition">
                        Salin dari Peta
                    </button>
                </div>
                @error('location')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
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

            <!-- Leaflet Map Container -->
            <div class="relative rounded-2xl overflow-hidden border border-slate-200 shadow-inner">
                <div id="harvest-edit-map" class="w-full h-80 relative z-10"></div>
            </div>

            <!-- Status Lokasi Ramah Petani -->
            <div class="p-4 rounded-xl bg-emerald-50/70 border border-emerald-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-emerald-500 ring-4 ring-emerald-200 animate-pulse flex-shrink-0"></div>
                    <div>
                        <span class="font-bold text-emerald-950 block">📍 Titik Lokasi Kebun Berhasil Ditandai</span>
                        <p id="map-readable-status" class="text-emerald-800 text-[11px] mt-0.5 font-medium">
                            {{ $harvest->location }}
                        </p>
                    </div>
                </div>
                <span class="text-[11px] font-semibold text-emerald-700 bg-white/80 px-2.5 py-1 rounded-lg border border-emerald-200 shadow-xs">
                    ✓ Terhubung ke SINTESA Match
                </span>
            </div>

            <!-- Opsi Sinkronisasi ke Profil Petani -->
            <div class="pt-1">
                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="update_profile_location" value="1" 
                           class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                    <span class="text-xs text-slate-700 font-medium">Perbarui juga alamat lokasi kebun utama di profil petani saya</span>
                </label>
            </div>

            <!-- Kolom Koordinat Tersembunyi -->
            <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $harvest->latitude ?? $farmerProfile->latitude ?? '-7.8712') }}">
            <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $harvest->longitude ?? $farmerProfile->longitude ?? '112.5273') }}">
        </div>

        <!-- Bagian 3: Catatan -->
        <div class="space-y-4 pt-2">
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100">
                <i data-lucide="file-text" class="w-5 h-5 text-emerald-600"></i>
                <span>Catatan Hasil Panen (Opsional)</span>
            </h2>
            <div>
                <textarea id="notes" name="notes" rows="3" 
                          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition">{{ old('notes', $harvest->notes) }}</textarea>
            </div>
        </div>

        <!-- Submit Bar -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('farmer.harvests.index') }}" class="px-6 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                Batal
            </a>
            <button type="submit" class="px-8 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 transition flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    let editMap, editMarker;
    let latestAddressString = '';
    let geocodeTimeout = null;

    document.addEventListener('DOMContentLoaded', function() {
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

        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const locationInput = document.getElementById('location');
        const statusEl = document.getElementById('map-readable-status');

        let initialLat = parseFloat(latInput.value) || -7.8712;
        let initialLng = parseFloat(lngInput.value) || 112.5273;

        editMap = L.map('harvest-edit-map', {
            center: [initialLat, initialLng],
            zoom: 14,
            scrollWheelZoom: false
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(editMap);

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

        editMarker = L.marker([initialLat, initialLng], {
            draggable: true,
            icon: farmIcon
        }).addTo(editMap);

        const currentLoc = locationInput.value.trim() || 'Lokasi Kebun Panen';
        editMarker.bindPopup(`<b>Lokasi Kebun Panen</b><br><span style="font-size:11px;color:#334155;">${currentLoc}</span>`).openPopup();

        // Reverse Geocode: when marker moves, update lat/lng and sync address text
        function onMarkerMoved(lat, lng, autoUpdateInput = true) {
            latInput.value = parseFloat(lat).toFixed(7);
            lngInput.value = parseFloat(lng).toFixed(7);

            if (statusEl) {
                statusEl.innerHTML = '<span class="text-amber-700 animate-pulse font-semibold">📍 Mengambil alamat dari titik pin peta...</span>';
            }

            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.display_name) {
                        latestAddressString = data.display_name;
                        if (statusEl) {
                            statusEl.innerHTML = '<span class="text-emerald-900 font-medium">Area terdeteksi: <strong>' + data.display_name + '</strong></span>';
                        }
                        if (autoUpdateInput && locationInput) {
                            locationInput.value = data.display_name;
                        }
                        editMarker.bindPopup(`<b>Lokasi Kebun Panen</b><br><span style="font-size:11px;color:#334155;">${data.display_name}</span>`).openPopup();
                    } else if (statusEl) {
                        statusEl.textContent = 'Pin lokasi lahan terpasang di koordinat: ' + parseFloat(lat).toFixed(5) + ', ' + parseFloat(lng).toFixed(5);
                    }
                })
                .catch(() => {
                    if (statusEl) {
                        statusEl.textContent = 'Pin lokasi lahan terpasang di koordinat: ' + parseFloat(lat).toFixed(5) + ', ' + parseFloat(lng).toFixed(5);
                    }
                });
        }

        // Marker dragend event (auto updates address text)
        editMarker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            onMarkerMoved(pos.lat, pos.lng, true);
        });

        // Map click event (moves pin and auto updates address text)
        editMap.on('click', function(e) {
            editMarker.setLatLng(e.latlng);
            onMarkerMoved(e.latlng.lat, e.latlng.lng, true);
        });

        // Forward Geocode: when user types/changes location input, move pin on map
        if (locationInput) {
            locationInput.addEventListener('input', function() {
                clearTimeout(geocodeTimeout);
                const query = this.value.trim();
                if (query.length < 4) return;

                if (statusEl) {
                    statusEl.innerHTML = '<span class="text-blue-700 animate-pulse font-semibold">🔍 Menyesuaikan pin peta dengan alamat baru...</span>';
                }

                geocodeTimeout = setTimeout(function() {
                    geocodeAddressToMap(query);
                }, 800);
            });

            locationInput.addEventListener('change', function() {
                const query = this.value.trim();
                if (query.length >= 3) {
                    geocodeAddressToMap(query);
                }
            });
        }

        function geocodeAddressToMap(query) {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`)
                .then(res => res.json())
                .then(results => {
                    if (results && results.length > 0) {
                        const place = results[0];
                        const lat = parseFloat(place.lat);
                        const lon = parseFloat(place.lon);

                        latInput.value = lat.toFixed(7);
                        lngInput.value = lon.toFixed(7);

                        editMap.flyTo([lat, lon], 15);
                        editMarker.setLatLng([lat, lon]);
                        editMarker.bindPopup(`<b>Lokasi Kebun Disesuaikan</b><br><span style="font-size:11px;color:#334155;">${place.display_name}</span>`).openPopup();

                        if (statusEl) {
                            statusEl.innerHTML = '<span class="text-emerald-900 font-medium">📍 Pin disinkronkan: <strong>' + place.display_name + '</strong></span>';
                        }
                    }
                })
                .catch(() => {});
        }

        setTimeout(function() {
            editMap.invalidateSize();
        }, 350);
    });

    window.detectFarmerGPS = function() {
        if (!navigator.geolocation) {
            alert('Browser perangkat Anda belum mendukung deteksi lokasi GPS.');
            return;
        }

        const statusEl = document.getElementById('map-readable-status');
        if (statusEl) {
            statusEl.innerHTML = '<span class="text-amber-700 animate-pulse font-semibold">Mencari sinyal GPS perangkat...</span>';
        }

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                if (editMap && editMarker) {
                    editMap.flyTo([lat, lng], 16);
                    editMarker.setLatLng([lat, lng]);
                }

                document.getElementById('latitude').value = parseFloat(lat).toFixed(7);
                document.getElementById('longitude').value = parseFloat(lng).toFixed(7);

                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.display_name) {
                            latestAddressString = data.display_name;
                            if (statusEl) {
                                statusEl.innerHTML = '<span class="text-emerald-900 font-medium">Area kebun (GPS): <strong>' + data.display_name + '</strong></span>';
                            }
                            const locationInput = document.getElementById('location');
                            if (locationInput) {
                                locationInput.value = data.display_name;
                            }
                            if (editMarker) {
                                editMarker.bindPopup(`<b>Lokasi Kebun (GPS)</b><br><span style="font-size:11px;color:#334155;">${data.display_name}</span>`).openPopup();
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
                alert('Gagal mendeteksi lokasi GPS: ' + error.message);
                if (statusEl) {
                    statusEl.textContent = 'Silakan geser pin pada peta untuk menentukan lokasi.';
                }
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    };

    window.searchLocationOnMap = function() {
        const query = document.getElementById('map-search-query').value.trim();
        const msgEl = document.getElementById('search-status-message');
        const statusEl = document.getElementById('map-readable-status');
        const locationInput = document.getElementById('location');

        if (!query) {
            alert('Silakan ketik nama desa, kecamatan, atau kota kebun Anda terlebih dahulu.');
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

                    if (editMap && editMarker) {
                        editMap.flyTo([lat, lon], 15);
                        editMarker.setLatLng([lat, lon]);
                        editMarker.bindPopup(`<b>${place.display_name}</b>`).openPopup();
                    }

                    document.getElementById('latitude').value = lat.toFixed(7);
                    document.getElementById('longitude').value = lon.toFixed(7);

                    latestAddressString = place.display_name;
                    if (locationInput) {
                        locationInput.value = place.display_name;
                    }
                    if (statusEl) {
                        statusEl.innerHTML = '<span class="text-emerald-900 font-medium">Area terpilih: <strong>' + place.display_name + '</strong></span>';
                    }

                    if (msgEl) {
                        msgEl.textContent = '✓ Lokasi ditemukan dan pin telah disinkronkan: ' + place.display_name;
                    }
                } else {
                    if (msgEl) {
                        msgEl.textContent = 'Lokasi tidak ditemukan. Silakan coba nama kecamatan atau kota terdekat.';
                    }
                }
            })
            .catch(() => {
                if (msgEl) {
                    msgEl.textContent = 'Gagal menghubungi layanan pencarian peta.';
                }
            });
    };

    window.applyDetectedAddress = function() {
        const locationInput = document.getElementById('location');
        if (latestAddressString && locationInput) {
            locationInput.value = latestAddressString;
        } else {
            alert('Silakan geser pin pada peta untuk mendeteksi alamat kebun terlebih dahulu.');
        }
    };
</script>
@endpush
