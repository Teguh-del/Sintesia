<?php

namespace App\Services;

use App\Models\Commodity;
use App\Models\CommodityPrice;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PriceAnalyticsService
{
    /**
     * Get price analytics for a commodity within a specific day range and location.
     *
     * @param int $commodityId
     * @param int $days Number of past days (e.g. 7, 30, 90)
     * @param string|null $location Optional location filter
     * @return array
     */
    public function getAnalytics(int $commodityId, int $days = 30, ?string $location = null): array
    {
        $commodity = Commodity::findOrFail($commodityId);
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays($days);

        // Fetch chronological records
        $query = CommodityPrice::forCommodity($commodityId)
            ->whereBetween('recorded_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('recorded_date', 'asc');

        if (!empty($location) && $location !== 'all') {
            $query->where('location', 'like', "%{$location}%");
        }

        $records = $query->get();

        if ($records->isEmpty()) {
            // Fallback: fetch any latest record for this commodity
            $latestFallback = CommodityPrice::forCommodity($commodityId)->latest('recorded_date')->first();
            $latestPrice = $latestFallback ? (float) $latestFallback->price : 0.0;

            return [
                'commodity' => $commodity,
                'days' => $days,
                'location' => $location ?: 'Semua Wilayah',
                'latest_price' => $latestPrice,
                'highest_price' => $latestPrice,
                'lowest_price' => $latestPrice,
                'average_price' => $latestPrice,
                'change_nominal' => 0.0,
                'change_percentage' => 0.0,
                'trend' => 'Stabil',
                'trend_icon' => 'minus',
                'trend_color' => 'text-slate-600',
                'trend_badge' => 'bg-slate-100 text-slate-700',
                'chart_labels' => [],
                'chart_prices' => [],
                'records' => collect(),
            ];
        }

        $prices = $records->pluck('price')->map(fn($p) => (float) $p);
        $latestRecord = $records->last();
        $earliestRecord = $records->first();

        $latestPrice = (float) $latestRecord->price;
        $earliestPrice = (float) $earliestRecord->price;
        $highestPrice = $prices->max();
        $lowestPrice = $prices->min();
        $averagePrice = round($prices->avg(), 0);

        // Price change
        $changeNominal = $latestPrice - $earliestPrice;
        $changePercentage = $earliestPrice > 0 ? round(($changeNominal / $earliestPrice) * 100, 2) : 0.0;

        // Trend Classification
        if ($changeNominal > 0) {
            $trend = 'Naik';
            $trendIcon = 'trending-up';
            $trendColor = 'text-rose-600';
            $trendBadge = 'bg-rose-50 text-rose-700 border-rose-200';
        } elseif ($changeNominal < 0) {
            $trend = 'Turun';
            $trendIcon = 'trending-down';
            $trendColor = 'text-emerald-600';
            $trendBadge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
        } else {
            $trend = 'Stabil';
            $trendIcon = 'minus';
            $trendColor = 'text-blue-600';
            $trendBadge = 'bg-blue-50 text-blue-700 border-blue-200';
        }

        // Prepare time-series for Chart.js
        $chartLabels = [];
        $chartPrices = [];

        foreach ($records as $rec) {
            $chartLabels[] = Carbon::parse($rec->recorded_date)->format('d M');
            $chartPrices[] = (float) $rec->price;
        }

        return [
            'commodity' => $commodity,
            'days' => $days,
            'location' => $location ?: 'Semua Wilayah',
            'latest_price' => $latestPrice,
            'highest_price' => $highestPrice,
            'lowest_price' => $lowestPrice,
            'average_price' => $averagePrice,
            'change_nominal' => $changeNominal,
            'change_percentage' => $changePercentage,
            'trend' => $trend,
            'trend_icon' => $trendIcon,
            'trend_color' => $trendColor,
            'trend_badge' => $trendBadge,
            'chart_labels' => $chartLabels,
            'chart_prices' => $chartPrices,
            'records' => $records->sortByDesc('recorded_date')->values(),
        ];
    }

    /**
     * Get summary cards for all active commodities to display in overview widget.
     *
     * @return Collection
     */
    public function getCommoditiesOverview(): Collection
    {
        $commodities = Commodity::where('is_active', true)->get();

        return $commodities->map(function ($c) {
            $latest = CommodityPrice::forCommodity($c->id)->latest('recorded_date')->first();
            $prev = null;
            if ($latest) {
                $prev = CommodityPrice::forCommodity($c->id)
                    ->where('recorded_date', '<', $latest->recorded_date)
                    ->latest('recorded_date')
                    ->first();
            }

            $currentPrice = $latest ? (float) $latest->price : 0.0;
            $prevPrice = $prev ? (float) $prev->price : $currentPrice;
            $change = $currentPrice - $prevPrice;
            $pct = $prevPrice > 0 ? round(($change / $prevPrice) * 100, 1) : 0.0;

            return [
                'id' => $c->id,
                'name' => $c->name,
                'unit' => $c->unit,
                'icon' => $c->icon ?? 'sprout',
                'current_price' => $currentPrice,
                'formatted_price' => 'Rp ' . number_format($currentPrice, 0, ',', '.'),
                'change' => $change,
                'change_pct' => $pct,
                'trend' => $change > 0 ? 'Naik' : ($change < 0 ? 'Turun' : 'Stabil'),
                'trend_color' => $change > 0 ? 'text-rose-600' : ($change < 0 ? 'text-emerald-600' : 'text-slate-500'),
                'last_updated' => $latest ? Carbon::parse($latest->recorded_date)->translatedFormat('d M Y') : 'Belum Ada Data',
            ];
        });
    }

    /**
     * List distinct locations in the prices database.
     */
    public function getAvailableLocations(): array
    {
        return CommodityPrice::distinct()->pluck('location')->filter()->values()->all();
    }
}
