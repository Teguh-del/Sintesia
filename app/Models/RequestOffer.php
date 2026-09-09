<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'commodity_request_id',
        'farmer_id',
        'product_id',
        'stock_id',
        'offered_quantity',
        'offered_price',
        'shipping_method',
        'notes',
        'status',
    ];

    protected $casts = [
        'offered_quantity' => 'decimal:2',
        'offered_price' => 'decimal:2',
    ];

    public function commodityRequest(): BelongsTo
    {
        return $this->belongsTo(CommodityRequest::class, 'commodity_request_id');
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->offered_price, 0, ',', '.');
    }

    public function getTotalAmountAttribute(): float
    {
        return (float) ($this->offered_quantity * $this->offered_price);
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }
}
