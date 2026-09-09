<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommodityPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'commodity_id',
        'price',
        'unit',
        'location',
        'recorded_date',
        'source',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'recorded_date' => 'date',
    ];

    /**
     * Relationship to Commodity.
     */
    public function commodity(): BelongsTo
    {
        return $this->belongsTo(Commodity::class);
    }

    /**
     * Scope filter by commodity.
     */
    public function scopeForCommodity(Builder $query, $commodityId): Builder
    {
        return $query->where('commodity_id', $commodityId);
    }

    /**
     * Scope filter by location.
     */
    public function scopeForLocation(Builder $query, ?string $location): Builder
    {
        if (empty($location) || $location === 'all') {
            return $query;
        }

        return $query->where('location', 'like', "%{$location}%");
    }

    /**
     * Scope date range.
     */
    public function scopeDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('recorded_date', [$startDate, $endDate]);
    }
}
