<?php

namespace App\Http\Controllers\Collector;

use App\Http\Controllers\Controller;
use App\Models\Commodity;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CollectorDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user()->load('collectorProfile');
        $commodities = Commodity::where('is_active', true)->get();

        return view('collector.dashboard', compact('user', 'commodities'));
    }
}
