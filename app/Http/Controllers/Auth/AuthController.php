<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\CollectorProfile;
use App\Models\ConsumerProfile;
use App\Models\FarmerProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect(Auth::user()->getDashboardRoute());
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi admin.',
                ]);
            }

            return redirect()->intended($user->getDashboardRoute())
                ->with('success', "Selamat datang kembali, {$user->name}!");
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    public function showRegisterForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect(Auth::user()->getDashboardRoute());
        }

        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'phone' => $validated['phone'] ?? null,
                'is_active' => true,
            ]);

            // Create corresponding role profile
            if ($user->role === 'petani') {
                FarmerProfile::create([
                    'user_id' => $user->id,
                    'farm_name' => $validated['farm_name'] ?? ('Kebun ' . $user->name),
                    'farm_area_hectares' => $validated['farm_area_hectares'] ?? 1.0,
                    'primary_commodity' => $validated['primary_commodity'] ?? 'Jagung',
                    'address' => $validated['address'] ?? 'Kabupaten Kediri, Jawa Timur',
                    'latitude' => -7.8228400,
                    'longitude' => 112.0118640,
                ]);
            } elseif ($user->role === 'pengepul') {
                CollectorProfile::create([
                    'user_id' => $user->id,
                    'business_name' => $validated['business_name'] ?? ('Usaha Dagang ' . $user->name),
                    'business_type' => $validated['business_type'] ?? 'Pengepul Lokal',
                    'address' => $validated['address'] ?? 'Kabupaten Kediri, Jawa Timur',
                    'latitude' => -7.8184500,
                    'longitude' => 112.0156000,
                ]);
            } elseif ($user->role === 'konsumen') {
                ConsumerProfile::create([
                    'user_id' => $user->id,
                    'address' => $validated['address'] ?? 'Kabupaten Kediri, Jawa Timur',
                    'latitude' => -7.8200000,
                    'longitude' => 112.0100000,
                ]);
            }

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect($user->getDashboardRoute())
            ->with('success', "Pendaftaran berhasil! Selamat datang di ekosistem SINTESA.");
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar.');
    }
}
