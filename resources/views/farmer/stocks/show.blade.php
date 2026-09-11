@extends('layouts.dashboard')

@section('title', 'Detail Batch Stok - ' . $stock->batch_code)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .custom-farm-pin-mini {
        background-color: #059669;
        width: 30px;
        height: 30px;
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #ffffff;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2);
    }
    .custom-farm-pin-mini span {
        transform: rotate(45deg);
        font-size: 13px;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumbs -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <a href="{{ route('farmer.stocks.index') }}" class="hover:text-emerald-600 transition">Manajemen Stok</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-slate-800">{{ $stock->batch_code }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Detail Batch Stok: {{ $stock->batch_code }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('farmer.stocks.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                Kembali
            </a>
        </div>
    </div>

    <!-- Main Stock Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">{{ $stock->commodity->name ?? 'Komoditas' }}</span>
                <h2 class="text-xl font-bold text-slate-900 mt-0.5">Kode Batch: {{ $stock->batch_code }}</h2>
                <p class="text-xs text-slate-400 mt-1">Diterbitkan pada {{ $stock->created_at->translatedFormat('d F Y, H:i') }}</p>
            </div>
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold border shadow-xs {{ $stock->status_badge_color }}">
                Status: {{ $stock->status }}
            </span>
        </div>

        <!-- Inventory Metrics Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                <p class="text-xs font-semibold text-slate-400">Kuantitas Awal</p>
                <p class="text-lg font-bold text-slate-900 mt-1">{{ number_format($stock->initial_quantity, 0, ',', '.') }} {{ $stock->unit }}</p>
            </div>
            <div class="p-4 rounded-xl bg-emerald-50/60 border border-emerald-100">
                <p class="text-xs font-semibold text-emerald-700">Tersedia Saat Ini</p>
                <p class="text-lg font-black text-emerald-700 mt-1">{{ $stock->formatted_available }}</p>
            </div>
            <div class="p-4 rounded-xl bg-amber-50/60 border border-amber-100">
                <p class="text-xs font-semibold text-amber-700">Sedang Dipesan</p>
                <p class="text-lg font-bold text-amber-700 mt-1">{{ $stock->formatted_ordered }}</p>
            </div>
            <div class="p-4 rounded-xl bg-blue-50/60 border border-blue-100">
                <p class="text-xs font-semibold text-blue-700">Sudah Terjual</p>
                <p class="text-lg font-bold text-blue-700 mt-1">{{ $stock->formatted_sold }}</p>
            </div>
        </div>

        <!-- Source Harvest Detail -->
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="sprout" class="w-4 h-4 text-emerald-600"></i>
                <span>Sumber Hasil Panen</span>
            </h3>
            @if($stock->harvest)
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs text-slate-600 pt-1">
                    <div>
                        <span class="text-slate-400 block font-medium">Tanggal Panen:</span>
                        <strong class="text-slate-900">{{ $stock->harvest->harvest_date->translatedFormat('d F Y') }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Lokasi Lahan:</span>
                        <strong class="text-slate-900">{{ $stock->harvest->location }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Mutu Kualitas:</span>
                        <strong class="text-slate-900">{{ $stock->harvest->quality }}</strong>
                    </div>
                </div>

                @php
                    $hLat = $stock->harvest->latitude ?? $stock->user->farmerProfile?->latitude;
                    $hLng = $stock->harvest->longitude ?? $stock->user->farmerProfile?->longitude;
                @endphp

                @if($hLat && $hLng)
                    <div class="mt-3 space-y-2">
                        <div class="flex items-center justify-between text-xs text-emerald-800">
                            <span class="font-bold flex items-center gap-1.5">
                                <i data-lucide="map" class="w-3.5 h-3.5 text-emerald-600"></i>
                                <span>Peta Titik Lahan Kebun Sumber Panen</span>
                            </span>
                            <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-100/70 px-2 py-0.5 rounded-md">
                                Lokasi Tersimpan ✓
                            </span>
                        </div>
                        <div id="stock-harvest-map" class="w-full h-48 rounded-xl border border-slate-200 overflow-hidden relative z-10"></div>
                    </div>
                @endif

                @if($stock->harvest->notes)
                    <p class="text-xs text-slate-500 italic mt-2 bg-white p-3 rounded-xl border border-slate-200">
                        Catatan panen: "{{ $stock->harvest->notes }}"
                    </p>
                @endif
            @else
                <p class="text-xs text-slate-400 italic">Data panen sumber tidak terhubung secara langsung.</p>
            @endif
        </div>

        <!-- Linked Marketplace Products -->
        <div class="space-y-4 pt-2">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="store" class="w-4 h-4 text-emerald-600"></i>
                    <span>Produk Marketplace yang Terhubung</span>
                </h3>
                @if($stock->available_quantity > 0)
                    <a href="{{ route('farmer.products.create', ['stock_id' => $stock->id, 'commodity_id' => $stock->commodity_id, 'quality' => $stock->quality, 'stock' => $stock->available_quantity, 'unit' => $stock->unit]) }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition">
                        <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                        <span>Jual ke Marketplace</span>
                    </a>
                @endif
            </div>

            @if($stock->products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($stock->products as $p)
                        <div class="p-4 rounded-xl border border-slate-200 bg-white hover:border-emerald-500/50 shadow-xs flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3 overflow-hidden">
                                <div class="w-12 h-12 rounded-lg bg-slate-100 overflow-hidden flex-shrink-0">
                                    <img src="{{ $p->primary_image_url }}" alt="{{ $p->name }}" class="w-full h-full object-cover">
                                </div>
                                <div class="truncate">
                                    <a href="{{ route('marketplace.show', $p->slug) }}" target="_blank" class="font-bold text-slate-900 hover:text-emerald-700 text-xs truncate block">
                                        {{ $p->name }}
                                    </a>
                                    <p class="text-[11px] text-emerald-600 font-semibold mt-0.5">{{ $p->formatted_price }} / {{ $p->unit }}</p>
                                </div>
                            </div>
                            <a href="{{ route('farmer.products.edit', $p->id) }}" class="p-2 rounded-lg bg-slate-100 hover:bg-amber-50 text-slate-600 hover:text-amber-700 transition" title="Edit Produk">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-6 rounded-xl border border-dashed border-slate-300 text-center bg-slate-50/50">
                    <p class="text-xs text-slate-500 mb-2">Batch stok ini belum ditautkan ke produk jual di marketplace.</p>
                    @if($stock->available_quantity > 0)
                        <a href="{{ route('farmer.products.create', ['stock_id' => $stock->id, 'commodity_id' => $stock->commodity_id, 'quality' => $stock->quality, 'stock' => $stock->available_quantity, 'unit' => $stock->unit]) }}" 
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-sm transition">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i>
                            <span>Buat Produk Marketplace Sekarang</span>
                        </a>
                    @endif
                </div>
            @endif
        </div>
</div>
@endsection

@if(isset($hLat) && isset($hLng) && $hLat && $hLng)
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lat = {{ (float) $hLat }};
        const lng = {{ (float) $hLng }};

        const map = L.map('stock-harvest-map', {
            center: [lat, lng],
            zoom: 14,
            zoomControl: false,
            scrollWheelZoom: false,
            dragging: false
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        const farmIcon = L.divIcon({
            className: 'custom-farm-marker',
            html: `
                <div class="custom-farm-pin-mini">
                    <span>🌱</span>
                </div>
            `,
            iconSize: [30, 30],
            iconAnchor: [15, 30],
            popupAnchor: [0, -30]
        });

        const marker = L.marker([lat, lng], { icon: farmIcon }).addTo(map);
        marker.bindPopup('<b>Lokasi Kebun Sumber</b><br><span style="font-size:11px;">{{ addslashes($stock->harvest->location) }}</span>').openPopup();

        setTimeout(() => map.invalidateSize(), 300);
    });
</script>
@endpush
@endif
