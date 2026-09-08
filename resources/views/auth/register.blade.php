@extends('layouts.app')

@section('title', 'Daftar Akun Baru — SINTESA')

@section('content')
<div class="min-h-[calc(100vh-12rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50">
    <div class="max-w-xl w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-emerald-600 text-white shadow-lg shadow-emerald-500/20 mb-4">
                <i data-lucide="user-plus" class="w-7 h-7"></i>
            </div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Bergabung dengan SINTESA</h2>
            <p class="mt-2 text-sm text-slate-500">
                Pilih peran Anda dan lengkapi data untuk mulai bertransaksi niaga pertanian cerdas.
            </p>
        </div>

        <!-- Register Card -->
        <div class="bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-200/80">
            <form action="{{ route('register') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Role Selection -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">Pilih Peran Akun Anda</label>
                    <div class="grid grid-cols-3 gap-3">
                        <!-- Petani -->
                        <label class="relative flex flex-col items-center justify-center p-4 rounded-2xl border-2 cursor-pointer transition text-center hover:bg-emerald-50/50" id="role-label-petani">
                            <input type="radio" name="role" value="petani" class="sr-only" onchange="updateRoleFields('petani')" {{ old('role', 'petani') === 'petani' ? 'checked' : '' }}>
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center mb-2">
                                <i data-lucide="sprout" class="w-5 h-5"></i>
                            </div>
                            <span class="text-sm font-bold text-slate-900">Petani</span>
                            <span class="text-[10px] text-slate-500 mt-0.5">Penjual Hasil Panen</span>
                        </label>

                        <!-- Pengepul -->
                        <label class="relative flex flex-col items-center justify-center p-4 rounded-2xl border-2 cursor-pointer transition text-center hover:bg-amber-50/50" id="role-label-pengepul">
                            <input type="radio" name="role" value="pengepul" class="sr-only" onchange="updateRoleFields('pengepul')" {{ old('role') === 'pengepul' ? 'checked' : '' }}>
                            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center mb-2">
                                <i data-lucide="truck" class="w-5 h-5"></i>
                            </div>
                            <span class="text-sm font-bold text-slate-900">Pengepul</span>
                            <span class="text-[10px] text-slate-500 mt-0.5">Pedagang & Grosir</span>
                        </label>

                        <!-- Konsumen -->
                        <label class="relative flex flex-col items-center justify-center p-4 rounded-2xl border-2 cursor-pointer transition text-center hover:bg-blue-50/50" id="role-label-konsumen">
                            <input type="radio" name="role" value="konsumen" class="sr-only" onchange="updateRoleFields('konsumen')" {{ old('role') === 'konsumen' ? 'checked' : '' }}>
                            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center mb-2">
                                <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                            </div>
                            <span class="text-sm font-bold text-slate-900">Konsumen</span>
                            <span class="text-[10px] text-slate-500 mt-0.5">Pembeli Langsung</span>
                        </label>
                    </div>
                    @error('role')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Credentials -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Nama Lengkap</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required
                            class="block w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition @error('name') border-rose-500 @enderror"
                            placeholder="Contoh: Budi Santoso">
                        @error('name')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Alamat Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required
                            class="block w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition @error('email') border-rose-500 @enderror"
                            placeholder="nama@email.com">
                        @error('email')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Nomor Telepon / WhatsApp</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone') }}"
                            class="block w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition"
                            placeholder="081234567890">
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Kata Sandi</label>
                        <input id="password" name="password" type="password" required
                            class="block w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition @error('password') border-rose-500 @enderror"
                            placeholder="Minimal 8 karakter">
                        @error('password')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Konfirmasi Kata Sandi</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="block w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition"
                            placeholder="Ulangi kata sandi">
                    </div>
                </div>

                <!-- Dynamic Farmer Fields -->
                <div id="farmer-fields" class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200 space-y-4">
                    <div class="flex items-center gap-2">
                        <i data-lucide="info" class="w-4 h-4 text-emerald-700"></i>
                        <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Informasi Pertanian (Petani)</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lahan / Kelompok Tani</label>
                            <input type="text" name="farm_name" value="{{ old('farm_name') }}"
                                class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                placeholder="Contoh: Tani Subur Pare">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Luas Lahan (Hektare)</label>
                            <input type="number" step="0.1" name="farm_area_hectares" value="{{ old('farm_area_hectares') }}"
                                class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                placeholder="Contoh: 1.5">
                        </div>
                    </div>
                </div>

                <!-- Dynamic Collector Fields -->
                <div id="collector-fields" class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200 space-y-4 hidden">
                    <div class="flex items-center gap-2">
                        <i data-lucide="info" class="w-4 h-4 text-amber-700"></i>
                        <span class="text-xs font-bold text-amber-800 uppercase tracking-wider">Informasi Usaha (Pengepul)</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Usaha / Gudang</label>
                            <input type="text" name="business_name" value="{{ old('business_name') }}"
                                class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-500"
                                placeholder="Contoh: UD Hasil Bumi">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tipe Bisnis</label>
                            <input type="text" name="business_type" value="{{ old('business_type') }}"
                                class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-500"
                                placeholder="Contoh: Pengepul Grosir">
                        </div>
                    </div>
                </div>

                <!-- Address Field -->
                <div>
                    <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Alamat Domisili / Lokasi Lahan</label>
                    <textarea id="address" name="address" rows="2"
                        class="block w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition"
                        placeholder="Kecamatan, Kabupaten, Provinsi">{{ old('address') }}</textarea>
                </div>

                <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md shadow-emerald-600/20 hover:shadow-lg transition duration-200 flex items-center justify-center gap-2">
                    <span>Selesaikan Pendaftaran</span>
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                </button>
            </form>

            <div class="mt-6 text-center text-xs text-slate-500">
                Sudah memiliki akun?
                <a href="{{ route('login') }}" class="font-bold text-emerald-600 hover:text-emerald-700">Masuk di sini</a>
            </div>
        </div>
    </div>
</div>

<script>
function updateRoleFields(role) {
    const farmerFields = document.getElementById('farmer-fields');
    const collectorFields = document.getElementById('collector-fields');

    const labels = {
        petani: document.getElementById('role-label-petani'),
        pengepul: document.getElementById('role-label-pengepul'),
        konsumen: document.getElementById('role-label-konsumen'),
    };

    // Reset styles
    for (const key in labels) {
        labels[key].className = "relative flex flex-col items-center justify-center p-4 rounded-2xl border-2 border-slate-200 cursor-pointer transition text-center hover:bg-slate-50";
    }

    if (role === 'petani') {
        labels.petani.className = "relative flex flex-col items-center justify-center p-4 rounded-2xl border-2 border-emerald-600 bg-emerald-50/50 cursor-pointer transition text-center shadow-sm";
        farmerFields.classList.remove('hidden');
        collectorFields.classList.add('hidden');
    } else if (role === 'pengepul') {
        labels.pengepul.className = "relative flex flex-col items-center justify-center p-4 rounded-2xl border-2 border-amber-600 bg-amber-50/50 cursor-pointer transition text-center shadow-sm";
        farmerFields.classList.add('hidden');
        collectorFields.classList.remove('hidden');
    } else {
        labels.konsumen.className = "relative flex flex-col items-center justify-center p-4 rounded-2xl border-2 border-blue-600 bg-blue-50/50 cursor-pointer transition text-center shadow-sm";
        farmerFields.classList.add('hidden');
        collectorFields.classList.add('hidden');
    }
}

// Init selection on page load
document.addEventListener('DOMContentLoaded', () => {
    const selectedRole = document.querySelector('input[name="role"]:checked')?.value || 'petani';
    updateRoleFields(selectedRole);
});
</script>
@endsection
