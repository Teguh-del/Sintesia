<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Models\Commodity;
use App\Models\Stock;
use App\Services\HarvestStockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FarmerStockController extends Controller
{
    protected HarvestStockService $service;

    public function __construct(HarvestStockService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of real stocks.
     */
    public function index(Request $request): View
    {
        $farmer = Auth::user();

        $filters = $request->only([
            'commodity_id',
            'status',
            'search',
        ]);

        $query = Stock::with(['commodity', 'harvest', 'products'])
            ->where('user_id', $farmer->id)
            ->filter($filters);

        $stocks = $query->latest()->paginate(10)->withQueryString();

        // Statistics
        $totalAvailable = Stock::where('user_id', $farmer->id)->sum('available_quantity');
        $totalOrdered = Stock::where('user_id', $farmer->id)->sum('ordered_quantity');
        $totalSold = Stock::where('user_id', $farmer->id)->sum('sold_quantity');
        $lowStockCount = Stock::where('user_id', $farmer->id)
            ->whereIn('status', ['Stok Terbatas', 'Habis'])
            ->count();

        $commodities = Commodity::where('is_active', true)->orderBy('name')->get();
        $statusOptions = ['Tersedia', 'Stok Terbatas', 'Habis'];

        return view('farmer.stocks.index', compact(
            'stocks',
            'totalAvailable',
            'totalOrdered',
            'totalSold',
            'lowStockCount',
            'commodities',
            'statusOptions',
            'filters'
        ));
    }

    /**
     * Display the specified stock batch and linked products.
     */
    public function show(Stock $stock): View
    {
        if ($stock->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke data stok ini.');
        }

        $stock->load(['commodity', 'harvest', 'products.images']);

        return view('farmer.stocks.show', compact('stock'));
    }

    /**
     * Adjust physical stock quantity safely (e.g. inventory audit / stock take).
     */
    public function adjust(Request $request, Stock $stock): RedirectResponse
    {
        if ($stock->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'available_quantity' => ['required', 'numeric', 'min:0'],
            'reason' => ['nullable', 'string', 'max:255'],
        ], [
            'available_quantity.required' => 'Jumlah stok baru wajib diisi.',
            'available_quantity.min' => 'Stok tidak boleh bernilai negatif.',
        ]);

        try {
            $this->service->adjustStockQuantity(
                $stock,
                (float) $validated['available_quantity'],
                $validated['reason'] ?? 'Penyesuaian stok manual'
            );

            return back()->with('success', "Kuantitas stok batch '{$stock->batch_code}' berhasil disesuaikan.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyesuaikan stok: ' . $e->getMessage());
        }
    }
}
