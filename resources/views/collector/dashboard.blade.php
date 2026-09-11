@extends('layouts.dashboard')

@section('title', 'Dashboard Pengepul')
@section('header_title', 'Dashboard Pengepul')

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-emerald-700 to-teal-800 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-block px-3 py-1 rounded-full bg-emerald-600/60 border border-emerald-500/40 text-xs font-semibold uppercase tracking-wider mb-3">
                Mitra Pengepul & Pedagang Besar
            </span>
            <h2 class="text-2xl sm:text-3xl font-black tracking-tight mb-2">
                Selamat Datang, {{ $user->name }}!
            </h2>
            <p class="text-emerald-100 text-sm leading-relaxed">
                Pantau ketersediaan pasokan komoditas dari para petani, buka permintaan kuota panen, dan lakukan negosiasi harga secara transparan.
            </p>
        </div>
        <div class="absolute right-6 -bottom-6 opacity-10 hidden sm:block">
            <i data-lucide="truck" class="w-48 h-48"></i>
        </div>
    </div>

    <!-- Order & Purchase Metrics (Phase 4 Real System Data) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pesanan Aktif</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $activeOrdersCount }}</p>
                <a href="{{ route('orders.index') }}" class="text-[11px] font-bold text-emerald-600 hover:underline inline-block mt-1">
                    Lihat Progres &rarr;
                </a>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pembelian Selesai</p>
                <p class="text-xl font-black text-slate-900 mt-1">Rp {{ number_format($totalSpending, 0, ',', '.') }}</p>
                <a href="{{ route('orders.index', ['status' => 'Selesai']) }}" class="text-[11px] font-bold text-emerald-600 hover:underline inline-block mt-1">
                    Riwayat Transaksi &rarr;
                </a>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="receipt" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pesanan</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $totalOrdersCount }}</p>
                <a href="{{ route('orders.index') }}" class="text-[11px] font-bold text-emerald-600 hover:underline inline-block mt-1">
                    Semua Pesanan &rarr;
                </a>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <i data-lucide="package" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Katalog Produk</p>
                <p class="text-sm font-bold text-slate-900 mt-1">Pasokan Komoditas</p>
                <a href="{{ route('marketplace.index') }}" class="text-[11px] font-bold text-emerald-600 hover:underline inline-block mt-1">
                    Buka Marketplace &rarr;
                </a>
            </div>
            <div class="w-11 h-11 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                <i data-lucide="shopping-cart" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Recent Orders / Purchases Table -->
    <div class="p-6 rounded-3xl bg-white border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Pesanan & Pembelian Terkini</h3>
                    <p class="text-xs text-slate-500">Status pesanan komoditas hasil panen langsung dari kebun petani</p>
                </div>
            </div>
            <a href="{{ route('orders.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                <span>Kelola Semua Pesanan</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        @if($recentOrders->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                            <th class="pb-3 px-3">No. Pesanan</th>
                            <th class="pb-3 px-3">Petani / Penjual</th>
                            <th class="pb-3 px-3">Komoditas / Item</th>
                            <th class="pb-3 px-3">Total Tagihan</th>
                            <th class="pb-3 px-3">Status</th>
                            <th class="pb-3 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($recentOrders as $order)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-3">
                                <span class="font-bold text-slate-900">#{{ $order->order_number }}</span>
                                <p class="text-[10px] text-slate-400">{{ $order->created_at->diffForHumans() }}</p>
                            </td>
                            <td class="py-3 px-3">
                                <span class="font-bold text-slate-800">{{ $order->seller->name }}</span>
                                <p class="text-[10px] text-slate-400">{{ $order->seller->farmerProfile->farm_name ?? 'Kebun Petani' }}</p>
                            </td>
                            <td class="py-3 px-3">
                                @foreach($order->items as $item)
                                     <p class="truncate max-w-[200px]">{{ $item->product_name }} ({{ number_format($item->quantity, 0) }} {{ $item->unit }})</p>
                                @endforeach
                            </td>
                            <td class="py-3 px-3 font-bold text-slate-900">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3">
                                @php
                                    $statusClasses = [
                                        'Menunggu Konfirmasi' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'Dikonfirmasi' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'Diproses' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                        'Selesai' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'Dibatalkan' => 'bg-rose-100 text-rose-800 border-rose-200',
                                    ];
                                @endphp
                                <span class="inline-block px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $statusClasses[$order->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-right">
                                <a href="{{ route('orders.show', $order) }}" 
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-slate-700 font-bold text-xs transition">
                                    <span>Invoice & Detail</span>
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                <i data-lucide="shopping-cart" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
                <h4 class="text-sm font-bold text-slate-700">Belum Ada Transaksi Pembelian</h4>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Jelajahi marketplace untuk menemukan komoditas langsung dari petani dengan harga dan stok transparan.</p>
                <a href="{{ route('marketplace.index') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow transition">
                    <span>Lihat Komoditas</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        @endif
    </div>

    <!-- Collector Profile Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                <i data-lucide="building-2" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Usaha / Gudang</p>
                <h4 class="text-base font-bold text-slate-900">{{ $user->collectorProfile->business_name ?? 'Gudang Pengepul' }}</h4>
                <p class="text-xs text-slate-400 truncate max-w-[180px]">{{ $user->collectorProfile->address ?? 'Kediri' }}</p>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                <i data-lucide="tag" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipe Usaha</p>
                <h4 class="text-base font-bold text-slate-900">{{ $user->collectorProfile->business_type ?? 'Pengepul Grosir' }}</h4>
                <p class="text-xs text-emerald-600 font-medium">Mitra Terverifikasi</p>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center flex-shrink-0">
                <i data-lucide="sparkles" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Fitur Utama</p>
                <h4 class="text-base font-bold text-slate-900">SINTESA Match</h4>
                <p class="text-xs text-teal-600 font-medium">Pencocokan Cerdas 4 Bobot</p>
            </div>
        </div>
    </div>

    <!-- SINTESA Match Spotlight & Live Top Recommendations -->
    <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/80 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <i data-lucide="sparkles" class="w-5 h-5 text-emerald-600"></i>
                    <h3 class="text-lg font-bold text-slate-900">Rekomendasi Cerdas SINTESA Match</h3>
                </div>
                <p class="text-xs text-slate-500">
                    Pilihan pasokan terbaik yang disesuaikan dengan kebutuhan komoditas, stok petani yang siap kirim, harga, dan jarak ke gudang Anda.
                </p>
            </div>
            <a href="{{ route('matching.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition">
                <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                <span>Buka Engine SINTESA Match Lengkap</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        @if(isset($topMatches) && $topMatches->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
                @foreach($topMatches as $match)
                    <div class="p-5 rounded-2xl bg-gradient-to-b from-slate-50 to-white border border-slate-200/90 shadow-sm hover:border-emerald-300 hover:shadow-md transition">
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <div>
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ $match['farmer']['farm_name'] ?? 'Petani Terverifikasi' }}</span>
                                <h4 class="text-sm font-black text-slate-900 line-clamp-1">{{ $match['product']->name }}</h4>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-black {{ $match['category_badge'] }} shadow-sm">
                                    {{ $match['final_score'] }}%
                                </span>
                                <span class="text-[10px] font-bold text-slate-500 mt-0.5">{{ $match['category'] }}</span>
                            </div>
                        </div>

                        <div class="space-y-1.5 text-xs text-slate-600 py-2 border-y border-slate-100">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Harga Jual:</span>
                                <span class="font-bold text-slate-900">Rp {{ number_format($match['product']->price, 0, ',', '.') }}/{{ $match['product']->unit }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Stok Siap Kirim:</span>
                                <span class="font-bold text-emerald-700">{{ number_format($match['product']->stock, 0) }} {{ $match['product']->unit }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Estimasi Jarak:</span>
                                <span class="font-bold text-slate-700">{{ $match['distance_km'] }} km</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 mt-4">
                            <a href="{{ route('marketplace.show', $match['product']->slug ?? $match['product']->id) }}" class="flex-1 text-center py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 text-xs transition">
                                Detail
                            </a>
                            <a href="{{ route('matching.index', ['commodity_id' => $match['product']->commodity_id, 'max_price' => $match['product']->price]) }}" class="flex-1 text-center py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 font-bold text-white text-xs transition shadow-sm">
                                Cocokkan
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200 mb-8">
                <i data-lucide="sparkles" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                <p class="text-xs text-slate-500">Katalog petani sedang diperbarui. Buka halaman SINTESA Match untuk kustomisasi pencarian komoditas.</p>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-4 border-t border-slate-100">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-500 mb-1">1. Kesesuaian Komoditas</p>
                <h4 class="text-xl font-black text-slate-900">35%</h4>
                <p class="text-[11px] text-slate-400 mt-1">Sesuai dengan jenis komoditas panen yang dicari.</p>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-500 mb-1">2. Ketersediaan Stok</p>
                <h4 class="text-xl font-black text-slate-900">25%</h4>
                <p class="text-[11px] text-slate-400 mt-1">Stok siap dikirim langsung dari kebun petani.</p>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-500 mb-1">3. Kesesuaian Harga</p>
                <h4 class="text-xl font-black text-slate-900">20%</h4>
                <p class="text-[11px] text-slate-400 mt-1">Harga yang bersaing dan sesuai batas anggaran.</p>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-500 mb-1">4. Kedekatan Jarak</p>
                <h4 class="text-xl font-black text-slate-900">20%</h4>
                <p class="text-[11px] text-slate-400 mt-1">Perkiraan jarak tempuh ke lokasi gudang Anda.</p>
            </div>
        </div>
    </div>
</div>
@endsection
