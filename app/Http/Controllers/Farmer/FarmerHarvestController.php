<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Farmer\StoreHarvestRequest;
use App\Http\Requests\Farmer\UpdateHarvestRequest;
use App\Models\Commodity;
use App\Models\Harvest;
use App\Services\HarvestStockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FarmerHarvestController extends Controller
{
    protected HarvestStockService $service;

    public function __construct(HarvestStockService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of harvests.
     */
    public function index(Request $request): View
    {
        $farmer = Auth::user();

        $filters = $request->only([
            'commodity_id',
            'quality',
            'date_from',
            'date_to',
            'search',
        ]);

        $query = Harvest::with(['commodity', 'stock'])
            ->where('user_id', $farmer->id)
            ->filter($filters);

        $harvests = $query->orderBy('harvest_date', 'desc')->paginate(10)->withQueryString();

        // Statistics
        $totalHarvestsCount = Harvest::where('user_id', $farmer->id)->count();
        $totalHarvestQty = Harvest::where('user_id', $farmer->id)->sum('quantity');
        $thisMonthQty = Harvest::where('user_id', $farmer->id)
            ->whereYear('harvest_date', now()->year)
            ->whereMonth('harvest_date', now()->month)
            ->sum('quantity');
        $distinctCommoditiesCount = Harvest::where('user_id', $farmer->id)
            ->distinct('commodity_id')
            ->count('commodity_id');

        $commodities = Commodity::where('is_active', true)->orderBy('name')->get();
        $qualityOptions = ['Grade A (Super)', 'Grade B (Standar)', 'Organik Premium', 'Campuran / Curah'];

        return view('farmer.harvests.index', compact(
            'harvests',
            'totalHarvestsCount',
            'totalHarvestQty',
            'thisMonthQty',
            'distinctCommoditiesCount',
            'commodities',
            'qualityOptions',
            'filters'
        ));
    }

    /**
     * Show the form for creating a new harvest record.
     */
    public function create(): View
    {
        $commodities = Commodity::where('is_active', true)->orderBy('name')->get();
        $farmerProfile = Auth::user()->farmerProfile;

        return view('farmer.harvests.create', compact('commodities', 'farmerProfile'));
    }

    /**
     * Store a newly created harvest record in storage.
     */
    public function store(StoreHarvestRequest $request): RedirectResponse
    {
        try {
            $harvest = $this->service->recordHarvest(Auth::user(), $request->validated());

            return redirect()->route('farmer.harvests.index')
                ->with('success', "Hasil panen '{$harvest->commodity->name}' ({$harvest->formatted_quantity}) berhasil dicatat dan otomatis masuk ke inventaris stok riil!");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal mencatat panen: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified harvest record.
     */
    public function edit(Harvest $harvest): View
    {
        if ($harvest->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki izin mengedit data panen ini.');
        }

        $commodities = Commodity::where('is_active', true)->orderBy('name')->get();
        $farmerProfile = Auth::user()->farmerProfile;

        return view('farmer.harvests.edit', compact('harvest', 'commodities', 'farmerProfile'));
    }

    /**
     * Update the specified harvest record in storage.
     */
    public function update(UpdateHarvestRequest $request, Harvest $harvest): RedirectResponse
    {
        if ($harvest->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        try {
            $this->service->updateHarvest($harvest, $request->validated());

            return redirect()->route('farmer.harvests.index')
                ->with('success', "Data hasil panen berhasil diperbarui dan disinkronkan ke stok terkait.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui hasil panen: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified harvest record from storage.
     */
    public function destroy(Harvest $harvest): RedirectResponse
    {
        if ($harvest->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        try {
            $this->service->deleteHarvest($harvest);

            return redirect()->route('farmer.harvests.index')
                ->with('success', 'Data hasil panen dan batch stok terkait berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus hasil panen: ' . $e->getMessage());
        }
    }
}
