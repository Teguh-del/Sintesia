@extends('layouts.dashboard')

@section('title', 'Pre-Order Saya')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pre-Order Saya</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar alokasi kuota komoditas panen yang telah Anda pesan</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('preorders.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition">
                <i data-lucide="search" class="w-4 h-4"></i>
                <span>Jelajahi Pre-Order Lainnya</span>
            </a>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex flex-wrap items-center gap-2 pb-2 border-b border-slate-200">
        <a href="{{ route('preorders.my') }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ !$status ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Semua</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ !$status ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $counts['all'] }}</span>
        </a>
        <a href="{{ route('preorders.my', ['status' => 'Menunggu Panen']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Menunggu Panen' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Menunggu Panen</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $status === 'Menunggu Panen' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-700' }}">{{ $counts['pending'] }}</span>
        </a>
        <a href="{{ route('preorders.my', ['status' => 'Dikonfirmasi']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Dikonfirmasi' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Telah Dikonversi ke Pesanan</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $status === 'Dikonfirmasi' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-700' }}">{{ $counts['converted'] }}</span>
        </a>
        <a href="{{ route('preorders.my', ['status' => 'Dibatalkan']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Dibatalkan' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Dibatalkan</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $status === 'Dibatalkan' ? 'bg-white/20 text-white' : 'bg-rose-100 text-rose-700' }}">{{ $counts['cancelled'] }}</span>
        </a>
    </div>

    <!-- Items List -->
    @if($items->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center max-w-lg mx-auto space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto">
                <i data-lucide="calendar-clock" class="w-8 h-8"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Pemesanan Pre-Order</h3>
                <p class="text-xs text-slate-500 mt-1">Amankan kuota panen petani dari awal untuk menjamin kepastian pasokan pangan Anda.</p>
            </div>
            <a href="{{ route('preorders.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold transition">
                <span>Lihat Daftar Pre-Order</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($items as $item)
                <div class="bg-white rounded-3xl border border-slate-200 hover:border-slate-300 shadow-sm p-5 sm:p-6 transition">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3.5">
                            <img src="{{ $item->preorder->image_url }}" alt="{{ $item->preorder->title }}" class="w-16 h-16 rounded-2xl object-cover border border-slate-200 flex-shrink-0">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap mb-0.5">
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
                                        {{ $item->preorder->commodity->name }}
                                    </span>
                                    <span class="text-xs text-slate-400">•</span>
                                    <span class="text-xs text-slate-500 font-medium">Petani: <strong class="text-slate-800">{{ $item->preorder->farmer->name }}</strong> ({{ $item->preorder->location }})</span>
                                </div>
                                <h3 class="text-base font-bold text-slate-900">
                                    <a href="{{ route('preorders.show', $item->preorder->slug) }}" class="hover:text-emerald-600 transition">
                                        {{ $item->preorder->title }}
                                    </a>
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1.5">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                                    <span>Estimasi Panen: <strong class="text-slate-700">{{ \Carbon\Carbon::parse($item->preorder->estimated_harvest_date)->translatedFormat('d F Y') }}</strong></span>
                                </p>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            @if($item->status === 'Menunggu Panen')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span>Menunggu Masa Panen</span>
                                </span>
                            @elseif($item->status === 'Dikonfirmasi')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-600"></i>
                                    <span>Panen Siap & Dikonversi ke Pesanan</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                                    <span>{{ $item->status }}</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Metrics -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 py-4 text-xs">
                        <div class="p-3 bg-slate-50 rounded-2xl">
                            <span class="text-slate-400 font-semibold block">Kuota Dipesan:</span>
                            <span class="text-sm font-black text-slate-800 mt-0.5 block">{{ number_format($item->quantity, 0, ',', '.') }} {{ $item->preorder->unit }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-2xl">
                            <span class="text-slate-400 font-semibold block">Harga Satuan Terkunci:</span>
                            <span class="text-sm font-black text-emerald-700 mt-0.5 block">Rp {{ number_format($item->price_per_unit, 0, ',', '.') }} / {{ $item->preorder->unit }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-2xl">
                            <span class="text-slate-400 font-semibold block">Total Komitmen Biaya:</span>
                            <span class="text-sm font-black text-slate-900 mt-0.5 block">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-2xl">
                            <span class="text-slate-400 font-semibold block">Metode Pengiriman:</span>
                            <span class="text-xs font-bold text-slate-700 mt-1 block truncate">{{ $item->shipping_method }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
                        <span class="text-slate-400">Dipesan pada {{ $item->created_at->translatedFormat('d F Y H:i') }}</span>
                        <div class="flex items-center gap-3">
                            @if($item->order_id)
                                <a href="{{ route('orders.show', $item->order_id) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition flex items-center gap-1.5 shadow-sm">
                                    <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                                    <span>Lihat Pesanan Resmi</span>
                                </a>
                            @elseif($item->status === 'Menunggu Panen')
                                <form action="{{ route('preorders.cancelBooking', $item) }}" method="POST">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Batalkan pemesanan kuota pre-order ini? Kuota akan dikembalikan ke petani.')" 
                                            class="text-xs font-semibold text-rose-600 hover:text-rose-700 transition">
                                        Batalkan Kuota
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="pt-4">
                {{ $items->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
