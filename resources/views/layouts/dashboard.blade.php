<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — SINTESA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex flex-col md:flex-row">
    <!-- Mobile Sidebar Backdrop -->
    <div id="mobile-backdrop" onclick="toggleSidebar()" class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm hidden md:hidden"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-slate-300 flex flex-col transition-transform duration-300 -translate-x-full md:translate-x-0 md:static md:inset-auto md:min-h-screen">
        <!-- Logo Area -->
        <div class="h-20 flex items-center justify-between px-6 border-b border-slate-800">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center text-white shadow-md">
                    <i data-lucide="sprout" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="text-xl font-bold tracking-tight text-white">SINTESA</span>
                    <p class="text-[9px] font-semibold text-emerald-400 tracking-wider uppercase">Portal {{ ucfirst(Auth::user()->role) }}</p>
                </div>
            </a>
            <button onclick="toggleSidebar()" class="md:hidden text-slate-400 hover:text-white">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <!-- Navigation Menu per Role -->
        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
            <div class="px-3 pb-2 text-[11px] font-bold tracking-wider text-slate-500 uppercase">Navigasi Utama</div>

            @if(Auth::user()->role === 'petani')
                <a href="{{ route('farmer.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('farmer.dashboard') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span>Dashboard Petani</span>
                </a>
                <a href="{{ route('farmer.harvests.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('farmer.harvests.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="wheat" class="w-5 h-5"></i>
                        <span>Hasil Panen</span>
                    </div>
                    <span class="text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-md">Aktif</span>
                </a>
                <a href="{{ route('farmer.stocks.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('farmer.stocks.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="boxes" class="w-5 h-5"></i>
                        <span>Manajemen Stok</span>
                    </div>
                    <span class="text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-md">Aktif</span>
                </a>
                <a href="{{ route('farmer.products.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('farmer.products.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="store" class="w-5 h-5"></i>
                        <span>Produk Marketplace</span>
                    </div>
                    <span class="text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-md">Aktif</span>
                </a>
                <!-- Farmer Orders (Phase 4) -->
                <a href="{{ route('farmer.orders.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('farmer.orders.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="inbox" class="w-5 h-5"></i>
                        <span>Pesanan Masuk</span>
                    </div>
                    @php
                        $pendingCount = \App\Models\Order::where('seller_id', Auth::id())->where('status', 'Menunggu Konfirmasi')->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="text-[10px] font-bold bg-amber-500 text-white px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                    @else
                        <span class="text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-md">Aktif</span>
                    @endif
                </a>
                <!-- Farmer Negotiations (Phase 5) -->
                <a href="{{ route('farmer.negotiations.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('farmer.negotiations.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="handshake" class="w-5 h-5"></i>
                        <span>Negosiasi Masuk</span>
                    </div>
                    @php
                        $pendingNegoCount = \App\Models\PriceOffer::where('seller_id', Auth::id())->where('status', 'Menunggu')->count();
                    @endphp
                    @if($pendingNegoCount > 0)
                        <span class="text-[10px] font-bold bg-amber-500 text-white px-2 py-0.5 rounded-full">{{ $pendingNegoCount }}</span>
                    @else
                        <span class="text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-md">Aktif</span>
                    @endif
                </a>
                <!-- Farmer Commodity Requests (Phase 5) -->
                <a href="{{ route('farmer.requests.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('farmer.requests.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                        <span>Bursa Permintaan</span>
                    </div>
                    <span class="text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-md">Aktif</span>
                </a>
                <!-- Farmer Pre-Orders (Phase 5) -->
                <a href="{{ route('farmer.preorders.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('farmer.preorders.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="calendar-clock" class="w-5 h-5"></i>
                        <span>Kampanye Pre-Order</span>
                    </div>
                    <span class="text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-md">Aktif</span>
                </a>

            @elseif(Auth::user()->role === 'pengepul')
                <a href="{{ route('collector.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('collector.dashboard') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span>Dashboard Pengepul</span>
                </a>
                <!-- Pengepul Orders (Phase 4) -->
                <a href="{{ route('orders.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('orders.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="receipt" class="w-5 h-5"></i>
                        <span>Pesanan Saya</span>
                    </div>
                    <span class="text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-md">Aktif</span>
                </a>
                <!-- Negotiations (Phase 5) -->
                <a href="{{ route('negotiations.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('negotiations.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="handshake" class="w-5 h-5"></i>
                        <span>Negosiasi Harga</span>
                    </div>
                    <span class="text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-md">Aktif</span>
                </a>
                <!-- Commodity Requests (Phase 5) -->
                <a href="{{ route('requests.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('requests.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                        <span>Permintaan Pasokan</span>
                    </div>
                    <span class="text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-md">Aktif</span>
                </a>
                <!-- Preorders (Phase 5) -->
                <a href="{{ route('preorders.my') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('preorders.my') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="calendar-clock" class="w-5 h-5"></i>
                        <span>Pre-Order Saya</span>
                    </div>
                    <span class="text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-md">Aktif</span>
                </a>
                <a href="{{ route('preorders.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('preorders.index') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="calendar-search" class="w-5 h-5"></i>
                        <span>Katalog Pre-Order</span>
                    </div>
                    <span class="text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-md">Eksplor</span>
                </a>
                <a href="{{ route('marketplace.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('marketplace.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                        <span>Katalog Marketplace</span>
                    </div>
                    <span class="text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-md">Eksplor</span>
                </a>
                <a href="{{ route('matching.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('matching.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="sparkles" class="w-5 h-5 text-amber-400"></i>
                        <span>SINTESA Match</span>
                    </div>
                    <span class="text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 px-2 py-0.5 rounded-md">Smart Match</span>
                </a>

            @elseif(Auth::user()->role === 'konsumen')
                <a href="{{ route('consumer.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('consumer.dashboard') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span>Dashboard Konsumen</span>
                </a>
                <a href="{{ route('marketplace.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('marketplace.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                        <span>Belanja Komoditas</span>
                    </div>
                    <span class="text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-md">Eksplor</span>
                </a>
                <!-- SINTESA Match for Konsumen (Phase 6) -->
                <a href="{{ route('matching.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('matching.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="sparkles" class="w-5 h-5 text-amber-400"></i>
                        <span>SINTESA Match</span>
                    </div>
                    <span class="text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 px-2 py-0.5 rounded-md">Smart Match</span>
                </a>
                <!-- Konsumen Orders (Phase 4) -->
                <a href="{{ route('orders.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('orders.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="receipt" class="w-5 h-5"></i>
                        <span>Pesanan Saya</span>
                    </div>
                    <span class="text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-md">Aktif</span>
                </a>
                <!-- Negotiations (Phase 5) -->
                <a href="{{ route('negotiations.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('negotiations.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="handshake" class="w-5 h-5"></i>
                        <span>Negosiasi Harga</span>
                    </div>
                    <span class="text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-md">Aktif</span>
                </a>
                <!-- Commodity Requests (Phase 5) -->
                <a href="{{ route('requests.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('requests.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                        <span>Permintaan Komoditas</span>
                    </div>
                    <span class="text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-md">Aktif</span>
                </a>
                <!-- Preorders (Phase 5) -->
                <a href="{{ route('preorders.my') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('preorders.my') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                        <span>Pre-Order Saya</span>
                    </div>
                    <span class="text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-md">Aktif</span>
                </a>
                <a href="{{ route('preorders.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('preorders.index') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="calendar-search" class="w-5 h-5"></i>
                        <span>Katalog Pre-Order</span>
                    </div>
                    <span class="text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-md">Eksplor</span>
                </a>

            @elseif(Auth::user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-purple-600 text-white shadow-md shadow-purple-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span>Overview Sistem</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.users.*') ? 'bg-purple-600 text-white shadow-md shadow-purple-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="users" class="w-5 h-5 text-purple-400"></i>
                        <span>Kelola Pengguna</span>
                    </div>
                    <span class="text-[10px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30 px-2 py-0.5 rounded-md">Admin</span>
                </a>
                <a href="{{ route('admin.commodities.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.commodities.*') ? 'bg-purple-600 text-white shadow-md shadow-purple-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="sprout" class="w-5 h-5 text-emerald-400"></i>
                        <span>Master Komoditas</span>
                    </div>
                    <span class="text-[10px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30 px-2 py-0.5 rounded-md">Admin</span>
                </a>
                <a href="{{ route('admin.products.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.products.*') ? 'bg-purple-600 text-white shadow-md shadow-purple-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="shopping-bag" class="w-5 h-5 text-amber-400"></i>
                        <span>Moderasi Produk</span>
                    </div>
                    <span class="text-[10px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30 px-2 py-0.5 rounded-md">Admin</span>
                </a>
                <a href="{{ route('admin.transactions.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.transactions.*') ? 'bg-purple-600 text-white shadow-md shadow-purple-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="receipt" class="w-5 h-5 text-blue-400"></i>
                        <span>Monitor Transaksi</span>
                    </div>
                    <span class="text-[10px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30 px-2 py-0.5 rounded-md">Admin</span>
                </a>
                <a href="{{ route('admin.prices.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.prices.*') ? 'bg-purple-600 text-white shadow-md shadow-purple-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="tag" class="w-5 h-5 text-purple-400"></i>
                        <span>Kelola Harga Pasar</span>
                    </div>
                    <span class="text-[10px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30 px-2 py-0.5 rounded-md">Admin</span>
                </a>
            @endif

            <!-- Common Notification Link -->
            <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('notifications.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    <span>Notifikasi</span>
                </div>
                @php
                    $unreadCount = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count();
                @endphp
                @if($unreadCount > 0)
                    <span class="text-[10px] font-bold bg-rose-500 text-white px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
                @endif
            </a>

            <!-- Phase 7: Maps & Price Analytics -->
            <div class="pt-4 px-3 pb-2 text-[11px] font-bold tracking-wider text-slate-500 uppercase">Peta & Pasar (Phase 7)</div>
            <a href="{{ route('maps.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('maps.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="map" class="w-5 h-5 text-emerald-400"></i>
                    <span>Peta Sebaran Petani</span>
                </div>
                <span class="text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-md">Geospasial</span>
            </a>
            <a href="{{ route('prices.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('prices.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="line-chart" class="w-5 h-5 text-teal-400"></i>
                    <span>Tren Harga Pasar</span>
                </div>
                <span class="text-[10px] font-bold bg-teal-500/20 text-teal-300 border border-teal-500/30 px-2 py-0.5 rounded-md">Chart</span>
            </a>

            <div class="pt-4 px-3 pb-2 text-[11px] font-bold tracking-wider text-slate-500 uppercase">Ekosistem SINTESA</div>
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-400 hover:bg-slate-800 hover:text-white transition">
                <i data-lucide="globe" class="w-5 h-5"></i>
                <span>Lihat Landing Page</span>
            </a>
        </nav>

        <!-- Sidebar User Card -->
        <div class="p-4 border-t border-slate-800">
            <div class="p-3 bg-slate-800/60 rounded-xl flex items-center justify-between">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-lg bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center font-bold flex-shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="truncate">
                        <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-emerald-400 uppercase font-medium">{{ Auth::user()->role }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition" title="Keluar">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top Navbar -->
        <header class="h-20 bg-white border-b border-slate-200/80 px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="p-2 -ml-2 rounded-xl text-slate-600 hover:bg-slate-100 md:hidden">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <div>
                    <h1 class="text-lg sm:text-xl font-bold text-slate-900">@yield('header_title', 'Dashboard')</h1>
                    <p class="text-xs text-slate-500 hidden sm:block">Sistem Integrasi Niaga Pertanian Cerdas</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Notifications Bell -->
                <a href="{{ route('notifications.index') }}" class="relative p-2 rounded-xl text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition" title="Notifikasi">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    @php
                        $unreadTopCount = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count();
                    @endphp
                    @if($unreadTopCount > 0)
                        <span class="absolute top-1 right-1 w-2.5 h-2.5 rounded-full bg-rose-500 ring-2 ring-white"></span>
                    @endif
                </a>

                <div class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                    {{ Auth::user()->role === 'admin' ? 'bg-purple-100 text-purple-700' : '' }}
                    {{ Auth::user()->role === 'petani' ? 'bg-emerald-100 text-emerald-700' : '' }}
                    {{ Auth::user()->role === 'pengepul' ? 'bg-amber-100 text-amber-700' : '' }}
                    {{ Auth::user()->role === 'konsumen' ? 'bg-blue-100 text-blue-700' : '' }}">
                    Role: {{ ucfirst(Auth::user()->role) }}
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-rose-50 hover:border-rose-200 hover:text-rose-600 text-xs font-semibold text-slate-600 transition">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="px-4 sm:px-8 pt-6">
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="px-4 sm:px-8 pt-6">
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 flex-shrink-0"></i>
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Page Content -->
        <main class="flex-1 p-4 sm:p-8">
            @yield('content')
        </main>
    </div>

    <script>
        lucide.createIcons();

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('mobile-backdrop');
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }
    </script>
    @stack('scripts')
</body>
</html>
