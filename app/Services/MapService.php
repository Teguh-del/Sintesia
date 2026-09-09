<?php

namespace App\Services;

use App\Models\Commodity;
use App\Models\FarmerProfile;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;

class MapService
{
    /**
     * Regional coordinate fallbacks if GPS is null in database.
     */
    protected array $fallbackCoordinates = [
        'sleman' => ['lat' => -7.7123, 'lng' => 110.3854],
        'bantul' => ['lat' => -7.8921, 'lng' => 110.3412],
        'kulon progo' => ['lat' => -7.8601, 'lng' => 110.1583],
        'wates' => ['lat' => -7.8601, 'lng' => 110.1583],
        'gunungkidul' => ['lat' => -7.9654, 'lng' => 110.6012],
        'yogyakarta' => ['lat' => -7.7956, 'lng' => 110.3695],
        'kediri' => ['lat' => -7.81845, 'lng' => 112.0156],
        'pare' => ['lat' => -7.7651, 'lng' => 112.1983],
        'malang' => ['lat' => -7.9797, 'lng' => 112.6304],
        'batu' => ['lat' => -7.8712, 'lng' => 112.5273],
        'magelang' => ['lat' => -7.5432, 'lng' => 110.3214],
        'banyuwangi' => ['lat' => -8.2192, 'lng' => 114.3692],
    ];

    /**
     * Retrieve farmer and product markers from database with optional filters.
     *
     * @param array $filters ['commodity_id' => int|null, 'location' => string|null]
     * @return Collection
     */
    public function getFarmerMarkers(array $filters = []): Collection
    {
        $commodityId = !empty($filters['commodity_id']) ? (int) $filters['commodity_id'] : null;
        $location = !empty($filters['location']) ? trim($filters['location']) : null;

        // Query active farmers who have products or profiles
        $farmers = User::where('role', 'petani')
            ->where('is_active', true)
            ->with(['farmerProfile', 'products' => function ($pq) use ($commodityId) {
                $pq->where('status', 'active')
                   ->where('stock', '>', 0)
                   ->when($commodityId, fn($q) => $q->where('commodity_id', $commodityId))
                   ->with(['commodity', 'primaryImage']);
            }])
            ->get();

        $markers = collect();

        foreach ($farmers as $farmer) {
            $profile = $farmer->farmerProfile;
            $activeProducts = $farmer->products;

            // If filtering by commodity and farmer has no matching active products, skip
            if ($commodityId && $activeProducts->isEmpty()) {
                continue;
            }

            // If filtering by location, match in address or farm name
            if ($location && $location !== 'all') {
                $searchHaystack = strtolower(($profile->address ?? '') . ' ' . ($profile->farm_name ?? ''));
                if (!str_contains($searchHaystack, strtolower($location))) {
                    continue;
                }
            }

            // Resolve coordinates
            $coords = $this->resolveFarmerCoordinates($profile, $activeProducts->first());

            // Build products summary
            $productsList = $activeProducts->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'commodity' => $p->commodity->name ?? 'Komoditas',
                    'price' => (float) $p->price,
                    'formatted_price' => 'Rp ' . number_format($p->price, 0, ',', '.'),
                    'stock' => (float) $p->stock,
                    'unit' => $p->unit,
                    'image' => $p->primaryImage->image_path ?? 'https://images.unsplash.com/photo-1598170845058-32b9d6a5da37?w=400',
                    'allow_negotiation' => (bool) $p->allow_negotiation,
                    'detail_url' => route('marketplace.show', $p->slug ?: $p->id),
                ];
            });

            $totalStock = $activeProducts->sum('stock');
            $minPrice = $activeProducts->min('price');
            $maxPrice = $activeProducts->max('price');

            $markers->push([
                'id' => $farmer->id,
                'name' => $farmer->name,
                'phone' => $farmer->phone,
                'farm_name' => $profile->farm_name ?? 'Lahan Petani',
                'primary_commodity' => $profile->primary_commodity ?? ($activeProducts->first()->commodity->name ?? 'Komoditas'),
                'farm_area' => $profile->farm_area_hectares ?? 1.0,
                'address' => $profile->address ?? 'Lokasi Terdaftar',
                'latitude' => $coords['lat'],
                'longitude' => $coords['lng'],
                'has_active_products' => $activeProducts->isNotEmpty(),
                'products_count' => $activeProducts->count(),
                'products' => $productsList->values()->all(),
                'total_stock' => $totalStock,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'formatted_price_range' => $minPrice ? ('Rp ' . number_format($minPrice, 0, ',', '.') . ($minPrice != $maxPrice ? ' - Rp ' . number_format($maxPrice, 0, ',', '.') : '')) : '-',
            ]);
        }

        return $markers;
    }

    /**
     * Compute map summary statistics.
     */
    public function getMapSummary(Collection $markers): array
    {
        $uniqueCommodities = [];
        $totalStock = 0;

        foreach ($markers as $m) {
            $totalStock += $m['total_stock'];
            foreach ($m['products'] as $p) {
                $uniqueCommodities[$p['commodity']] = true;
            }
        }

        return [
            'total_farmers' => $markers->count(),
            'total_commodities' => count($uniqueCommodities),
            'total_stock' => $totalStock,
        ];
    }

    /**
     * Resolve GPS coordinates from profile, product, or regional dictionary.
     */
    public function resolveFarmerCoordinates(?FarmerProfile $profile, ?Product $sampleProduct = null): array
    {
        // 1. From FarmerProfile database fields
        if ($profile && !empty($profile->latitude) && !empty($profile->longitude)) {
            return [
                'lat' => (float) $profile->latitude,
                'lng' => (float) $profile->longitude,
            ];
        }

        // 2. From Product database fields
        if ($sampleProduct && !empty($sampleProduct->latitude) && !empty($sampleProduct->longitude)) {
            return [
                'lat' => (float) $sampleProduct->latitude,
                'lng' => (float) $sampleProduct->longitude,
            ];
        }

        // 3. From text address matching
        $text = strtolower(($profile->address ?? '') . ' ' . ($sampleProduct->location ?? ''));
        foreach ($this->fallbackCoordinates as $region => $coords) {
            if (str_contains($text, $region)) {
                return $coords;
            }
        }

        // Default to Yogyakarta center
        return ['lat' => -7.7956, 'lng' => 110.3695];
    }
}
