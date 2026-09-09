@extends('layouts.dashboard')

@section('title', 'Permintaan Komoditas Saya')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Permintaan Komoditas Saya</h1>
            <p class="text-sm text-slate-500 mt-1">Publikasikan kebutuhan pasokan komoditas untuk dipenuhi langsung oleh Petani terverifikasi</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('requests.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Buat Permintaan Baru</span>
            </a>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex flex-wrap items-center gap-2 pb-2 border-b border-slate-200">
        <a href="{{ route('requests.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ !$status ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Semua</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ !$status ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $counts['all'] }}</span>
        </a>
        <a href="{{ route('requests.index', ['status' => 'Aktif']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Aktif' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Aktif</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $status === 'Aktif' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-700' }}">{{ $counts['active'] }}</span>
        </a>
        <a href="{{ route('requests.index', ['status' => 'Mendapat Penawaran']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Mendapat Penawaran' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Ada Tawaran Masuk</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $status === 'Mendapat Penawaran' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-700' }}">{{ $counts['offers'] }}</span>
        </a>
        <a href="{{ route('requests.index', ['status' => 'Dipenuhi']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Dipenuhi' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Telah Dipenuhi</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $status === 'Dipenuhi' ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-700' }}">{{ $counts['fulfilled'] }}</span>
        </a>
        <a href="{{ route('requests.index', ['status' => 'Ditutup']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Ditutup' ? 'bg-slate-700 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Ditutup</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $status === 'Ditutup' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $counts['closed'] }}</span>
        </a>
    </div>

    <!-- Request Cards -->
    @if($requests->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center max-w-lg mx-auto space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto">
                <i data-lucide="clipboard-list" class="w-8 h-8"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Permintaan Komoditas</h3>
                <p class="text-xs text-slate-500 mt-1">Buat permintaan pasokan komoditas jika Anda membutuhkan volume tertentu dengan batas harga yang Anda tentukan.</p>
            </div>
            <a href="{{ route('requests.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Buat Permintaan Pertama</span>
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 gap-4">
            @foreach($requests as $req)
                <div class="bg-white rounded-3xl border border-slate-200 hover:border-slate-300 shadow-sm p-5 sm:p-6 transition flex flex-col justify-between">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
                                    {{ $req->commodity->name }}
                                </span>
                                <span class="text-xs text-slate-400">•</span>
                                <span class="text-xs text-slate-500 font-medium">Batas Waktu: <strong class="text-slate-800">{{ \Carbon\Carbon::parse($req->deadline)->translatedFormat('d F Y') }}</strong></span>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 mt-1.5">
                                <a href="{{ route('requests.show', $req) }}" class="hover:text-emerald-600 transition">
                                    {{ $req->title }}
                                </a>
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>{{ $req->location }}</span>
                            </p>
                        </div>

                        <!-- Status & Offers Badge -->
                        <div class="flex items-center gap-2">
                            @if($req->status === 'Aktif')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Aktif Mencari</span>
                                </span>
                            @elseif($req->status === 'Mendapat Penawaran')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-600"></i>
                                    <span>{{ $req->offers->count() }} Tawaran Masuk</span>
                                </span>
                            @elseif($req->status === 'Dipenuhi')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-indigo-600"></i>
                                    <span>Telah Dipenuhi</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    <span>{{ $req->status }}</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 py-4 text-xs">
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-slate-400 font-semibold block">Volume Dibutuhkan:</span>
                            <span class="text-sm font-black text-slate-800 mt-0.5 block">{{ number_format($req->required_quantity, 0, ',', '.') }} {{ $req->unit }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-slate-400 font-semibold block">Batas Maksimal Harga:</span>
                            <span class="text-sm font-black text-emerald-700 mt-0.5 block">Rp {{ number_format($req->max_price, 0, ',', '.') }} / {{ $req->unit }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-slate-400 font-semibold block">Estimasi Anggaran:</span>
                            <span class="text-sm font-black text-slate-800 mt-0.5 block">Rp {{ number_format($req->required_quantity * $req->max_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="p-3 bg-amber-50/60 rounded-2xl border border-amber-100">
                            <span class="text-amber-800 font-semibold block">Tawaran Petani:</span>
                            <span class="text-sm font-black text-amber-900 mt-0.5 block">{{ $req->offers->count() }} Penawaran</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                        <span class="text-[11px] text-slate-400">Dibuat {{ $req->created_at->diffForHumans() }}</span>
                        <a href="{{ route('requests.show', $req) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                            <span>Tinjau Tawaran & Detail</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
            @endforeach

            <div class="pt-4">
                {{ $requests->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
