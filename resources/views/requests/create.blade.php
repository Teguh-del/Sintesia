@extends('layouts.dashboard')

@section('title', 'Buat Permintaan Pasokan Komoditas')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('requests.index') }}" class="hover:text-slate-800 transition">Permintaan Komoditas</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold">Buat Permintaan Baru</span>
        </div>
        <a href="{{ route('requests.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs font-bold transition shadow-xs">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Bursa Permintaan</span>
        </a>
    </div>

    <!-- Card Form -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="flex items-center gap-4 pb-6 border-b border-slate-100">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="clipboard-edit" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">Publikasikan Permintaan Pasokan Komoditas</h1>
                <p class="text-xs text-slate-500 mt-0.5">Sistem akan menyiarkan permintaan Anda ke seluruh mitra petani terkait di platform</p>
            </div>
        </div>

        <form action="{{ route('requests.store') }}" method="POST" class="space-y-6 pt-6">
            @csrf

            <!-- Commodity & Title -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Komoditas yang Dibutuhkan <span class="text-rose-500">*</span>
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
                        Judul Singkat Permintaan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" required 
                           placeholder="Contoh: Butuh Pasokan Cabai Rawit Merah Segar Grade A"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    @error('title')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Quantity & Unit & Max Price -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Volume Kebutuhan <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="any" min="0.01" name="required_quantity" value="{{ old('required_quantity') }}" required 
                           placeholder="Contoh: 500"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    @error('required_quantity')
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
                        Batas Harga Maksimal (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">Rp</span>
                        <input type="number" min="1" name="max_price" value="{{ old('max_price') }}" required 
                               placeholder="Contoh: 35000"
                               class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                    @error('max_price')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Location & Deadline -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Lokasi Penerimaan Pasokan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="location" value="{{ old('location', Auth::user()->consumerProfile->address ?? (Auth::user()->collectorProfile->address ?? '')) }}" required 
                           placeholder="Contoh: Gudang Induk, Sleman, DI Yogyakarta"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    @error('location')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Batas Waktu Pengadaan (Deadline) <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="deadline" value="{{ old('deadline', date('Y-m-d', strtotime('+14 days'))) }}" min="{{ date('Y-m-d') }}" required 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    @error('deadline')
                        <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description & Specifications -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Spesifikasi Kualitas & Catatan Tambahan <span class="text-slate-400 text-[10px] font-normal">(Opsional)</span>
                </label>
                <textarea name="description" rows="3" 
                          placeholder="Jelaskan standar kesegaran, toleransi kadar air, kemasan karung, atau ketentuan penimbangan yang diinginkan..."
                          class="w-full px-4 py-3 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('requests.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span>Publikasikan Permintaan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
