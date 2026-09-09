@extends('layouts.dashboard')

@section('title', 'Detail Kampanye Pre-Order')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('farmer.preorders.index') }}" class="hover:text-slate-800 transition">Pre-Order</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold truncate">{{ $preorder->title }}</span>
        </div>

        <a href="{{ route('preorders.show', $preorder->slug) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold transition shadow-sm">
            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
            <span>Lihat Tampilan Publik</span>
        </a>
    </div>

    <!-- Campaign Overview Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6 pb-6 border-b border-slate-100">
            <div class="flex items-start gap-4">
                <img src="{{ $preorder->image_url }}" alt="{{ $preorder->title }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover border border-slate-200 flex-shrink-0">
                <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
                            {{ $preorder->commodity->name }}
                        </span>
                        <span class="text-xs text-slate-400">•</span>
                        <span class="text-xs text-slate-500">Lokasi: <strong class="text-slate-700">{{ $preorder->location }}</strong></span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $preorder->title }}</h1>
                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Perkiraan Panen: <strong class="text-slate-800">{{ \Carbon\Carbon::parse($preorder->estimated_harvest_date)->translatedFormat('d F Y') }}</strong> ({{ \Carbon\Carbon::parse($preorder->estimated_harvest_date)->diffForHumans() }})</span>
                    </p>
                </div>
            </div>

            <!-- Status & Advance Controls -->
            <div class="flex flex-col sm:items-end gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Status: {{ $preorder->status }}</span>
                </span>

                <!-- Status Update Form -->
                <form action="{{ route('farmer.preorders.status', $preorder) }}" method="POST" class="flex items-center gap-2 mt-2">
                    @csrf
                    <select name="status" class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="Dibuka" {{ $preorder->status === 'Dibuka' ? 'selected' : '' }}>Dibuka (Menerima Kuota)</option>
                        <option value="Menunggu Panen" {{ $preorder->status === 'Menunggu Panen' ? 'selected' : '' }}>Menunggu Panen (Tutup Kuota)</option>
                        <option value="Siap Diproses" {{ $preorder->status === 'Siap Diproses' ? 'selected' : '' }}>Siap Diproses (Konversi ke Pesanan)</option>
                        <option value="Diproses" {{ $preorder->status === 'Diproses' ? 'selected' : '' }}>Diproses (Sedang Dikemas/Kirim)</option>
                        <option value="Selesai" {{ $preorder->status === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="Dibatalkan" {{ $preorder->status === 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                    <button type="submit" onclick="return confirm('Perbarui status kampanye ini? Jika memilih Siap Diproses / Diproses, semua kuota terpesan akan otomatis dikonversi menjadi Pesanan Resmi!')" 
                            class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                        Simpan
                    </button>
                </form>
            </div>
        </div>

        <!-- Metrics & Quota Bar -->
        @php
            $total = (float) $preorder->estimated_production;
            $avail = (float) $preorder->preorder_available_quantity;
            $booked = max(0, $total - $avail);
            $percent = $total > 0 ? min(100, round(($booked / $total) * 100)) : 0;
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-6 text-xs border-b border-slate-100">
            <div class="p-3.5 bg-slate-50 rounded-2xl">
                <span class="text-slate-400 font-semibold block">Total Rencana Produksi:</span>
                <span class="text-lg font-black text-slate-900 mt-0.5 block">{{ number_format($total, 0, ',', '.') }} {{ $preorder->unit }}</span>
            </div>
            <div class="p-3.5 bg-emerald-50/70 rounded-2xl border border-emerald-100">
                <span class="text-emerald-700 font-semibold block">Harga Satuan Pre-Order:</span>
                <span class="text-lg font-black text-emerald-900 mt-0.5 block">Rp {{ number_format($preorder->price, 0, ',', '.') }} / {{ $preorder->unit }}</span>
            </div>
            <div class="p-3.5 bg-amber-50/60 rounded-2xl border border-amber-100">
                <span class="text-amber-800 font-semibold block">Kuota Terpesan:</span>
                <span class="text-lg font-black text-amber-900 mt-0.5 block">{{ number_format($booked, 0, ',', '.') }} {{ $preorder->unit }} ({{ $percent }}%)</span>
            </div>
            <div class="p-3.5 bg-slate-50 rounded-2xl">
                <span class="text-slate-400 font-semibold block">Sisa Kuota Tersedia:</span>
                <span class="text-lg font-black text-slate-900 mt-0.5 block">{{ number_format($avail, 0, ',', '.') }} {{ $preorder->unit }}</span>
            </div>
        </div>

        <div class="pt-4">
            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                <div class="bg-emerald-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
            </div>
        </div>
    </div>

    <!-- Booked Buyers Section -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Daftar Pemesan Kuota Pre-Order</h2>
                <p class="text-xs text-slate-500">Daftar pembeli yang telah mengunci alokasi kuota panen Anda</p>
            </div>
            <span class="text-xs font-bold text-slate-500 bg-white px-3 py-1.5 rounded-xl border border-slate-200">
                {{ $preorder->items->count() }} Pemesan
            </span>
        </div>

        @if($preorder->items->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-200 p-8 text-center max-w-md mx-auto space-y-3">
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center mx-auto">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-800">Belum Ada Kuota Terpesan</h3>
                <p class="text-xs text-slate-500">Kampanye ini terbuka di katalog publik. Calon pembeli akan segera memesan kuota komoditas Anda.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($preorder->items as $item)
                    <div class="bg-white rounded-3xl border border-slate-200 hover:border-slate-300 shadow-sm p-5 transition">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 font-black flex items-center justify-center text-sm flex-shrink-0">
                                    {{ substr($item->buyer->name, 0, 2) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-sm font-bold text-slate-900">{{ $item->buyer->name }}</h3>
                                        <span class="text-[10px] font-bold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md">{{ ucfirst($item->buyer->role) }}</span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5">Alamat Kirim: {{ $item->shipping_address }} | Metode: {{ $item->shipping_method }}</p>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div>
                                @if($item->status === 'Menunggu Panen')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        Menunggu Panen
                                    </span>
                                @elseif($item->status === 'Dikonfirmasi')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Dikonversi ke Pesanan
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600">
                                        {{ $item->status }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Quantity & Total -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 py-3 text-xs">
                            <div class="p-3 bg-slate-50 rounded-xl">
                                <span class="text-slate-400 font-semibold block">Volume Dipesan:</span>
                                <span class="text-sm font-black text-slate-800 mt-0.5 block">{{ number_format($item->quantity, 0, ',', '.') }} {{ $preorder->unit }}</span>
                            </div>
                            <div class="p-3 bg-emerald-50/70 rounded-xl border border-emerald-100">
                                <span class="text-emerald-700 font-semibold block">Total Nilai Pesanan:</span>
                                <span class="text-sm font-black text-emerald-900 mt-0.5 block">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl">
                                <span class="text-slate-400 font-semibold block">Waktu Pemesanan:</span>
                                <span class="text-xs font-bold text-slate-700 mt-1 block">{{ $item->created_at->translatedFormat('d F Y H:i') }}</span>
                            </div>
                        </div>

                        @if($item->notes)
                            <div class="p-2.5 bg-slate-50 rounded-xl text-xs text-slate-600 mb-2 border border-slate-100">
                                <strong>Catatan Pembeli:</strong> "{{ $item->notes }}"
                            </div>
                        @endif

                        @if($item->order_id)
                            <div class="flex justify-end pt-2 border-t border-slate-100">
                                <a href="{{ route('farmer.orders.show', $item->order_id) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 hover:text-emerald-800">
                                    <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                                    <span>Buka Pesanan Masuk Resmi (#{{ $item->order->order_number ?? '' }})</span>
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
