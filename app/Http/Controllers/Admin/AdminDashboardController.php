<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commodity;
use App\Models\CommodityPrice;
use App\Models\CommodityRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display comprehensive Admin command center overview with live MySQL statistics.
     */
    public function index(): View
    {
        $user = Auth::user();

        // 1. User Distribution
        $totalUsers = User::count();
        $totalFarmers = User::where('role', 'petani')->count();
        $totalCollectors = User::where('role', 'pengepul')->count();
        $totalConsumers = User::where('role', 'konsumen')->count();

        // 2. Transaction & GMV Metrics
        $totalGmv = (float) Order::where('status', '!=', 'Dibatalkan')->sum('total_amount');
        $totalOrders = Order::count();
        $completedOrders = Order::where('status', 'Selesai')->count();
        $pendingOrders = Order::where('status', 'Menunggu Konfirmasi')->count();

        // 3. Marketplace & Operations Metrics
        $totalProducts = Product::count();
        $activeProducts = Product::where('status', 'active')->count();
        $activeRequests = CommodityRequest::where('status', 'Aktif')->count();
        $totalPriceRecords = CommodityPrice::count();

        // 4. Commodities & Recent Platform Activity
        $commodities = Commodity::withCount('products')->get();
        $recentOrders = Order::with(['buyer', 'seller.farmerProfile', 'items'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'user',
            'totalUsers',
            'totalFarmers',
            'totalCollectors',
            'totalConsumers',
            'totalGmv',
            'totalOrders',
            'completedOrders',
            'pendingOrders',
            'totalProducts',
            'activeProducts',
            'activeRequests',
            'totalPriceRecords',
            'commodities',
            'recentOrders'
        ));
    }
}
