<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Models\Commodity;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FarmerDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user()->load('farmerProfile');
        $commodities = Commodity::where('is_active', true)->get();

        $totalHarvestsCount = $user->harvests()->count();
        $totalHarvestQty = $user->harvests()->sum('quantity');
        $realAvailableStock = $user->stocks()->sum('available_quantity');
        $recentHarvests = $user->harvests()->with('commodity')->latest('harvest_date')->take(3)->get();

        $totalProducts = $user->products()->count();
        $activeProducts = $user->products()->where('status', 'active')->count();
        $totalStock = $user->products()->sum('stock');
        $recentProducts = $user->products()->with(['commodity', 'images'])->latest()->take(4)->get();

        $pendingOrdersCount = $user->sellerOrders()->where('status', 'Menunggu Konfirmasi')->count();
        $totalOrdersCount = $user->sellerOrders()->count();
        $recentOrders = $user->sellerOrders()->with(['buyer', 'items'])->latest()->take(4)->get();

        return view('farmer.dashboard', compact(
            'user',
            'commodities',
            'totalHarvestsCount',
            'totalHarvestQty',
            'realAvailableStock',
            'recentHarvests',
            'totalProducts',
            'activeProducts',
            'totalStock',
            'recentProducts',
            'pendingOrdersCount',
            'totalOrdersCount',
            'recentOrders'
        ));
    }
}
