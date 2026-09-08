@extends('layouts.app')

@section('title', 'Masuk ke Akun — SINTESA')

@section('content')
<div class="min-h-[calc(100vh-12rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50">
    <div class="max-w-md w-full space-y-8">
        <!-- Header Card -->
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-emerald-600 text-white shadow-lg shadow-emerald-500/20 mb-4">
                <i data-lucide="lock" class="w-7 h-7"></i>
            </div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Masuk ke SINTESA</h2>
            <p class="mt-2 text-sm text-slate-500">
                Pilih akses peran Anda atau masukkan email & kata sandi terdaftar.
            </p>
        </div>

        <!-- Login Card -->
        <div class="bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-200/80">
            <!-- Quick Fill Demo Accounts -->
            <div class="mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                <div class="flex items-center gap-2 mb-2.5">
                    <i data-lucide="zap" class="w-4 h-4 text-amber-500"></i>
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Akses Cepat Akun Demo (Phase 1):</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" onclick="fillDemo('petani@sintesa.id', 'password')" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-white hover:bg-emerald-50 text-emerald-800 border border-slate-200 hover:border-emerald-300 transition text-left">
                        🌱 Petani (Supardi)
                    </button>
                    <button type="button" onclick="fillDemo('pengepul@sintesa.id', 'password')" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-white hover:bg-amber-50 text-amber-800 border border-slate-200 hover:border-amber-300 transition text-left">
                        🚛 Pengepul (H. Slamet)
                    </button>
                    <button type="button" onclick="fillDemo('konsumen@sintesa.id', 'password')" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-white hover:bg-blue-50 text-blue-800 border border-slate-200 hover:border-blue-300 transition text-left">
                        🛒 Konsumen (Ibu Sari)
                    </button>
                    <button type="button" onclick="fillDemo('admin@sintesa.id', 'password')" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-white hover:bg-purple-50 text-purple-800 border border-slate-200 hover:border-purple-300 transition text-left">
                        👑 Admin Sistem
                    </button>
                </div>
            </div>

            <!-- Login Form -->
            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="mail" class="w-5 h-5"></i>
                        </div>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                            class="block w-full pl-11 pr-4 py-3 rounded-xl border border-slate-300 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition @error('email') border-rose-500 bg-rose-50/20 @enderror"
                            placeholder="nama@sintesa.id">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="key-round" class="w-5 h-5"></i>
                        </div>
                        <input id="password" name="password" type="password" required
                            class="block w-full pl-11 pr-4 py-3 rounded-xl border border-slate-300 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition @error('password') border-rose-500 bg-rose-50/20 @enderror"
                            placeholder="••••••••">
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 rounded">
                        <label for="remember" class="ml-2 block text-xs text-slate-600 font-medium">Ingat saya</label>
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md shadow-emerald-600/20 hover:shadow-lg transition duration-200 flex items-center justify-center gap-2">
                    <span>Masuk ke Dashboard</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>

            <div class="mt-6 text-center text-xs text-slate-500">
                Belum memiliki akun?
                <a href="{{ route('register') }}" class="font-bold text-emerald-600 hover:text-emerald-700">Daftar sekarang</a>
            </div>
        </div>
    </div>
</div>

<script>
function fillDemo(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
}
</script>
@endsection
