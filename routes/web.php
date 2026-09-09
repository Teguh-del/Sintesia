<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Collector\CollectorDashboardController;
use App\Http\Controllers\Consumer\ConsumerDashboardController;
use App\Http\Controllers\Farmer\FarmerDashboardController;
use App\Http\Controllers\Farmer\FarmerHarvestController;
use App\Http\Controllers\Farmer\FarmerProductController;
use App\Http\Controllers\Farmer\FarmerStockController;
use App\Http\Controllers\MarketplaceController;
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

// Public Marketplace Routes
Route::prefix('marketplace')->name('marketplace.')->group(function () {
    Route::get('/', [MarketplaceController::class, 'index'])->name('index');
    Route::get('/{slug}', [MarketplaceController::class, 'show'])->name('show');
});

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
    
    // Farmer Harvest Management (Phase 3)
    Route::resource('harvests', FarmerHarvestController::class)->except(['show']);

    // Farmer Stock Management (Phase 3)
    Route::get('stocks', [FarmerStockController::class, 'index'])->name('stocks.index');
    Route::get('stocks/{stock}', [FarmerStockController::class, 'show'])->name('stocks.show');
    Route::patch('stocks/{stock}/adjust', [FarmerStockController::class, 'adjust'])->name('stocks.adjust');

    // Farmer Product Management (Phase 2)
    Route::resource('products', FarmerProductController::class)->except(['show']);
    Route::patch('products/{product}/toggle', [FarmerProductController::class, 'toggleStatus'])->name('products.toggle');

    // Farmer Orders Management (Phase 4)
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Farmer\FarmerOrderController::class, 'index'])->name('index');
        Route::get('/{order}', [\App\Http\Controllers\Farmer\FarmerOrderController::class, 'show'])->name('show');
        Route::post('/{order}/confirm', [\App\Http\Controllers\Farmer\FarmerOrderController::class, 'confirm'])->name('confirm');
        Route::post('/{order}/process', [\App\Http\Controllers\Farmer\FarmerOrderController::class, 'process'])->name('process');
        Route::post('/{order}/complete', [\App\Http\Controllers\Farmer\FarmerOrderController::class, 'complete'])->name('complete');
        Route::post('/{order}/reject', [\App\Http\Controllers\Farmer\FarmerOrderController::class, 'reject'])->name('reject');
    });
});

// Authenticated Order & Notification Routes (Phase 4 - Buyers & Global)
Route::middleware('auth')->group(function () {
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [\App\Http\Controllers\OrderController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\OrderController::class, 'store'])->name('store');
        Route::get('/{order}', [\App\Http\Controllers\OrderController::class, 'show'])->name('show');
        Route::post('/{order}/cancel', [\App\Http\Controllers\OrderController::class, 'cancel'])->name('cancel');
        Route::post('/{order}/receive', [\App\Http\Controllers\OrderController::class, 'receive'])->name('receive');
    });

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('readAll');
    });
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
