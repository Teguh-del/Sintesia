<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Services\MapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MapController extends Controller
{
    protected MapService $mapService;

    public function __construct(MapService $mapService)
    {
        $this->mapService = $mapService;
    }

    /**
     * Display interactive Leaflet.js map of farmers and agricultural commodities.
     */
    public function index(Request $request)
    {
        if (auth()->check() && auth()->user()->role === 'petani') {
            return redirect()->route('farmer.dashboard')->with('error', 'Halaman peta sebaran petani hanya diperuntukkan bagi mitra Pengepul dan Konsumen.');
        }

        $filters = [
            'commodity_id' => $request->query('commodity_id'),
            'location' => $request->query('location'),
        ];

        $commodities = Commodity::where('is_active', true)->get();
        $markers = $this->mapService->getFarmerMarkers($filters);
        $summary = $this->mapService->getMapSummary($markers);

        // Pre-package locations list
        $locations = [
            'Sleman' => 'Sleman, DI Yogyakarta',
            'Bantul' => 'Bantul, DI Yogyakarta',
            'Kulon Progo' => 'Kulon Progo, DI Yogyakarta',
            'Kediri' => 'Kediri, Jawa Timur',
            'Malang' => 'Malang & Batu, Jawa Timur',
            'Magelang' => 'Magelang, Jawa Tengah',
        ];

        return view('maps.index', compact(
            'commodities',
            'markers',
            'summary',
            'filters',
            'locations'
        ));
    }

    /**
     * JSON endpoint for AJAX marker dynamic reloading.
     */
    public function getFarmerMarkers(Request $request): JsonResponse
    {
        $filters = [
            'commodity_id' => $request->query('commodity_id'),
            'location' => $request->query('location'),
        ];

        $markers = $this->mapService->getFarmerMarkers($filters);
        $summary = $this->mapService->getMapSummary($markers);

        return response()->json([
            'success' => true,
            'count' => $markers->count(),
            'summary' => $summary,
            'markers' => $markers,
        ]);
    }
}
