@extends('layouts.dashboard')

@section('title', 'Pesanan Saya — SINTESA')
@section('header_title', 'Pesanan Saya')

@section('content')
<div class="space-y-6">
    <!-- Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-emerald-800 to-teal-900 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-block px-3 py-1 rounded-full bg-emerald-700/60 border border-emerald-500/40 text-xs font-semibold uppercase tracking-wider mb-3">
                Manajemen Pembelian
            </span>
            <h2 class="text-2xl sm:text-3xl font-black tracking-tight mb-2">
                Daftar Pesanan & Riwayat Transaksi
            </h2>
            <p class="text-emerald-100 text-sm leading-relaxed">
                Pantau progres pesanan hasil panen langsung dari petani, konfirmasi penerimaan barang, dan kelola arsip invoice digital Anda.
            </p>
        </div>
        <div class="absolute right-6 -bottom-6 opacity-10 hidden sm:block">
            <i data-lucide="shopping-bag" class="w-48 h-48"></i>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2">
        <a href="{{ route('orders.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ !$status ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
            <span>Semua Pesanan</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ !$status ? 'bg-emerald-700 text-emerald-100' : 'bg-slate-100 text-slate-600' }}">{{ $counts['all'] }}</span>
        </a>
        <a href="{{ route('orders.index', ['status' => 'Menunggu Konfirmasi']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ $status === 'Menunggu Konfirmasi' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
            <span>Menunggu Konfirmasi</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'Menunggu Konfirmasi' ? 'bg-amber-600 text-amber-100' : 'bg-slate-100 text-slate-600' }}">{{ $counts['pending'] }}</span>
        </a>
        <a href="{{ route('orders.index', ['status' => 'Dikonfirmasi']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ $status === 'Dikonfirmasi' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
            <span>Dikonfirmasi</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'Dikonfirmasi' ? 'bg-blue-700 text-blue-100' : 'bg-slate-100 text-slate-600' }}">{{ $counts['confirmed'] }}</span>
        </a>
        <a href="{{ route('orders.index', ['status' => 'Diproses']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ $status === 'Diproses' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
            <span>Diproses</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'Diproses' ? 'bg-indigo-700 text-indigo-100' : 'bg-slate-100 text-slate-600' }}">{{ $counts['processing'] }}</span>
        </a>
        <a href="{{ route('orders.index', ['status' => 'Selesai']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ $status === 'Selesai' ? 'bg-emerald-700 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
            <span>Selesai</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'Selesai' ? 'bg-emerald-800 text-emerald-100' : 'bg-slate-100 text-slate-600' }}">{{ $counts['completed'] }}</span>
        </a>
        <a href="{{ route('orders.index', ['status' => 'Dibatalkan']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ $status === 'Dibatalkan' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
            <span>Dibatalkan</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'Dibatalkan' ? 'bg-rose-700 text-rose-100' : 'bg-slate-100 text-slate-600' }}">{{ $counts['cancelled'] }}</span>
        </a>
    </div>

    <!-- Search & Filter Filter Form -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('orders.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            @if($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ $search }}" 
                       placeholder="Cari nomor pesanan, nama komoditas, atau nama petani..." 
                       class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <button type="submit" class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition flex items-center justify-center gap-2">
                <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                <span>Terapkan</span>
            </button>
            @if($search || $status)
                <a href="{{ route('orders.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition flex items-center justify-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Order List -->
    @if($orders->count() > 0)
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 hover:border-emerald-500/40 shadow-sm hover:shadow transition space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-100 gap-2">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-sm font-black text-slate-900">{{ $order->order_number }}</span>
                            <span class="text-xs text-slate-400">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $order->status_badge_color }}">
                                {{ $order->status }}
                            </span>
                        </div>
                        <div class="text-xs text-slate-500">
                            Petani: <strong class="text-slate-800">{{ $order->seller->name }}</strong>
                            <span class="text-slate-400">({{ $order->seller->farmerProfile->farm_name ?? 'Kebun Petani' }})</span>
                        </div>
                    </div>

                    <!-- Items Summary -->
                    <div class="space-y-2">
                        @foreach($order->items as $item)
                            <div class="flex items-center justify-between text-xs py-1">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span class="font-bold text-slate-800">{{ $item->product_name }}</span>
                                    <span class="text-slate-400">({{ $item->formatted_quantity }})</span>
                                </div>
                                <div class="font-bold text-slate-900">
                                    {{ $item->formatted_subtotal }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="text-xs text-slate-600">
                            Metode: <span class="font-semibold text-slate-800">{{ $order->shipping_method }}</span> &bull; 
                            Pembayaran: <span class="font-semibold text-slate-800">{{ $order->payment_method }}</span>
                        </div>
                        <div class="flex items-center justify-between sm:justify-end gap-4">
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 block uppercase font-bold">Total Pembayaran</span>
                                <span class="text-base font-black text-emerald-700">{{ $order->formatted_total_amount }}</span>
                            </div>
                            <a href="{{ route('orders.show', $order) }}" 
                               class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                                <span>Detail Pesanan</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            <div class="pt-4">
                {{ $orders->links() }}
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white p-12 rounded-3xl border border-slate-200/80 text-center space-y-4">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center">
                <i data-lucide="inbox" class="w-8 h-8"></i>
            </div>
            <h3 class="text-base font-bold text-slate-900">Belum Ada Pesanan</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                Anda belum memiliki riwayat pesanan dengan filter saat ini. Kunjungi marketplace untuk menemukan komoditas langsung dari petani.
            </p>
            <div>
                <a href="{{ route('marketplace.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition">
                    <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                    <span>Jelajahi Marketplace</span>
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
