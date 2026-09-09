@extends('layouts.dashboard')

@section('title', 'Dashboard Petani')
@section('header_title', 'Dashboard Petani')

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-emerald-700 to-teal-800 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-block px-3 py-1 rounded-full bg-emerald-600/60 border border-emerald-500/40 text-xs font-semibold uppercase tracking-wider mb-3">
                Mitra Petani Terdaftar
            </span>
            <h2 class="text-2xl sm:text-3xl font-black tracking-tight mb-2">
                Selamat Datang, {{ $user->name }}!
            </h2>
            <p class="text-emerald-100 text-sm leading-relaxed">
                Kelola hasil panen, pantau ketersediaan stok riil, serta pasang produk siap jual ke jaringan pembeli dan pengepul di SINTESA.
            </p>
        </div>
        <div class="absolute right-6 -bottom-6 opacity-10 hidden sm:block">
            <i data-lucide="sprout" class="w-48 h-48"></i>
        </div>
    </div>

    <!-- Farm Profile Summary Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                <i data-lucide="map-pin" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Lahan / Kelompok</p>
                <h4 class="text-base font-bold text-slate-900">{{ $user->farmerProfile->farm_name ?? 'Kebun Petani' }}</h4>
                <p class="text-xs text-slate-400 truncate max-w-[180px]">{{ $user->farmerProfile->address ?? 'Kediri' }}</p>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center flex-shrink-0">
                <i data-lucide="maximize" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Estimasi Luas Lahan</p>
                <h4 class="text-xl font-bold text-slate-900">{{ $user->farmerProfile->farm_area_hectares ?? 0 }} <span class="text-sm font-semibold text-slate-500">Ha</span></h4>
                <p class="text-xs text-emerald-600 font-medium">Status: Aktif Terverifikasi</p>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0">
                <i data-lucide="wheat" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Komoditas Utama</p>
                <h4 class="text-base font-bold text-slate-900">{{ $user->farmerProfile->primary_commodity ?? 'Jagung' }}</h4>
                <p class="text-xs text-slate-400">Dukungan 5 Komoditas</p>
            </div>
        </div>
    </div>

    <!-- Product, Harvest, Stock & Order Metrics (Real System Data) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Hasil Panen</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalHarvestQty, 0, ',', '.') }}</p>
                <a href="{{ route('farmer.harvests.index') }}" class="text-[11px] font-bold text-emerald-600 hover:underline inline-block mt-1">
                    Lihat Riwayat &rarr;
                </a>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="wheat" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Stok Riil Tersedia</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($realAvailableStock, 0, ',', '.') }}</p>
                <a href="{{ route('farmer.stocks.index') }}" class="text-[11px] font-bold text-emerald-600 hover:underline inline-block mt-1">
                    Kelola Stok &rarr;
                </a>
            </div>
            <div class="w-11 h-11 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                <i data-lucide="boxes" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pesanan Masuk</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <p class="text-2xl font-black text-amber-600">{{ $pendingOrdersCount }}</p>
                    <span class="text-xs font-semibold text-slate-400">/ {{ $totalOrdersCount }} Total</span>
                </div>
                <a href="{{ route('farmer.orders.index') }}" class="text-[11px] font-bold text-amber-600 hover:underline inline-block mt-1">
                    Kelola Pesanan &rarr;
                </a>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <i data-lucide="inbox" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Penjualan Selesai</p>
                <p class="text-xl font-black text-slate-900 mt-1">Rp {{ number_format($totalSalesRevenue, 0, ',', '.') }}</p>
                <a href="{{ route('farmer.orders.index', ['status' => 'Selesai']) }}" class="text-[11px] font-bold text-emerald-600 hover:underline inline-block mt-1">
                    Riwayat Transaksi &rarr;
                </a>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <i data-lucide="badge-check" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Recent Orders Section (Phase 4 Real Data) -->
    <div class="p-6 rounded-3xl bg-white border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="receipt" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Pesanan Masuk Terbaru</h3>
                    <p class="text-xs text-slate-500">Daftar transaksi komoditas dari pembeli yang memerlukan perhatian Anda</p>
                </div>
            </div>
            <a href="{{ route('farmer.orders.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                <span>Lihat Semua Pesanan</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        @if($recentOrders->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                            <th class="pb-3 px-3">No. Pesanan</th>
                            <th class="pb-3 px-3">Pembeli</th>
                            <th class="pb-3 px-3">Komoditas / Item</th>
                            <th class="pb-3 px-3">Total Nilai</th>
                            <th class="pb-3 px-3">Status</th>
                            <th class="pb-3 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($recentOrders as $order)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-3">
                                <span class="font-bold text-slate-900">#{{ $order->order_number }}</span>
                                <p class="text-[10px] text-slate-400">{{ $order->created_at->diffForHumans() }}</p>
                            </td>
                            <td class="py-3 px-3">
                                <span class="font-bold text-slate-800">{{ $order->buyer->name }}</span>
                                <span class="inline-block text-[10px] px-2 py-0.5 rounded bg-slate-100 text-slate-600 ml-1">
                                    {{ ucfirst($order->buyer->role) }}
                                </span>
                            </td>
                            <td class="py-3 px-3">
                                @foreach($order->items as $item)
                                    <p class="truncate max-w-[200px]">{{ $item->product_name }} ({{ number_format($item->quantity, 0) }} {{ $item->unit }})</p>
                                @endforeach
                            </td>
                            <td class="py-3 px-3 font-bold text-slate-900">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3">
                                @php
                                    $statusClasses = [
                                        'Menunggu Konfirmasi' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'Dikonfirmasi' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'Diproses' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                        'Selesai' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'Dibatalkan' => 'bg-rose-100 text-rose-800 border-rose-200',
                                    ];
                                @endphp
                                <span class="inline-block px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $statusClasses[$order->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-right">
                                <a href="{{ route('farmer.orders.show', $order) }}" 
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-slate-700 font-bold text-xs transition">
                                    <span>Detail</span>
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                <i data-lucide="inbox" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
                <h4 class="text-sm font-bold text-slate-700">Belum Ada Pesanan Masuk</h4>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Pesanan dari pembeli di marketplace akan otomatis muncul di sini untuk dikonfirmasi dan diproses.</p>
            </div>
        @endif
    </div>

    <!-- Quick Actions & Roadmap -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Workflow Cards -->
        <div class="lg:col-span-2 p-6 rounded-3xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900">Alur Niaga Petani SINTESA</h3>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg">Aktif & Terintegrasi</span>
            </div>
            <p class="text-xs text-slate-600 mb-6">
                Data hasil panen Anda otomatis mengalir ke inventaris stok riil dan dapat langsung ditautkan ke etalase penjualan marketplace:
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a href="{{ route('farmer.harvests.index') }}" class="p-4 rounded-2xl bg-emerald-50/50 hover:bg-emerald-50 border border-emerald-200 transition group block">
                    <div class="flex items-center justify-between mb-2 text-emerald-700">
                        <div class="flex items-center gap-2">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i>
                            <span class="text-xs font-bold uppercase">1. Hasil Panen</span>
                        </div>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                    <p class="text-xs text-slate-600">Catat volume panen, tanggal petik, mutu grade panen kebun.</p>
                </a>
                <a href="{{ route('farmer.stocks.index') }}" class="p-4 rounded-2xl bg-teal-50/50 hover:bg-teal-50 border border-teal-200 transition group block">
                    <div class="flex items-center justify-between mb-2 text-teal-700">
                        <div class="flex items-center gap-2">
                            <i data-lucide="boxes" class="w-4 h-4"></i>
                            <span class="text-xs font-bold uppercase">2. Stok Riil</span>
                        </div>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                    <p class="text-xs text-slate-600">Sistem mengunci stok otomatis per batch panen.</p>
                </a>
                <a href="{{ route('farmer.products.index') }}" class="p-4 rounded-2xl bg-amber-50/50 hover:bg-amber-50 border border-amber-200 transition group block">
                    <div class="flex items-center justify-between mb-2 text-amber-700">
                        <div class="flex items-center gap-2">
                            <i data-lucide="store" class="w-4 h-4"></i>
                            <span class="text-xs font-bold uppercase">3. Marketplace</span>
                        </div>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                    <p class="text-xs text-slate-600">Jual komoditas langsung ke pembeli dengan harga jelas.</p>
                </a>
            </div>
        </div>

        <!-- Master Commodities Quick View -->
        <div class="p-6 rounded-3xl bg-white border border-slate-200/80 shadow-sm">
            <h3 class="text-base font-bold text-slate-900 mb-4">Daftar Komoditas Tersedia</h3>
            <div class="space-y-2.5">
                @foreach($commodities as $c)
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="font-bold text-slate-800">{{ $c->name }}</span>
                    </div>
                    <span class="font-semibold text-slate-500">per {{ $c->unit }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
