<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Preorder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'commodity_id',
        'title',
        'slug',
        'description',
        'price',
        'estimated_production',
        'preorder_available_quantity',
        'min_order',
        'unit',
        'estimated_harvest_date',
        'location',
        'latitude',
        'longitude',
        'image',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'estimated_production' => 'decimal:2',
        'preorder_available_quantity' => 'decimal:2',
        'min_order' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'estimated_harvest_date' => 'date',
    ];

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function commodity(): BelongsTo
    {
        return $this->belongsTo(Commodity::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PreorderItem::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['Dibuka', 'Menunggu Panen']);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getBookedQuantityAttribute(): float
    {
        return max(0, (float) ($this->estimated_production - $this->preorder_available_quantity));
    }

    public function getProgressPercentageAttribute(): int
    {
        if ($this->estimated_production <= 0) return 0;
        return min(100, (int) round(($this->booked_quantity / $this->estimated_production) * 100));
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return str_starts_with($this->image, 'http') ? $this->image : asset('storage/' . $this->image);
        }
        return 'https://images.unsplash.com/photo-1500937386664-56d1dfef3854?w=800&auto=format&fit=crop&q=80';
    }
}
