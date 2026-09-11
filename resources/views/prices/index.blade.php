@extends('layouts.dashboard')

@section('title', 'Tren Harga Komoditas Pasar — SINTESA')

@section('content')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="space-y-8">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-emerald-950 via-teal-900 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div class="max-w-2xl space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-bold">
                    <i data-lucide="line-chart" class="w-3.5 h-3.5 text-amber-400"></i>
                    <span>Analitik Pasar & Informasi Harga</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Tren Harga Komoditas Pertanian</h1>
                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                    Pantau pergerakan harga pasar acuan, riwayat perubahan berkala, dan analisis tren komoditas untuk transparansi niaga dan penentuan harga yang adil.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white border border-white/20 text-xs font-bold transition backdrop-blur-md shrink-0">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Kembali ke Dashboard</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Overview Commodity Carousel / Cards -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Ringkasan Harga Acuan Terkini</h3>
                <p class="text-xs text-slate-500">Harga rata-rata pasar per komoditas utama</p>
            </div>
            <span class="text-xs font-bold text-slate-400 bg-slate-100 px-3 py-1 rounded-lg">5 Komoditas Utama</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($overviewCards as $cCard)
                <a href="{{ route('prices.index', ['commodity_id' => $cCard['id'], 'days' => $days, 'location' => $location]) }}" 
                   class="p-4 rounded-2xl border transition text-left flex flex-col justify-between {{ $cCard['id'] == $commodityId ? 'bg-emerald-50/80 border-emerald-400 shadow-sm ring-2 ring-emerald-500/20' : 'bg-white border-slate-200/90 hover:border-slate-300 shadow-sm' }}">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold {{ $cCard['id'] == $commodityId ? 'text-emerald-900' : 'text-slate-700' }}">{{ $cCard['name'] }}</span>
                        <div class="w-7 h-7 rounded-lg {{ $cCard['id'] == $commodityId ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600' }} flex items-center justify-center">
                            <i data-lucide="{{ $cCard['icon'] }}" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div>
                        <div class="text-lg font-black text-slate-900">{{ $cCard['formatted_price'] }}</div>
                        <div class="flex items-center justify-between mt-1 text-[11px] font-semibold">
                            <span class="text-slate-400">/ {{ $cCard['unit'] }}</span>
                            <span class="{{ $cCard['trend_color'] }} flex items-center gap-0.5">
                                @if($cCard['change'] > 0) +@endif{{ $cCard['change_pct'] }}%
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Main Chart & KPI Section -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-sm space-y-6">
        <!-- Interactive Controls Bar -->
        <form method="GET" action="{{ route('prices.index') }}" id="price-filter-form" class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div class="flex flex-wrap items-center gap-3">
                <!-- Commodity Selector -->
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Pilih Komoditas</label>
                    <select name="commodity_id" onchange="this.form.submit()" class="px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        @foreach($commodities as $c)
                            <option value="{{ $c->id }}" {{ $c->id == $commodityId ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->category }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Location Selector -->
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Wilayah / Pasar</label>
                    <select name="location" onchange="this.form.submit()" class="px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="">Semua Wilayah</option>
                        @foreach($availableLocations as $loc)
                            <option value="{{ $loc }}" {{ $location == $loc ? 'selected' : '' }}>
                                {{ $loc }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Days Range Selector (7 / 30 / 90 Days) -->
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Rentang Waktu</label>
                <div class="inline-flex rounded-xl bg-slate-100 p-1">
                    @foreach([7 => '7 Hari', 30 => '30 Hari', 90 => '90 Hari'] as $num => $label)
                        <button type="submit" name="days" value="{{ $num }}" 
                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $days == $num ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </form>

        <!-- KPI Statistic Cards Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Latest Price -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-xs text-slate-500 block mb-1">Harga Terkini</span>
                <div class="text-xl sm:text-2xl font-black text-slate-900">
                    Rp {{ number_format($analytics['latest_price'], 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-slate-400 font-semibold">per {{ $analytics['commodity']->unit }}</span>
            </div>

            <!-- 2. Highest Price -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-xs text-slate-500 block mb-1">Harga Tertinggi ({{ $days }} Hari)</span>
                <div class="text-xl sm:text-2xl font-black text-slate-900">
                    Rp {{ number_format($analytics['highest_price'], 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-emerald-600 font-semibold">Batas Puncak Pasar</span>
            </div>

            <!-- 3. Lowest Price -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-xs text-slate-500 block mb-1">Harga Terendah ({{ $days }} Hari)</span>
                <div class="text-xl sm:text-2xl font-black text-slate-900">
                    Rp {{ number_format($analytics['lowest_price'], 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-teal-600 font-semibold">Batas Dasar Pasar</span>
            </div>

            <!-- 4. Price Change & Trend -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-slate-500">Tren Pergerakan</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $analytics['trend_badge'] }}">
                        {{ $analytics['trend'] }}
                    </span>
                </div>
                <div class="text-xl sm:text-2xl font-black {{ $analytics['trend_color'] }} flex items-center gap-1">
                    <i data-lucide="{{ $analytics['trend_icon'] }}" class="w-5 h-5"></i>
                    <span>@if($analytics['change_nominal'] > 0)+@endif{{ $analytics['change_percentage'] }}%</span>
                </div>
                <span class="text-[11px] text-slate-500 font-semibold">
                    @if($analytics['change_nominal'] > 0)+@endif Rp {{ number_format($analytics['change_nominal'], 0, ',', '.') }} dalam {{ $days }} hari
                </span>
            </div>
        </div>

        <!-- Chart.js Line Chart Canvas Container -->
        <div class="pt-4">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-bold text-slate-800">Kurva Pergerakan Harga: {{ $analytics['commodity']->name }}</h4>
                <div class="flex items-center gap-3 text-xs text-slate-500">
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-emerald-600 inline-block"></span>
                        <span>Harga Pasar (Rp/{{ $analytics['commodity']->unit }})</span>
                    </span>
                </div>
            </div>

            <div class="w-full h-80 sm:h-96 relative">
                <canvas id="commodityPriceChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Historical Recorded Price Table -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-base font-bold text-slate-900">Riwayat Catatan Harga Pasar</h3>
                <p class="text-xs text-slate-500">Data entri historis pencatatan harga acuan komoditas</p>
            </div>
            <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-lg">
                {{ $analytics['records']->count() }} Entri Data
            </span>
        </div>

        @if($analytics['records']->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                            <th class="pb-3 px-3">Tanggal Catat</th>
                            <th class="pb-3 px-3">Komoditas</th>
                            <th class="pb-3 px-3">Harga Acuan</th>
                            <th class="pb-3 px-3">Wilayah / Pasar</th>
                            <th class="pb-3 px-3">Sumber Data</th>
                            <th class="pb-3 px-3">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($analytics['records'] as $record)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-3 font-bold text-slate-900">
                                {{ \Carbon\Carbon::parse($record->recorded_date)->translatedFormat('d M Y') }}
                            </td>
                            <td class="py-3 px-3 font-semibold text-slate-800">
                                {{ $analytics['commodity']->name }}
                            </td>
                            <td class="py-3 px-3 font-black text-emerald-700">
                                Rp {{ number_format($record->price, 0, ',', '.') }} <span class="text-slate-400 font-normal">/ {{ $record->unit }}</span>
                            </td>
                            <td class="py-3 px-3 text-slate-600">
                                {{ $record->location }}
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                    {{ $record->source ?? 'Survei Pasar' }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-slate-400 max-w-xs truncate">
                                {{ $record->notes ?: '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-12 text-center text-slate-400">
                <i data-lucide="database-zap" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                <h4 class="text-sm font-bold text-slate-700">Belum Ada Riwayat Tercatat</h4>
                <p class="text-xs text-slate-400 mt-1">Data historis belum tersedia untuk filter yang dipilih.</p>
            </div>
        @endif
    </div>
</div>

<!-- Chart.js Render Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const labels = @json($analytics['chart_labels']);
        const prices = @json($analytics['chart_prices']);
        const commodityName = @json($analytics['commodity']->name);
        const unit = @json($analytics['commodity']->unit);

        const ctx = document.getElementById('commodityPriceChart').getContext('2d');

        // Create gradient fill
        const gradient = ctx.createLinearGradient(0, 0, 0, 350);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.35)');
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: `Harga ${commodityName} (Rp/${unit})`,
                    data: prices,
                    borderColor: '#059669',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#047857',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: labels.length > 20 ? 3 : 5,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + Number(context.parsed.y).toLocaleString('id-ID') + ' / ' + unit;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 11 },
                            color: '#94a3b8',
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 10,
                        }
                    },
                    y: {
                        grid: {
                            color: '#f1f5f9',
                        },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 11 },
                            color: '#94a3b8',
                            callback: function(value) {
                                return 'Rp ' + Number(value).toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
