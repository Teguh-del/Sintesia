@extends('layouts.dashboard')

@section('title', 'Audit Transaksi #' . $order->order_number)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.transactions.index') }}" class="p-2 bg-white hover:bg-stone-100 rounded-xl border border-stone-200 text-stone-600 transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-stone-900 tracking-tight">Audit Pesanan #{{ $order->order_number }}</h1>
                <p class="text-sm text-stone-500">Dibuat pada {{ $order->created_at ? $order->created_at->format('d F Y, H:i') : '-' }} WIB</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-stone-100 text-stone-700 rounded-xl border border-stone-200 text-sm font-semibold transition shadow-sm">
                <i data-lucide="printer" class="w-4 h-4"></i> Cetak Dokumen
            </button>
        </div>
    </div>

    <!-- Main Audit Card -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6 sm:p-8 space-y-8">
        <!-- Status Banner -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 rounded-xl bg-stone-50 border border-stone-200 gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="text-xs text-stone-500 uppercase tracking-wider block">Status Sistem</span>
                    <span class="font-bold text-stone-900 capitalize">{{ $order->status }}</span>
                </div>
            </div>
            <div class="text-right">
                <span class="text-xs text-stone-500 uppercase tracking-wider block">Metode Pembayaran</span>
                <span class="font-bold text-stone-800 uppercase text-sm">
                    {{ $order->payment_method ?? 'Transfer Bank / SINTESA Escrow' }}
                </span>
                <div class="text-xs text-stone-500 mt-0.5">
                    Status: <span class="font-semibold text-emerald-700">{{ $order->payment_status }}</span>
                </div>
            </div>
        </div>

        <!-- Parties Involved (Buyer & Seller) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Buyer -->
            <div class="p-5 rounded-xl border border-stone-200 space-y-3 bg-stone-50/50">
                <div class="flex items-center justify-between border-b border-stone-200 pb-2">
                    <h3 class="font-bold text-stone-900 text-sm flex items-center gap-2">
                        <i data-lucide="user" class="w-4 h-4 text-blue-600"></i> Pihak Pembeli
                    </h3>
                    <span class="text-xs px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-semibold uppercase">
                        {{ $order->buyer->role ?? 'Buyer' }}
                    </span>
                </div>
                <div class="text-sm space-y-1">
                    <div class="font-bold text-stone-800">{{ $order->buyer->name ?? '-' }}</div>
                    <div class="text-stone-500 text-xs">{{ $order->buyer->email ?? '-' }}</div>
                    <div class="text-stone-500 text-xs">{{ $order->buyer->phone ?? '-' }}</div>
                    <div class="text-stone-600 text-xs pt-2">
                        <span class="font-semibold block text-stone-700">Alamat Pengiriman:</span>
                        {{ $order->shipping_address ?? ($order->buyer->address ?? '-') }}
                    </div>
                </div>
            </div>

            <!-- Seller -->
            <div class="p-5 rounded-xl border border-stone-200 space-y-3 bg-stone-50/50">
                <div class="flex items-center justify-between border-b border-stone-200 pb-2">
                    <h3 class="font-bold text-stone-900 text-sm flex items-center gap-2">
                        <i data-lucide="sprout" class="w-4 h-4 text-emerald-600"></i> Pihak Penjual (Petani)
                    </h3>
                    <span class="text-xs px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-semibold uppercase">
                        Petani
                    </span>
                </div>
                <div class="text-sm space-y-1">
                    <div class="font-bold text-stone-800">{{ $order->seller->name ?? '-' }}</div>
                    <div class="text-stone-500 text-xs">{{ $order->seller->email ?? '-' }}</div>
                    <div class="text-stone-500 text-xs">{{ $order->seller->phone ?? '-' }}</div>
                    <div class="text-stone-600 text-xs pt-2">
                        <span class="font-semibold block text-stone-700">Lokasi Kebun / Asal:</span>
                        {{ $order->seller->address ?? '-' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items Table -->
        <div>
            <h3 class="font-bold text-stone-900 text-base mb-3 flex items-center gap-2">
                <i data-lucide="package" class="w-4 h-4 text-emerald-600"></i> Rincian Komoditas & Produk
            </h3>
            <div class="border border-stone-200 rounded-xl overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-stone-50 text-xs font-semibold text-stone-600 uppercase border-b border-stone-200">
                            <th class="py-3 px-4">Item</th>
                            <th class="py-3 px-4 text-right">Harga Satuan</th>
                            <th class="py-3 px-4 text-center">Jumlah</th>
                            <th class="py-3 px-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-sm">
                        @foreach($order->items as $item)
                        <tr>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-stone-900 block">
                                    {{ $item->product_name ?? ($item->product->name ?? 'Komoditas') }}
                                </span>
                                @if($item->product && $item->product->commodity)
                                <span class="text-xs text-stone-400">
                                    Varietas: {{ $item->product->commodity->name }}
                                </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right text-stone-700">
                                Rp {{ number_format($item->price, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-medium text-stone-800">
                                {{ number_format($item->quantity) }} {{ $item->unit ?? 'kg' }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-stone-900">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-stone-50/70 border-t border-stone-200 text-sm">
                        @if($order->shipping_cost > 0)
                        <tr>
                            <td colspan="3" class="py-2.5 px-4 text-right text-stone-500">Biaya Pengiriman:</td>
                            <td class="py-2.5 px-4 text-right font-medium text-stone-800">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr class="font-bold text-base">
                            <td colspan="3" class="py-3 px-4 text-right text-stone-900">Total Transaksi:</td>
                            <td class="py-3 px-4 text-right text-emerald-700">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if($order->notes)
        <div class="bg-amber-50/50 p-4 rounded-xl border border-amber-200/70">
            <span class="text-xs font-bold text-amber-800 block mb-1">Catatan Tambahan Transaksi:</span>
            <p class="text-xs text-amber-900">{{ $order->notes }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
