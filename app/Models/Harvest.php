<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Harvest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'commodity_id',
        'quantity',
        'unit',
        'harvest_date',
        'quality',
        'location',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'harvest_date' => 'date',
    ];

    /* -------------------------------------------------------------------------- */
    /*                                Relationships                               */
    /* -------------------------------------------------------------------------- */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function commodity(): BelongsTo
    {
        return $this->belongsTo(Commodity::class);
    }

    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class);
    }

    /* -------------------------------------------------------------------------- */
    /*                                   Scopes                                   */
    /* -------------------------------------------------------------------------- */

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['commodity_id'] ?? null, function ($q, $commodityId) {
                $q->where('commodity_id', $commodityId);
            })
            ->when($filters['quality'] ?? null, function ($q, $quality) {
                $q->where('quality', $quality);
            })
            ->when($filters['date_from'] ?? null, function ($q, $from) {
                $q->whereDate('harvest_date', '>=', $from);
            })
            ->when($filters['date_to'] ?? null, function ($q, $to) {
                $q->whereDate('harvest_date', '<=', $to);
            })
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('location', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%")
                        ->orWhereHas('commodity', function ($cq) use ($search) {
                            $cq->where('name', 'like', "%{$search}%");
                        });
                });
            });
    }

    /* -------------------------------------------------------------------------- */
    /*                                  Accessors                                 */
    /* -------------------------------------------------------------------------- */

    public function getFormattedQuantityAttribute(): string
    {
        return number_format($this->quantity, 0, ',', '.') . ' ' . $this->unit;
    }
}
