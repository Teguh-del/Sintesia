@extends('layouts.app')

@section('title', $preorder->title . ' - Pre-Order SINTESA')

@section('content')
<div class="bg-slate-50 min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-slate-800 transition">Beranda</a>
            <span>/</span>
            <a href="{{ route('preorders.index') }}" class="hover:text-slate-800 transition">Pre-Order</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold truncate">{{ $preorder->title }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Left Column: Preorder Media & Info (7 cols) -->
            <div class="lg:col-span-7 space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
                    <div class="relative h-72 sm:h-96 w-full bg-slate-100">
                        <img src="{{ $preorder->image_url }}" alt="{{ $preorder->title }}" class="w-full h-full object-cover">
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1.5 rounded-xl text-xs font-black uppercase tracking-wider bg-slate-900/80 backdrop-blur-md text-emerald-400 border border-emerald-500/30">
                                {{ $preorder->commodity->name }}
                            </span>
                        </div>
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1.5 rounded-xl text-xs font-black bg-amber-500 text-slate-950 shadow-md flex items-center gap-1.5">
                                <i data-lucide="clock" class="w-4 h-4"></i>
                                <span>Panen: {{ \Carbon\Carbon::parse($preorder->estimated_harvest_date)->translatedFormat('d F Y') }}</span>
                            </span>
                        </div>
                    </div>

                    <div class="p-6 sm:p-8 space-y-6">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ $preorder->title }}</h1>
                            <p class="text-xs text-slate-500 mt-2 flex items-center gap-1.5">
                                <i data-lucide="map-pin" class="w-4 h-4 text-slate-400"></i>
                                <span>Lokasi Kebun: <strong class="text-slate-700">{{ $preorder->location }}</strong></span>
                            </p>
                        </div>

                        <!-- Quota Capacity Card -->
                        @php
                            $total = (float) $preorder->estimated_production;
                            $avail = (float) $preorder->preorder_available_quantity;
                            $booked = max(0, $total - $avail);
                            $percent = $total > 0 ? min(100, round(($booked / $total) * 100)) : 0;
                        @endphp
                        <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-3">
                            <div class="flex items-center justify-between text-xs font-bold">
                                <span class="text-slate-600">Alokasi Kuota Terpesan:</span>
                                <span class="text-emerald-700">{{ number_format($booked, 0, ',', '.') }} / {{ number_format($total, 0, ',', '.') }} {{ $preorder->unit }} ({{ $percent }}%)</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 h-3 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500">Sisa Kuota Tersedia: <strong class="text-slate-800">{{ number_format($avail, 0, ',', '.') }} {{ $preorder->unit }}</strong></span>
                                <span class="text-slate-500">Min. Pemesanan: <strong class="text-slate-800">{{ number_format($preorder->min_order, 0, ',', '.') }} {{ $preorder->unit }}</strong></span>
                            </div>
                        </div>

                        <!-- Farmer Profile Box -->
                        <div class="p-4 bg-emerald-50/50 rounded-2xl border border-emerald-100 flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white font-black flex items-center justify-center text-base flex-shrink-0">
                                {{ substr($preorder->farmer->name, 0, 2) }}
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">{{ $preorder->farmer->name }}</h3>
                                <p class="text-xs text-slate-500">Petani Produsen Resmi SINTESA</p>
                                <p class="text-xs text-emerald-800 font-semibold mt-0.5">{{ $preorder->farmer->farmerProfile->regency ?? $preorder->location }}</p>
                            </div>
                        </div>

                        @if($preorder->description)
                            <div class="space-y-2 pt-2 border-t border-slate-100">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Rencana Budidaya & Mutu Panen</h3>
                                <p class="text-xs text-slate-600 leading-relaxed">{{ $preorder->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Booking Order Form (5 cols) -->
            <div class="lg:col-span-5">
                <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6 sticky top-24">
                    <div class="pb-4 border-b border-slate-100">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Harga Satuan Pre-Order:</span>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-3xl font-black text-emerald-700">Rp {{ number_format($preorder->price, 0, ',', '.') }}</span>
                            <span class="text-xs font-semibold text-slate-400">/ {{ $preorder->unit }}</span>
                        </div>
                    </div>

                    @auth
                        @if(Auth::id() === $preorder->user_id)
                            <div class="p-4 bg-amber-50 rounded-2xl border border-amber-200 text-xs text-amber-800 space-y-3">
                                <p class="font-semibold">Ini adalah kampanye pre-order yang Anda buka.</p>
                                <a href="{{ route('farmer.preorders.show', $preorder) }}" class="block text-center py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold transition">
                                    Kelola Pemesanan Kuota
                                </a>
                            </div>
                        @elseif($preorder->preorder_available_quantity <= 0)
                            <div class="p-4 bg-slate-100 rounded-2xl text-center text-xs font-bold text-slate-600">
                                Kuota Pre-Order Telah Habis Terpesan
                            </div>
                        @else
                            <form action="{{ route('preorders.book', $preorder) }}" method="POST" class="space-y-4">
                                @csrf

                                <!-- Quantity -->
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="text-xs font-bold uppercase tracking-wider text-slate-700">
                                            Jumlah Pesanan ({{ $preorder->unit }}) <span class="text-rose-500">*</span>
                                        </label>
                                        <span class="text-[11px] text-slate-400">Min: {{ number_format($preorder->min_order, 0) }} {{ $preorder->unit }}</span>
                                    </div>
                                    <input type="number" id="po-qty-input" name="quantity" 
                                           step="any"
                                           min="{{ $preorder->min_order ?: 1 }}" 
                                           max="{{ $preorder->preorder_available_quantity }}"
                                           value="{{ max((int)$preorder->min_order, 1) }}"
                                           oninput="calcPoSubtotal()"
                                           required
                                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                    <p class="text-[11px] text-slate-400 mt-1">Maksimal: {{ number_format($preorder->preorder_available_quantity, 0, ',', '.') }} {{ $preorder->unit }}</p>
                                </div>

                                <!-- Shipping Method -->
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                        Rencana Pengiriman saat Panen <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="shipping_method" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                        <option value="Ambil di Lokasi Petani">Ambil Sendiri di Kebun ({{ $preorder->location }})</option>
                                        <option value="Pengiriman / Kurir">Kirim ke Alamat Pembeli (Ekspedisi/Armada Lokal)</option>
                                    </select>
                                </div>

                                <!-- Shipping Address -->
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                        Alamat Pengiriman / Domisili Pembeli <span class="text-rose-500">*</span>
                                    </label>
                                    <textarea name="shipping_address" rows="2" required 
                                              placeholder="Masukkan alamat lengkap tujuan pengiriman saat panen"
                                              class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">{{ Auth::user()->consumerProfile->address ?? (Auth::user()->collectorProfile->address ?? '') }}</textarea>
                                </div>

                                <!-- Notes -->
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                        Catatan Kebutuhan <span class="text-slate-400 text-[10px] font-normal">(Opsional)</span>
                                    </label>
                                    <input type="text" name="notes" placeholder="Contoh: Kemasan karung 50kg, siap diambil pagi"
                                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                </div>

                                <!-- Subtotal Preview -->
                                <div class="p-4 bg-emerald-50/70 rounded-2xl border border-emerald-200 space-y-1">
                                    <div class="flex justify-between text-xs text-slate-600">
                                        <span>Estimasi Total Pembayaran:</span>
                                        <span id="po-total-display" class="font-black text-emerald-800 text-base">
                                            Rp {{ number_format($preorder->price * max((int)$preorder->min_order, 1), 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <p class="text-[10px] text-slate-400">Pembayaran resmi diproses saat status panen siap diterbitkan pesanan.</p>
                                </div>

                                <button type="submit" class="w-full py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-lg shadow-emerald-600/20 transition flex items-center justify-center gap-2">
                                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                                    <span>Pesan Kuota Pre-Order</span>
                                </button>
                            </form>
                        @endif
                    @else
                        <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 text-center space-y-3">
                            <p class="text-xs text-slate-600">Masuk sebagai Pengepul atau Konsumen untuk memesan kuota pre-order panen ini.</p>
                            <div class="flex gap-2">
                                <a href="{{ route('login') }}" class="flex-1 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs">Masuk</a>
                                <a href="{{ route('register') }}" class="flex-1 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold text-xs">Daftar</a>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const poUnitPrice = {{ (float) $preorder->price }};
    function calcPoSubtotal() {
        const qty = parseFloat(document.getElementById('po-qty-input').value) || 0;
        const total = Math.max(0, qty) * poUnitPrice;
        document.getElementById('po-total-display').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
    }
</script>
@endsection
