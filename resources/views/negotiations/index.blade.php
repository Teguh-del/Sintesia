@extends('layouts.dashboard')

@section('title', 'Negosiasi Harga Saya')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Negosiasi Harga</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar penawaran harga khusus untuk produk marketplace dengan petani langsung</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('marketplace.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition">
                <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                <span>Jelajahi Produk Marketplace</span>
            </a>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex flex-wrap items-center gap-2 pb-2 border-b border-slate-200">
        <a href="{{ route('negotiations.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ !$status ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Semua</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ !$status ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $counts['all'] }}</span>
        </a>
        <a href="{{ route('negotiations.index', ['status' => 'Menunggu']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Menunggu' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Menunggu Respon</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $status === 'Menunggu' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-700' }}">{{ $counts['pending'] }}</span>
        </a>
        <a href="{{ route('negotiations.index', ['status' => 'Counter Offer']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Counter Offer' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Tawaran Balik Petani</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $status === 'Counter Offer' ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-700' }}">{{ $counts['counter'] }}</span>
        </a>
        <a href="{{ route('negotiations.index', ['status' => 'Selesai']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Selesai' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Disepakati (Selesai)</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $status === 'Selesai' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-700' }}">{{ $counts['completed'] }}</span>
        </a>
        <a href="{{ route('negotiations.index', ['status' => 'Ditolak']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Ditolak' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Ditolak / Batal</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $status === 'Ditolak' ? 'bg-white/20 text-white' : 'bg-rose-100 text-rose-700' }}">{{ $counts['rejected'] }}</span>
        </a>
    </div>

    <!-- Negotiation List -->
    @if($offers->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center max-w-lg mx-auto space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto">
                <i data-lucide="handshake" class="w-8 h-8"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Riwayat Negosiasi</h3>
                <p class="text-xs text-slate-500 mt-1">Anda belum mengajukan tawaran harga khusus untuk produk marketplace.</p>
            </div>
            <a href="{{ route('marketplace.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                <span>Buka Marketplace</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($offers as $offer)
                <div class="bg-white rounded-3xl border border-slate-200 hover:border-slate-300 shadow-sm p-5 sm:p-6 transition">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3.5">
                            <img src="{{ $offer->product->primary_image_url }}" alt="{{ $offer->product->name }}" class="w-16 h-16 rounded-2xl object-cover border border-slate-200 flex-shrink-0">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
                                        {{ $offer->product->commodity->name ?? 'Komoditas' }}
                                    </span>
                                    <span class="text-xs text-slate-400">•</span>
                                    <span class="text-xs text-slate-500 font-medium">Petani: <strong class="text-slate-800">{{ $offer->seller->name }}</strong> ({{ $offer->seller->farmerProfile->regency ?? $offer->product->location }})</span>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 mt-1">
                                    <a href="{{ route('marketplace.show', $offer->product->slug) }}" class="hover:text-emerald-600 transition">
                                        {{ $offer->product->name }}
                                    </a>
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Kuantitas diajukan: <strong class="text-slate-700">{{ number_format($offer->quantity, 0, ',', '.') }} {{ $offer->product->unit }}</strong> | Metode: <span class="text-slate-700">{{ $offer->shipping_method }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div class="flex items-center gap-3">
                            @if($offer->status === 'Menunggu')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span>Menunggu Respon Petani</span>
                                </span>
                            @elseif($offer->status === 'Counter Offer')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-indigo-600"></i>
                                    <span>Tawaran Balik dari Petani</span>
                                </span>
                            @elseif($offer->status === 'Diterima' || $offer->status === 'Selesai')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-600"></i>
                                    <span>Disepakati & Dipesan</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    <span>{{ $offer->status }}</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Price Comparison Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 py-4 text-xs">
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-slate-400 font-semibold block">Harga Katalog Awal:</span>
                            <span class="text-sm font-bold text-slate-600 mt-0.5 block">Rp {{ number_format($offer->product_price, 0, ',', '.') }} / {{ $offer->product->unit }}</span>
                        </div>
                        <div class="p-3 bg-amber-50/60 rounded-2xl border border-amber-100">
                            <span class="text-amber-700 font-semibold block">Tawaran Anda:</span>
                            <span class="text-sm font-black text-amber-900 mt-0.5 block">Rp {{ number_format($offer->offered_price, 0, ',', '.') }} / {{ $offer->product->unit }}</span>
                            <span class="text-[10px] text-amber-700">Total: Rp {{ number_format($offer->offered_price * $offer->quantity, 0, ',', '.') }}</span>
                        </div>
                        <div class="p-3 {{ $offer->counter_price ? 'bg-indigo-50 border border-indigo-200' : 'bg-slate-50 border border-slate-100 text-slate-400' }} rounded-2xl">
                            <span class="{{ $offer->counter_price ? 'text-indigo-700 font-semibold' : 'text-slate-400' }} block">Tawaran Balik Petani:</span>
                            @if($offer->counter_price)
                                <span class="text-sm font-black text-indigo-900 mt-0.5 block">Rp {{ number_format($offer->counter_price, 0, ',', '.') }} / {{ $offer->product->unit }}</span>
                                <span class="text-[10px] text-indigo-700">Total: Rp {{ number_format($offer->counter_price * $offer->quantity, 0, ',', '.') }}</span>
                            @else
                                <span class="text-xs font-medium text-slate-400 mt-1 block">Belum ada counter offer</span>
                            @endif
                        </div>
                    </div>

                    @if($offer->notes)
                        <div class="p-3 bg-slate-50 rounded-xl text-xs text-slate-600 mb-3 border border-slate-100">
                            <strong>Catatan Anda:</strong> "{{ $offer->notes }}"
                        </div>
                    @endif

                    @if($offer->status === 'Counter Offer')
                        <!-- Counter Offer Resolution Action Box -->
                        <div class="p-4 bg-indigo-50/80 rounded-2xl border border-indigo-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold text-indigo-950">Petani menawarkan harga balik Rp {{ number_format($offer->counter_price, 0, ',', '.') }} / {{ $offer->product->unit }}</p>
                                <p class="text-[11px] text-indigo-700 mt-0.5">Jika Anda setuju, pesanan resmi akan langsung diterbitkan secara otomatis.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <form action="{{ route('offers.reject', $offer) }}" method="POST">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Tolak tawaran balik ini?')" class="px-3.5 py-2 rounded-xl bg-white hover:bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold transition">
                                        Tolak
                                    </button>
                                </form>
                                <form action="{{ route('offers.acceptCounter', $offer) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/20 transition flex items-center gap-1.5">
                                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                        <span>Sepakati Tawaran Balik</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @elseif($offer->status === 'Menunggu')
                        <div class="flex justify-end pt-2">
                            <form action="{{ route('offers.reject', $offer) }}" method="POST">
                                @csrf
                                <button type="submit" onclick="return confirm('Batalkan pengajuan negosiasi harga ini?')" class="text-xs font-semibold text-rose-600 hover:text-rose-700 transition">
                                    Batalkan Tawaran
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach

            <div class="pt-4">
                {{ $offers->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
