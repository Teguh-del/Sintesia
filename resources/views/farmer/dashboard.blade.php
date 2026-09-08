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

    <!-- Quick Actions & Roadmap for Phase 2/3 -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Next Phases Info -->
        <div class="lg:col-span-2 p-6 rounded-3xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900">Alur Niaga Petani (PRD Guideline)</h3>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg">Alur Wajib SINTESA</span>
            </div>
            <p class="text-xs text-slate-600 mb-6">
                Data hasil panen Anda akan otomatis terintegrasi ke modul Stok dan Produk Marketplace tanpa risiko overselling atau data dummy:
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="flex items-center gap-2 mb-2 text-emerald-700">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span class="text-xs font-bold uppercase">1. Hasil Panen</span>
                    </div>
                    <p class="text-xs text-slate-500">Catat volume panen, tanggal petik, mutu kualitas grade (Phase 3).</p>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="flex items-center gap-2 mb-2 text-teal-700">
                        <i data-lucide="boxes" class="w-4 h-4"></i>
                        <span class="text-xs font-bold uppercase">2. Stok Riil</span>
                    </div>
                    <p class="text-xs text-slate-500">Sistem mengunci kuantitas stok agar tidak pernah minus (Phase 3).</p>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="flex items-center gap-2 mb-2 text-amber-700">
                        <i data-lucide="store" class="w-4 h-4"></i>
                        <span class="text-xs font-bold uppercase">3. Marketplace</span>
                    </div>
                    <p class="text-xs text-slate-500">Produk tampil di katalog pembeli dengan foto dan harga jelas (Phase 2).</p>
                </div>
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
