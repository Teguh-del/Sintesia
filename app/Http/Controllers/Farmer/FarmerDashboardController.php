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

        return view('farmer.dashboard', compact('user', 'commodities'));
    }
}
