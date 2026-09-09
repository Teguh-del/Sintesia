@extends('layouts.dashboard')

@section('title', 'Admin Panel Dashboard — SINTESA')
@section('header_title', 'Overview Panel Administrator')

@section('content')
<div class="space-y-8">
    <!-- Admin Hero Card -->
    <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-purple-900 via-indigo-950 to-slate-900 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="max-w-2xl space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/20 border border-purple-400/30 text-purple-300 text-xs font-bold">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-purple-300"></i>
                    <span>Sistem Otoritas SINTESA — Phase 8 Final Command Center</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                    Pusat Komando & Pengawasan Sistem
                </h1>
                <p class="text-purple-200 text-xs sm:text-sm leading-relaxed">
                    Kelola seluruh ekosistem pengguna, integritas master komoditas, moderasi katalog produk, dan monitor transaksi digital secara menyeluruh dari basis data MySQL terpusat.
                </p>
            </div>

            <!-- GMV Badge -->
            <div class="px-5 py-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/10 text-center min-w-[160px]">
                <span class="text-[10px] text-purple-200 uppercase tracking-wider font-bold block mb-1">Total Nilai Transaksi (GMV)</span>
                <span class="text-2xl sm:text-3xl font-black text-emerald-400">Rp {{ number_format($totalGmv, 0, ',', '.') }}</span>
                <span class="text-[10px] text-slate-300 block mt-1">{{ $completedOrders }} Pesanan Selesai</span>
            </div>
        </div>
    </div>

    <!-- Platform User Stats (4 Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pengguna</p>
                <h4 class="text-2xl font-black text-slate-900 mt-1">{{ $totalUsers }}</h4>
                <p class="text-[11px] text-purple-600 font-medium mt-1">Pengguna Terverifikasi</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Mitra Petani</p>
                <h4 class="text-2xl font-black text-slate-900 mt-1">{{ $totalFarmers }}</h4>
                <p class="text-[11px] text-emerald-600 font-medium mt-1">Produsen Panen</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                <i data-lucide="sprout" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Mitra Pengepul</p>
                <h4 class="text-2xl font-black text-slate-900 mt-1">{{ $totalCollectors }}</h4>
                <p class="text-[11px] text-amber-600 font-medium mt-1">Distribusi & Grosir</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                <i data-lucide="truck" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Konsumen</p>
                <h4 class="text-2xl font-black text-slate-900 mt-1">{{ $totalConsumers }}</h4>
                <p class="text-[11px] text-blue-600 font-medium mt-1">Pembeli Langsung</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">
                <i data-lucide="shopping-bag" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Quick Operations & System Management Grid -->
    <div>
        <h3 class="text-base font-bold text-slate-900 mb-4">Modul Manajemen Sistem Administrator</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <!-- 1. User Management -->
            <a href="{{ route('admin.users.index') }}" class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-sm hover:border-purple-300 hover:shadow-md transition flex items-start gap-4 group">
                <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition">
                    <i data-lucide="user-cog" class="w-6 h-6"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-sm group-hover:text-purple-700 transition">Kelola Pengguna</h4>
                    <p class="text-xs text-slate-500 mt-1">Filter role, penangguhan status akun, dan audit profil lengkap petani/pembeli.</p>
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-purple-700 mt-2">
                        <span>Buka Modul</span>
                        <i data-lucide="arrow-right" class="w-3 h-3"></i>
                    </span>
                </div>
            </a>

            <!-- 2. Master Commodity Management -->
            <a href="{{ route('admin.commodities.index') }}" class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-sm hover:border-emerald-300 hover:shadow-md transition flex items-start gap-4 group">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition">
                    <i data-lucide="sprout" class="w-6 h-6"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-sm group-hover:text-emerald-700 transition">Master Komoditas</h4>
                    <p class="text-xs text-slate-500 mt-1">Tambah dan kelola standar komoditas acuan untuk transaksi dan algoritma matching.</p>
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 mt-2">
                        <span>{{ $commodities->count() }} Komoditas Terdaftar</span>
                        <i data-lucide="arrow-right" class="w-3 h-3"></i>
                    </span>
                </div>
            </a>

            <!-- 3. Product Moderation -->
            <a href="{{ route('admin.products.index') }}" class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-sm hover:border-amber-300 hover:shadow-md transition flex items-start gap-4 group">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition">
                    <i data-lucide="shopping-bag" class="w-6 h-6"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-sm group-hover:text-amber-700 transition">Moderasi Produk</h4>
                    <p class="text-xs text-slate-500 mt-1">Audit katalog produk pasar, aktivasi/penangguhan produk demi perlindungan pembeli.</p>
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-700 mt-2">
                        <span>{{ $activeProducts }} Produk Aktif</span>
                        <i data-lucide="arrow-right" class="w-3 h-3"></i>
                    </span>
                </div>
            </a>

            <!-- 4. Transaction Monitoring -->
            <a href="{{ route('admin.transactions.index') }}" class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-sm hover:border-blue-300 hover:shadow-md transition flex items-start gap-4 group">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition">
                    <i data-lucide="receipt" class="w-6 h-6"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-sm group-hover:text-blue-700 transition">Monitor Transaksi</h4>
                    <p class="text-xs text-slate-500 mt-1">Audit status pesanan, pembayaran, serta rincian invoice transaksi resmi.</p>
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-700 mt-2">
                        <span>{{ $totalOrders }} Pesanan Tercatat</span>
                        <i data-lucide="arrow-right" class="w-3 h-3"></i>
                    </span>
                </div>
            </a>

            <!-- 5. Commodity Price Management -->
            <a href="{{ route('admin.prices.index') }}" class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-sm hover:border-purple-300 hover:shadow-md transition flex items-start gap-4 group">
                <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition">
                    <i data-lucide="tag" class="w-6 h-6"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-sm group-hover:text-purple-700 transition">Kelola Harga Pasar</h4>
                    <p class="text-xs text-slate-500 mt-1">Catat dan update data referensi harga pasar harian untuk visualisasi Chart.js.</p>
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-purple-700 mt-2">
                        <span>{{ $totalPriceRecords }} Catatan Harga</span>
                        <i data-lucide="arrow-right" class="w-3 h-3"></i>
                    </span>
                </div>
            </a>

            <!-- 6. Geospatial Map Explorer -->
            <a href="{{ route('maps.index') }}" class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-sm hover:border-teal-300 hover:shadow-md transition flex items-start gap-4 group">
                <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition">
                    <i data-lucide="map" class="w-6 h-6"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-sm group-hover:text-teal-700 transition">Peta Geospasial</h4>
                    <p class="text-xs text-slate-500 mt-1">Visualisasi sebaran lahan dan komoditas pertanian via OpenStreetMap.</p>
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-teal-700 mt-2">
                        <span>Buka Peta</span>
                        <i data-lucide="arrow-right" class="w-3 h-3"></i>
                    </span>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent System Orders Table -->
    <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-base font-bold text-slate-900">Aktivitas Transaksi Terkini</h3>
                <p class="text-xs text-slate-500">Pemantauan 5 pesanan terbaru di platform</p>
            </div>
            <a href="{{ route('admin.transactions.index') }}" class="text-xs font-bold text-purple-700 hover:text-purple-800 flex items-center gap-1">
                <span>Lihat Semua Transaksi</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        @if($recentOrders->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                            <th class="pb-3 px-3">No. Pesanan</th>
                            <th class="pb-3 px-3">Pembeli</th>
                            <th class="pb-3 px-3">Petani Penjual</th>
                            <th class="pb-3 px-3">Total Transaksi</th>
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
                            <td class="py-3 px-3 font-semibold text-slate-800">
                                {{ $order->buyer->name }} ({{ ucfirst($order->buyer->role) }})
                            </td>
                            <td class="py-3 px-3">
                                <span class="font-bold text-slate-800">{{ $order->seller->name }}</span>
                                <p class="text-[10px] text-slate-400">{{ $order->seller->farmerProfile->farm_name ?? 'Kebun Petani' }}</p>
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
                                <a href="{{ route('admin.transactions.show', $order) }}" 
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-purple-50 hover:text-purple-700 text-slate-700 font-bold text-xs transition">
                                    <span>Detail Invoice</span>
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-8 text-center text-slate-400">
                <p class="text-xs">Belum ada transaksi tercatat di platform.</p>
            </div>
        @endif
    </div>
</div>
@endsection
