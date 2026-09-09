@extends('layouts.dashboard')

@section('title', 'Monitoring Transaksi Sistem')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-stone-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-bold text-stone-900 tracking-tight">Monitoring Transaksi Sistem</h1>
            <p class="text-sm text-stone-500 mt-1">Audit seluruh aktivitas perdagangan, pesanan, dan arus dana transaksi antar pengguna SINTESA.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <i data-lucide="receipt" class="w-3.5 h-3.5 mr-1.5"></i> Total: {{ $totalOrders }} Pesanan
            </span>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-stone-500 uppercase tracking-wider">Gross Merchandise Value</p>
                <h3 class="text-xl font-black text-emerald-700 mt-1">Rp {{ number_format($totalGmv, 0, ',', '.') }}</h3>
                <p class="text-[11px] text-stone-400 mt-0.5">Total transaksi berhasil</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="wallet" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-stone-500 uppercase tracking-wider">Total Pesanan</p>
                <h3 class="text-xl font-black text-stone-900 mt-1">{{ number_format($totalOrders) }}</h3>
                <p class="text-[11px] text-stone-400 mt-0.5">Sepanjang sistem berjalan</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <i data-lucide="shopping-cart" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-stone-500 uppercase tracking-wider">Transaksi Selesai</p>
                <h3 class="text-xl font-black text-emerald-700 mt-1">{{ number_format($completedOrders) }}</h3>
                <p class="text-[11px] text-stone-400 mt-0.5">Tuntas & Diterima</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-stone-500 uppercase tracking-wider">Perlu Dipantau / Berjalan</p>
                <h3 class="text-xl font-black text-amber-600 mt-1">{{ number_format($pendingOrders) }}</h3>
                <p class="text-[11px] text-stone-400 mt-0.5">Konfirmasi & Pengiriman</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <i data-lucide="clock" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
        <form action="{{ route('admin.transactions.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-6 relative">
                <i data-lucide="search" class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor order (ORD-...) atau nama pembeli/penjual..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-stone-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
            </div>
            <div class="md:col-span-3">
                <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                    <option value="">Semua Status Transaksi</option>
                    <option value="Menunggu Konfirmasi" {{ request('status') == 'Menunggu Konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                    <option value="Dikonfirmasi" {{ request('status') == 'Dikonfirmasi' ? 'selected' : '' }}>Dikonfirmasi</option>
                    <option value="Diproses" {{ request('status') == 'Diproses' ? 'selected' : '' }}>Diproses / Dikirim</option>
                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="Dibatalkan" {{ request('status') == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="md:col-span-3 flex gap-2">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold py-2.5 px-4 rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.transactions.index') }}" class="px-3.5 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-600 rounded-xl transition flex items-center justify-center">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200 text-xs font-semibold text-stone-600 uppercase tracking-wider">
                        <th class="py-3.5 px-4">Invoice & Tanggal</th>
                        <th class="py-3.5 px-4">Pihak Transaksi</th>
                        <th class="py-3.5 px-4">Tipe & Komoditas</th>
                        <th class="py-3.5 px-4">Total Nilai</th>
                        <th class="py-3.5 px-4">Status Transaksi</th>
                        <th class="py-3.5 px-4 text-center">Audit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-sm">
                    @forelse($orders as $order)
                    <tr class="hover:bg-stone-50/70 transition">
                        <td class="py-4 px-4">
                            <span class="font-bold text-stone-900 block font-mono text-xs">#{{ $order->order_number }}</span>
                            <span class="text-xs text-stone-400">{{ $order->created_at ? $order->created_at->format('d M Y, H:i') : '-' }}</span>
                        </td>
                        <td class="py-4 px-4 text-xs space-y-1">
                            <div>
                                <span class="text-stone-400">Beli:</span>
                                <span class="font-bold text-stone-800">{{ $order->buyer->name ?? 'Anonim' }}</span>
                                <span class="text-[10px] px-1.5 py-0.2 rounded bg-stone-100 text-stone-600 uppercase">{{ $order->buyer->role ?? '' }}</span>
                            </div>
                            <div>
                                <span class="text-stone-400">Jual:</span>
                                <span class="font-bold text-stone-800">{{ $order->seller->name ?? 'Anonim' }}</span>
                                <span class="text-[10px] px-1.5 py-0.2 rounded bg-emerald-50 text-emerald-700 uppercase">Petani</span>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-xs">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-stone-100 text-stone-700 uppercase mb-1">
                                {{ str_replace('_', ' ', $order->source_type ?? 'direct_purchase') }}
                            </span>
                            <div class="text-stone-600 font-medium truncate max-w-xs">
                                @if($order->items->isNotEmpty())
                                    {{ $order->items->first()->product_name ?? ($order->items->first()->product->name ?? 'Komoditas') }}
                                    @if($order->items->count() > 1)
                                        <span class="text-stone-400">+{{ $order->items->count() - 1 }} item lain</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="font-bold text-stone-900 block">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            <span class="text-[11px]">
                                @if($order->payment_status === 'Sudah Dibayar')
                                    <span class="text-emerald-600 font-semibold">Lunas</span>
                                @else
                                    <span class="text-amber-600 font-semibold">{{ $order->payment_status ?? 'Pending' }}</span>
                                @endif
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            @if($order->status === 'Selesai')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Selesai
                                </span>
                            @elseif($order->status === 'Diproses')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span> Diproses
                                </span>
                            @elseif($order->status === 'Dikonfirmasi')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 mr-1.5"></span> Dikonfirmasi
                                </span>
                            @elseif($order->status === 'Dibatalkan')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span> Dibatalkan
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span> Menunggu Konfirmasi
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-center">
                            <a href="{{ route('admin.transactions.show', $order->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-stone-100 hover:bg-emerald-50 hover:text-emerald-700 text-stone-700 rounded-lg text-xs font-semibold transition" title="Lihat Invoice & Detail">
                                <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-stone-500">
                            <i data-lucide="receipt" class="w-12 h-12 mx-auto text-stone-300 mb-3"></i>
                            <p class="font-medium text-stone-700">Tidak ada transaksi ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="p-4 border-t border-stone-200">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
