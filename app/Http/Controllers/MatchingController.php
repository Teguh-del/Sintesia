<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Services\MatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MatchingController extends Controller
{
    protected MatchingService $matchingService;

    public function __construct(MatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    /**
     * SINTESA Match: Interactive agricultural smart matching engine.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $commodities = Commodity::where('is_active', true)->orderBy('name')->get();

        // Default inputs from user profile or request
        $defaultAddress = $user->consumerProfile->address ?? ($user->collectorProfile->address ?? 'Sleman, DI Yogyakarta');
        $defaultLat = $user->consumerProfile->latitude ?? ($user->collectorProfile->latitude ?? -7.7167);
        $defaultLon = $user->consumerProfile->longitude ?? ($user->collectorProfile->longitude ?? 110.3556);

        $hasSearch = $request->has('commodity_id') || $request->has('quantity') || $request->has('max_price');

        $criteria = [
            'commodity_id' => $request->query('commodity_id', $commodities->first()?->id),
            'quantity' => (float) $request->query('quantity', 100),
            'max_price' => (float) $request->query('max_price', 35000),
            'location_name' => $request->query('location_name', $defaultAddress),
            'latitude' => (float) $request->query('latitude', $defaultLat),
            'longitude' => (float) $request->query('longitude', $defaultLon),
        ];

        // Execute matching algorithm
        $matches = $this->matchingService->findMatches($criteria, $user);

        // Calculate summary stats
        $summary = [
            'total_candidates' => $matches->count(),
            'best_score' => $matches->max('match_score') ?? 0,
            'avg_score' => $matches->isNotEmpty() ? round($matches->avg('match_score'), 1) : 0,
            'top_recommendation' => $matches->first(),
        ];

        return view('matching.index', compact('commodities', 'criteria', 'matches', 'summary', 'hasSearch'));
    }
}
