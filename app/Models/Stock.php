<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'commodity_id',
        'harvest_id',
        'batch_code',
        'initial_quantity',
        'available_quantity',
        'ordered_quantity',
        'sold_quantity',
        'unit',
        'quality',
        'status',
    ];

    protected $casts = [
        'initial_quantity' => 'decimal:2',
        'available_quantity' => 'decimal:2',
        'ordered_quantity' => 'decimal:2',
        'sold_quantity' => 'decimal:2',
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

    public function harvest(): BelongsTo
    {
        return $this->belongsTo(Harvest::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /* -------------------------------------------------------------------------- */
    /*                               Business Logic                               */
    /* -------------------------------------------------------------------------- */

    /**
     * Synchronize and recalculate stock status based on available quantity.
     * PRD Status: Tersedia, Stok Terbatas (<= 10), Habis (<= 0)
     */
    public function syncStatus(): void
    {
        if ($this->available_quantity <= 0) {
            $this->status = 'Habis';
            $this->available_quantity = 0;
        } elseif ($this->available_quantity <= 10) {
            $this->status = 'Stok Terbatas';
        } else {
            $this->status = 'Tersedia';
        }
        $this->save();
    }

    /**
     * Reserve stock for an incoming order.
     * Throws exception if requested quantity exceeds available stock.
     */
    public function reserve(float $quantity): bool
    {
        if ($quantity <= 0) {
            return false;
        }

        if ($this->available_quantity < $quantity) {
            throw new \InvalidArgumentException("Stok tersedia ({$this->available_quantity} {$this->unit}) tidak mencukupi permintaan ({$quantity} {$this->unit}).");
        }

        $this->available_quantity -= $quantity;
        $this->ordered_quantity += $quantity;
        $this->syncStatus();

        return true;
    }

    /**
     * Release previously ordered stock back to available stock (e.g. order cancelled).
     */
    public function release(float $quantity): bool
    {
        if ($quantity <= 0) {
            return false;
        }

        $releaseQty = min($this->ordered_quantity, $quantity);
        $this->ordered_quantity -= $releaseQty;
        $this->available_quantity += $releaseQty;
        $this->syncStatus();

        return true;
    }

    /**
     * Complete sale for an order.
     */
    public function completeSale(float $quantity): bool
    {
        if ($quantity <= 0) {
            return false;
        }

        $saleQty = min($this->ordered_quantity, $quantity);
        $this->ordered_quantity -= $saleQty;
        $this->sold_quantity += $saleQty;
        $this->save();

        return true;
    }

    /* -------------------------------------------------------------------------- */
    /*                                   Scopes                                   */
    /* -------------------------------------------------------------------------- */

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('available_quantity', '>', 0);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['commodity_id'] ?? null, function ($q, $commodityId) {
                $q->where('commodity_id', $commodityId);
            })
            ->when($filters['status'] ?? null, function ($q, $status) {
                $q->where('status', $status);
            })
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('batch_code', 'like', "%{$search}%")
                        ->orWhere('quality', 'like', "%{$search}%")
                        ->orWhereHas('commodity', function ($cq) use ($search) {
                            $cq->where('name', 'like', "%{$search}%");
                        });
                });
            });
    }

    /* -------------------------------------------------------------------------- */
    /*                                  Accessors                                 */
    /* -------------------------------------------------------------------------- */

    public function getFormattedAvailableAttribute(): string
    {
        return number_format($this->available_quantity, 0, ',', '.') . ' ' . $this->unit;
    }

    public function getFormattedOrderedAttribute(): string
    {
        return number_format($this->ordered_quantity, 0, ',', '.') . ' ' . $this->unit;
    }

    public function getFormattedSoldAttribute(): string
    {
        return number_format($this->sold_quantity, 0, ',', '.') . ' ' . $this->unit;
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'Tersedia' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'Stok Terbatas' => 'bg-amber-100 text-amber-800 border-amber-200',
            default => 'bg-rose-100 text-rose-800 border-rose-200',
        };
    }
}
