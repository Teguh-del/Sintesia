@extends('layouts.dashboard')

@section('title', 'Detail Pengguna - ' . $user->name)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="p-2 bg-white hover:bg-stone-100 rounded-xl border border-stone-200 text-stone-600 transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-stone-900 tracking-tight">Profil Pengguna</h1>
                <p class="text-sm text-stone-500">ID Pengguna: #USR-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            @if($user->id !== auth()->id())
            <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status akun ini?');">
                @csrf
                @method('PATCH')
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition shadow-sm {{ ($user->is_active ?? true) ? 'bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}">
                    <i data-lucide="{{ ($user->is_active ?? true) ? 'user-x' : 'user-check' }}" class="w-4 h-4"></i>
                    {{ ($user->is_active ?? true) ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                </button>
            </form>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3">
        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Profile Overview Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Identity Card -->
        <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-6">
            <div class="text-center">
                <div class="w-20 h-20 rounded-full bg-emerald-100 text-emerald-800 font-bold text-2xl flex items-center justify-center mx-auto mb-3 shadow-inner">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h2 class="text-xl font-bold text-stone-900">{{ $user->name }}</h2>
                <p class="text-xs text-stone-500 mt-0.5">{{ $user->email }}</p>
                
                <div class="mt-3 flex items-center justify-center gap-2">
                    @if($user->role === 'petani')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                            <i data-lucide="sprout" class="w-3.5 h-3.5 mr-1.5"></i> Petani
                        </span>
                    @elseif($user->role === 'pengepul')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                            <i data-lucide="truck" class="w-3.5 h-3.5 mr-1.5"></i> Pengepul
                        </span>
                    @elseif($user->role === 'konsumen')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                            <i data-lucide="shopping-bag" class="w-3.5 h-3.5 mr-1.5"></i> Konsumen
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5 mr-1.5"></i> Admin
                        </span>
                    @endif

                    @if($user->is_active ?? true)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">
                            Nonaktif
                        </span>
                    @endif
                </div>
            </div>

            <div class="border-t border-stone-100 pt-4 space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-stone-500">Nomor Telepon</span>
                    <span class="font-medium text-stone-800">{{ $user->phone ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-stone-500">Tanggal Daftar</span>
                    <span class="font-medium text-stone-800">{{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}</span>
                </div>
                <div class="pt-2">
                    <span class="text-stone-500 text-xs block mb-1">Alamat Domisili / Operasional</span>
                    <p class="text-stone-800 text-xs bg-stone-50 p-3 rounded-xl border border-stone-200">
                        {{ $user->address ?? 'Belum melengkapi alamat lengkap.' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Role Profile Details Card -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-6">
            <h3 class="text-lg font-bold text-stone-900 border-b border-stone-100 pb-3 flex items-center gap-2">
                <i data-lucide="badge-info" class="w-5 h-5 text-emerald-600"></i> Informasi Spesifik Peran
            </h3>

            @if($user->role === 'petani' && $user->farmerProfile)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div class="bg-stone-50 p-4 rounded-xl border border-stone-200/70">
                        <span class="text-xs text-stone-500 block">Nama Kelompok Tani</span>
                        <span class="font-bold text-stone-800 text-base mt-1 block">{{ $user->farmerProfile->farmer_group ?? '-' }}</span>
                    </div>
                    <div class="bg-stone-50 p-4 rounded-xl border border-stone-200/70">
                        <span class="text-xs text-stone-500 block">Luas Lahan Pertanian</span>
                        <span class="font-bold text-stone-800 text-base mt-1 block">{{ $user->farmerProfile->land_area ? $user->farmerProfile->land_area . ' Ha' : '-' }}</span>
                    </div>
                    <div class="bg-stone-50 p-4 rounded-xl border border-stone-200/70">
                        <span class="text-xs text-stone-500 block">Lokasi Koordinat Lahan</span>
                        <span class="font-medium text-stone-800 text-xs mt-1 block font-mono">
                            {{ $user->farmerProfile->latitude ?? '-' }}, {{ $user->farmerProfile->longitude ?? '-' }}
                        </span>
                    </div>
                    <div class="bg-stone-50 p-4 rounded-xl border border-stone-200/70">
                        <span class="text-xs text-stone-500 block">Status Sertifikasi</span>
                        <span class="font-medium text-stone-800 text-sm mt-1 block">{{ $user->farmerProfile->certification ?? 'Belum Tersertifikasi' }}</span>
                    </div>
                </div>
            @elseif($user->role === 'pengepul' && $user->collectorProfile)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div class="bg-stone-50 p-4 rounded-xl border border-stone-200/70">
                        <span class="text-xs text-stone-500 block">Nama Perusahaan / Gudang</span>
                        <span class="font-bold text-stone-800 text-base mt-1 block">{{ $user->collectorProfile->company_name ?? '-' }}</span>
                    </div>
                    <div class="bg-stone-50 p-4 rounded-xl border border-stone-200/70">
                        <span class="text-xs text-stone-500 block">Kapasitas Gudang</span>
                        <span class="font-bold text-stone-800 text-base mt-1 block">{{ $user->collectorProfile->storage_capacity ? number_format($user->collectorProfile->storage_capacity) . ' Kg' : '-' }}</span>
                    </div>
                </div>
            @else
                <div class="p-6 text-center text-stone-500 bg-stone-50 rounded-xl border border-stone-200/70">
                    <p class="text-sm">Pengguna belum menambahkan data profil spesifik atau profil tidak tersedia.</p>
                </div>
            @endif

            <!-- Mini Summary Activity -->
            <div class="border-t border-stone-100 pt-5">
                <h4 class="text-sm font-bold text-stone-900 mb-3">Ringkasan Aktivitas Terkini</h4>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-center">
                    @if($user->role === 'petani')
                    <div class="p-3 bg-emerald-50/60 rounded-xl border border-emerald-100">
                        <div class="text-lg font-bold text-emerald-800">{{ $user->products->count() }}</div>
                        <div class="text-xs text-emerald-600 font-medium mt-0.5">Produk Terdaftar</div>
                    </div>
                    <div class="p-3 bg-blue-50/60 rounded-xl border border-blue-100">
                        <div class="text-lg font-bold text-blue-800">{{ $user->sellerOrders->count() }}</div>
                        <div class="text-xs text-blue-600 font-medium mt-0.5">Pesanan Diterima</div>
                    </div>
                    <div class="p-3 bg-amber-50/60 rounded-xl border border-amber-100">
                        <div class="text-lg font-bold text-amber-800">{{ $user->harvests()->count() }}</div>
                        <div class="text-xs text-amber-600 font-medium mt-0.5">Catatan Panen</div>
                    </div>
                    @else
                    <div class="p-3 bg-blue-50/60 rounded-xl border border-blue-100">
                        <div class="text-lg font-bold text-blue-800">{{ $user->buyerOrders->count() }}</div>
                        <div class="text-xs text-blue-600 font-medium mt-0.5">Total Pesanan Dibuat</div>
                    </div>
                    <div class="p-3 bg-purple-50/60 rounded-xl border border-purple-100">
                        <div class="text-lg font-bold text-purple-800">{{ $user->commodityRequests()->count() }}</div>
                        <div class="text-xs text-purple-600 font-medium mt-0.5">Permintaan Komoditas</div>
                    </div>
                    <div class="p-3 bg-emerald-50/60 rounded-xl border border-emerald-100">
                        <div class="text-lg font-bold text-emerald-800">{{ $user->buyerPriceOffers()->count() }}</div>
                        <div class="text-xs text-emerald-600 font-medium mt-0.5">Penawaran Harga</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
