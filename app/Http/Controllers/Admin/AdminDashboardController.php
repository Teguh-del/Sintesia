<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commodity;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $totalUsers = User::count();
        $totalFarmers = User::where('role', 'petani')->count();
        $totalCollectors = User::where('role', 'pengepul')->count();
        $totalConsumers = User::where('role', 'konsumen')->count();
        $commodities = Commodity::all();

        return view('admin.dashboard', compact(
            'user',
            'totalUsers',
            'totalFarmers',
            'totalCollectors',
            'totalConsumers',
            'commodities'
        ));
    }
}
