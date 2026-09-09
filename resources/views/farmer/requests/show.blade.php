@extends('layouts.dashboard')

@section('title', 'Tinjau & Ajukan Pasokan Komoditas')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb & Back Button -->
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('farmer.requests.index') }}" class="hover:text-slate-800 transition">Bursa Permintaan Pasokan</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold">Tinjau Permintaan #REQ-{{ $commodityRequest->id }}</span>
        </div>
        <a href="{{ route('farmer.requests.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs font-bold transition shadow-xs">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Bursa Permintaan</span>
        </a>
    </div>

    <!-- Request Detail Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2 flex-wrap mb-2">
                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-lg border border-emerald-200">
                        {{ $commodityRequest->commodity->name }}
                    </span>
                    <span class="text-xs text-slate-400">•</span>
                    <span class="text-xs text-slate-500">Pembeli: <strong class="text-slate-800">{{ $commodityRequest->user->name }}</strong> ({{ ucfirst($commodityRequest->user->role) }})</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $commodityRequest->title }}</h1>
                <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                    <i data-lucide="map-pin" class="w-4 h-4 text-slate-400"></i>
                    <span>Tujuan Pengiriman: <strong class="text-slate-700">{{ $commodityRequest->location }}</strong></span>
                </p>
            </div>

            <div class="text-left sm:text-right">
                <span class="text-xs text-slate-400 font-semibold block">Batas Waktu Pengadaan:</span>
                <span class="text-sm font-black text-rose-600 block mt-0.5">{{ \Carbon\Carbon::parse($commodityRequest->deadline)->translatedFormat('d F Y') }}</span>
                <span class="text-[11px] text-slate-400">({{ \Carbon\Carbon::parse($commodityRequest->deadline)->diffForHumans() }})</span>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 py-6 text-xs border-b border-slate-100">
            <div class="p-3.5 bg-slate-50 rounded-2xl">
                <span class="text-slate-400 font-semibold block">Volume Kebutuhan:</span>
                <span class="text-lg font-black text-slate-900 mt-0.5 block">{{ number_format($commodityRequest->required_quantity, 0, ',', '.') }} {{ $commodityRequest->unit }}</span>
            </div>
            <div class="p-3.5 bg-emerald-50/70 rounded-2xl border border-emerald-100">
                <span class="text-emerald-700 font-semibold block">Batas Maksimal Harga Pembeli:</span>
                <span class="text-lg font-black text-emerald-900 mt-0.5 block">Rp {{ number_format($commodityRequest->max_price, 0, ',', '.') }} / {{ $commodityRequest->unit }}</span>
            </div>
            <div class="p-3.5 bg-slate-50 rounded-2xl">
                <span class="text-slate-400 font-semibold block">Total Anggaran Pembeli:</span>
                <span class="text-lg font-black text-slate-900 mt-0.5 block">Rp {{ number_format($commodityRequest->required_quantity * $commodityRequest->max_price, 0, ',', '.') }}</span>
            </div>
        </div>

        @if($commodityRequest->description)
            <div class="pt-4 text-xs text-slate-600">
                <strong class="text-slate-800 block mb-1">Catatan Kriteria & Kualitas:</strong>
                <p class="leading-relaxed bg-slate-50 p-3.5 rounded-xl border border-slate-100">{{ $commodityRequest->description }}</p>
            </div>
        @endif
    </div>

    <!-- Farmer Action Section: Existing Offer vs Submit Form -->
    @if($existingOffer)
        <div class="bg-white rounded-3xl border border-emerald-300 ring-4 ring-emerald-500/10 shadow-sm p-6 sm:p-8 space-y-4">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Anda Sudah Mengajukan Penawaran</h3>
                        <p class="text-xs text-slate-500">Penawaran Anda telah dikirimkan ke pembeli pada {{ $existingOffer->created_at->translatedFormat('d F Y H:i') }}</p>
                    </div>
                </div>
                <div>
                    @if($existingOffer->status === 'Menunggu')
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                            Menunggu Persetujuan Pembeli
                        </span>
                    @elseif($existingOffer->status === 'Diterima')
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Disetujui Pembeli
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                            {{ $existingOffer->status }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 py-2 text-xs">
                <div class="p-3 bg-slate-50 rounded-xl">
                    <span class="text-slate-400 font-semibold block">Volume Ditawarkan:</span>
                    <span class="text-sm font-bold text-slate-800 mt-0.5 block">{{ number_format($existingOffer->offered_quantity, 0, ',', '.') }} {{ $commodityRequest->unit }}</span>
                </div>
                <div class="p-3 bg-emerald-50/70 rounded-xl border border-emerald-100">
                    <span class="text-emerald-700 font-semibold block">Harga Penawaran:</span>
                    <span class="text-sm font-black text-emerald-900 mt-0.5 block">Rp {{ number_format($existingOffer->offered_price, 0, ',', '.') }} / {{ $commodityRequest->unit }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl">
                    <span class="text-slate-400 font-semibold block">Total Transaksi:</span>
                    <span class="text-sm font-black text-slate-900 mt-0.5 block">Rp {{ number_format($existingOffer->offered_quantity * $existingOffer->offered_price, 0, ',', '.') }}</span>
                </div>
            </div>

            @if($existingOffer->notes)
                <p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <strong>Catatan Anda:</strong> "{{ $existingOffer->notes }}"
                </p>
            @endif
        </div>
    @else
        <!-- Submit Supply Offer Form -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
            <div class="flex items-center gap-3 pb-6 border-b border-slate-100">
                <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="handshake" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Ajukan Penawaran Pasokan Hasil Tani</h2>
                    <p class="text-xs text-slate-500">Tawarkan volume dan harga komoditas terbaik Anda ke pembeli</p>
                </div>
            </div>

            <form action="{{ route('farmer.requests.offer', $commodityRequest) }}" method="POST" class="space-y-5 pt-6">
                @csrf

                <!-- Offered Quantity & Price -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Volume Pasokan yang Disanggupi ({{ $commodityRequest->unit }}) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" step="any" min="0.01" name="offered_quantity" 
                               value="{{ old('offered_quantity', $commodityRequest->required_quantity) }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <p class="text-[11px] text-slate-400 mt-1">Kebutuhan pembeli: {{ number_format($commodityRequest->required_quantity, 0, ',', '.') }} {{ $commodityRequest->unit }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Harga Penawaran Anda (Rp / {{ $commodityRequest->unit }}) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">Rp</span>
                            <input type="number" min="1" name="offered_price" 
                                   value="{{ old('offered_price', $commodityRequest->max_price) }}" required
                                   class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Batas maksimal pembeli: Rp {{ number_format($commodityRequest->max_price, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Shipping Method & Linked Product -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Metode Pengiriman / Penyerahan <span class="text-rose-500">*</span>
                        </label>
                        <select name="shipping_method" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <option value="Ambil di Lokasi Petani">Pembeli Ambil Sendiri di Kebun Petani</option>
                            <option value="Pengiriman / Kurir">Petani Mengantar ke Alamat Pembeli ({{ $commodityRequest->location }})</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Tautkan dengan Produk Marketplace <span class="text-slate-400 text-[10px] font-normal">(Opsional)</span>
                        </label>
                        <select name="product_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <option value="">-- Tanpa Tautan Produk --</option>
                            @foreach($farmerProducts as $prod)
                                <option value="{{ $prod->id }}" {{ old('product_id') == $prod->id ? 'selected' : '' }}>
                                    {{ $prod->name }} (Stok: {{ $prod->stock }} {{ $prod->unit }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Catatan Pasokan & Spesifikasi Komoditas <span class="text-slate-400 text-[10px] font-normal">(Opsional)</span>
                    </label>
                    <textarea name="notes" rows="2" 
                              placeholder="Jelaskan kualitas panen, kemasan (misal karung 50kg), dan jadwal siap kirim..."
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">{{ old('notes') }}</textarea>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <a href="{{ route('farmer.requests.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        Kembali
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition flex items-center gap-2">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Kirim Penawaran Pasokan</span>
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
@endsection
