@extends('layouts.app')

@section('title', 'Katalog Pre-Order Panen - SINTESA')

@section('content')
<div class="bg-slate-50 min-h-screen pb-16">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-emerald-950 via-teal-900 to-emerald-900 text-white py-12 px-4 sm:px-6 lg:px-8 border-b border-emerald-950/20 shadow-inner">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-800/70 border border-emerald-600/30 text-emerald-200 text-xs font-semibold mb-3">
                        <i data-lucide="calendar-clock" class="w-3.5 h-3.5 text-amber-400"></i>
                        <span>Sistem Alokasi Kuota Panen Bergaransi</span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-white">Pre-Order Hasil Panen</h1>
                    <p class="text-emerald-100/90 text-sm sm:text-base mt-2 max-w-2xl leading-relaxed">
                        Amankan kepastian pasokan komoditas sebelum masa panen tiba langsung dari kebun petani dengan harga terbaik.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    @auth
                        @if(auth()->user()->isPetani())
                            <a href="{{ route('farmer.preorders.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-sm shadow-md transition">
                                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                                <span>Buka Pre-Order Baru</span>
                            </a>
                        @else
                            <a href="{{ route('preorders.my') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-md transition">
                                <i data-lucide="receipt" class="w-4 h-4"></i>
                                <span>Pre-Order Saya</span>
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content & Filters -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- Filter Form -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
            <form method="GET" action="{{ route('preorders.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari komoditas panen atau lokasi..." 
                           class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                </div>

                <div class="w-full sm:w-64">
                    <select name="commodity_id" onchange="this.form.submit()" 
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                        <option value="">Semua Jenis Komoditas</option>
                        @foreach($commodities as $c)
                            <option value="{{ $c->id }}" {{ $commodityId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full sm:w-auto px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition">
                    Terapkan
                </button>
            </form>
        </div>

        <!-- Preorder Campaigns Grid -->
        @if($preorders->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center max-w-lg mx-auto space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto">
                    <i data-lucide="calendar-clock" class="w-8 h-8"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Tidak Ada Kampanye Pre-Order Aktif</h3>
                    <p class="text-xs text-slate-500 mt-1">Saat ini belum ada jadwal rencana panen yang membuka kuota pemesanan di kategori ini.</p>
                </div>
                <a href="{{ route('marketplace.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold transition">
                    <span>Lihat Marketplace Reguler</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($preorders as $po)
                    <div class="bg-white rounded-3xl border border-slate-200 hover:border-emerald-500/50 shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col overflow-hidden group">
                        <!-- Image & Badges -->
                        <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                            <img src="{{ $po->image_url }}" alt="{{ $po->title }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            
                            <div class="absolute top-3 left-3">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider bg-slate-900/80 backdrop-blur-sm text-emerald-400 border border-emerald-500/30 shadow-sm">
                                    {{ $po->commodity->name }}
                                </span>
                            </div>

                            <div class="absolute top-3 right-3">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-amber-500 text-slate-950 shadow-sm flex items-center gap-1">
                                    <i data-lucide="clock" class="w-3 h-3"></i>
                                    <span>Panen: {{ \Carbon\Carbon::parse($po->estimated_harvest_date)->translatedFormat('d M Y') }}</span>
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            <div>
                                <h3 class="text-base font-bold text-slate-900 group-hover:text-emerald-700 transition line-clamp-1">
                                    <a href="{{ route('preorders.show', $po->slug) }}">
                                        {{ $po->title }}
                                    </a>
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                                    <span class="truncate">{{ $po->location }}</span>
                                </p>
                            </div>

                            <!-- Quota Progress Bar -->
                            @php
                                $total = (float) $po->estimated_production;
                                $avail = (float) $po->preorder_available_quantity;
                                $booked = max(0, $total - $avail);
                                $percent = $total > 0 ? min(100, round(($booked / $total) * 100)) : 0;
                            @endphp
                            <div class="space-y-1.5 p-3 bg-slate-50 rounded-2xl border border-slate-100 text-xs">
                                <div class="flex justify-between font-semibold">
                                    <span class="text-slate-500">Sisa Kuota Pre-Order:</span>
                                    <span class="text-emerald-700 font-bold">{{ number_format($avail, 0, ',', '.') }} {{ $po->unit }}</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-gradient-to-r from-emerald-500 to-teal-600 h-2 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                                </div>
                                <div class="flex justify-between text-[11px] text-slate-400">
                                    <span>Terpesan {{ $percent }}%</span>
                                    <span>Total: {{ number_format($total, 0, ',', '.') }} {{ $po->unit }}</span>
                                </div>
                            </div>

                            <!-- Footer / Price & CTA -->
                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] text-slate-400 font-semibold block uppercase">Harga Pre-Order:</span>
                                    <span class="text-base font-black text-emerald-700">Rp {{ number_format($po->price, 0, ',', '.') }}</span>
                                    <span class="text-[10px] text-slate-400 font-normal">/ {{ $po->unit }}</span>
                                </div>

                                <a href="{{ route('preorders.show', $po->slug) }}" 
                                   class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5">
                                    <span>Pesan Kuota</span>
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-6">
                {{ $preorders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
