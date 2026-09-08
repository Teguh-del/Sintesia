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

    <!-- Product, Harvest & Stock Metrics (Phase 2 & 3 Real Data) -->
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
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Produk Aktif Dijual</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $activeProducts }} <span class="text-xs font-normal text-slate-400">/ {{ $totalProducts }}</span></p>
                <a href="{{ route('farmer.products.index') }}" class="text-[11px] font-bold text-emerald-600 hover:underline inline-block mt-1">
                    Katalog Produk &rarr;
                </a>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <i data-lucide="store" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Aksi Cepat</p>
                <div class="flex flex-col gap-1 mt-1.5">
                    <a href="{{ route('farmer.harvests.create') }}" class="text-[11px] font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                        <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                        <span>Catat Panen</span>
                    </a>
                    <a href="{{ route('farmer.products.create') }}" class="text-[11px] font-bold text-slate-600 hover:text-slate-900 flex items-center gap-1">
                        <i data-lucide="tag" class="w-3.5 h-3.5"></i>
                        <span>Jual Produk</span>
                    </a>
                </div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <i data-lucide="zap" class="w-5 h-5"></i>
            </div>
        </div>
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
