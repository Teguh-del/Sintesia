@extends('layouts.dashboard')

@section('title', 'Dashboard Pengepul')
@section('header_title', 'Dashboard Pengepul')

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-amber-700 to-orange-800 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-block px-3 py-1 rounded-full bg-amber-600/60 border border-amber-500/40 text-xs font-semibold uppercase tracking-wider mb-3">
                Mitra Pengepul & Pedagang Besar
            </span>
            <h2 class="text-2xl sm:text-3xl font-black tracking-tight mb-2">
                Selamat Datang, {{ $user->name }}!
            </h2>
            <p class="text-amber-100 text-sm leading-relaxed">
                Akses pasokan komoditas langsung dari petani dengan algoritma SINTESA Match, pasang permintaan kebutuhan kuota, dan ajukan penawaran harga transparan.
            </p>
        </div>
        <div class="absolute right-6 -bottom-6 opacity-10 hidden sm:block">
            <i data-lucide="truck" class="w-48 h-48"></i>
        </div>
    </div>

    <!-- Collector Profile Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0">
                <i data-lucide="building-2" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Usaha / Gudang</p>
                <h4 class="text-base font-bold text-slate-900">{{ $user->collectorProfile->business_name ?? 'Gudang Pengepul' }}</h4>
                <p class="text-xs text-slate-400 truncate max-w-[180px]">{{ $user->collectorProfile->address ?? 'Kediri' }}</p>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                <i data-lucide="tag" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipe Usaha</p>
                <h4 class="text-base font-bold text-slate-900">{{ $user->collectorProfile->business_type ?? 'Pengepul Grosir' }}</h4>
                <p class="text-xs text-emerald-600 font-medium">Mitra Terverifikasi</p>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center flex-shrink-0">
                <i data-lucide="sparkles" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Fitur Utama</p>
                <h4 class="text-base font-bold text-slate-900">SINTESA Match</h4>
                <p class="text-xs text-purple-600 font-medium">Pencocokan Cerdas 4 Bobot</p>
            </div>
        </div>
    </div>

    <!-- SINTESA Match Spotlight Card -->
    <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/80 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <i data-lucide="sparkles" class="w-5 h-5 text-amber-500"></i>
                    <h3 class="text-lg font-bold text-slate-900">Algoritma SINTESA Match (Phase 6 Core Feature)</h3>
                </div>
                <p class="text-xs text-slate-500">
                    Perhitungan pencocokan kebutuhan riil dari database MySQL tanpa angka hardcode atau dummy:
                </p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                Formula PRD: 35% Komoditas + 25% Stok + 20% Harga + 20% Jarak
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-500 mb-1">1. Kesesuaian Komoditas</p>
                <h4 class="text-xl font-black text-slate-900">35%</h4>
                <p class="text-[11px] text-slate-400 mt-1">Mencocokkan jenis dan varietas komoditas yang diminta.</p>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-500 mb-1">2. Ketersediaan Stok</p>
                <h4 class="text-xl font-black text-slate-900">25%</h4>
                <p class="text-[11px] text-slate-400 mt-1">Mengecek kuantitas stok riil petani yang siap kirim.</p>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-500 mb-1">3. Kesesuaian Harga</p>
                <h4 class="text-xl font-black text-slate-900">20%</h4>
                <p class="text-[11px] text-slate-400 mt-1">Evaluasi batas harga maksimum permintaan vs harga jual.</p>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-500 mb-1">4. Kedekatan Jarak</p>
                <h4 class="text-xl font-black text-slate-900">20%</h4>
                <p class="text-[11px] text-slate-400 mt-1">Perhitungan radius koordinat via Haversine formula.</p>
            </div>
        </div>
    </div>
</div>
@endsection
