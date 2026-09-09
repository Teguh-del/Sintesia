@extends('layouts.dashboard')

@section('title', 'Detail Pesanan Masuk #' . $order->order_number . ' — SINTESA')
@section('header_title', 'Pesanan Masuk #' . $order->order_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Back & Action Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('farmer.orders.index') }}" 
           class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Daftar Pesanan Masuk</span>
        </a>

        <!-- Farmer Stage Action Controls -->
        <div class="flex items-center gap-2">
            @if($order->status === 'Menunggu Konfirmasi')
                <button type="button" onclick="openRejectModal()" 
                        class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200 transition flex items-center gap-1.5">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    <span>Tolak Pesanan</span>
                </button>
                <form action="{{ route('farmer.orders.confirm', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Konfirmasi Pesanan</span>
                    </button>
                </form>
            @elseif($order->status === 'Dikonfirmasi')
                <button type="button" onclick="openRejectModal()" 
                        class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200 transition flex items-center gap-1.5">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    <span>Batalkan</span>
                </button>
                <form action="{{ route('farmer.orders.process', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/20 transition flex items-center gap-1.5">
                        <i data-lucide="package" class="w-4 h-4"></i>
                        <span>Mulai Proses / Kemas</span>
                    </button>
                </form>
            @elseif($order->status === 'Diproses')
                <form action="{{ route('farmer.orders.complete', $order) }}" method="POST" onsubmit="return confirm('Apakah pesanan ini sudah diterima pembeli? Selesaikan pesanan?');">
                    @csrf
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-md transition flex items-center gap-1.5">
                        <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                        <span>Selesaikan Transaksi</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Status Overview -->
    <div class="p-6 rounded-3xl bg-white border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Status Pesanan Saat Ini</span>
                <div class="flex items-center gap-3 mt-1">
                    <span class="text-xl font-black text-slate-900 font-mono">#{{ $order->order_number }}</span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $order->status_badge_color }}">
                        {{ $order->status }}
                    </span>
                </div>
            </div>
            <div class="sm:text-right text-xs text-slate-500">
                <p>Waktu Masuk: <strong class="text-slate-900">{{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</strong></p>
                <p>Sumber: <strong class="text-emerald-700 uppercase">{{ str_replace('_', ' ', $order->source_type) }}</strong></p>
            </div>
        </div>

        @if($order->status === 'Dibatalkan')
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                <strong>Pesanan Dibatalkan:</strong> {{ $order->cancellation_reason ?? 'Tidak ada catatan alasan.' }}
                <span class="block text-[11px] text-rose-600 mt-1">Waktu: {{ $order->cancelled_at?->translatedFormat('d M Y, H:i') }} &bull; Stok telah otomatis dikembalikan ke inventaris.</span>
            </div>
        @endif
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Buyer Info -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100 text-slate-900 font-bold text-sm">
                <i data-lucide="user" class="w-4 h-4 text-emerald-600"></i>
                <span>Informasi Pembeli</span>
            </div>

            <div class="space-y-2.5 text-xs">
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Nama Pembeli</span>
                    <h4 class="font-bold text-slate-900 text-sm">{{ $order->buyer->name }}</h4>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 uppercase text-slate-600 inline-block mt-0.5">
                        {{ $order->buyer->role }}
                    </span>
                </div>

                @if($order->buyer->phone)
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Kontak / WhatsApp</span>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->buyer->phone) }}" target="_blank" class="font-semibold text-emerald-600 hover:underline flex items-center gap-1 mt-0.5">
                            <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                            <span>{{ $order->buyer->phone }}</span>
                        </a>
                    </div>
                @endif

                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Alamat Pengiriman</span>
                    <p class="text-slate-700 font-medium leading-relaxed">{{ $order->shipping_address }}</p>
                </div>
            </div>
        </div>

        <!-- Order Items & Stock Integration -->
        <div class="md:col-span-2 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2 text-slate-900 font-bold text-sm">
                    <i data-lucide="boxes" class="w-4 h-4 text-emerald-600"></i>
                    <span>Rincian Komoditas & Integrasi Stok</span>
                </div>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg">Stok Riil Terpotong</span>
            </div>

            <div class="space-y-4">
                @foreach($order->items as $item)
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-slate-900 text-sm">{{ $item->product_name }}</h4>
                            <span class="text-base font-black text-emerald-700">{{ $item->formatted_subtotal }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-slate-500">
                            <span>Kuantitas: <strong class="text-slate-800">{{ $item->formatted_quantity }}</strong> @ {{ $item->formatted_price }}</span>
                            @if($item->stock)
                                <a href="{{ route('farmer.stocks.show', $item->stock_id) }}" class="text-emerald-600 hover:underline font-semibold text-[11px] flex items-center gap-1">
                                    <span>Lihat Batch: {{ $item->stock->batch_code }}</span>
                                    <i data-lucide="external-link" class="w-3 h-3"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Transaction & Logistics Summary -->
            <div class="p-4 bg-slate-50/70 rounded-2xl border border-slate-200/80 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Metode Pengambilan:</span>
                    <strong class="text-slate-800">{{ $order->shipping_method }}</strong>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Metode Pembayaran:</span>
                    <strong class="text-slate-800">{{ $order->payment_method }}</strong>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Status Pembayaran:</span>
                    <strong class="{{ $order->payment_status === 'Sudah Dibayar' ? 'text-emerald-600' : 'text-amber-600' }}">{{ $order->payment_status }}</strong>
                </div>
                <div class="pt-2 border-t border-slate-200 flex justify-between items-baseline text-sm">
                    <span class="font-bold text-slate-900">Total Transaksi:</span>
                    <span class="font-black text-emerald-700 text-lg">{{ $order->formatted_total_amount }}</span>
                </div>
            </div>

            @if($order->notes)
                <div class="p-3.5 bg-amber-50/60 border border-amber-200 rounded-xl text-xs text-amber-900">
                    <strong>Catatan Khusus dari Pembeli:</strong>
                    <p class="mt-0.5 italic">"{{ $order->notes }}"</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Tolak Pesanan -->
<div id="reject-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
            <i data-lucide="alert-triangle" class="w-6 h-6"></i>
        </div>
        <div>
            <h3 class="text-lg font-bold text-slate-900">Tolak / Batalkan Pesanan Masuk?</h3>
            <p class="text-xs text-slate-500 mt-1">
                Kuantitas komoditas yang dipesan akan otomatis dikembalikan ke stok aktif produk Anda di marketplace.
            </p>
        </div>

        <form action="{{ route('farmer.orders.reject', $order) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Alasan Penolakan <span class="text-rose-500">*</span></label>
                <textarea name="reason" rows="3" required 
                          class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500"
                          placeholder="Contoh: Stok telah dialokasikan untuk pesanan kontrak, atau kendala panen..."></textarea>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeRejectModal()" class="flex-1 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition">
                    Konfirmasi Penolakan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal() {
        document.getElementById('reject-modal').classList.remove('hidden');
    }
    function closeRejectModal() {
        document.getElementById('reject-modal').classList.add('hidden');
    }
</script>
@endsection
