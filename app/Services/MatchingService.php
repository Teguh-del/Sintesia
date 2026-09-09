<?php

namespace App\Services;

use App\Models\Commodity;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;

class MatchingService
{
    /**
     * Default coordinates fallback for regions in Indonesia (DI Yogyakarta & Jawa Timur)
     */
    protected array $regionCoordinates = [
        'sleman' => ['lat' => -7.7167, 'lon' => 110.3556],
        'bantul' => ['lat' => -7.8878, 'lon' => 110.3283],
        'yogyakarta' => ['lat' => -7.7956, 'lon' => 110.3695],
        'kulon progo' => ['lat' => -7.8286, 'lon' => 110.1583],
        'gunungkidul' => ['lat' => -7.9608, 'lon' => 110.6033],
        'kediri' => ['lat' => -7.8184, 'lon' => 112.0156],
        'surabaya' => ['lat' => -7.2575, 'lon' => 112.7521],
        'malang' => ['lat' => -7.9666, 'lon' => 112.6326],
        'semarang' => ['lat' => -6.9667, 'lon' => 110.4167],
        'solo' => ['lat' => -7.5667, 'lon' => 110.8167],
        'default' => ['lat' => -7.7956, 'lon' => 110.3695], // Yogyakarta default
    ];

    /**
     * Find matched farmer products based on PRD Weighted Scoring.
     *
     * @param array $criteria ['commodity_id' => int, 'quantity' => float, 'max_price' => float, 'latitude' => ?float, 'longitude' => ?float, 'location_name' => ?string]
     * @param User|null $buyer Optional buyer for contextual defaults
     * @return Collection Collection of ranked candidate objects with scores and breakdown
     */
    public function findMatches(array $criteria, ?User $buyer = null): Collection
    {
        $commodityId = !empty($criteria['commodity_id']) ? (int) $criteria['commodity_id'] : null;
        $requiredQuantity = !empty($criteria['quantity']) ? (float) $criteria['quantity'] : 100.0;
        $maxPrice = !empty($criteria['max_price']) ? (float) $criteria['max_price'] : 50000.0;

        // Resolve buyer location / coordinates
        $buyerCoords = $this->resolveCoordinates(
            $criteria['latitude'] ?? null,
            $criteria['longitude'] ?? null,
            $criteria['location_name'] ?? ($buyer->consumerProfile->address ?? ($buyer->collectorProfile->address ?? null))
        );

        $targetCommodity = $commodityId ? Commodity::find($commodityId) : null;

        // 1. Fetch live active candidates from MySQL
        $query = Product::with(['commodity', 'user.farmerProfile', 'images'])
            ->where('status', 'active')
            ->where('stock', '>', 0);

        if ($targetCommodity) {
            // Prioritize target commodity or same category
            $query->where(function ($q) use ($targetCommodity) {
                $q->where('commodity_id', $targetCommodity->id)
                  ->orWhereHas('commodity', function ($cq) use ($targetCommodity) {
                      $cq->where('category', $targetCommodity->category);
                  });
            });
        }

        $products = $query->get();

        // 2. Score each candidate according to PRD formulation
        $rankedCandidates = $products->map(function (Product $product) use ($targetCommodity, $requiredQuantity, $maxPrice, $buyerCoords) {
            return $this->evaluateCandidate($product, $targetCommodity, $requiredQuantity, $maxPrice, $buyerCoords);
        });

        // 3. Sort descending by match score
        return $rankedCandidates->sortByDesc('match_score')->values();
    }

    /**
     * Evaluate a single product candidate.
     */
    public function evaluateCandidate(Product $product, ?Commodity $targetCommodity, float $requiredQuantity, float $maxPrice, array $buyerCoords): array
    {
        // 1. Commodity Fit (35%)
        $commodityScore = $this->calculateCommodityScore($product, $targetCommodity);

        // 2. Stock Availability (25%)
        $stockScore = $this->calculateStockScore((float) $product->stock, $requiredQuantity);

        // 3. Price Fit (20%)
        $priceScore = $this->calculatePriceScore((float) $product->price, $maxPrice);

        // 4. Geographic Proximity (20%)
        $farmerCoords = $this->resolveCoordinates(
            $product->user->farmerProfile->latitude ?? null,
            $product->user->farmerProfile->longitude ?? null,
            $product->user->farmerProfile->address ?? $product->location
        );

        $distanceKm = $this->calculateHaversineDistance(
            $buyerCoords['lat'],
            $buyerCoords['lon'],
            $farmerCoords['lat'],
            $farmerCoords['lon']
        );

        $distanceScore = $this->calculateDistanceScore($distanceKm);

        // Weighted Match Score Calculation (AGENTS.md & PRD Rule)
        // (commodity * 0.35) + (stock * 0.25) + (price * 0.20) + (distance * 0.20)
        $compositeScore = (
            ($commodityScore * 0.35) +
            ($stockScore * 0.25) +
            ($priceScore * 0.20) +
            ($distanceScore * 0.20)
        );

        $matchScore = round($compositeScore, 1);

        // Classification Category (PRD 6.12)
        $category = $this->classifyMatchCategory($matchScore);

        return [
            'product' => $product,
            'farmer' => $product->user,
            'match_score' => $matchScore,
            'final_score' => $matchScore,
            'category' => $category['name'],
            'category_badge' => $category['badge'],
            'category_color' => $category['color'],
            'distance_km' => round($distanceKm, 1),
            'commodity_score' => round($commodityScore, 1),
            'stock_score' => round($stockScore, 1),
            'price_score' => round($priceScore, 1),
            'distance_score' => round($distanceScore, 1),
            'scores' => [
                'commodity' => round($commodityScore, 1),
                'stock' => round($stockScore, 1),
                'price' => round($priceScore, 1),
                'distance' => round($distanceScore, 1),
            ],
            'weights' => [
                'commodity' => 35,
                'stock' => 25,
                'price' => 20,
                'distance' => 20,
            ],
            'is_exact_commodity' => $targetCommodity ? ($product->commodity_id === $targetCommodity->id) : true,
            'is_sufficient_stock' => $product->stock >= $requiredQuantity,
            'is_within_budget' => $product->price <= $maxPrice,
        ];
    }

    /**
     * Commodity Fit (35% weight):
     * 100 for exact commodity match, 60 for same category, 20 for different.
     */
    public function calculateCommodityScore($product, $targetCommodity = null): float
    {
        $productCommodityId = $product instanceof Product ? $product->commodity_id : (is_numeric($product) ? (int) $product : null);
        $targetCommodityId = $targetCommodity instanceof Commodity ? $targetCommodity->id : (is_numeric($targetCommodity) ? (int) $targetCommodity : null);

        if (!$targetCommodityId) {
            return 80.0;
        }

        if ($productCommodityId && $productCommodityId === $targetCommodityId) {
            return 100.0;
        }

        if ($product instanceof Product && $targetCommodity instanceof Commodity) {
            if ($product->commodity && $product->commodity->category && $product->commodity->category === $targetCommodity->category) {
                return 60.0;
            }
        }

        return 0.0;
    }

    /**
     * Stock Availability (25% weight):
     * 100 if available >= required, proportional score otherwise.
     */
    public function calculateStockScore(float $availableStock, float $requiredQuantity): float
    {
        if ($requiredQuantity <= 0) {
            return 100.0;
        }

        if ($availableStock >= $requiredQuantity) {
            return 100.0;
        }

        $ratio = $availableStock / $requiredQuantity;

        if ($ratio >= 0.75) {
            return 85.0;
        }

        if ($ratio >= 0.50) {
            return 70.0;
        }

        return (float) max(15.0, round($ratio * 100, 1));
    }

    /**
     * Price Fit (20% weight):
     * 100 if <= 80% budget, 95 if <= 90%, 90 if <= 100%, penalty if exceeding.
     */
    public function calculatePriceScore(float $productPrice, float $maxPrice): float
    {
        if ($maxPrice <= 0) {
            return 50.0;
        }

        if ($productPrice <= ($maxPrice * 0.80)) {
            return 100.0;
        }

        if ($productPrice <= ($maxPrice * 0.90)) {
            return 95.0;
        }

        if ($productPrice <= $maxPrice) {
            return 90.0;
        }

        // Product is higher than maximum price budget
        $percentOver = (($productPrice - $maxPrice) / $maxPrice) * 100;
        return (float) max(0.0, round(85.0 - $percentOver, 1));
    }

    /**
     * Distance Proximity (20% weight):
     * <= 5 km: 100
     * <= 15 km: 90
     * <= 30 km: 80
     * <= 50 km: 70
     * <= 100 km: 55
     * <= 200 km: 40
     */
    public function calculateDistanceScore(float $distanceKm): float
    {
        if ($distanceKm <= 5.0) {
            return 100.0;
        }

        if ($distanceKm <= 15.0) {
            return 90.0;
        }

        if ($distanceKm <= 30.0) {
            return 80.0;
        }

        if ($distanceKm <= 50.0) {
            return 70.0;
        }

        if ($distanceKm <= 100.0) {
            return 55.0;
        }

        if ($distanceKm <= 200.0) {
            return 40.0;
        }

        return (float) max(10.0, round(100.0 - ($distanceKm * 0.3), 1));
    }

    /**
     * Haversine formula to compute great-circle distance between two GPS coordinates in kilometers.
     */
    public function calculateHaversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadiusKm = 6371.0;

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return (float) ($earthRadiusKm * $c);
    }

    /**
     * Classify Match Score into PRD mandated categories:
     * 85–100: Sangat Cocok
     * 70–84: Cocok
     * 50–69: Cukup Cocok
     * 0–49: Kurang Cocok
     */
    public function classifyMatchCategory(float $score): array
    {
        if ($score >= 85.0) {
            return [
                'name' => 'Sangat Cocok',
                'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'color' => 'emerald',
            ];
        }

        if ($score >= 70.0) {
            return [
                'name' => 'Cocok',
                'badge' => 'bg-teal-50 text-teal-700 border-teal-200',
                'color' => 'teal',
            ];
        }

        if ($score >= 50.0) {
            return [
                'name' => 'Cukup Cocok',
                'badge' => 'bg-amber-50 text-amber-700 border-amber-200',
                'color' => 'amber',
            ];
        }

        return [
            'name' => 'Kurang Cocok',
            'badge' => 'bg-slate-100 text-slate-600 border-slate-200',
            'color' => 'slate',
        ];
    }

    /**
     * Resolve latitude and longitude from explicit coords or address text fallback.
     */
    public function resolveCoordinates(?float $lat, ?float $lon, ?string $locationText = null): array
    {
        if (!is_null($lat) && !is_null($lon) && $lat != 0 && $lon != 0) {
            return ['lat' => (float) $lat, 'lon' => (float) $lon];
        }

        if ($locationText) {
            $lower = strtolower($locationText);
            foreach ($this->regionCoordinates as $keyword => $coords) {
                if ($keyword !== 'default' && str_contains($lower, $keyword)) {
                    return $coords;
                }
            }
        }

        return $this->regionCoordinates['default'];
    }
}
