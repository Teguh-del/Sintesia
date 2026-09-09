@extends('layouts.dashboard')

@section('title', 'Peta Sebaran Petani & Komoditas — SINTESA')

@section('content')
<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<style>
    .leaflet-popup-content-wrapper {
        border-radius: 1.25rem;
        padding: 0;
        overflow: hidden;
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        border: 1px solid #e2e8f0;
    }
    .leaflet-popup-content {
        margin: 0;
        line-height: 1.5;
    }
    .leaflet-container {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .custom-marker-icon {
        background: transparent;
        border: none;
    }
</style>

<div class="space-y-6">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-emerald-950 via-teal-900 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div class="max-w-2xl space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-bold">
                    <i data-lucide="map" class="w-3.5 h-3.5 text-amber-400"></i>
                    <span>Phase 7 — Geospasial Pertanian Cerdas</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Peta Sebaran Petani & Komoditas</h1>
                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                    Eksplorasi titik lahan pertanian, komoditas unggulan panen, dan ketersediaan stok riil langsung dari database mitra petani terdaftar.
                </p>
            </div>

            <!-- KPI Metric Badges -->
            <div class="flex items-center gap-3">
                <div class="px-4 py-3 rounded-2xl bg-white/10 backdrop-blur-md border border-white/10 text-center min-w-[100px]">
                    <span class="text-2xl font-black text-emerald-400 block">{{ $summary['total_farmers'] }}</span>
                    <span class="text-[10px] text-slate-300 uppercase tracking-wider font-bold">Titik Lahan</span>
                </div>
                <div class="px-4 py-3 rounded-2xl bg-white/10 backdrop-blur-md border border-white/10 text-center min-w-[100px]">
                    <span class="text-2xl font-black text-amber-400 block">{{ $summary['total_commodities'] }}</span>
                    <span class="text-[10px] text-slate-300 uppercase tracking-wider font-bold">Komoditas</span>
                </div>
                <div class="px-4 py-3 rounded-2xl bg-white/10 backdrop-blur-md border border-white/10 text-center min-w-[110px]">
                    <span class="text-2xl font-black text-teal-300 block">{{ number_format($summary['total_stock'], 0) }}</span>
                    <span class="text-[10px] text-slate-300 uppercase tracking-wider font-bold">Total Stok (kg)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-5 shadow-sm">
        <form method="GET" action="{{ route('maps.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <!-- Commodity Filter -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                    Filter Komoditas
                </label>
                <select name="commodity_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">Semua Komoditas</option>
                    @foreach($commodities as $c)
                        <option value="{{ $c->id }}" {{ ($filters['commodity_id'] == $c->id) ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->category }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Location Filter -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                    Wilayah Sentra Produksi
                </label>
                <select name="location" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">Semua Wilayah</option>
                    @foreach($locations as $key => $label)
                        <option value="{{ $key }}" {{ (strtolower($filters['location'] ?? '') == strtolower($key)) ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-1.5">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Terapkan Filter</span>
                </button>
                <a href="{{ route('maps.index') }}" class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Map & Sidebar Split Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Interactive Leaflet Map (Col 3/4) -->
        <div class="lg:col-span-3 bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm flex flex-col">
            <div class="p-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-2 text-xs font-bold text-slate-700">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Peta Interaktif OpenStreetMap (Leaflet.js)</span>
                </div>
                <span class="text-[11px] text-slate-400 font-semibold">
                    Klik marker untuk melihat detail kebun & produk
                </span>
            </div>

            <div id="agricultural-map" class="w-full h-[580px] z-0"></div>
        </div>

        <!-- Sidebar Farmer Points List (Col 1/4) -->
        <div class="lg:col-span-1 bg-white rounded-3xl border border-slate-200 p-5 shadow-sm flex flex-col max-h-[640px]">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900">Daftar Titik Lahan</h3>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
                    {{ $markers->count() }} Lahan
                </span>
            </div>

            <div class="flex-1 overflow-y-auto space-y-3 pr-1 divide-y divide-slate-100">
                @forelse($markers as $marker)
                    <div class="pt-3 first:pt-0 cursor-pointer group hover:bg-emerald-50/50 p-2 rounded-xl transition" onclick="zoomToMarker({{ $marker['latitude'] }}, {{ $marker['longitude'] }}, {{ $marker['id'] }})">
                        <div class="flex items-start justify-between gap-1 mb-1">
                            <h4 class="text-xs font-bold text-slate-900 group-hover:text-emerald-700 transition line-clamp-1">
                                {{ $marker['farm_name'] }}
                            </h4>
                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded flex-shrink-0">
                                {{ $marker['primary_commodity'] }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 truncate mb-1.5">
                            {{ $marker['name'] }} • {{ $marker['address'] }}
                        </p>
                        <div class="flex items-center justify-between text-[11px] font-semibold text-slate-600">
                            <span>Stok: <strong class="text-slate-900">{{ number_format($marker['total_stock'], 0) }} kg</strong></span>
                            <span class="text-emerald-700 font-bold">{{ $marker['formatted_price_range'] }}</span>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400">
                        <i data-lucide="map-pin-off" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                        <p class="text-xs">Tidak ada lahan ditemukan dengan filter ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Map Script Initialization -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rawMarkers = @json($markers);

        // Center map around first marker or default to Central/East Java center (-7.80, 111.00)
        const defaultLat = rawMarkers.length > 0 ? rawMarkers[0].latitude : -7.8000;
        const defaultLng = rawMarkers.length > 0 ? rawMarkers[0].longitude : 110.8000;

        const map = L.map('agricultural-map').setView([defaultLat, defaultLng], 9);

        // OpenStreetMap Tile Layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors | SINTESA'
        }).addTo(map);

        // Custom Agricultural Icon SVG
        const customSvgIcon = L.divIcon({
            className: 'custom-marker-icon',
            html: `
                <div class="relative flex items-center justify-center w-10 h-10 -ml-2 -mt-2">
                    <span class="absolute w-8 h-8 rounded-full bg-emerald-500/30 animate-ping"></span>
                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-emerald-600 to-teal-500 text-white shadow-lg flex items-center justify-center border-2 border-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 20h10"></path>
                            <path d="M10 20c0-3 2-4 2-8"></path>
                            <path d="M9.5 9.4c1.1.8 1.8 2.2 2.3 3.7-2 .4-3.5.4-4.8-.3-1.2-.6-2.3-1.9-3-4.2 2.8-.5 4.4 0 5.5.8z"></path>
                            <path d="M14.1 6a7 7 0 0 0-1.1 4c1.9-.1 3.3-.6 4.3-1.4 1-1 1.6-2.3 1.7-4.6-2.7.1-4.3.9-4.9 2z"></path>
                        </svg>
                    </div>
                </div>
            `,
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        });

        const markersById = {};
        const bounds = [];

        rawMarkers.forEach(farmer => {
            const lat = parseFloat(farmer.latitude);
            const lng = parseFloat(farmer.longitude);

            if (isNaN(lat) || isNaN(lng)) return;

            bounds.push([lat, lng]);

            // Construct rich interactive popup HTML
            let productsHtml = '';
            if (farmer.products && farmer.products.length > 0) {
                farmer.products.slice(0, 3).forEach(p => {
                    productsHtml += `
                        <div class="flex items-center justify-between gap-3 p-2 rounded-xl bg-slate-50 border border-slate-100 mb-1.5 text-xs">
                            <div class="flex items-center gap-2 truncate">
                                <img src="${p.image}" class="w-8 h-8 rounded-lg object-cover border border-slate-200 flex-shrink-0" alt="${p.name}">
                                <div class="truncate">
                                    <h6 class="font-bold text-slate-800 truncate text-[11px]">${p.name}</h6>
                                    <span class="text-[10px] text-emerald-700 font-bold">${p.formatted_price}/${p.unit}</span>
                                </div>
                            </div>
                            <a href="${p.detail_url}" class="px-2 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] flex-shrink-0 shadow-sm transition">
                                Beli
                            </a>
                        </div>
                    `;
                });
            } else {
                productsHtml = `<p class="text-xs text-slate-400 italic">Belum ada produk aktif saat ini.</p>`;
            }

            const popupContent = `
                <div class="w-72 sm:w-80">
                    <div class="bg-gradient-to-r from-emerald-800 to-teal-800 p-3.5 text-white">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider bg-emerald-700/80 px-2 py-0.5 rounded text-emerald-100">
                                ${farmer.primary_commodity}
                            </span>
                            <span class="text-[10px] text-emerald-200 font-semibold">${farmer.farm_area} Ha Lahan</span>
                        </div>
                        <h4 class="font-bold text-sm text-white">${farmer.farm_name}</h4>
                        <p class="text-[11px] text-emerald-100 flex items-center gap-1 mt-0.5">
                            <span>Petani: <strong>${farmer.name}</strong></span>
                        </p>
                    </div>

                    <div class="p-3.5 bg-white space-y-3">
                        <div class="text-[11px] text-slate-500">
                            <p class="truncate"><i class="inline-block mr-1">📍</i>${farmer.address}</p>
                            <p class="mt-1 font-bold text-slate-700">Total Stok Tersedia: <span class="text-emerald-700">${Number(farmer.total_stock).toLocaleString('id-ID')} kg</span></p>
                        </div>

                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Produk Panen Lahan</span>
                            ${productsHtml}
                        </div>

                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                            <a href="/matching?commodity_id=${farmer.products[0]?.commodity_id || ''}&location=${encodeURIComponent(farmer.address)}" class="text-emerald-700 font-bold hover:underline text-[11px]">
                                Cocokkan di SINTESA Match &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            `;

            const marker = L.marker([lat, lng], { icon: customSvgIcon })
                .addTo(map)
                .bindPopup(popupContent);

            markersById[farmer.id] = marker;
        });

        // Fit map bounds to encompass all active markers
        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [40, 40] });
        }

        // Global zoom helper called from sidebar
        window.zoomToMarker = function(lat, lng, farmerId) {
            map.setView([lat, lng], 13, { animate: true });
            if (markersById[farmerId]) {
                setTimeout(() => {
                    markersById[farmerId].openPopup();
                }, 300);
            }
        };
    });
</script>
@endsection
