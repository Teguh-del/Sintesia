<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    /**
     * Display a listing of the marketplace products.
     */
    public function index(Request $request): View
    {
        $filters = $request->only([
            'search',
            'commodity',
            'location',
            'min_price',
            'max_price',
            'quality',
            'stock_status',
            'sort',
        ]);

        $query = Product::with(['commodity', 'user.farmerProfile', 'images'])
            ->where('status', 'active')
            ->filter($filters);

        $products = $query->paginate(12)->withQueryString();

        $commodities = Commodity::where('is_active', true)
            ->withCount(['products' => function ($q) {
                $q->where('status', 'active')->where('stock', '>', 0);
            }])
            ->get();

        // Extract clean unique city/regency names for location filter suggestions
        $locations = Product::where('status', 'active')
            ->whereNotNull('location')
            ->distinct()
            ->pluck('location');

        // Quality options
        $qualityOptions = ['Grade A (Super)', 'Grade B (Standar)', 'Organik Premium', 'Standar Pasar'];

        return view('marketplace.index', compact(
            'products',
            'commodities',
            'locations',
            'filters',
            'qualityOptions'
        ));
    }

    /**
     * Display the specified marketplace product.
     */
    public function show(string $slug): View
    {
        $product = Product::with(['commodity', 'user.farmerProfile', 'images'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Related products in the same commodity
        $relatedProducts = Product::with(['commodity', 'user.farmerProfile', 'images'])
            ->where('status', 'active')
            ->where('commodity_id', $product->commodity_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('marketplace.show', compact('product', 'relatedProducts'));
    }
}
