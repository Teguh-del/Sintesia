@extends('layouts.dashboard')

@section('title', 'Negosiasi Harga Masuk')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Tawaran Negosiasi Harga Masuk</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola dan respon tawaran harga dari calon pembeli (Pengepul & Konsumen)</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('farmer.products.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                <i data-lucide="store" class="w-4 h-4"></i>
                <span>Kelola Produk Saya</span>
            </a>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex flex-wrap items-center gap-2 pb-2 border-b border-slate-200">
        <a href="{{ route('farmer.negotiations.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ !$status ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Semua</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ !$status ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $counts['all'] }}</span>
        </a>
        <a href="{{ route('farmer.negotiations.index', ['status' => 'Menunggu']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Menunggu' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Perlu Tindakan</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $status === 'Menunggu' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-700' }}">{{ $counts['pending'] }}</span>
        </a>
        <a href="{{ route('farmer.negotiations.index', ['status' => 'Counter Offer']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Counter Offer' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Tawaran Balik Terkirim</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $status === 'Counter Offer' ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-700' }}">{{ $counts['counter'] }}</span>
        </a>
        <a href="{{ route('farmer.negotiations.index', ['status' => 'Selesai']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Selesai' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Disepakati</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $status === 'Selesai' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-700' }}">{{ $counts['completed'] }}</span>
        </a>
        <a href="{{ route('farmer.negotiations.index', ['status' => 'Ditolak']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $status === 'Ditolak' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Ditolak</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $status === 'Ditolak' ? 'bg-white/20 text-white' : 'bg-rose-100 text-rose-700' }}">{{ $counts['rejected'] }}</span>
        </a>
    </div>

    <!-- Negotiation Offer Cards -->
    @if($offers->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center max-w-lg mx-auto space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto">
                <i data-lucide="handshake" class="w-8 h-8"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-800">Tidak Ada Tawaran Negosiasi</h3>
                <p class="text-xs text-slate-500 mt-1">Belum ada calon pembeli yang mengajukan penawaran harga pada filter ini.</p>
            </div>
        </div>
    @else
        <div class="space-y-4">
            @foreach($offers as $offer)
                <div class="bg-white rounded-3xl border border-slate-200 hover:border-slate-300 shadow-sm p-5 sm:p-6 transition">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3.5">
                            <img src="{{ $offer->product->primary_image_url }}" alt="{{ $offer->product->name }}" class="w-16 h-16 rounded-2xl object-cover border border-slate-200 flex-shrink-0">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
                                        {{ $offer->product->commodity->name ?? 'Komoditas' }}
                                    </span>
                                    <span class="text-xs text-slate-400">•</span>
                                    <span class="text-xs text-slate-500 font-medium">Pembeli: <strong class="text-slate-800">{{ $offer->buyer->name }}</strong> ({{ ucfirst($offer->buyer->role) }})</span>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 mt-1">
                                    {{ $offer->product->name }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Permintaan volume: <strong class="text-slate-700">{{ number_format($offer->quantity, 0, ',', '.') }} {{ $offer->product->unit }}</strong> | Metode: <span class="text-slate-700">{{ $offer->shipping_method }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            @if($offer->status === 'Menunggu')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span>Menunggu Keputusan Anda</span>
                                </span>
                            @elseif($offer->status === 'Counter Offer')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 text-indigo-600"></i>
                                    <span>Menunggu Konfirmasi Pembeli</span>
                                </span>
                            @elseif($offer->status === 'Diterima' || $offer->status === 'Selesai')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-600"></i>
                                    <span>Disepakati</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    <span>{{ $offer->status }}</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Comparison Details -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 py-4 text-xs">
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-slate-400 font-semibold block">Harga Normal Anda:</span>
                            <span class="text-sm font-bold text-slate-700 mt-0.5 block">Rp {{ number_format($offer->product_price, 0, ',', '.') }} / {{ $offer->product->unit }}</span>
                            <span class="text-[10px] text-slate-400">Total Normal: Rp {{ number_format($offer->product_price * $offer->quantity, 0, ',', '.') }}</span>
                        </div>
                        <div class="p-3 bg-amber-50/70 rounded-2xl border border-amber-200">
                            <span class="text-amber-700 font-semibold block">Tawaran Pembeli:</span>
                            <span class="text-sm font-black text-amber-900 mt-0.5 block">Rp {{ number_format($offer->offered_price, 0, ',', '.') }} / {{ $offer->product->unit }}</span>
                            <span class="text-[10px] text-amber-700 font-bold">Total Tawaran: Rp {{ number_format($offer->offered_price * $offer->quantity, 0, ',', '.') }}</span>
                        </div>
                        <div class="p-3 {{ $offer->counter_price ? 'bg-indigo-50 border border-indigo-200' : 'bg-slate-50 border border-slate-100' }} rounded-2xl">
                            <span class="{{ $offer->counter_price ? 'text-indigo-700 font-semibold' : 'text-slate-400' }} block">Tawaran Balik Anda:</span>
                            @if($offer->counter_price)
                                <span class="text-sm font-black text-indigo-900 mt-0.5 block">Rp {{ number_format($offer->counter_price, 0, ',', '.') }} / {{ $offer->product->unit }}</span>
                                <span class="text-[10px] text-indigo-700">Total: Rp {{ number_format($offer->counter_price * $offer->quantity, 0, ',', '.') }}</span>
                            @else
                                <span class="text-xs text-slate-400 font-medium mt-1 block">Belum ada counter offer</span>
                            @endif
                        </div>
                    </div>

                    @if($offer->notes)
                        <div class="p-3 bg-slate-50 rounded-xl text-xs text-slate-600 mb-3 border border-slate-100">
                            <strong>Pesan dari Pembeli:</strong> "{{ $offer->notes }}"
                        </div>
                    @endif

                    <!-- Action Bar -->
                    @if($offer->status === 'Menunggu')
                        <div class="flex flex-col sm:flex-row sm:items-center justify-end gap-3 pt-3 border-t border-slate-100">
                            <form action="{{ route('farmer.negotiations.reject', $offer) }}" method="POST">
                                @csrf
                                <button type="submit" onclick="return confirm('Tolak tawaran ini?')" class="px-4 py-2.5 rounded-xl bg-white hover:bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold transition">
                                    Tolak Tawaran
                                </button>
                            </form>

                            <!-- Counter Offer Button toggles inline drawer -->
                            <button type="button" onclick="document.getElementById('counter-box-{{ $offer->id }}').classList.toggle('hidden')" class="px-4 py-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs font-bold transition flex items-center gap-1.5">
                                <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                                <span>Tawar Balik</span>
                            </button>

                            <form action="{{ route('farmer.negotiations.accept', $offer) }}" method="POST">
                                @csrf
                                <button type="submit" onclick="return confirm('Sepakati tawaran pembeli Rp {{ number_format($offer->offered_price, 0, ',', '.') }}/{{ $offer->product->unit }}? Pesanan masuk baru akan otomatis dibuat.')" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5">
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                    <span>Sepakati Harga Ini</span>
                                </button>
                            </form>
                        </div>

                        <!-- Hidden Counter Offer Form -->
                        <div id="counter-box-{{ $offer->id }}" class="mt-4 p-4 bg-indigo-50/50 rounded-2xl border border-indigo-200 hidden">
                            <form action="{{ route('farmer.negotiations.counter', $offer) }}" method="POST" class="space-y-3">
                                @csrf
                                <h4 class="text-xs font-bold text-indigo-950 uppercase tracking-wider">Kirim Tawaran Harga Balik ke Pembeli</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Harga Tawaran Balik (Rp / {{ $offer->product->unit }})</label>
                                        <input type="number" name="counter_price" required min="1" 
                                               placeholder="Contoh: {{ (int)(($offer->product_price + $offer->offered_price) / 2) }}" 
                                               class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                                        <input type="text" name="notes" placeholder="Contoh: Termasuk biaya sortir grade A" 
                                               class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2 pt-1">
                                    <button type="button" onclick="document.getElementById('counter-box-{{ $offer->id }}').classList.add('hidden')" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">Batal</button>
                                    <button type="submit" class="px-4 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm">Kirim Tawaran Balik</button>
                                </div>
                            </form>
                        </div>
                    @elseif($offer->status === 'Counter Offer')
                        <div class="pt-3 border-t border-slate-100 text-xs text-slate-500 flex items-center justify-between">
                            <span>Tawaran balik Anda sebesar <strong>Rp {{ number_format($offer->counter_price, 0, ',', '.') }}/{{ $offer->product->unit }}</strong> sedang ditinjau oleh pembeli.</span>
                            <form action="{{ route('farmer.negotiations.reject', $offer) }}" method="POST">
                                @csrf
                                <button type="submit" onclick="return confirm('Batalkan negosiasi ini?')" class="text-rose-600 hover:text-rose-700 font-semibold">
                                    Batalkan
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach

            <div class="pt-4">
                {{ $offers->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
