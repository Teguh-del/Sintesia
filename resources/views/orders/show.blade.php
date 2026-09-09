@extends('layouts.dashboard')

@section('title', 'Detail Pesanan #' . $order->order_number . ' — SINTESA')
@section('header_title', 'Detail Pesanan #' . $order->order_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Back & Actions Bar (Hidden on Print) -->
    <div class="no-print flex items-center justify-between">
        <a href="{{ Auth::user()->id === $order->seller_id ? route('farmer.orders.index') : route('orders.index') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-xs font-bold text-slate-700 shadow-sm transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Daftar Pesanan</span>
        </a>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                <span>Cetak Faktur / Invoice</span>
            </button>
        </div>
    </div>

    <!-- Status Progression Banner & Timeline (Hidden on Print) -->
    <div class="no-print p-6 rounded-3xl bg-white border border-slate-200/80 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Status Transaksi Berjalan</span>
                <div class="flex items-center gap-3 mt-1">
                    <span class="text-xl font-black text-slate-900 font-mono">#{{ $order->order_number }}</span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $order->status_badge_color }}">
                        {{ $order->status }}
                    </span>
                </div>
            </div>

            <!-- Buyer Action Buttons -->
            @if(Auth::user()->id === $order->buyer_id)
                <div class="flex items-center gap-2">
                    @if($order->canBeCancelledByBuyer())
                        <button type="button" onclick="openCancelModal()" 
                                class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200 transition flex items-center gap-1.5">
                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                            <span>Batalkan Pesanan</span>
                        </button>
                    @endif

                    @if($order->canBeCompleted())
                        <form action="{{ route('orders.receive', $order) }}" method="POST" onsubmit="return confirm('Konfirmasi bahwa Anda telah menerima komoditas ini dengan baik?');">
                            @csrf
                            <button type="submit" 
                                    class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                <span>Konfirmasi Pesanan Diterima</span>
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>

        <!-- 4-Step Timeline Flow -->
        <div class="relative py-2">
            @if($order->status === 'Dibatalkan')
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5"></i>
                    <div>
                        <h4 class="font-bold text-xs uppercase tracking-wider">Pesanan Ini Telah Dibatalkan</h4>
                        <p class="text-xs mt-1">Alasan pembatalan: <span class="font-semibold">{{ $order->cancellation_reason ?? 'Tidak ada alasan khusus dicatat' }}</span></p>
                        <p class="text-[11px] text-rose-600 mt-1">Waktu: {{ $order->cancelled_at?->translatedFormat('d F Y, H:i') ?? '-' }} &bull; Seluruh kuantitas stok telah dipulihkan ke sistem.</p>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <!-- Step 1 -->
                    <div class="p-3 rounded-2xl bg-slate-50 border {{ $order->created_at ? 'border-emerald-500 bg-emerald-50/40' : 'border-slate-200' }}">
                        <div class="w-8 h-8 rounded-full {{ $order->created_at ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500' }} mx-auto flex items-center justify-center font-bold text-xs mb-2">
                            1
                        </div>
                        <p class="text-xs font-bold text-slate-800">Pesanan Dibuat</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $order->created_at->translatedFormat('d M, H:i') }}</p>
                    </div>

                    <!-- Step 2 -->
                    <div class="p-3 rounded-2xl bg-slate-50 border {{ $order->confirmed_at ? 'border-emerald-500 bg-emerald-50/40' : 'border-slate-200' }}">
                        <div class="w-8 h-8 rounded-full {{ $order->confirmed_at ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500' }} mx-auto flex items-center justify-center font-bold text-xs mb-2">
                            2
                        </div>
                        <p class="text-xs font-bold text-slate-800">Dikonfirmasi Petani</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $order->confirmed_at ? $order->confirmed_at->translatedFormat('d M, H:i') : 'Menunggu' }}</p>
                    </div>

                    <!-- Step 3 -->
                    <div class="p-3 rounded-2xl bg-slate-50 border {{ $order->processed_at ? 'border-emerald-500 bg-emerald-50/40' : 'border-slate-200' }}">
                        <div class="w-8 h-8 rounded-full {{ $order->processed_at ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500' }} mx-auto flex items-center justify-center font-bold text-xs mb-2">
                            3
                        </div>
                        <p class="text-xs font-bold text-slate-800">Diproses / Dikemas</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $order->processed_at ? $order->processed_at->translatedFormat('d M, H:i') : 'Menunggu' }}</p>
                    </div>

                    <!-- Step 4 -->
                    <div class="p-3 rounded-2xl bg-slate-50 border {{ $order->completed_at ? 'border-emerald-500 bg-emerald-50/40' : 'border-slate-200' }}">
                        <div class="w-8 h-8 rounded-full {{ $order->completed_at ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500' }} mx-auto flex items-center justify-center font-bold text-xs mb-2">
                            4
                        </div>
                        <p class="text-xs font-bold text-slate-800">Pesanan Selesai</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $order->completed_at ? $order->completed_at->translatedFormat('d M, H:i') : 'Menunggu' }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Official Printable Invoice Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-10 space-y-8 print:border-none print:shadow-none print:p-0">
        <!-- Invoice Header -->
        <div class="flex justify-between items-start pb-6 border-b border-slate-200 gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-md print:shadow-none">
                    <i data-lucide="sprout" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black tracking-tight text-slate-900">SINTESA</h3>
                    <p class="text-xs text-emerald-700 font-semibold uppercase">Faktur Transaksi Niaga Pertanian Cerdas</p>
                </div>
            </div>

            <div class="text-right text-xs text-slate-600 space-y-1">
                <div class="text-sm font-black text-slate-900 font-mono">INVOICE: #{{ $order->order_number }}</div>
                <div>Tanggal: <strong>{{ $order->created_at->translatedFormat('d F Y') }}</strong></div>
                <div>
                    Status: 
                    <span class="font-bold {{ $order->status === 'Selesai' ? 'text-emerald-700' : 'text-slate-800' }}">
                        {{ $order->status }}
                    </span>
                    &bull; 
                    <span class="font-bold text-emerald-700">{{ $order->payment_status }}</span>
                </div>
            </div>
        </div>

        <!-- Parties Involved (Buyer & Seller) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5 print:bg-white print:border-slate-300">
                <span class="font-bold text-slate-400 uppercase tracking-wider block text-[10px]">Pihak Penjual (Mitra Petani)</span>
                <h4 class="text-sm font-bold text-slate-900">{{ $order->seller->name }}</h4>
                <p class="text-slate-600">{{ $order->seller->farmerProfile->farm_name ?? 'Kebun Petani' }}</p>
                <p class="text-slate-500">Kontak: {{ $order->seller->phone ?? '-' }}</p>
                <p class="text-slate-500">Lokasi Asal: {{ $order->seller->farmerProfile->address ?? ($order->seller->address ?? 'Jawa Timur') }}</p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5 print:bg-white print:border-slate-300">
                <span class="font-bold text-slate-400 uppercase tracking-wider block text-[10px]">Pihak Pembeli ({{ ucfirst($order->buyer->role) }})</span>
                <h4 class="text-sm font-bold text-slate-900">{{ $order->buyer->name }}</h4>
                <p class="text-slate-600">{{ $order->buyer->collectorProfile->company_name ?? ($order->buyer->consumerProfile->address ?? 'Pembeli Terdaftar') }}</p>
                <p class="text-slate-500">Kontak: {{ $order->buyer->phone ?? '-' }}</p>
                <p class="text-slate-500">Alamat Pengiriman: {{ $order->shipping_address }}</p>
            </div>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500 bg-slate-50/50 print:bg-slate-100">
                        <th class="py-3 px-4">Komoditas / Produk</th>
                        <th class="py-3 px-4 text-center">Jumlah</th>
                        <th class="py-3 px-4 text-right">Harga Satuan</th>
                        <th class="py-3 px-4 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="py-4 px-4 font-bold text-slate-900">
                                {{ $item->product_name }}
                                @if($item->stock)
                                    <span class="block text-[10px] text-slate-400 font-normal">Batch: {{ $item->stock->batch_code }} &bull; Mutu: {{ $item->stock->quality }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center font-semibold text-slate-700">{{ $item->formatted_quantity }}</td>
                            <td class="py-4 px-4 text-right text-slate-600">{{ $item->formatted_price }}</td>
                            <td class="py-4 px-4 text-right font-black text-slate-900">{{ $item->formatted_subtotal }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t border-slate-200 text-xs">
                        <td colspan="3" class="py-2.5 px-4 text-right text-slate-500">Metode Pengiriman:</td>
                        <td class="py-2.5 px-4 text-right font-semibold text-slate-800">{{ $order->shipping_method }}</td>
                    </tr>
                    @if($order->shipping_cost > 0)
                    <tr class="text-xs">
                        <td colspan="3" class="py-2.5 px-4 text-right text-slate-500">Biaya Pengiriman:</td>
                        <td class="py-2.5 px-4 text-right font-semibold text-slate-800">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr class="text-xs">
                        <td colspan="3" class="py-2.5 px-4 text-right text-slate-500">Metode Pembayaran:</td>
                        <td class="py-2.5 px-4 text-right font-semibold text-slate-800">{{ $order->payment_method }}</td>
                    </tr>
                    <tr class="border-t-2 border-slate-900 text-sm">
                        <td colspan="3" class="py-4 px-4 text-right font-bold text-slate-900">Total Transaksi:</td>
                        <td class="py-4 px-4 text-right font-black text-emerald-700 text-lg">{{ $order->formatted_total_amount }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($order->notes)
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-xs print:bg-white">
                <span class="font-bold text-slate-700 block mb-1">Catatan Tambahan:</span>
                <p class="text-slate-600 italic">"{{ $order->notes }}"</p>
            </div>
        @endif

        <!-- Printable Footer Stamp -->
        <div class="pt-6 border-t border-slate-200 flex justify-between items-end text-[11px] text-slate-500">
            <div>
                <p class="font-bold text-slate-800">SINTESA — Niaga Pertanian Cerdas</p>
                <p class="text-slate-400 mt-0.5">Dokumen ini diterbitkan secara otomatis dan sah tanpa tanda tangan basah.</p>
            </div>
            <div class="text-right">
                <p class="text-slate-400">Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Batalkan Pesanan (Buyer, Hidden on Print) -->
@if(Auth::user()->id === $order->buyer_id && $order->canBeCancelledByBuyer())
    <div id="cancel-modal" class="no-print fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">Batalkan Pesanan Ini?</h3>
                <p class="text-xs text-slate-500 mt-1">Kuantitas stok yang telah di-reserve akan segera dikembalikan ke inventaris petani.</p>
            </div>

            <form action="{{ route('orders.cancel', $order) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Alasan Pembatalan <span class="text-rose-500">*</span></label>
                    <textarea name="reason" rows="3" required 
                              class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500"
                              placeholder="Tuliskan alasan pembatalan Anda..."></textarea>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="closeCancelModal()" class="flex-1 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        Kembali
                    </button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition">
                        Ya, Batalkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCancelModal() {
            document.getElementById('cancel-modal').classList.remove('hidden');
        }
        function closeCancelModal() {
            document.getElementById('cancel-modal').classList.add('hidden');
        }
    </script>
@endif
@endsection
