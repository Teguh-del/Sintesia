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

        // Indonesian Provinces for Agricultural Production Centers (All 38 Provinces)
        $locations = \App\Models\Province::orderBy('id')->pluck('name', 'name')->toArray();
        if (empty($locations)) {
            $locations = [
                'Aceh' => 'Aceh', 'Sumatera Utara' => 'Sumatera Utara', 'Sumatera Barat' => 'Sumatera Barat',
                'Riau' => 'Riau', 'Jambi' => 'Jambi', 'Sumatera Selatan' => 'Sumatera Selatan',
                'Bengkulu' => 'Bengkulu', 'Lampung' => 'Lampung', 'Kepulauan Bangka Belitung' => 'Kepulauan Bangka Belitung',
                'Kepulauan Riau' => 'Kepulauan Riau', 'DKI Jakarta' => 'DKI Jakarta', 'Jawa Barat' => 'Jawa Barat',
                'Jawa Tengah' => 'Jawa Tengah', 'DI Yogyakarta' => 'DI Yogyakarta', 'Jawa Timur' => 'Jawa Timur',
                'Banten' => 'Banten', 'Bali' => 'Bali', 'Nusa Tenggara Barat' => 'Nusa Tenggara Barat',
                'Nusa Tenggara Timur' => 'Nusa Tenggara Timur', 'Kalimantan Barat' => 'Kalimantan Barat',
                'Kalimantan Tengah' => 'Kalimantan Tengah', 'Kalimantan Selatan' => 'Kalimantan Selatan',
                'Kalimantan Timur' => 'Kalimantan Timur', 'Kalimantan Utara' => 'Kalimantan Utara',
                'Sulawesi Utara' => 'Sulawesi Utara', 'Sulawesi Tengah' => 'Sulawesi Tengah',
                'Sulawesi Selatan' => 'Sulawesi Selatan', 'Sulawesi Tenggara' => 'Sulawesi Tenggara',
                'Gorontalo' => 'Gorontalo', 'Sulawesi Barat' => 'Sulawesi Barat', 'Maluku' => 'Maluku',
                'Maluku Utara' => 'Maluku Utara', 'Papua Barat' => 'Papua Barat', 'Papua Barat Daya' => 'Papua Barat Daya',
                'Papua' => 'Papua', 'Papua Selatan' => 'Papua Selatan', 'Papua Pegunungan' => 'Papua Pegunungan',
                'Papua Tengah' => 'Papua Tengah'
            ];
        }

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
