<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Services\PriceAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PriceAnalyticsController extends Controller
{
    protected PriceAnalyticsService $priceService;

    public function __construct(PriceAnalyticsService $priceService)
    {
        $this->priceService = $priceService;
    }

    /**
     * Display commodity price analytics dashboard with Chart.js.
     */
    public function index(Request $request): View
    {
        $commodities = Commodity::where('is_active', true)->get();

        // Default to first commodity or request
        $commodityId = (int) ($request->query('commodity_id') ?: ($commodities->first()->id ?? 1));
        $days = (int) ($request->query('days', 30));
        $location = $request->query('location');

        $analytics = $this->priceService->getAnalytics($commodityId, $days, $location);
        $overviewCards = $this->priceService->getCommoditiesOverview();
        $availableLocations = $this->priceService->getAvailableLocations();

        return view('prices.index', compact(
            'commodities',
            'commodityId',
            'days',
            'location',
            'analytics',
            'overviewCards',
            'availableLocations'
        ));
    }

    /**
     * JSON endpoint for dynamic Chart.js reload.
     */
    public function chartData(Request $request): JsonResponse
    {
        $commodityId = (int) $request->query('commodity_id');
        $days = (int) ($request->query('days', 30));
        $location = $request->query('location');

        if (!$commodityId) {
            return response()->json(['error' => 'Commodity ID is required'], 400);
        }

        $analytics = $this->priceService->getAnalytics($commodityId, $days, $location);

        return response()->json([
            'success' => true,
            'labels' => $analytics['chart_labels'],
            'prices' => $analytics['chart_prices'],
            'latest_price' => $analytics['latest_price'],
            'highest_price' => $analytics['highest_price'],
            'lowest_price' => $analytics['lowest_price'],
            'average_price' => $analytics['average_price'],
            'change_nominal' => $analytics['change_nominal'],
            'change_percentage' => $analytics['change_percentage'],
            'trend' => $analytics['trend'],
            'commodity_name' => $analytics['commodity']->name,
            'unit' => $analytics['commodity']->unit,
        ]);
    }
}
