@extends('layouts.dashboard')

@section('title', 'Manajemen Kampanye Pre-Order')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Manajemen Kampanye Pre-Order</h1>
            <p class="text-sm text-slate-500 mt-1">Buka alokasi kuota komoditas sebelum panen untuk mengamankan pembeli dari awal</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('farmer.preorders.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Buka Kampanye Baru</span>
            </a>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex flex-wrap items-center gap-2 pb-2 border-b border-slate-200">
        <a href="{{ route('farmer.preorders.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ !$status ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Semua</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ !$status ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $counts['all'] }}</span>
        </a>
        <a href="{{ route('farmer.preorders.index', ['status' => 'Dibuka']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Dibuka' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Masa Pemesanan (Dibuka)</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $status === 'Dibuka' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-700' }}">{{ $counts['open'] }}</span>
        </a>
        <a href="{{ route('farmer.preorders.index', ['status' => 'Menunggu Panen']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Menunggu Panen' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Menunggu Panen</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $status === 'Menunggu Panen' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-700' }}">{{ $counts['waiting'] }}</span>
        </a>
        <a href="{{ route('farmer.preorders.index', ['status' => 'Siap Diproses']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ in_array($status, ['Siap Diproses', 'Diproses']) ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Sedang Diproses</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ in_array($status, ['Siap Diproses', 'Diproses']) ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-700' }}">{{ $counts['processing'] }}</span>
        </a>
        <a href="{{ route('farmer.preorders.index', ['status' => 'Selesai']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Selesai' ? 'bg-slate-700 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Selesai</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $status === 'Selesai' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $counts['completed'] }}</span>
        </a>
    </div>

    <!-- Campaigns List -->
    @if($preorders->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center max-w-lg mx-auto space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto">
                <i data-lucide="calendar-clock" class="w-8 h-8"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Kampanye Pre-Order</h3>
                <p class="text-xs text-slate-500 mt-1">Buka kampanye pre-order untuk komoditas kebun Anda yang akan segera dipanen dalam beberapa minggu ke depan.</p>
            </div>
            <a href="{{ route('farmer.preorders.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold transition">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Buka Kampanye Pertama</span>
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($preorders as $po)
                <div class="bg-white rounded-3xl border border-slate-200 hover:border-slate-300 shadow-sm p-5 sm:p-6 transition">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3.5">
                            <img src="{{ $po->image_url }}" alt="{{ $po->title }}" class="w-16 h-16 rounded-2xl object-cover border border-slate-200 flex-shrink-0">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
                                        {{ $po->commodity->name }}
                                    </span>
                                    <span class="text-xs text-slate-400">•</span>
                                    <span class="text-xs text-slate-500 font-medium">Panen: <strong class="text-slate-800">{{ \Carbon\Carbon::parse($po->estimated_harvest_date)->translatedFormat('d F Y') }}</strong></span>
                                </div>
                                <h3 class="text-base font-bold text-slate-900">
                                    <a href="{{ route('farmer.preorders.show', $po) }}" class="hover:text-emerald-600 transition">
                                        {{ $po->title }}
                                    </a>
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">Lokasi: {{ $po->location }}</p>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            @if($po->status === 'Dibuka')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Pemesanan Dibuka</span>
                                </span>
                            @elseif($po->status === 'Menunggu Panen')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 text-amber-600"></i>
                                    <span>Menunggu Panen</span>
                                </span>
                            @elseif(in_array($po->status, ['Siap Diproses', 'Diproses']))
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-indigo-600"></i>
                                    <span>{{ $po->status }}</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                                    <span>{{ $po->status }}</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Quota & Metrics -->
                    @php
                        $total = (float) $po->estimated_production;
                        $avail = (float) $po->preorder_available_quantity;
                        $booked = max(0, $total - $avail);
                        $percent = $total > 0 ? min(100, round(($booked / $total) * 100)) : 0;
                    @endphp
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 py-4 text-xs">
                        <div class="p-3 bg-slate-50 rounded-2xl">
                            <span class="text-slate-400 font-semibold block">Total Rencana Panen:</span>
                            <span class="text-sm font-black text-slate-800 mt-0.5 block">{{ number_format($total, 0, ',', '.') }} {{ $po->unit }}</span>
                        </div>
                        <div class="p-3 bg-emerald-50/70 rounded-2xl border border-emerald-100">
                            <span class="text-emerald-700 font-semibold block">Harga Satuan:</span>
                            <span class="text-sm font-black text-emerald-900 mt-0.5 block">Rp {{ number_format($po->price, 0, ',', '.') }} / {{ $po->unit }}</span>
                        </div>
                        <div class="p-3 bg-amber-50/60 rounded-2xl border border-amber-100">
                            <span class="text-amber-800 font-semibold block">Kuota Terpesan:</span>
                            <span class="text-sm font-black text-amber-900 mt-0.5 block">{{ number_format($booked, 0, ',', '.') }} {{ $po->unit }} ({{ $percent }}%)</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-2xl">
                            <span class="text-slate-400 font-semibold block">Sisa Kuota:</span>
                            <span class="text-sm font-black text-slate-800 mt-0.5 block">{{ number_format($avail, 0, ',', '.') }} {{ $po->unit }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
                        <span class="text-slate-400">{{ $po->items->count() }} Pembeli Memesan Kuota</span>
                        <a href="{{ route('farmer.preorders.show', $po) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold transition">
                            <span>Kelola Kampanye & Kuota</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
            @endforeach

            <div class="pt-4">
                {{ $preorders->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
