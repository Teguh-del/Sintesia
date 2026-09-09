@extends('layouts.dashboard')

@section('title', 'Buka Kampanye Pre-Order Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('farmer.preorders.index') }}" class="hover:text-slate-800 transition">Pre-Order</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Buka Kampanye Baru</span>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="flex items-center gap-4 pb-6 border-b border-slate-100">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="calendar-plus" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">Buka Kampanye Pre-Order Panen</h1>
                <p class="text-xs text-slate-500 mt-0.5">Tawarkan alokasi hasil panen masa depan untuk mengamankan pasar & cashflow</p>
            </div>
        </div>

        <form action="{{ route('farmer.preorders.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 pt-6">
            @csrf

            <!-- Commodity & Title -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Komoditas yang Ditanam <span class="text-rose-500">*</span>
                    </label>
                    <select name="commodity_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="">-- Pilih Jenis Komoditas --</option>
                        @foreach($commodities as $c)
                            <option value="{{ $c->id }}" {{ old('commodity_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->category }})
                            </option>
                        @endforeach
                    </select>
                    @error('commodity_id')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Judul Kampanye Pre-Order <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" required 
                           placeholder="Contoh: Pre-Order Jagung Manis Hibrida Masa Panen Oktober"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    @error('title')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Estimated Production, Unit, Min Order -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Estimasi Hasil Produksi <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="any" min="1" name="estimated_production" value="{{ old('estimated_production') }}" required 
                           placeholder="Contoh: 1000"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    @error('estimated_production')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Satuan Ukuran <span class="text-rose-500">*</span>
                    </label>
                    <select name="unit" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                        <option value="ton" {{ old('unit') == 'ton' ? 'selected' : '' }}>Ton</option>
                        <option value="kuintal" {{ old('unit') == 'kuintal' ? 'selected' : '' }}>Kuintal</option>
                        <option value="ikat" {{ old('unit') == 'ikat' ? 'selected' : '' }}>Ikat</option>
                        <option value="butir" {{ old('unit') == 'butir' ? 'selected' : '' }}>Butir</option>
                    </select>
                    @error('unit')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Minimal Pemesanan <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="any" min="1" name="min_order" value="{{ old('min_order', 10) }}" required 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    @error('min_order')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Price & Harvest Date -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Harga Pre-Order per Satuan (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">Rp</span>
                        <input type="number" min="1" name="price" value="{{ old('price') }}" required 
                               placeholder="Contoh: 12000"
                               class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                    @error('price')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Perkiraan Tanggal Panen <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="estimated_harvest_date" value="{{ old('estimated_harvest_date', date('Y-m-d', strtotime('+30 days'))) }}" min="{{ date('Y-m-d') }}" required 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    @error('estimated_harvest_date')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Location & Image -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Lokasi Lahan Kebun <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="location" value="{{ old('location', Auth::user()->farmerProfile->farm_location ?? (Auth::user()->farmerProfile->regency ?? '')) }}" required 
                           placeholder="Contoh: Lahan Blok C, Desa Margodadi, Sleman"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    @error('location')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Foto Lahan / Bibit Komoditas <span class="text-slate-400 text-[10px] font-normal">(Opsional)</span>
                    </label>
                    <input type="file" name="image" accept="image/*" 
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    @error('image')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Deskripsi Perawatan & Kualitas Rencana Panen <span class="text-slate-400 text-[10px] font-normal">(Opsional)</span>
                </label>
                <textarea name="description" rows="3" 
                          placeholder="Jelaskan varietas bibit, metode budidaya (organik/non-organik), estimasi bobot per buah, serta komitmen pengiriman..."
                          class="w-full px-4 py-3 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('farmer.preorders.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Buka Kampanye Pre-Order</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
