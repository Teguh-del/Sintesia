<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use App\Models\Commodity;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ConsumerDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user()->load('consumerProfile');
        $commodities = Commodity::where('is_active', true)->get();

        $activeOrdersCount = $user->buyerOrders()->whereIn('status', ['Menunggu Konfirmasi', 'Dikonfirmasi', 'Diproses'])->count();
        $totalOrdersCount = $user->buyerOrders()->count();
        $totalSpending = $user->buyerOrders()->where('status', 'Selesai')->sum('total_amount');
        $recentOrders = $user->buyerOrders()->with(['seller.farmerProfile', 'items'])->latest()->take(5)->get();

        $topMatches = app(\App\Services\MatchingService::class)->findMatches([
            'quantity' => 25,
            'max_price' => 40000,
        ], $user)->take(3);

        return view('consumer.dashboard', compact(
            'user',
            'commodities',
            'activeOrdersCount',
            'totalOrdersCount',
            'totalSpending',
            'recentOrders',
            'topMatches'
        ));
    }
}
