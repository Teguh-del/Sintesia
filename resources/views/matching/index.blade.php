@extends('layouts.dashboard')

@section('title', 'SINTESA Match - Pencocokan Cerdas Hasil Pertanian')

@section('content')
<div class="space-y-8">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-emerald-950 via-teal-900 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 relative z-10">
            <div class="max-w-3xl space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-bold">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-400"></i>
                    <span>Fitur Utama — Inovasi Algoritma Weighted Scoring</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">SINTESA Match</h1>
                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                    Algoritma pencocokan cerdas yang mengevaluasi basis data petani, stok riil, daya saing harga, dan jarak geolokasi untuk menemukan produsen pertanian yang paling optimal untuk kebutuhan bisnis Anda.
                </p>
            </div>
            <a href="{{ route('dashboard') }}" class="self-start inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/20 text-xs font-bold transition backdrop-blur-md shrink-0">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Dashboard</span>
            </a>
        </div>
    </div>

    <!-- Algorithm Weight Breakdown Card -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-5 sm:p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="sliders" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Formulasi Pembobotan Multi-Kriteria (PRD Standar)</h3>
                    <p class="text-[11px] text-slate-500">Kalkulasi matematis transparan dari data MySQL nyata</p>
                </div>
            </div>
            <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-lg">Total Bobot: 100%</span>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            <div class="p-3.5 bg-emerald-50/60 rounded-2xl border border-emerald-100/80">
                <div class="flex items-center justify-between mb-1">
                    <span class="font-bold text-emerald-900">Kesesuaian Komoditas</span>
                    <span class="font-black text-emerald-700 text-sm">35%</span>
                </div>
                <p class="text-[11px] text-emerald-800/80">Evaluasi jenis & varietas komoditas terhadap permintaan</p>
            </div>
            <div class="p-3.5 bg-teal-50/60 rounded-2xl border border-teal-100/80">
                <div class="flex items-center justify-between mb-1">
                    <span class="font-bold text-teal-900">Ketersediaan Stok</span>
                    <span class="font-black text-teal-700 text-sm">25%</span>
                </div>
                <p class="text-[11px] text-teal-800/80">Rasio stok fisik riil di kebun terhadap kuantitas yang dicari</p>
            </div>
            <div class="p-3.5 bg-blue-50/60 rounded-2xl border border-blue-100/80">
                <div class="flex items-center justify-between mb-1">
                    <span class="font-bold text-blue-900">Kesesuaian Harga</span>
                    <span class="font-black text-blue-700 text-sm">20%</span>
                </div>
                <p class="text-[11px] text-blue-800/80">Tingkat efisiensi harga jual petani terhadap batas anggaran</p>
            </div>
            <div class="p-3.5 bg-indigo-50/60 rounded-2xl border border-indigo-100/80">
                <div class="flex items-center justify-between mb-1">
                    <span class="font-bold text-indigo-900">Kedekatan Lokasi</span>
                    <span class="font-black text-indigo-700 text-sm">20%</span>
                </div>
                <p class="text-[11px] text-indigo-800/80">Jarak tempuh Haversine radius km kebun petani ke pembeli</p>
            </div>
        </div>
    </div>

    <!-- Interactive Parameter Input Form -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form method="GET" action="{{ route('matching.index') }}" class="space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="filter" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Tentukan Kriteria Pengadaan Komoditas</h2>
                    <p class="text-xs text-slate-500">Sesuaikan parameter untuk menemukan petani dengan peringkat kecocokan terbaik</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- 1. Commodity -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Komoditas Target <span class="text-rose-500">*</span>
                    </label>
                    <select name="commodity_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        @foreach($commodities as $c)
                            <option value="{{ $c->id }}" {{ $criteria['commodity_id'] == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->category }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Quantity Needed -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Volume Kebutuhan <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" step="any" min="1" name="quantity" value="{{ $criteria['quantity'] }}" required 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">kg/satuan</span>
                    </div>
                </div>

                <!-- 3. Maximum Budget Price -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Batas Harga Maksimal <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                        <input type="number" min="1" name="max_price" value="{{ $criteria['max_price'] }}" required 
                               class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                </div>

                <!-- 4. Location / Destination -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Lokasi Penerimaan / Wilayah
                    </label>
                    <input type="text" name="location_name" value="{{ $criteria['location_name'] }}" 
                           placeholder="Contoh: Sleman, Bantul, Kediri"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>

            <!-- Hidden Coords -->
            <input type="hidden" id="geo-lat" name="latitude" value="{{ $criteria['latitude'] }}">
            <input type="hidden" id="geo-lon" name="longitude" value="{{ $criteria['longitude'] }}">

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <i data-lucide="map-pin" class="w-4 h-4 text-emerald-600"></i>
                    <span>Koordinat Titik Hitung: <strong>{{ number_format($criteria['latitude'], 4) }}, {{ number_format($criteria['longitude'], 4) }}</strong></span>
                    <button type="button" onclick="detectGPS()" class="text-emerald-600 hover:text-emerald-700 font-bold underline ml-1">Deteksi GPS Saya</button>
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <a href="{{ route('matching.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition text-center flex-1 sm:flex-none">
                        Reset
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2 flex-1 sm:flex-none">
                        <i data-lucide="sparkles" class="w-4 h-4 text-amber-300"></i>
                        <span>Hitung Rekomendasi SINTESA Match</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Match Candidates Results Section -->
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Hasil Pemeringkatan Kecocokan Petani</h2>
                <p class="text-xs text-slate-500">Diurutkan berdasarkan skor tertinggi dari evaluasi multi-kriteria secara real-time</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-600 bg-white px-3 py-1.5 rounded-xl border border-slate-200">
                    {{ $summary['total_candidates'] }} Kandidat Ditemukan
                </span>
                @if($summary['best_score'] > 0)
                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200">
                        Skor Terbaik: {{ $summary['best_score'] }}%
                    </span>
                @endif
            </div>
        </div>

        @if($matches->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center max-w-lg mx-auto space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto">
                    <i data-lucide="search-x" class="w-8 h-8"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Tidak Ada Produk yang Cocok</h3>
                    <p class="text-xs text-slate-500 mt-1">Belum ada petani dengan produk aktif yang mendekati kriteria komoditas dan batas anggaran Anda saat ini.</p>
                </div>
                <div class="flex justify-center gap-3">
                    <a href="{{ route('requests.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold transition">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span>Publikasikan Permintaan Pasokan</span>
                    </a>
                </div>
            </div>
        @else
            <div class="space-y-5">
                @foreach($matches as $index => $match)
                    @php
                        $p = $match['product'];
                        $farmer = $match['farmer'];
                        $scores = $match['scores'];
                        $isTop = $index === 0;
                    @endphp
                    <div class="bg-white rounded-3xl border {{ $isTop ? 'border-emerald-400 ring-4 ring-emerald-500/10' : 'border-slate-200 hover:border-slate-300' }} shadow-sm hover:shadow-md p-6 sm:p-7 transition relative overflow-hidden">
                        @if($isTop)
                            <div class="absolute top-0 right-0">
                                <span class="bg-gradient-to-l from-emerald-600 to-teal-600 text-white text-[10px] font-extrabold uppercase tracking-wider py-1 px-4 rounded-bl-2xl shadow-sm flex items-center gap-1">
                                    <i data-lucide="award" class="w-3.5 h-3.5 text-amber-300"></i>
                                    <span>Rekomendasi Utama #1</span>
                                </span>
                            </div>
                        @endif

                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-slate-100">
                            <!-- Left: Product & Farmer Info -->
                            <div class="flex items-start gap-4">
                                <img src="{{ $p->primary_image_url }}" alt="{{ $p->name }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover border border-slate-200 flex-shrink-0">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
                                            {{ $p->commodity->name }}
                                        </span>
                                        <span class="text-xs text-slate-400">•</span>
                                        <span class="text-xs font-semibold text-slate-500">Peringkat #{{ $index + 1 }}</span>
                                    </div>
                                    <h3 class="text-lg font-bold text-slate-900">
                                        <a href="{{ route('marketplace.show', $p->slug) }}" class="hover:text-emerald-600 transition">
                                            {{ $p->name }}
                                        </a>
                                    </h3>
                                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-2 flex-wrap">
                                        <span>Petani: <strong class="text-slate-800">{{ $farmer->name }}</strong> ({{ $farmer->farmerProfile->farm_name ?? 'Kebun Petani' }})</span>
                                        <span class="text-slate-300">•</span>
                                        <span class="flex items-center gap-1 text-slate-600">
                                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                                            <span>{{ $p->location }} (<strong>{{ $match['distance_km'] }} km</strong>)</span>
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <!-- Right: Match Score Card -->
                            <div class="flex items-center gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100/90 min-w-[200px] justify-between">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Match Score</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold border mt-1 {{ $match['category_badge'] }}">
                                        {{ $match['category'] }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <div class="flex items-baseline justify-end gap-0.5">
                                        <span class="text-3xl font-black text-slate-900 tracking-tight">{{ $match['match_score'] }}</span>
                                        <span class="text-xs font-bold text-slate-400">%</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400">Weighted Total</span>
                                </div>
                            </div>
                        </div>

                        <!-- 4-Subscore Progress Bars -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 py-5 border-b border-slate-100 text-xs">
                            <!-- 1. Commodity -->
                            <div class="space-y-1.5">
                                <div class="flex justify-between items-center text-[11px]">
                                    <span class="text-slate-500 font-semibold">Komoditas (35%)</span>
                                    <span class="font-bold text-emerald-700">{{ $scores['commodity'] }}/100</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $scores['commodity'] }}%"></div>
                                </div>
                                <p class="text-[10px] text-slate-400">{{ $match['is_exact_commodity'] ? 'Komoditas Persis' : 'Kategori Sejenis' }}</p>
                            </div>

                            <!-- 2. Stock -->
                            <div class="space-y-1.5">
                                <div class="flex justify-between items-center text-[11px]">
                                    <span class="text-slate-500 font-semibold">Stok Riil (25%)</span>
                                    <span class="font-bold text-teal-700">{{ $scores['stock'] }}/100</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-teal-600 h-2 rounded-full" style="width: {{ $scores['stock'] }}%"></div>
                                </div>
                                <p class="text-[10px] text-slate-400">Tersedia {{ number_format($p->stock, 0, ',', '.') }} {{ $p->unit }} (Min: {{ $criteria['quantity'] }})</p>
                            </div>

                            <!-- 3. Price -->
                            <div class="space-y-1.5">
                                <div class="flex justify-between items-center text-[11px]">
                                    <span class="text-slate-500 font-semibold">Kesesuaian Harga (20%)</span>
                                    <span class="font-bold text-blue-700">{{ $scores['price'] }}/100</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $scores['price'] }}%"></div>
                                </div>
                                <p class="text-[10px] text-slate-400">Rp {{ number_format($p->price, 0, ',', '.') }}/{{ $p->unit }} (Budget: {{ number_format($criteria['max_price'], 0, ',', '.') }})</p>
                            </div>

                            <!-- 4. Distance -->
                            <div class="space-y-1.5">
                                <div class="flex justify-between items-center text-[11px]">
                                    <span class="text-slate-500 font-semibold">Kedekatan Jarak (20%)</span>
                                    <span class="font-bold text-indigo-700">{{ $scores['distance'] }}/100</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $scores['distance'] }}%"></div>
                                </div>
                                <p class="text-[10px] text-slate-400">Radius {{ $match['distance_km'] }} km dari lokasi Anda</p>
                            </div>
                        </div>

                        <!-- Card Footer Action Buttons -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-4">
                            <div class="text-xs text-slate-500">
                                <span>Harga Katalog: <strong class="text-slate-900 font-bold">Rp {{ number_format($p->price, 0, ',', '.') }}</strong> / {{ $p->unit }}</span>
                                <span class="mx-1 text-slate-300">•</span>
                                <span>Estimasi Total: <strong class="text-emerald-700 font-bold">Rp {{ number_format($p->price * $criteria['quantity'], 0, ',', '.') }}</strong></span>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('marketplace.show', $p->slug) }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                                    Lihat Produk
                                </a>
                                @if($p->allow_negotiation)
                                    <a href="{{ route('marketplace.show', $p->slug) }}#nego-modal" class="px-4 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 text-xs font-bold transition flex items-center gap-1.5">
                                        <i data-lucide="handshake" class="w-3.5 h-3.5 text-amber-600"></i>
                                        <span>Ajukan Nego</span>
                                    </a>
                                @endif
                                <a href="{{ route('marketplace.show', $p->slug) }}#order-modal" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5">
                                    <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                                    <span>Beli Sekarang</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
    function detectGPS() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('geo-lat').value = position.coords.latitude;
                document.getElementById('geo-lon').value = position.coords.longitude;
                alert('GPS berhasil dideteksi: ' + position.coords.latitude.toFixed(4) + ', ' + position.coords.longitude.toFixed(4) + '. Klik tombol "Hitung Rekomendasi" untuk menghitung ulang.');
            }, function(error) {
                alert('Tidak dapat mendeteksi GPS: ' + error.message);
            });
        } else {
            alert('Browser Anda tidak mendukung geolokasi.');
        }
    }
</script>
@endsection
