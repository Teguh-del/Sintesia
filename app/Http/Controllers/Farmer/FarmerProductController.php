<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Farmer\StoreProductRequest;
use App\Http\Requests\Farmer\UpdateProductRequest;
use App\Models\Commodity;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FarmerProductController extends Controller
{
    /**
     * Display a listing of products owned by the farmer.
     */
    public function index(Request $request): View
    {
        $farmer = Auth::user();

        $query = Product::with(['commodity', 'images'])
            ->where('user_id', $farmer->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('quality', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(10)->withQueryString();

        // Statistics
        $totalProducts = Product::where('user_id', $farmer->id)->count();
        $activeProducts = Product::where('user_id', $farmer->id)->where('status', 'active')->count();
        $totalStockKg = Product::where('user_id', $farmer->id)->sum('stock');
        $soldOutCount = Product::where('user_id', $farmer->id)->where('stock', '<=', 0)->count();

        return view('farmer.products.index', compact(
            'products',
            'totalProducts',
            'activeProducts',
            'totalStockKg',
            'soldOutCount'
        ));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(Request $request): View
    {
        $commodities = Commodity::where('is_active', true)->orderBy('name')->get();
        $farmerProfile = Auth::user()->farmerProfile;
        $selectedStock = null;
        if ($request->filled('stock_id')) {
            $selectedStock = Auth::user()->stocks()->find($request->stock_id);
        }

        return view('farmer.products.create', compact('commodities', 'farmerProfile', 'selectedStock'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $product = Product::create([
                'user_id' => Auth::id(),
                'commodity_id' => $validated['commodity_id'],
                'stock_id' => $validated['stock_id'] ?? null,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'unit' => $validated['unit'],
                'min_order' => $validated['min_order'],
                'quality' => $validated['quality'],
                'harvest_date' => $validated['harvest_date'] ?? null,
                'location' => $validated['location'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'status' => $validated['status'],
                'allow_negotiation' => $request->boolean('allow_negotiation', true),
            ]);

            // Handle image uploads
            if ($request->hasFile('images')) {
                $isFirst = true;
                foreach ($request->file('images') as $index => $file) {
                    $path = $file->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => $isFirst,
                        'sort_order' => $index,
                    ]);
                    $isFirst = false;
                }
            }

            DB::commit();

            return redirect()->route('farmer.products.index')
                ->with('success', "Produk '{$product->name}' berhasil ditambahkan ke marketplace!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        // Authorization check
        if ($product->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit produk ini.');
        }

        $commodities = Commodity::where('is_active', true)->orderBy('name')->get();
        $product->load(['images', 'commodity']);

        return view('farmer.products.edit', compact('product', 'commodities'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        // Authorization check
        if ($product->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah produk ini.');
        }

        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $product->update([
                'commodity_id' => $validated['commodity_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'unit' => $validated['unit'],
                'min_order' => $validated['min_order'],
                'quality' => $validated['quality'],
                'harvest_date' => $validated['harvest_date'] ?? null,
                'location' => $validated['location'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'status' => $validated['status'],
                'allow_negotiation' => $request->boolean('allow_negotiation', true),
            ]);

            // Handle deleted existing images
            if (!empty($validated['delete_images'])) {
                $imagesToDelete = ProductImage::where('product_id', $product->id)
                    ->whereIn('id', $validated['delete_images'])
                    ->get();

                foreach ($imagesToDelete as $img) {
                    if (!Str::startsWith($img->image_path, ['http://', 'https://'])) {
                        Storage::disk('public')->delete($img->image_path);
                    }
                    $img->delete();
                }
            }

            // Handle new uploaded images
            if ($request->hasFile('images')) {
                $existingCount = $product->images()->count();
                $hasPrimary = $product->images()->where('is_primary', true)->exists();

                foreach ($request->file('images') as $index => $file) {
                    $path = $file->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => !$hasPrimary && $index === 0,
                        'sort_order' => $existingCount + $index,
                    ]);
                }
            }

            // Ensure there is at least one primary image if any images exist
            $remainingImages = $product->images()->get();
            if ($remainingImages->isNotEmpty() && !$remainingImages->contains('is_primary', true)) {
                $remainingImages->first()->update(['is_primary' => true]);
            }

            DB::commit();

            return redirect()->route('farmer.products.index')
                ->with('success', "Produk '{$product->name}' berhasil diperbarui!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    /**
     * Toggle product active/inactive status quickly.
     */
    public function toggleStatus(Product $product): RedirectResponse
    {
        if ($product->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $newStatus = $product->status === 'active' ? 'inactive' : 'active';
        $product->update(['status' => $newStatus]);

        $statusText = $newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Status produk '{$product->name}' berhasil {$statusText}.");
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $name = $product->name;

        DB::beginTransaction();
        try {
            // Delete associated physical image files
            foreach ($product->images as $img) {
                if (!Str::startsWith($img->image_path, ['http://', 'https://'])) {
                    Storage::disk('public')->delete($img->image_path);
                }
            }

            $product->delete();
            DB::commit();

            return redirect()->route('farmer.products.index')
                ->with('success', "Produk '{$name}' berhasil dihapus.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }
}
