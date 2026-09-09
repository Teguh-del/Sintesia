@extends('layouts.dashboard')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-stone-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-bold text-stone-900 tracking-tight">Manajemen Pengguna</h1>
            <p class="text-sm text-stone-500 mt-1">Kelola data seluruh akun Petani, Pengepul, Konsumen, dan Administrator sistem.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <i data-lucide="users" class="w-3.5 h-3.5 mr-1.5"></i> Total: {{ $users->total() }} Pengguna
            </span>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3">
        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center gap-3">
        <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
        <p class="text-sm font-medium">{{ session('error') }}</p>
    </div>
    @endif

    <!-- Filter & Search Bar -->
    <div class="bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
        <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-6 relative">
                <i data-lucide="search" class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau telepon..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-stone-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
            </div>
            <div class="md:col-span-3">
                <select name="role" class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                    <option value="">Semua Peran (Role)</option>
                    <option value="petani" {{ request('role') == 'petani' ? 'selected' : '' }}>Petani</option>
                    <option value="pengepul" {{ request('role') == 'pengepul' ? 'selected' : '' }}>Pengepul</option>
                    <option value="konsumen" {{ request('role') == 'konsumen' ? 'selected' : '' }}>Konsumen</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <div class="md:col-span-3 flex gap-2">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold py-2.5 px-4 rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'role']))
                <a href="{{ route('admin.users.index') }}" class="px-3.5 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-600 rounded-xl transition flex items-center justify-center">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200 text-xs font-semibold text-stone-600 uppercase tracking-wider">
                        <th class="py-3.5 px-4">Pengguna</th>
                        <th class="py-3.5 px-4">Role</th>
                        <th class="py-3.5 px-4">Kontak & Lokasi</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Bergabung</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-sm">
                    @forelse($users as $user)
                    <tr class="hover:bg-stone-50/70 transition">
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-stone-900">{{ $user->name }}</div>
                                    <div class="text-xs text-stone-500">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            @if($user->role === 'petani')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                    <i data-lucide="sprout" class="w-3 h-3 mr-1"></i> Petani
                                </span>
                            @elseif($user->role === 'pengepul')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                    <i data-lucide="truck" class="w-3 h-3 mr-1"></i> Pengepul
                                </span>
                            @elseif($user->role === 'konsumen')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    <i data-lucide="shopping-bag" class="w-3 h-3 mr-1"></i> Konsumen
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                    <i data-lucide="shield-check" class="w-3 h-3 mr-1"></i> Admin
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-stone-600 text-xs space-y-0.5">
                            <div><i data-lucide="phone" class="w-3 h-3 inline mr-1 text-stone-400"></i>{{ $user->phone ?? '-' }}</div>
                            <div class="truncate max-w-xs text-stone-400"><i data-lucide="map-pin" class="w-3 h-3 inline mr-1 text-stone-400"></i>{{ $user->address ?? 'Belum ada alamat' }}</div>
                        </td>
                        <td class="py-4 px-4">
                            @if($user->is_active ?? true)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-xs text-stone-500">
                            {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
                        </td>
                        <td class="py-4 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="p-1.5 text-stone-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Lihat Profil">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" onsubmit="return confirm('Ubah status akun pengguna ini?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-1.5 {{ ($user->is_active ?? true) ? 'text-rose-600 hover:bg-rose-50' : 'text-emerald-600 hover:bg-emerald-50' }} rounded-lg transition" title="{{ ($user->is_active ?? true) ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                        <i data-lucide="{{ ($user->is_active ?? true) ? 'user-x' : 'user-check' }}" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-stone-500">
                            <i data-lucide="users" class="w-12 h-12 mx-auto text-stone-300 mb-3"></i>
                            <p class="font-medium text-stone-700">Tidak ada data pengguna ditemukan.</p>
                            <p class="text-xs text-stone-400 mt-1">Coba sesuaikan kata kunci atau filter pencarian Anda.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="p-4 border-t border-stone-200">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
