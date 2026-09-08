@extends('layouts.dashboard')

@section('title', 'Pencatatan Hasil Panen - Petani SINTESA')

@section('content')
<div class="space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pencatatan Hasil Panen</h1>
            <p class="text-sm text-slate-500 mt-0.5">Catat hasil produksi panen Anda. Setiap panen baru akan otomatis masuk ke inventaris stok riil.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('farmer.stocks.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs shadow-sm transition">
                <i data-lucide="boxes" class="w-4 h-4 text-emerald-600"></i>
                <span>Lihat Stok Riil</span>
            </a>
            <a href="{{ route('farmer.harvests.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition transform hover:-translate-y-0.5">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Catat Panen Baru</span>
            </a>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Hasil Panen</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalHarvestQty, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="wheat" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Panen Bulan Ini</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($thisMonthQty, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                <i data-lucide="calendar" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Komoditas Dipanen</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $distinctCommoditiesCount }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <i data-lucide="layers" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Frekuensi Panen</p>
                <p class="text-2xl font-black text-indigo-600 mt-1">{{ $totalHarvestsCount }} <span class="text-xs font-semibold text-slate-400">kali</span></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <i data-lucide="clipboard-check" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Harvest Table & Filters -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <!-- Filter Form -->
        <div class="p-4 sm:p-5 border-b border-slate-100">
            <form action="{{ route('farmer.harvests.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Search Keyword -->
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kebun, catatan..." 
                           class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:bg-white focus:outline-none transition">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
                </div>

                <!-- Commodity Select -->
                <div>
                    <select name="commodity_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="">Semua Komoditas</option>
                        @foreach($commodities as $com)
                            <option value="{{ $com->id }}" {{ request('commodity_id') == $com->id ? 'selected' : '' }}>
                                {{ $com->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Quality Select -->
                <div>
                    <select name="quality" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="">Semua Kualitas</option>
                        @foreach($qualityOptions as $qOpt)
                            <option value="{{ $qOpt }}" {{ request('quality') == $qOpt ? 'selected' : '' }}>
                                {{ $qOpt }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range -->
                <div class="flex items-center gap-1">
                    <input type="date" name="date_from" value="{{ request('date_from') }}" title="Dari tanggal" class="w-full px-2 py-2 bg-slate-50 border border-slate-200 rounded-xl text-[11px] text-slate-700 focus:outline-none">
                    <span class="text-xs text-slate-400">-</span>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" title="Sampai tanggal" class="w-full px-2 py-2 bg-slate-50 border border-slate-200 rounded-xl text-[11px] text-slate-700 focus:outline-none">
                </div>

                <!-- Buttons -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition">
                        Filter
                    </button>
                    @if(request()->anyFilled(['search', 'commodity_id', 'quality', 'date_from', 'date_to']))
                        <a href="{{ route('farmer.harvests.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition" title="Reset Filter">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Harvests Table -->
        @if($harvests->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50/75 border-b border-slate-100 uppercase font-bold text-slate-500 text-[11px] tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5">Tanggal Panen</th>
                            <th class="px-6 py-3.5">Komoditas & Mutu</th>
                            <th class="px-6 py-3.5">Kuantitas Panen</th>
                            <th class="px-6 py-3.5">Lokasi Kebun</th>
                            <th class="px-6 py-3.5">Integrasi Stok Riil</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($harvests as $h)
                            <tr class="hover:bg-slate-50/50 transition">
                                <!-- Harvest Date -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center flex-shrink-0">
                                            <i data-lucide="calendar" class="w-4 h-4"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900">{{ $h->harvest_date->translatedFormat('d M Y') }}</p>
                                            <p class="text-[10px] text-slate-400">{{ $h->harvest_date->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Commodity & Quality -->
                                <td class="px-6 py-4">
                                    <span class="font-bold text-slate-900 text-sm">{{ $h->commodity->name ?? 'Komoditas' }}</span>
                                    <div class="mt-1">
                                        <span class="px-2 py-0.5 rounded bg-slate-100 text-[10px] font-semibold text-slate-700">
                                            {{ $h->quality }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Quantity -->
                                <td class="px-6 py-4">
                                    <span class="font-black text-slate-900 text-sm">{{ $h->formatted_quantity }}</span>
                                </td>

                                <!-- Location & Notes -->
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-slate-800 flex items-center gap-1 truncate max-w-xs">
                                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0"></i>
                                        <span class="truncate">{{ $h->location }}</span>
                                    </p>
                                    @if($h->notes)
                                        <p class="text-[11px] text-slate-400 italic mt-0.5 truncate max-w-xs">"{{ $h->notes }}"</p>
                                    @endif
                                </td>

                                <!-- Stock Integration Link -->
                                <td class="px-6 py-4">
                                    @if($h->stock)
                                        <a href="{{ route('farmer.stocks.show', $h->stock->id) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition">
                                            <i data-lucide="boxes" class="w-3.5 h-3.5"></i>
                                            <span>{{ $h->stock->batch_code }}</span>
                                        </a>
                                        <p class="text-[10px] text-slate-400 mt-0.5">Sisa stok: {{ $h->stock->formatted_available }}</p>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">-</span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('farmer.harvests.edit', $h->id) }}" title="Edit Panen" 
                                           class="p-2 rounded-lg bg-slate-100 hover:bg-amber-50 text-slate-600 hover:text-amber-700 transition">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>

                                        <form action="{{ route('farmer.harvests.destroy', $h->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data panen ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus Panen" class="p-2 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-700 transition">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-100">
                {{ $harvests->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="sprout" class="w-8 h-8"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Belum Ada Catatan Hasil Panen</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mb-6">
                    Catat hasil panen kebun Anda untuk memastikan stok riil komoditas tercatat rapi dan siap dipasarkan secara transparan.
                </p>
                <a href="{{ route('farmer.harvests.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Catat Hasil Panen Pertama</span>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
