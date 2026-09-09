<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commodity;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminProductController extends Controller
{
    /**
     * Display all marketplace products across all farmers for moderation.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $commodityId = $request->query('commodity_id');
        $search = $request->query('search');

        $query = Product::with(['user.farmerProfile', 'commodity', 'primaryImage'])
            ->orderBy('created_at', 'desc');

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($commodityId && $commodityId !== 'all') {
            $query->where('commodity_id', $commodityId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $products = $query->paginate(15)->withQueryString();
        $commodities = Commodity::all();

        $counts = [
            'all' => Product::count(),
            'active' => Product::where('status', 'active')->count(),
            'inactive' => Product::where('status', 'inactive')->count(),
            'sold_out' => Product::where('status', 'sold_out')->count(),
        ];

        return view('admin.products.index', compact('products', 'commodities', 'status', 'commodityId', 'search', 'counts'));
    }

    /**
     * Toggle product status (moderate active <-> inactive).
     */
    public function toggleStatus(Product $product): RedirectResponse
    {
        if ($product->status === 'active') {
            $product->status = 'inactive';
        } else {
            $product->status = 'active';
        }

        $product->save();

        $statusLabel = $product->status === 'active' ? 'diaktifkan (Lolos Moderasi)' : 'dinonaktifkan (Ditangguhkan)';

        return back()->with('success', "Status produk '{$product->name}' berhasil {$statusLabel}.");
    }
}
