@extends('layouts.dashboard')

@section('title', 'Master Komoditas Pertanian')

@section('content')
<div class="space-y-6" x-data="{ openCreate: false, editModal: false, editData: {} }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-stone-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-bold text-stone-900 tracking-tight">Master Komoditas Pertanian</h1>
            <p class="text-sm text-stone-500 mt-1">Kelola katalog varietas komoditas resmi yang terintegrasi di sistem SINTESA.</p>
        </div>
        <div>
            <button @click="openCreate = true" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold text-sm transition shadow-sm">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Komoditas
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3">
        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl">
        <p class="text-sm font-bold mb-1">Gagal menyimpan komoditas:</p>
        <ul class="list-disc list-inside text-xs space-y-0.5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200 text-xs font-semibold text-stone-600 uppercase tracking-wider">
                        <th class="py-3.5 px-4">Nama Komoditas</th>
                        <th class="py-3.5 px-4">Kategori & Satuan</th>
                        <th class="py-3.5 px-4">Deskripsi</th>
                        <th class="py-3.5 px-4 text-center">Penggunaan</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-sm">
                    @forelse($commodities as $commodity)
                    <tr class="hover:bg-stone-50/70 transition">
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                                    <i data-lucide="sprout" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <span class="font-bold text-stone-900 block">{{ $commodity->name }}</span>
                                    <span class="text-xs text-stone-400 font-mono">slug: {{ $commodity->slug ?? '-' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex flex-col gap-1 items-start">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-stone-100 text-stone-700">
                                    {{ $commodity->category ?? 'Umum' }}
                                </span>
                                <span class="text-xs text-stone-400">Satuan: {{ $commodity->unit ?? 'kg' }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-xs text-stone-500 max-w-xs truncate">
                            {{ $commodity->description ?? '-' }}
                        </td>
                        <td class="py-4 px-4 text-center">
                            <div class="flex items-center justify-center gap-2 text-xs">
                                <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-medium" title="Produk">
                                    {{ $commodity->products_count ?? 0 }} Produk
                                </span>
                                <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-medium" title="Panen">
                                    {{ $commodity->harvests_count ?? 0 }} Panen
                                </span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            @if($commodity->is_active ?? true)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-stone-100 text-stone-600 border border-stone-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-stone-400 mr-1.5"></span> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" @click="editModal = true; editData = {
                                    id: {{ $commodity->id }},
                                    name: '{{ addslashes($commodity->name) }}',
                                    category: '{{ addslashes($commodity->category ?? '') }}',
                                    unit: '{{ addslashes($commodity->unit ?? 'kg') }}',
                                    description: '{{ addslashes($commodity->description ?? '') }}',
                                    icon: '{{ addslashes($commodity->icon ?? '') }}',
                                    is_active: {{ ($commodity->is_active ?? true) ? 'true' : 'false' }}
                                }" class="p-1.5 text-stone-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Edit Komoditas">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </button>

                                <form action="{{ route('admin.commodities.toggle-status', $commodity->id) }}" method="POST" onsubmit="return confirm('Ubah status aktif komoditas ini?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-1.5 {{ ($commodity->is_active ?? true) ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }} rounded-lg transition" title="{{ ($commodity->is_active ?? true) ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i data-lucide="{{ ($commodity->is_active ?? true) ? 'eye-off' : 'eye' }}" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-stone-500">
                            <i data-lucide="sprout" class="w-12 h-12 mx-auto text-stone-300 mb-3"></i>
                            <p class="font-medium text-stone-700">Belum ada komoditas terdaftar.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($commodities, 'hasPages') && $commodities->hasPages())
        <div class="p-4 border-t border-stone-200">
            {{ $commodities->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Tambah Komoditas -->
    <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/50 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div @click.away="openCreate = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-stone-200 space-y-4">
            <div class="flex justify-between items-center pb-3 border-b border-stone-100">
                <h3 class="text-lg font-bold text-stone-900">Tambah Komoditas Baru</h3>
                <button @click="openCreate = false" class="text-stone-400 hover:text-stone-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form action="{{ route('admin.commodities.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Nama Komoditas <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Bawang Merah Brebes" class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-stone-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                        <select name="category" class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:ring-2 focus:ring-emerald-500">
                            <option value="Hortikultura">Hortikultura</option>
                            <option value="Pangan">Pangan</option>
                            <option value="Perkebunan">Perkebunan</option>
                            <option value="Palawija">Palawija</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-stone-700 mb-1">Satuan Dasar <span class="text-rose-500">*</span></label>
                        <input type="text" name="unit" required value="kg" placeholder="kg, ton, ikat..." class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Deskripsi Singkat</label>
                    <textarea name="description" rows="3" placeholder="Informasi singkat tentang komoditas ini..." class="w-full px-3.5 py-2 rounded-xl border border-stone-300 text-sm focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="openCreate = false" class="px-4 py-2 text-stone-600 hover:bg-stone-100 rounded-xl text-sm font-medium">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold shadow-sm">Simpan Komoditas</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Komoditas -->
    <div x-show="editModal" class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/50 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div @click.away="editModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-stone-200 space-y-4">
            <div class="flex justify-between items-center pb-3 border-b border-stone-100">
                <h3 class="text-lg font-bold text-stone-900">Edit Komoditas</h3>
                <button @click="editModal = false" class="text-stone-400 hover:text-stone-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form :action="'{{ url('admin/commodities') }}/' + editData.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Nama Komoditas <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="editData.name" required class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-stone-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                        <select name="category" x-model="editData.category" class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:ring-2 focus:ring-emerald-500">
                            <option value="Hortikultura">Hortikultura</option>
                            <option value="Pangan">Pangan</option>
                            <option value="Perkebunan">Perkebunan</option>
                            <option value="Palawija">Palawija</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-stone-700 mb-1">Satuan Dasar <span class="text-rose-500">*</span></label>
                        <input type="text" name="unit" x-model="editData.unit" required class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Deskripsi Singkat</label>
                    <textarea name="description" x-model="editData.description" rows="3" class="w-full px-3.5 py-2 rounded-xl border border-stone-300 text-sm focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 text-stone-600 hover:bg-stone-100 rounded-xl text-sm font-medium">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold shadow-sm">Perbarui Komoditas</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
