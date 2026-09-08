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

        return view('consumer.dashboard', compact('user', 'commodities'));
    }
}
