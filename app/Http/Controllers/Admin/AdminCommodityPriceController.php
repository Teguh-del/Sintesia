<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commodity;
use App\Models\CommodityPrice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCommodityPriceController extends Controller
{
    /**
     * Display list of commodity market prices.
     */
    public function index(Request $request): View
    {
        $commodities = Commodity::all();
        $selectedCommodity = $request->query('commodity_id');
        $selectedLocation = $request->query('location');

        $query = CommodityPrice::with('commodity')
            ->orderBy('recorded_date', 'desc')
            ->orderBy('id', 'desc');

        if ($selectedCommodity) {
            $query->where('commodity_id', $selectedCommodity);
        }

        if ($selectedLocation) {
            $query->where('location', 'like', "%{$selectedLocation}%");
        }

        $prices = $query->paginate(15)->withQueryString();

        return view('admin.prices.index', compact(
            'prices',
            'commodities',
            'selectedCommodity',
            'selectedLocation'
        ));
    }

    /**
     * Store a newly created commodity price in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'commodity_id' => 'required|exists:commodities,id',
            'price' => 'required|numeric|min:100',
            'unit' => 'required|string|max:50',
            'location' => 'required|string|max:150',
            'recorded_date' => 'required|date',
            'source' => 'nullable|string|max:150',
            'notes' => 'nullable|string|max:500',
        ]);

        CommodityPrice::create($validated);

        return redirect()->route('admin.prices.index')
            ->with('success', 'Data harga acuan komoditas pasar berhasil dicatat.');
    }

    /**
     * Update the specified commodity price in storage.
     */
    public function update(Request $request, CommodityPrice $price): RedirectResponse
    {
        $validated = $request->validate([
            'commodity_id' => 'required|exists:commodities,id',
            'price' => 'required|numeric|min:100',
            'unit' => 'required|string|max:50',
            'location' => 'required|string|max:150',
            'recorded_date' => 'required|date',
            'source' => 'nullable|string|max:150',
            'notes' => 'nullable|string|max:500',
        ]);

        $price->update($validated);

        return redirect()->route('admin.prices.index')
            ->with('success', 'Data harga pasar komoditas berhasil diperbarui.');
    }

    /**
     * Remove the specified commodity price from storage.
     */
    public function destroy(CommodityPrice $price): RedirectResponse
    {
        $price->delete();

        return redirect()->route('admin.prices.index')
            ->with('success', 'Catatan harga pasar komoditas berhasil dihapus.');
    }
}
