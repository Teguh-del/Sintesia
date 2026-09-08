@extends('layouts.dashboard')

@section('title', 'Edit Hasil Panen - Petani SINTESA')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <a href="{{ route('farmer.harvests.index') }}" class="hover:text-emerald-600 transition">Hasil Panen</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-slate-800">Edit Panen</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Edit Catatan Panen</h1>
        </div>
        <a href="{{ route('farmer.harvests.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
            Kembali
        </a>
    </div>

    <!-- Main Form Container -->
    <form action="{{ route('farmer.harvests.update', $harvest->id) }}" method="POST" class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Commodity Select -->
            <div class="sm:col-span-2">
                <label for="commodity_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Komoditas Pertanian <span class="text-rose-500">*</span>
                </label>
                <select id="commodity_id" name="commodity_id" required 
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition @error('commodity_id') border-rose-500 @enderror">
                    @foreach($commodities as $com)
                        <option value="{{ $com->id }}" data-unit="{{ $com->unit }}" {{ old('commodity_id', $harvest->commodity_id) == $com->id ? 'selected' : '' }}>
                            {{ $com->name }} (Satuan: {{ $com->unit }})
                        </option>
                    @endforeach
                </select>
                @error('commodity_id')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Quantity -->
            <div>
                <label for="quantity" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Jumlah / Volume Hasil Panen <span class="text-rose-500">*</span>
                </label>
                <input type="number" id="quantity" name="quantity" value="{{ old('quantity', $harvest->quantity) }}" required min="0.01" step="any" 
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition @error('quantity') border-rose-500 @enderror">
                @error('quantity')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Unit -->
            <div>
                <label for="unit" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Satuan Komoditas <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="unit" name="unit" value="{{ old('unit', $harvest->unit) }}" required 
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition @error('unit') border-rose-500 @enderror">
                @error('unit')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Harvest Date -->
            <div>
                <label for="harvest_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Tanggal Petik / Panen <span class="text-rose-500">*</span>
                </label>
                <input type="date" id="harvest_date" name="harvest_date" value="{{ old('harvest_date', $harvest->harvest_date->format('Y-m-d')) }}" required 
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition @error('harvest_date') border-rose-500 @enderror">
                @error('harvest_date')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Quality Grade -->
            <div>
                <label for="quality" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Mutu / Grade Kualitas <span class="text-rose-500">*</span>
                </label>
                <select id="quality" name="quality" required 
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition @error('quality') border-rose-500 @enderror">
                    <option value="Grade A (Super)" {{ old('quality', $harvest->quality) == 'Grade A (Super)' ? 'selected' : '' }}>Grade A (Super / Ekspor)</option>
                    <option value="Grade B (Standar)" {{ old('quality', $harvest->quality) == 'Grade B (Standar)' ? 'selected' : '' }}>Grade B (Standar Pasar)</option>
                    <option value="Organik Premium" {{ old('quality', $harvest->quality) == 'Organik Premium' ? 'selected' : '' }}>Organik Premium</option>
                    <option value="Campuran / Curah" {{ old('quality', $harvest->quality) == 'Campuran / Curah' ? 'selected' : '' }}>Campuran / Curah Standar</option>
                </select>
                @error('quality')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Location -->
            <div class="sm:col-span-2">
                <label for="location" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Lokasi Lahan / Kebun Panen <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="location" name="location" value="{{ old('location', $harvest->location) }}" required 
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition @error('location') border-rose-500 @enderror">
                @error('location')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div class="sm:col-span-2">
                <label for="notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Catatan Hasil Panen (Opsional)
                </label>
                <textarea id="notes" name="notes" rows="3" 
                          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition">{{ old('notes', $harvest->notes) }}</textarea>
            </div>
        </div>

        <!-- Submit Bar -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('farmer.harvests.index') }}" class="px-6 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                Batal
            </a>
            <button type="submit" class="px-8 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 transition flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>
</div>
@endsection
