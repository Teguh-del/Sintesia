@extends('layouts.dashboard')

@section('title', 'Pesanan Masuk — SINTESA')
@section('header_title', 'Pesanan Masuk Hasil Panen')

@section('content')
<div class="space-y-6">
    <!-- Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-emerald-700 to-teal-800 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-block px-3 py-1 rounded-full bg-emerald-600/60 border border-emerald-500/40 text-xs font-semibold uppercase tracking-wider mb-3">
                Manajemen Penjualan Hasil Panen
            </span>
            <h2 class="text-2xl sm:text-3xl font-black tracking-tight mb-2">
                Pesanan Masuk dari Pembeli
            </h2>
            <p class="text-emerald-100 text-sm leading-relaxed">
                Kelola pesanan langsung dari pengepul dan konsumen. Konfirmasi permintaan, kemas hasil panen, dan selesaikan transaksi dengan sistem stok terintegrasi.
            </p>
        </div>
        <div class="absolute right-6 -bottom-6 opacity-10 hidden sm:block">
            <i data-lucide="inbox" class="w-48 h-48"></i>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2">
        <a href="{{ route('farmer.orders.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ !$status ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
            <span>Semua Pesanan</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ !$status ? 'bg-emerald-700 text-emerald-100' : 'bg-slate-100 text-slate-600' }}">{{ $counts['all'] }}</span>
        </a>
        <a href="{{ route('farmer.orders.index', ['status' => 'Menunggu Konfirmasi']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ $status === 'Menunggu Konfirmasi' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
            <span>Perlu Konfirmasi</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'Menunggu Konfirmasi' ? 'bg-amber-600 text-amber-100' : 'bg-slate-100 text-slate-600' }}">{{ $counts['pending'] }}</span>
        </a>
        <a href="{{ route('farmer.orders.index', ['status' => 'Dikonfirmasi']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ $status === 'Dikonfirmasi' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
            <span>Dikonfirmasi</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'Dikonfirmasi' ? 'bg-blue-700 text-blue-100' : 'bg-slate-100 text-slate-600' }}">{{ $counts['confirmed'] }}</span>
        </a>
        <a href="{{ route('farmer.orders.index', ['status' => 'Diproses']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ $status === 'Diproses' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
            <span>Sedang Dikemas / Kirim</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'Diproses' ? 'bg-indigo-700 text-indigo-100' : 'bg-slate-100 text-slate-600' }}">{{ $counts['processing'] }}</span>
        </a>
        <a href="{{ route('farmer.orders.index', ['status' => 'Selesai']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ $status === 'Selesai' ? 'bg-emerald-700 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
            <span>Selesai Terjual</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'Selesai' ? 'bg-emerald-800 text-emerald-100' : 'bg-slate-100 text-slate-600' }}">{{ $counts['completed'] }}</span>
        </a>
        <a href="{{ route('farmer.orders.index', ['status' => 'Dibatalkan']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ $status === 'Dibatalkan' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
            <span>Ditolak / Batal</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'Dibatalkan' ? 'bg-rose-700 text-rose-100' : 'bg-slate-100 text-slate-600' }}">{{ $counts['cancelled'] }}</span>
        </a>
    </div>

    <!-- Search & Filter Form -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('farmer.orders.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            @if($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ $search }}" 
                       placeholder="Cari nomor pesanan, nama komoditas, atau nama pembeli..." 
                       class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <button type="submit" class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition flex items-center justify-center gap-2">
                <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                <span>Terapkan</span>
            </button>
            @if($search || $status)
                <a href="{{ route('farmer.orders.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition flex items-center justify-center">
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
                            <span class="font-mono text-sm font-black text-slate-900">#{{ $order->order_number }}</span>
                            <span class="text-xs text-slate-400">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $order->status_badge_color }}">
                                {{ $order->status }}
                            </span>
                        </div>
                        <div class="text-xs text-slate-600">
                            Pembeli: <strong class="text-slate-900">{{ $order->buyer->name }}</strong>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 uppercase text-slate-600">{{ $order->buyer->role }}</span>
                            @if($order->buyer->phone)
                                &bull; <span class="text-slate-500">WA: {{ $order->buyer->phone }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Items Summary -->
                    <div class="space-y-2">
                        @foreach($order->items as $item)
                            <div class="flex items-center justify-between text-xs py-1">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span class="font-bold text-slate-800">{{ $item->product_name }}</span>
                                    <span class="text-slate-500 font-semibold">({{ $item->formatted_quantity }})</span>
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
                            @if($order->notes)
                                <p class="text-[11px] text-amber-700 font-medium mt-1">Catatan: "{{ $order->notes }}"</p>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between sm:justify-end gap-3 flex-wrap">
                            <div class="text-right sm:mr-3">
                                <span class="text-[10px] text-slate-400 block uppercase font-bold">Total Nilai</span>
                                <span class="text-base font-black text-emerald-700">{{ $order->formatted_total_amount }}</span>
                            </div>

                            @if($order->status === 'Menunggu Konfirmasi')
                                <form action="{{ route('farmer.orders.confirm', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                        <span>Konfirmasi</span>
                                    </button>
                                </form>
                            @elseif($order->status === 'Dikonfirmasi')
                                <form action="{{ route('farmer.orders.process', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                                        <i data-lucide="package" class="w-3.5 h-3.5"></i>
                                        <span>Mulai Proses / Kemas</span>
                                    </button>
                                </form>
                            @elseif($order->status === 'Diproses')
                                <form action="{{ route('farmer.orders.complete', $order) }}" method="POST" onsubmit="return confirm('Selesaikan pesanan ini? Stok terpesan akan resmi dicatat sebagai stok terjual.');">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                                        <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>
                                        <span>Selesaikan Transaksi</span>
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('farmer.orders.show', $order) }}" 
                               class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-sm transition flex items-center gap-1">
                                <span>Detail</span>
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
            <h3 class="text-base font-bold text-slate-900">Tidak Ada Pesanan Masuk</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                Belum ada pesanan dari pembeli dengan status ini. Pastikan produk marketplace Anda aktif dengan stok tersedia.
            </p>
            <div>
                <a href="{{ route('farmer.products.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition">
                    <i data-lucide="store" class="w-4 h-4"></i>
                    <span>Cek Produk Marketplace</span>
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
