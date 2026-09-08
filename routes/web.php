<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Collector\CollectorDashboardController;
use App\Http\Controllers\Consumer\ConsumerDashboardController;
use App\Http\Controllers\Farmer\FarmerDashboardController;
use App\Models\Commodity;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SINTESA
|--------------------------------------------------------------------------
*/

// Landing Page
Route::get('/', function () {
    $commodities = Commodity::where('is_active', true)->get();
    return view('welcome', compact('commodities'));
})->name('home');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Logout
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Role-Protected Dashboards
// 1. Petani
Route::middleware(['auth', 'role:petani'])->prefix('farmer')->name('farmer.')->group(function () {
    Route::get('/dashboard', [FarmerDashboardController::class, 'index'])->name('dashboard');
});

// 2. Pengepul
Route::middleware(['auth', 'role:pengepul'])->prefix('collector')->name('collector.')->group(function () {
    Route::get('/dashboard', [CollectorDashboardController::class, 'index'])->name('dashboard');
});

// 3. Konsumen
Route::middleware(['auth', 'role:konsumen'])->prefix('consumer')->name('consumer.')->group(function () {
    Route::get('/dashboard', [ConsumerDashboardController::class, 'index'])->name('dashboard');
});

// 4. Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
});
