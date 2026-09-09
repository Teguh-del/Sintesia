<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'buyer_id',
        'seller_id',
        'quantity',
        'offered_price',
        'original_price',
        'counter_price',
        'shipping_method',
        'notes',
        'status',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'offered_price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'counter_price' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function getOfferedTotalAttribute(): float
    {
        return (float) ($this->quantity * $this->offered_price);
    }

    public function getFormattedOfferedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->offered_price, 0, ',', '.');
    }

    public function getFormattedOriginalPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->original_price, 0, ',', '.');
    }

    public function getFormattedCounterPriceAttribute(): ?string
    {
        return $this->counter_price ? 'Rp ' . number_format($this->counter_price, 0, ',', '.') : null;
    }

    public function getProductPriceAttribute(): float
    {
        return (float) $this->original_price;
    }
}
