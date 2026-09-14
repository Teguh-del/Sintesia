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
    if (auth()->check()) {
        return redirect(auth()->user()->getDashboardRoute());
    }
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

// Authenticated Logout & Universal Dashboard Redirect
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/dashboard', function () {
    $user = auth()->user();
    return match ($user->role ?? null) {
        'petani' => redirect()->route('farmer.dashboard'),
        'pengepul' => redirect()->route('collector.dashboard'),
        'konsumen' => redirect()->route('consumer.dashboard'),
        'admin' => redirect()->route('admin.dashboard'),
        default => redirect()->route('home'),
    };
})->middleware('auth')->name('dashboard');

// Role-Protected Dashboards
// 1. Petani
Route::middleware(['auth', 'role:petani'])->prefix('farmer')->name('farmer.')->group(function () {
    Route::get('/dashboard', [FarmerDashboardController::class, 'index'])->name('dashboard');
    
    // Farmer Harvest Management (Phase 3)
    Route::resource('harvests', FarmerHarvestController::class)->except(['show']);

    // Farmer Stock Management (Phase 3)
    Route::get('stocks', [FarmerStockController::class, 'index'])->name('stocks.index');
    Route::get('stocks/create', fn() => redirect()->route('farmer.harvests.create'))->name('stocks.create');
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

    // Farmer Negotiations Management (Phase 5)
    Route::prefix('negotiations')->name('negotiations.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Farmer\FarmerNegotiationController::class, 'index'])->name('index');
        Route::post('/{offer}/accept', [\App\Http\Controllers\Farmer\FarmerNegotiationController::class, 'accept'])->name('accept');
        Route::post('/{offer}/counter', [\App\Http\Controllers\Farmer\FarmerNegotiationController::class, 'counter'])->name('counter');
        Route::post('/{offer}/reject', [\App\Http\Controllers\Farmer\FarmerNegotiationController::class, 'reject'])->name('reject');
    });

    // Farmer Commodity Requests Market & Offers (Phase 5)
    Route::prefix('requests')->name('requests.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Farmer\FarmerRequestController::class, 'index'])->name('index');
        Route::get('/{commodityRequest}', [\App\Http\Controllers\Farmer\FarmerRequestController::class, 'show'])->name('show');
        Route::post('/{commodityRequest}/offer', [\App\Http\Controllers\Farmer\FarmerRequestController::class, 'submitOffer'])->name('offer');
    });
});

// Authenticated User Routes (Phase 4 & Phase 5 - Buyers & General)
Route::middleware('auth')->group(function () {
    // Orders (Phase 4)
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [\App\Http\Controllers\OrderController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\OrderController::class, 'store'])->name('store');
        Route::get('/{order}', [\App\Http\Controllers\OrderController::class, 'show'])->name('show');
        Route::post('/{order}/cancel', [\App\Http\Controllers\OrderController::class, 'cancel'])->name('cancel');
        Route::post('/{order}/receive', [\App\Http\Controllers\OrderController::class, 'receive'])->name('receive');
    });

    // Notifications (Phase 4)
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('readAll');
    });

    // Buyer Price Offers / Negotiations (Phase 5)
    Route::prefix('negotiations')->name('negotiations.')->group(function () {
        Route::get('/', [\App\Http\Controllers\NegotiationController::class, 'index'])->name('index');
    });
    Route::prefix('offers')->name('offers.')->group(function () {
        Route::post('/', [\App\Http\Controllers\NegotiationController::class, 'store'])->name('store');
        Route::post('/{offer}/accept-counter', [\App\Http\Controllers\NegotiationController::class, 'acceptCounter'])->name('acceptCounter');
        Route::post('/{offer}/reject', [\App\Http\Controllers\NegotiationController::class, 'reject'])->name('reject');
    });

    // Commodity Requests for Buyers (Phase 5)
    Route::prefix('requests')->name('requests.')->group(function () {
        Route::get('/', [\App\Http\Controllers\CommodityRequestController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\CommodityRequestController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\CommodityRequestController::class, 'store'])->name('store');
        Route::get('/{commodityRequest}', [\App\Http\Controllers\CommodityRequestController::class, 'show'])->name('show');
        Route::post('/offers/{offer}/accept', [\App\Http\Controllers\CommodityRequestController::class, 'acceptOffer'])->name('acceptOffer');
        Route::post('/offers/{offer}/reject', [\App\Http\Controllers\CommodityRequestController::class, 'rejectOffer'])->name('rejectOffer');
        Route::post('/{commodityRequest}/close', [\App\Http\Controllers\CommodityRequestController::class, 'close'])->name('close');
    });

    // SINTESA Match (Phase 6)
    Route::get('/matching', [\App\Http\Controllers\MatchingController::class, 'index'])->name('matching.index');
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
    
    // User Management (Phase 8)
    Route::get('/users', [\App\Http\Controllers\Admin\AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [\App\Http\Controllers\Admin\AdminUserController::class, 'show'])->name('users.show');
    Route::match(['post', 'patch'], '/users/{user}/toggle-status', [\App\Http\Controllers\Admin\AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Master Commodity Management (Phase 8)
    Route::get('/commodities', [\App\Http\Controllers\Admin\AdminCommodityController::class, 'index'])->name('commodities.index');
    Route::post('/commodities', [\App\Http\Controllers\Admin\AdminCommodityController::class, 'store'])->name('commodities.store');
    Route::put('/commodities/{commodity}', [\App\Http\Controllers\Admin\AdminCommodityController::class, 'update'])->name('commodities.update');
    Route::match(['post', 'patch'], '/commodities/{commodity}/toggle-status', [\App\Http\Controllers\Admin\AdminCommodityController::class, 'toggleStatus'])->name('commodities.toggle-status');

    // Marketplace Product Moderation (Phase 8)
    Route::get('/products', [\App\Http\Controllers\Admin\AdminProductController::class, 'index'])->name('products.index');
    Route::match(['post', 'patch'], '/products/{product}/toggle-status', [\App\Http\Controllers\Admin\AdminProductController::class, 'toggleStatus'])->name('products.toggle-status');

    // Transaction & Order Monitoring (Phase 8)
    Route::get('/transactions', [\App\Http\Controllers\Admin\AdminTransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{order}', [\App\Http\Controllers\Admin\AdminTransactionController::class, 'show'])->name('transactions.show');

    // Commodity Prices Management (Phase 7)
    Route::resource('prices', \App\Http\Controllers\Admin\AdminCommodityPriceController::class);
});

// Phase 7: Interactive Agricultural Maps & Market Price Analytics
Route::middleware('auth')->group(function () {
    Route::get('/maps', [\App\Http\Controllers\MapController::class, 'index'])->name('maps.index');
    Route::get('/api/maps/markers', [\App\Http\Controllers\MapController::class, 'getFarmerMarkers'])->name('maps.markers');

    Route::get('/prices', [\App\Http\Controllers\PriceAnalyticsController::class, 'index'])->name('prices.index');
    Route::get('/api/prices/chart-data', [\App\Http\Controllers\PriceAnalyticsController::class, 'chartData'])->name('prices.chart');
});

// Indonesian Administrative Region API Endpoints (38 Provinces, Regencies, Districts, Villages)
Route::get('/provinces', [\App\Http\Controllers\RegionController::class, 'getProvinces'])->name('regions.provinces');
Route::get('/provinces/{province}/regencies', [\App\Http\Controllers\RegionController::class, 'getRegencies'])->name('regions.regencies');
Route::get('/regencies/{regency}/districts', [\App\Http\Controllers\RegionController::class, 'getDistricts'])->name('regions.districts');
Route::get('/districts/{district}/villages', [\App\Http\Controllers\RegionController::class, 'getVillages'])->name('regions.villages');

