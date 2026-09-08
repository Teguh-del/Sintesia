<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'commodity_id',
        'stock_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'unit',
        'min_order',
        'quality',
        'harvest_date',
        'location',
        'latitude',
        'longitude',
        'status',
        'allow_negotiation',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'decimal:2',
        'min_order' => 'decimal:2',
        'harvest_date' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'allow_negotiation' => 'boolean',
    ];

    /**
     * Boot function to automatically generate slugs.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $baseSlug = Str::slug($product->name);
                $uniqueSlug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $uniqueSlug)->exists()) {
                    $uniqueSlug = "{$baseSlug}-{$counter}";
                    $counter++;
                }
                $product->slug = $uniqueSlug;
            }
        });
    }

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

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /* -------------------------------------------------------------------------- */
    /*                                   Scopes                                   */
    /* -------------------------------------------------------------------------- */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')->where('stock', '>', 0);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($uq) use ($search) {
                          $uq->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->when($filters['commodity'] ?? null, function ($query, $commodity) {
                if (is_numeric($commodity)) {
                    $query->where('commodity_id', $commodity);
                } else {
                    $query->whereHas('commodity', function ($q) use ($commodity) {
                        $q->where('slug', $commodity)->orWhere('name', $commodity);
                    });
                }
            })
            ->when($filters['location'] ?? null, function ($query, $location) {
                $query->where('location', 'like', "%{$location}%");
            })
            ->when($filters['min_price'] ?? null, function ($query, $minPrice) {
                $query->where('price', '>=', (float) $minPrice);
            })
            ->when($filters['max_price'] ?? null, function ($query, $maxPrice) {
                $query->where('price', '<=', (float) $maxPrice);
            })
            ->when($filters['quality'] ?? null, function ($query, $quality) {
                $query->where('quality', $quality);
            })
            ->when($filters['stock_status'] ?? null, function ($query, $stockStatus) {
                if ($stockStatus === 'available') {
                    $query->where('stock', '>', 10);
                } elseif ($stockStatus === 'limited') {
                    $query->where('stock', '>', 0)->where('stock', '<=', 10);
                } elseif ($stockStatus === 'sold_out') {
                    $query->where('stock', '<=', 0);
                }
            })
            ->when($filters['sort'] ?? 'latest', function ($query, $sort) {
                match ($sort) {
                    'price_low' => $query->orderBy('price', 'asc'),
                    'price_high' => $query->orderBy('price', 'desc'),
                    'stock_high' => $query->orderBy('stock', 'desc'),
                    'oldest' => $query->orderBy('created_at', 'asc'),
                    default => $query->orderBy('created_at', 'desc'),
                };
            });
    }

    /* -------------------------------------------------------------------------- */
    /*                                  Accessors                                 */
    /* -------------------------------------------------------------------------- */

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getFormattedStockAttribute(): string
    {
        return number_format($this->stock, 0, ',', '.') . ' ' . $this->unit;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0 || $this->status === 'sold_out') {
            return 'Habis';
        }
        if ($this->stock <= 10) {
            return 'Stok Terbatas';
        }
        return 'Tersedia';
    }

    public function getStockStatusColorAttribute(): string
    {
        return match ($this->stock_status) {
            'Tersedia' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'Stok Terbatas' => 'bg-amber-100 text-amber-800 border-amber-200',
            default => 'bg-rose-100 text-rose-800 border-rose-200',
        };
    }

    public function getPrimaryImageUrlAttribute(): string
    {
        $primary = $this->images->where('is_primary', true)->first() ?? $this->images->first();

        if ($primary && $primary->image_path) {
            if (Str::startsWith($primary->image_path, ['http://', 'https://'])) {
                return $primary->image_path;
            }
            return asset('storage/' . $primary->image_path);
        }

        // Fallback realistic agriculture imagery based on commodity name
        $commodityName = strtolower($this->commodity->name ?? '');
        if (str_contains($commodityName, 'cabai')) {
            return 'https://images.unsplash.com/photo-1588252303782-cb80119abd6d?w=800&auto=format&fit=crop&q=80';
        }
        if (str_contains($commodityName, 'jagung')) {
            return 'https://images.unsplash.com/photo-1551754655-cd27e38d2076?w=800&auto=format&fit=crop&q=80';
        }
        if (str_contains($commodityName, 'tomat')) {
            return 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?w=800&auto=format&fit=crop&q=80';
        }
        if (str_contains($commodityName, 'kelapa')) {
            return 'https://images.unsplash.com/photo-1544378730-8b5104b18790?w=800&auto=format&fit=crop&q=80';
        }
        if (str_contains($commodityName, 'padi') || str_contains($commodityName, 'beras')) {
            return 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=800&auto=format&fit=crop&q=80';
        }

        return 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=800&auto=format&fit=crop&q=80';
    }
}
