@extends('layouts.dashboard')

@section('title', 'Bursa Permintaan Komoditas')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Bursa Permintaan Pasokan</h1>
            <p class="text-sm text-slate-500 mt-1">Temukan permintaan pasokan komoditas langsung dari Pengepul dan Konsumen</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('farmer.stocks.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                <i data-lucide="boxes" class="w-4 h-4"></i>
                <span>Cek Ketersediaan Stok</span>
            </a>
        </div>
    </div>

    <!-- Navigation Tabs (Open Market vs My Offers) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200">
        <div class="flex items-center gap-2">
            <a href="{{ route('farmer.requests.index', ['tab' => 'open']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tab !== 'my_offers' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                <span>Permintaan Terbuka</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $tab !== 'my_offers' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $counts['open'] }}</span>
            </a>
            <a href="{{ route('farmer.requests.index', ['tab' => 'my_offers']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tab === 'my_offers' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                <span>Penawaran Saya</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $tab === 'my_offers' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-700' }}">{{ $counts['my_offers'] }}</span>
            </a>
        </div>

        @if($tab !== 'my_offers')
            <!-- Commodity Filter -->
            <form method="GET" action="{{ route('farmer.requests.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="tab" value="open">
                <select name="commodity_id" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Komoditas</option>
                    @foreach($commodities as $c)
                        <option value="{{ $c->id }}" {{ $commodityId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    @if($tab === 'my_offers')
        <!-- Tab: Farmer's Submitted Offers -->
        @if($offers->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center max-w-lg mx-auto space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto">
                    <i data-lucide="send" class="w-8 h-8"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Belum Ada Penawaran yang Diajukan</h3>
                    <p class="text-xs text-slate-500 mt-1">Anda belum mengajukan penawaran pasokan untuk permintaan komoditas pembeli.</p>
                </div>
                <a href="{{ route('farmer.requests.index', ['tab' => 'open']) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold transition">
                    <span>Lihat Permintaan Terbuka</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($offers as $off)
                    <div class="bg-white rounded-3xl border border-slate-200 hover:border-slate-300 shadow-sm p-5 sm:p-6 transition">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
                                        {{ $off->commodityRequest->commodity->name }}
                                    </span>
                                    <span class="text-xs text-slate-400">•</span>
                                    <span class="text-xs text-slate-500 font-medium">Pembeli: <strong class="text-slate-800">{{ $off->commodityRequest->user->name }}</strong></span>
                                </div>
                                <h3 class="text-base font-bold text-slate-900">
                                    <a href="{{ route('farmer.requests.show', $off->commodityRequest) }}" class="hover:text-emerald-600 transition">
                                        {{ $off->commodityRequest->title }}
                                    </a>
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">Tujuan: {{ $off->commodityRequest->location }}</p>
                            </div>

                            <!-- Offer Status -->
                            <div>
                                @if($off->status === 'Menunggu')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                        <span>Menunggu Persetujuan Pembeli</span>
                                    </span>
                                @elseif($off->status === 'Diterima')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-600"></i>
                                        <span>Disetujui Pembeli (Pesanan Diterbitkan)</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        <span>{{ $off->status }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 py-4 text-xs">
                            <div class="p-3 bg-slate-50 rounded-2xl">
                                <span class="text-slate-400 font-semibold block">Volume Pasokan Anda:</span>
                                <span class="text-sm font-bold text-slate-800 mt-0.5 block">{{ number_format($off->offered_quantity, 0, ',', '.') }} {{ $off->commodityRequest->unit }}</span>
                            </div>
                            <div class="p-3 bg-emerald-50/60 rounded-2xl border border-emerald-100">
                                <span class="text-emerald-700 font-semibold block">Harga Pasokan Anda:</span>
                                <span class="text-sm font-black text-emerald-900 mt-0.5 block">Rp {{ number_format($off->offered_price, 0, ',', '.') }} / {{ $off->commodityRequest->unit }}</span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-2xl">
                                <span class="text-slate-400 font-semibold block">Total Transaksi:</span>
                                <span class="text-sm font-black text-slate-900 mt-0.5 block">Rp {{ number_format($off->offered_quantity * $off->offered_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-2xl">
                                <span class="text-slate-400 font-semibold block">Metode Pengiriman:</span>
                                <span class="text-xs font-bold text-slate-700 mt-1 block truncate">{{ $off->shipping_method }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
                            <span class="text-slate-400">Diajukan {{ $off->created_at->diffForHumans() }}</span>
                            <a href="{{ route('farmer.requests.show', $off->commodityRequest) }}" class="font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                                <span>Lihat Detail Permintaan</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                @endforeach

                <div class="pt-4">
                    {{ $offers->links() }}
                </div>
            </div>
        @endif
    @else
        <!-- Tab: Open Requests from Buyers -->
        @if($requests->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center max-w-lg mx-auto space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto">
                    <i data-lucide="search" class="w-8 h-8"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Tidak Ada Permintaan Terbuka</h3>
                    <p class="text-xs text-slate-500 mt-1">Saat ini belum ada permintaan komoditas terbuka pada filter yang dipilih.</p>
                </div>
            </div>
        @else
            <div class="space-y-4">
                @foreach($requests as $req)
                    <div class="bg-white rounded-3xl border border-slate-200 hover:border-slate-300 shadow-sm p-5 sm:p-6 transition">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
                                        {{ $req->commodity->name }}
                                    </span>
                                    <span class="text-xs text-slate-400">•</span>
                                    <span class="text-xs text-slate-500">Oleh: <strong class="text-slate-800">{{ $req->user->name }}</strong> ({{ ucfirst($req->user->role) }})</span>
                                </div>
                                <h3 class="text-base font-bold text-slate-900">
                                    {{ $req->title }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                                    <span>Lokasi Kirim: {{ $req->location }}</span>
                                </p>
                            </div>

                            <div class="text-right">
                                <span class="text-xs text-slate-400 font-semibold block">Batas Pengadaan:</span>
                                <span class="text-xs font-black text-rose-600 block mt-0.5">{{ \Carbon\Carbon::parse($req->deadline)->translatedFormat('d F Y') }}</span>
                            </div>
                        </div>

                        <!-- Metrics -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 py-4 text-xs">
                            <div class="p-3 bg-slate-50 rounded-2xl">
                                <span class="text-slate-400 font-semibold block">Volume Kebutuhan:</span>
                                <span class="text-sm font-black text-slate-800 mt-0.5 block">{{ number_format($req->required_quantity, 0, ',', '.') }} {{ $req->unit }}</span>
                            </div>
                            <div class="p-3 bg-emerald-50/70 rounded-2xl border border-emerald-100">
                                <span class="text-emerald-700 font-semibold block">Batas Harga Pembeli:</span>
                                <span class="text-sm font-black text-emerald-900 mt-0.5 block">Rp {{ number_format($req->max_price, 0, ',', '.') }} / {{ $req->unit }}</span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-2xl">
                                <span class="text-slate-400 font-semibold block">Estimasi Anggaran:</span>
                                <span class="text-sm font-black text-slate-900 mt-0.5 block">Rp {{ number_format($req->required_quantity * $req->max_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="p-3 bg-amber-50/60 rounded-2xl border border-amber-100">
                                <span class="text-amber-800 font-semibold block">Tawaran Masuk:</span>
                                <span class="text-sm font-black text-amber-900 mt-0.5 block">{{ $req->offers->count() }} Petani</span>
                            </div>
                        </div>

                        @if($req->description)
                            <div class="p-3 bg-slate-50 rounded-xl text-xs text-slate-600 mb-3 border border-slate-100">
                                <strong>Catatan Kebutuhan:</strong> {{ Str::limit($req->description, 150) }}
                            </div>
                        @endif

                        <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                            <span class="text-[11px] text-slate-400">Diposting {{ $req->created_at->diffForHumans() }}</span>
                            <a href="{{ route('farmer.requests.show', $req) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm shadow-emerald-600/20 transition">
                                <i data-lucide="handshake" class="w-3.5 h-3.5"></i>
                                <span>Ajukan Pasokan</span>
                            </a>
                        </div>
                    </div>
                @endforeach

                <div class="pt-4">
                    {{ $requests->links() }}
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
