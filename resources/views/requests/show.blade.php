@extends('layouts.dashboard')

@section('title', 'Detail Permintaan Pasokan')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('requests.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs font-bold transition shadow-xs">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Bursa Permintaan</span>
            </a>
            <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500">
                <span>/</span>
                <span class="text-slate-800 font-semibold">Detail Permintaan #REQ-{{ $commodityRequest->id }}</span>
            </div>
        </div>
        @if(in_array($commodityRequest->status, ['Aktif', 'Mendapat Penawaran']))
            <form action="{{ route('requests.close', $commodityRequest) }}" method="POST">
                @csrf
                <button type="submit" onclick="return confirm('Tutup permintaan pengadaan komoditas ini? Penawaran pending akan otomatis dibatalkan.')" 
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5">
                    <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                    <span>Tutup Permintaan</span>
                </button>
            </form>
        @endif
    </div>

    <!-- Request Overview Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6 pb-6 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2 flex-wrap mb-2">
                    <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-lg border border-emerald-200">
                        {{ $commodityRequest->commodity->name }}
                    </span>
                    <span class="text-xs text-slate-400">•</span>
                    <span class="text-xs text-slate-500">Batas Waktu: <strong class="text-slate-800">{{ \Carbon\Carbon::parse($commodityRequest->deadline)->translatedFormat('d F Y') }}</strong></span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $commodityRequest->title }}</h1>
                <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                    <i data-lucide="map-pin" class="w-4 h-4 text-slate-400"></i>
                    <span>Tujuan / Lokasi Pengiriman: <strong class="text-slate-700">{{ $commodityRequest->location }}</strong></span>
                </p>
            </div>

            <!-- Status Indicator -->
            <div>
                @if($commodityRequest->status === 'Aktif')
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Aktif Menanti Tawaran</span>
                    </span>
                @elseif($commodityRequest->status === 'Mendapat Penawaran')
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        <i data-lucide="sparkles" class="w-4 h-4 text-amber-600"></i>
                        <span>{{ $commodityRequest->offers->count() }} Penawaran Masuk</span>
                    </span>
                @elseif($commodityRequest->status === 'Dipenuhi')
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                        <i data-lucide="check-circle" class="w-4 h-4 text-indigo-600"></i>
                        <span>Pasokan Terpenuhi</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                        <span>{{ $commodityRequest->status }}</span>
                    </span>
                @endif
            </div>
        </div>

        <!-- Metric Specs -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-6 text-xs border-b border-slate-100">
            <div class="p-3.5 bg-slate-50 rounded-2xl">
                <span class="text-slate-400 font-semibold block">Volume Kebutuhan:</span>
                <span class="text-lg font-black text-slate-900 mt-0.5 block">{{ number_format($commodityRequest->required_quantity, 0, ',', '.') }} {{ $commodityRequest->unit }}</span>
            </div>
            <div class="p-3.5 bg-slate-50 rounded-2xl">
                <span class="text-slate-400 font-semibold block">Batas Maksimal Harga:</span>
                <span class="text-lg font-black text-emerald-700 mt-0.5 block">Rp {{ number_format($commodityRequest->max_price, 0, ',', '.') }} / {{ $commodityRequest->unit }}</span>
            </div>
            <div class="p-3.5 bg-slate-50 rounded-2xl">
                <span class="text-slate-400 font-semibold block">Total Anggaran Maksimal:</span>
                <span class="text-lg font-black text-slate-900 mt-0.5 block">Rp {{ number_format($commodityRequest->required_quantity * $commodityRequest->max_price, 0, ',', '.') }}</span>
            </div>
            <div class="p-3.5 bg-amber-50/60 rounded-2xl border border-amber-100">
                <span class="text-amber-800 font-semibold block">Total Tawaran Masuk:</span>
                <span class="text-lg font-black text-amber-900 mt-0.5 block">{{ $commodityRequest->offers->count() }} Penawaran</span>
            </div>
        </div>

        @if($commodityRequest->description)
            <div class="pt-4 text-xs text-slate-600">
                <strong class="text-slate-800 block mb-1">Spesifikasi Tambahan:</strong>
                <p class="leading-relaxed bg-slate-50 p-3.5 rounded-xl border border-slate-100">{{ $commodityRequest->description }}</p>
            </div>
        @endif
    </div>

    <!-- Incoming Farmer Offers Section -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Daftar Penawaran Pasokan dari Petani</h2>
                <p class="text-xs text-slate-500">Tinjau dan setujui penawaran yang paling sesuai dengan kriteria Anda</p>
            </div>
            <span class="text-xs font-bold text-slate-500 bg-white px-3 py-1.5 rounded-xl border border-slate-200">
                {{ $commodityRequest->offers->count() }} Terdaftar
            </span>
        </div>

        @if($commodityRequest->offers->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-200 p-8 text-center max-w-md mx-auto space-y-3">
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center mx-auto">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-800">Menunggu Penawaran Pertama</h3>
                <p class="text-xs text-slate-500">Permintaan ini sudah dipublikasikan. Petani komoditas ini akan segera merespon dengan penawaran pasokan.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($commodityRequest->offers as $offer)
                    <div class="bg-white rounded-3xl border {{ $offer->status === 'Diterima' ? 'border-emerald-300 ring-2 ring-emerald-500/20' : 'border-slate-200' }} shadow-sm p-5 sm:p-6 transition">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 font-black flex items-center justify-center text-sm border border-emerald-100 flex-shrink-0">
                                    {{ substr($offer->farmer->name, 0, 2) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-sm font-bold text-slate-900">{{ $offer->farmer->name }}</h3>
                                        <span class="text-[10px] font-bold bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-md border border-emerald-200">Petani Terverifikasi</span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        Lokasi Kebun: <strong class="text-slate-700">{{ $offer->farmer->farmerProfile->regency ?? 'Wilayah Petani' }}</strong> | Metode: <span class="text-slate-700">{{ $offer->shipping_method }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Offer Status Badge -->
                            <div>
                                @if($offer->status === 'Menunggu')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span>Menunggu Persetujuan Anda</span>
                                    </span>
                                @elseif($offer->status === 'Diterima')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-600"></i>
                                        <span>Tawaran Disetujui (Pesanan Diterbitkan)</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                        <span>{{ $offer->status }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Offer Numbers -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 py-4 text-xs">
                            <div class="p-3 bg-slate-50 rounded-2xl">
                                <span class="text-slate-400 font-semibold block">Volume Ditawarkan:</span>
                                <span class="text-sm font-bold text-slate-800 mt-0.5 block">{{ number_format($offer->offered_quantity, 0, ',', '.') }} {{ $commodityRequest->unit }}</span>
                            </div>
                            <div class="p-3 bg-emerald-50/70 rounded-2xl border border-emerald-100">
                                <span class="text-emerald-700 font-semibold block">Harga Pasokan Petani:</span>
                                <span class="text-sm font-black text-emerald-900 mt-0.5 block">Rp {{ number_format($offer->offered_price, 0, ',', '.') }} / {{ $commodityRequest->unit }}</span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-2xl">
                                <span class="text-slate-400 font-semibold block">Total Transaksi:</span>
                                <span class="text-sm font-black text-slate-900 mt-0.5 block">Rp {{ number_format($offer->offered_quantity * $offer->offered_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-2xl">
                                <span class="text-slate-400 font-semibold block">Bandingkan dg Budget:</span>
                                @php
                                    $diff = $commodityRequest->max_price - $offer->offered_price;
                                @endphp
                                @if($diff >= 0)
                                    <span class="text-xs font-bold text-emerald-700 mt-0.5 block">Hemat Rp {{ number_format($diff, 0, ',', '.') }}/{{ $commodityRequest->unit }}</span>
                                @else
                                    <span class="text-xs font-bold text-rose-600 mt-0.5 block">Lebih tinggi Rp {{ number_format(abs($diff), 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>

                        @if($offer->notes)
                            <div class="p-3 bg-slate-50 rounded-xl text-xs text-slate-600 mb-3 border border-slate-100">
                                <strong>Catatan dari Petani:</strong> "{{ $offer->notes }}"
                            </div>
                        @endif

                        <!-- Action Bar for Buyer -->
                        @if($offer->status === 'Menunggu' && !in_array($commodityRequest->status, ['Dipenuhi', 'Ditutup']))
                            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                                <form action="{{ route('requests.rejectOffer', $offer) }}" method="POST">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Tolak tawaran dari petani ini?')" class="px-4 py-2 rounded-xl bg-white hover:bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold transition">
                                        Tolak Tawaran
                                    </button>
                                </form>
                                <form action="{{ route('requests.acceptOffer', $offer) }}" method="POST">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Sepakati penawaran ini? Pesanan resmi akan diterbitkan ke petani {{ $offer->farmer->name }}.')" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5">
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                        <span>Sepakati & Terbitkan Pesanan</span>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
