@extends('layouts.dashboard')

@section('title', 'Admin Panel Dashboard')
@section('header_title', 'Overview Panel Administrator')

@section('content')
<div class="space-y-6">
    <!-- Admin Notice Card -->
    <div class="p-6 rounded-3xl bg-gradient-to-r from-purple-800 to-slate-900 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-block px-3 py-1 rounded-full bg-purple-700/60 border border-purple-500/40 text-xs font-semibold uppercase tracking-wider mb-3">
                Sistem Otoritas SINTESA
            </span>
            <h2 class="text-2xl font-black tracking-tight mb-2">
                Panel Monitoring Sistem Administrator
            </h2>
            <p class="text-purple-200 text-xs leading-relaxed">
                Pantau seluruh ekosistem pengguna, integritas data komoditas master, dan validasi kepatuhan aturan bisnis PRD. Akun admin ini diinisialisasi secara aman dan tidak dapat didaftarkan via form publik.
            </p>
        </div>
    </div>

    <!-- Platform User Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pengguna</p>
                <h4 class="text-2xl font-black text-slate-900 mt-1">{{ $totalUsers }}</h4>
                <p class="text-[11px] text-emerald-600 font-medium mt-1">Database Riil</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Petani Terdaftar</p>
                <h4 class="text-2xl font-black text-slate-900 mt-1">{{ $totalFarmers }}</h4>
                <p class="text-[11px] text-emerald-600 font-medium mt-1">Mitra Produsen</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                <i data-lucide="sprout" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pengepul Aktif</p>
                <h4 class="text-2xl font-black text-slate-900 mt-1">{{ $totalCollectors }}</h4>
                <p class="text-[11px] text-amber-600 font-medium mt-1">Mitra Distribusi</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                <i data-lucide="truck" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Konsumen</p>
                <h4 class="text-2xl font-black text-slate-900 mt-1">{{ $totalConsumers }}</h4>
                <p class="text-[11px] text-blue-600 font-medium mt-1">Pembeli Langsung</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">
                <i data-lucide="shopping-bag" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Master Commodity Table -->
    <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Master Komoditas Pertanian</h3>
                <p class="text-xs text-slate-500">Data acuan standar komoditas untuk transaksi, panen, dan SINTESA Match:</p>
            </div>
            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold border border-emerald-200">
                5 Komoditas Terpasang
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500 bg-slate-50/50">
                        <th class="py-3 px-4">Nama Komoditas</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Satuan</th>
                        <th class="py-3 px-4">Deskripsi</th>
                        <th class="py-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @foreach($commodities as $c)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3.5 px-4 font-bold text-slate-900 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>{{ $c->name }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-600">{{ $c->category }}</td>
                        <td class="py-3.5 px-4 font-semibold text-slate-700">{{ $c->unit }}</td>
                        <td class="py-3.5 px-4 text-slate-500 max-w-xs truncate">{{ $c->description }}</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                Aktif
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
