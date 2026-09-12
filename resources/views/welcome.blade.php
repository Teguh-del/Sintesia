@extends('layouts.app')

@section('title', 'SINTESA — Sistem Integrasi Niaga Pertanian Cerdas')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden pt-12 pb-24 lg:pt-20 lg:pb-32 bg-gradient-to-b from-emerald-50/60 via-white to-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-100/80 border border-emerald-200 text-emerald-800 text-xs font-bold tracking-wide uppercase mb-6 shadow-sm">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-emerald-600"></i>
                <span>Platform Niaga Hasil Pertanian Terpadu</span>
            </div>
            
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.15] mb-6">
                Menghubungkan Petani, <br class="hidden sm:inline">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-500">Memperluas Akses Pasar.</span>
            </h1>

            <p class="text-lg sm:text-xl text-slate-600 mb-10 leading-relaxed font-normal">
                SINTESA mengintegrasikan Petani, Pengepul, dan Konsumen dalam satu ekosistem niaga digital dengan algoritma pencocokan cerdas, transparansi harga pasar, dan kepastian transaksi.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-base shadow-lg shadow-emerald-600/30 hover:shadow-xl hover:-translate-y-0.5 transition duration-200 flex items-center justify-center gap-2">
                    <span>Mulai Bergabung Sekarang</span>
                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </a>
                <a href="#alur-kerja" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-base border border-slate-200 shadow-sm transition duration-200 flex items-center justify-center gap-2">
                    <i data-lucide="play-circle" class="w-5 h-5 text-emerald-600"></i>
                    <span>Pelajari Cara Kerja</span>
                </a>
            </div>

            <!-- Role Badge Pillows -->
            <div class="mt-12 pt-8 border-t border-slate-200/80 flex flex-wrap items-center justify-center gap-6 text-xs font-semibold text-slate-500">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span>Petani Mandiri</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <span>Pengepul & Pedagang Besar</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    <span>Konsumen & Rumah Tangga</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Problem & Solution Section -->
<section id="tentang" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Problem Card -->
            <div class="p-8 sm:p-10 rounded-3xl bg-rose-50/50 border border-rose-100 relative overflow-hidden">
                <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mb-6">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-4">Tantangan Niaga Pertanian Tradisional</h3>
                <ul class="space-y-3.5 text-sm text-slate-600">
                    <li class="flex items-start gap-3">
                        <i data-lucide="x-circle" class="w-5 h-5 text-rose-500 flex-shrink-0 mt-0.5"></i>
                        <span>Rantai pasok panjang dan perantara berlebih yang memangkas marjin keuntungan petani.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i data-lucide="x-circle" class="w-5 h-5 text-rose-500 flex-shrink-0 mt-0.5"></i>
                        <span>Asimetri informasi harga riil komoditas di tingkat pasar lokal maupun regional.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i data-lucide="x-circle" class="w-5 h-5 text-rose-500 flex-shrink-0 mt-0.5"></i>
                        <span>Pengepul kesulitan melacak kapasitas panen aktual yang tersebar di berbagai sentra tani.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i data-lucide="x-circle" class="w-5 h-5 text-rose-500 flex-shrink-0 mt-0.5"></i>
                        <span>Risiko gagal jual saat panen raya karena ketiadaan kepastian penyerapan pasar yang adil.</span>
                    </li>
                </ul>
            </div>

            <!-- Solution Card -->
            <div class="p-8 sm:p-10 rounded-3xl bg-emerald-50/50 border border-emerald-100 relative overflow-hidden">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-6">
                    <i data-lucide="check-check" class="w-6 h-6"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-4">Solusi Cerdas SINTESA</h3>
                <ul class="space-y-3.5 text-sm text-slate-600">
                    <li class="flex items-start gap-3">
                        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                        <span><strong>SINTESA Match:</strong> Sistem pencocokan otomatis kebutuhan komoditas berbobot 4 kriteria riil (komoditas, stok, harga, jarak).</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                        <span><strong>Alur Panen $\to$ Stok $\to$ Pasar:</strong> Jaminan data stok riil yang tidak pernah negatif dan terverifikasi kepemilikannya.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                        <span><strong>2-Way Marketplace & Negosiasi:</strong> Petani dapat menjual langsung atau merespons permintaan tender dari pengepul.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                        <span><strong>Peta Geospasial &amp; Tren Harga:</strong> Pemetaan lahan pertanian dan analitik fluktuasi harga pasar secara transparan.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Cara Kerja Section -->
<section id="alur-kerja" class="py-20 bg-slate-50 border-y border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <h2 class="text-xs font-bold uppercase tracking-wider text-emerald-600 mb-2">Alur Transaksi & Integrasi</h2>
            <p class="text-3xl font-black text-slate-900 tracking-tight">Bagaimana SINTESA Bekerja</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 relative">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm relative">
                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 font-black text-sm flex items-center justify-center mb-4">1</span>
                <h4 class="font-bold text-slate-900 mb-2">Pencatatan Panen</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Petani mencatat hasil panen riil beserta tanggal panen, kualitas mutu, dan stok siap jual ke sistem.</p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm relative">
                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 font-black text-sm flex items-center justify-center mb-4">2</span>
                <h4 class="font-bold text-slate-900 mb-2">Permintaan &amp; Katalog</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Pengepul memasang permintaan kuota komoditas, atau konsumen menelusuri produk segar langsung.</p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm relative">
                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 font-black text-sm flex items-center justify-center mb-4">3</span>
                <h4 class="font-bold text-slate-900 mb-2">SINTESA Match</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Algoritma otomatis menghitung kecocokan komoditas, kuantitas stok, harga batas, dan radius jarak geografis.</p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm relative">
                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 font-black text-sm flex items-center justify-center mb-4">4</span>
                <h4 class="font-bold text-slate-900 mb-2">Kesepakatan &amp; Pesanan</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Negosiasi harga transparan disepakati, sistem mengonfirmasi order, dan stok terpotong otomatis secara aman.</p>
            </div>
        </div>
    </div>
</section>

<!-- Fitur Unggulan Section -->
<section id="fitur" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-xs font-bold uppercase tracking-wider text-emerald-600 mb-2">Inovasi Digital Pertanian</h2>
            <p class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">Fitur Unggulan Platform SINTESA</p>
            <p class="text-sm text-slate-500 mt-3">Empat pilar utama yang mentransformasi ekosistem niaga komoditas hasil bumi secara cerdas, adil, dan transparan.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Feature 1 -->
            <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200 hover:border-emerald-300 hover:shadow-lg transition duration-200 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center mb-5">
                        <i data-lucide="sparkles" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">SINTESA Match</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Mesin pencocokan cerdas dengan 4 kriteria tertimbang: Komoditas (35%), Stok (25%), Anggaran Harga (20%), dan Kedekatan Jarak Haversine (20%).
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-200/80 flex items-center gap-2 text-xs font-bold text-amber-700">
                    <span>Multi-Criteria Ranking</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200 hover:border-emerald-300 hover:shadow-lg transition duration-200 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-5">
                        <i data-lucide="layers" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Panen &rarr; Stok &rarr; Pasar</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Siklus terintegrasi dari pencatatan hasil panen, otomatisasi nomor batch stok, alokasi inventaris, hingga penayangan produk siap transaksi tanpa risiko stok minus.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-200/80 flex items-center gap-2 text-xs font-bold text-emerald-700">
                    <span>Atomic Stock Guarantee</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200 hover:border-emerald-300 hover:shadow-lg transition duration-200 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center mb-5">
                        <i data-lucide="handshake" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Bursa Permintaan &amp; Nego</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Pengepul &amp; konsumen dapat menerbitkan tender kuota pasokan. Petani dapat merespons dengan penawaran kuantitas dan harga tawar bilateral.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-200/80 flex items-center gap-2 text-xs font-bold text-blue-700">
                    <span>2-Way Direct Trade</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </div>
            </div>

            <!-- Feature 4 -->
            <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200 hover:border-emerald-300 hover:shadow-lg transition duration-200 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-teal-100 text-teal-600 flex items-center justify-center mb-5">
                        <i data-lucide="map-pin" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Peta Lahan &amp; Tren Harga</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Visualisasi sebaran kebun mitra tani dengan peta geospasial interaktif serta grafik fluktuasi harga pasar harian untuk transparansi ekonomi.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-200/80 flex items-center gap-2 text-xs font-bold text-teal-700">
                    <span>Geospatial &amp; Analytics</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Komoditas Utama -->
<section id="komoditas" class="py-20 bg-slate-50 border-t border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-emerald-600 mb-2">Katalog Unggulan</h2>
                <p class="text-3xl font-black text-slate-900 tracking-tight">Komoditas Niaga Prioritas</p>
            </div>
            <p class="text-sm text-slate-500 mt-2 md:mt-0">Didukung oleh petani lokal terverifikasi di Jawa Timur dan sekitarnya</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($commodities as $commodity)
            <div class="p-5 rounded-2xl bg-slate-50 hover:bg-emerald-50/50 border border-slate-200/80 hover:border-emerald-200 transition group text-center">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-white shadow-sm flex items-center justify-center text-emerald-600 mb-3 group-hover:scale-110 transition duration-200">
                    <i data-lucide="{{ $commodity->icon ?? 'sprout' }}" class="w-7 h-7"></i>
                </div>
                <h4 class="font-bold text-slate-900 text-base mb-1">{{ $commodity->name }}</h4>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800 mb-2">
                    {{ $commodity->category }}
                </span>
                <p class="text-xs text-slate-500 line-clamp-2">{{ $commodity->description }}</p>
                <p class="text-[11px] font-bold text-slate-400 mt-2">Satuan: {{ $commodity->unit }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="py-20 bg-gradient-to-r from-emerald-800 to-teal-900 text-white relative overflow-hidden">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <h2 class="text-3xl sm:text-4xl font-black tracking-tight mb-4">
            Siap Membangun Ekosistem Pertanian yang Lebih Adil?
        </h2>
        <p class="text-emerald-100 text-base sm:text-lg mb-8 max-w-2xl mx-auto">
            Daftar sekarang sebagai Petani, Pengepul, atau Konsumen. Nikmati kemudahan transaksi transparan tanpa potongan liar perantara.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-white hover:bg-emerald-50 text-emerald-800 font-bold text-base shadow-lg transition">
                Daftar Akun Baru
            </a>
            <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-emerald-700/60 hover:bg-emerald-700 text-white font-bold text-base border border-emerald-500/40 transition">
                Masuk ke Akun
            </a>
        </div>
    </div>
</section>
@endsection
