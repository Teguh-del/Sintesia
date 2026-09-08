<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SINTESA - Sistem Integrasi Niaga Pertanian Cerdas')</title>
    <meta name="description" content="Platform integrasi niaga hasil pertanian cerdas yang menghubungkan Petani, Pengepul, dan Konsumen secara adil, transparan, dan efisien.">
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
                        },
                        amber: {
                            500: '#f59e0b',
                            600: '#d97706',
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
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">
    <!-- Navbar -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-md shadow-emerald-500/20 group-hover:scale-105 transition duration-200">
                        <i data-lucide="sprout" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <span class="text-2xl font-black tracking-tight text-slate-900">SINTESA</span>
                        <p class="text-[10px] font-semibold text-emerald-700 tracking-wider uppercase -mt-1">Niaga Pertanian Cerdas</p>
                    </div>
                </a>

                <!-- Nav Links -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                    <a href="{{ route('home') }}#tentang" class="hover:text-emerald-600 transition">Tentang</a>
                    <a href="{{ route('home') }}#alur-kerja" class="hover:text-emerald-600 transition">Alur Niaga</a>
                    <a href="{{ route('home') }}#fitur" class="hover:text-emerald-600 transition">Fitur Unggulan</a>
                    <a href="{{ route('home') }}#komoditas" class="hover:text-emerald-600 transition">Komoditas</a>
                </nav>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ Auth::user()->getDashboardRoute() }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-semibold text-sm transition">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            <span>Dashboard ({{ ucfirst(Auth::user()->role) }})</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-2 rounded-lg text-slate-500 hover:text-red-600 hover:bg-red-50 transition" title="Keluar">
                                <i data-lucide="log-out" class="w-5 h-5"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-slate-700 hover:text-emerald-600 transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-sm hover:shadow transition">
                            <span>Daftar Akun</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 mt-6">
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 mt-6">
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 flex-shrink-0"></i>
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-9 h-9 rounded-lg bg-emerald-500 flex items-center justify-center text-white">
                            <i data-lucide="sprout" class="w-5 h-5"></i>
                        </div>
                        <span class="text-xl font-bold text-white tracking-tight">SINTESA</span>
                    </div>
                    <p class="text-slate-400 text-sm max-w-sm mb-4 leading-relaxed">
                        Sistem Integrasi Niaga Pertanian Cerdas. Menghubungkan Petani, Pengepul, dan Konsumen dengan transparansi rantai pasok terpadu.
                    </p>
                    <p class="text-xs text-emerald-400 font-medium">"Menghubungkan Petani, Memperluas Akses Pasar."</p>
                </div>
                <div>
                    <h4 class="text-sm font-bold uppercase tracking-wider text-slate-200 mb-4">Peran Pengguna</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('register') }}" class="hover:text-white transition">Mitra Petani</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition">Mitra Pengepul</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition">Konsumen Langsung</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-bold uppercase tracking-wider text-slate-200 mb-4">Modul Sistem</h4>
                    <ul class="space-y-2 text-sm">
                        <li><span class="text-slate-300">SINTESA Match</span></li>
                        <li><span class="text-slate-300">Pencatatan Panen & Stok</span></li>
                        <li><span class="text-slate-300">Permintaan & Negosiasi</span></li>
                        <li><span class="text-slate-300">Peta & Analisis Harga</span></li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <p>&copy; {{ date('Y') }} SINTESA — Sistem Integrasi Niaga Pertanian Cerdas. All rights reserved.</p>
                <p>Arsitektur Laravel Monolith & Tailwind CSS</p>
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
