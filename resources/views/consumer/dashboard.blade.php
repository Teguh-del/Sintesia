@extends('layouts.dashboard')

@section('title', 'Dashboard Konsumen')
@section('header_title', 'Dashboard Konsumen')

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-blue-700 to-indigo-800 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-block px-3 py-1 rounded-full bg-blue-600/60 border border-blue-500/40 text-xs font-semibold uppercase tracking-wider mb-3">
                Konsumen & Pembeli Langsung
            </span>
            <h2 class="text-2xl sm:text-3xl font-black tracking-tight mb-2">
                Selamat Datang, {{ $user->name }}!
            </h2>
            <p class="text-blue-100 text-sm leading-relaxed">
                Beli hasil pertanian segar bermutu tinggi langsung dari tangan petani lokal, ikuti program Pre-Order panen, atau ajukan penawaran harga terbaik.
            </p>
        </div>
        <div class="absolute right-6 -bottom-6 opacity-10 hidden sm:block">
            <i data-lucide="shopping-cart" class="w-48 h-48"></i>
        </div>
    </div>

    <!-- Marketplace Discovery Banner -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <i data-lucide="store" class="w-6 h-6"></i>
            </div>
            <div>
                <h4 class="text-base font-bold text-slate-900">Belanja Hasil Pertanian Segar di Marketplace</h4>
                <p class="text-xs text-slate-500">Dapatkan cabai, jagung, tomat, beras, dan kelapa segar berkualitas dengan harga produsen langsung.</p>
            </div>
        </div>
        <a href="{{ route('marketplace.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition whitespace-nowrap">
            <span>Buka Marketplace</span>
            <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
    </div>

    <!-- Consumer Profile Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center flex-shrink-0">
                <i data-lucide="map-pin" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Alamat Pengiriman</p>
                <h4 class="text-base font-bold text-slate-900 truncate max-w-[200px]">{{ $user->consumerProfile->address ?? 'Kediri, Jawa Timur' }}</h4>
                <p class="text-xs text-slate-400">Lokasi Utama</p>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Akun</p>
                <h4 class="text-base font-bold text-slate-900">Konsumen Terverifikasi</h4>
                <p class="text-xs text-emerald-600 font-medium">Bebas Transaksi Langsung</p>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0">
                <i data-lucide="clock" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Fitur Pre-Order</p>
                <h4 class="text-base font-bold text-slate-900">Jaminan Panen Segar</h4>
                <p class="text-xs text-slate-400">Pesan Sebelum Panen</p>
            </div>
        </div>
    </div>

    <!-- Catalog Explorer Preview -->
    <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Katalog Komoditas Pilihan</h3>
                <p class="text-xs text-slate-500">Komoditas segar yang siap dipasarkan oleh mitra petani terdaftar:</p>
            </div>
            <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-lg">Phase 1 Data Foundation</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($commodities as $c)
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-center hover:bg-blue-50/50 transition">
                <div class="w-12 h-12 mx-auto rounded-xl bg-white shadow-sm flex items-center justify-center text-blue-600 mb-2">
                    <i data-lucide="{{ $c->icon ?? 'sprout' }}" class="w-6 h-6"></i>
                </div>
                <h4 class="font-bold text-slate-900 text-sm">{{ $c->name }}</h4>
                <span class="text-[10px] text-slate-500">Satuan: {{ $c->unit }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
