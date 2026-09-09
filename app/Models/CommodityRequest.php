<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommodityRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'commodity_id',
        'title',
        'required_quantity',
        'unit',
        'max_price',
        'location',
        'latitude',
        'longitude',
        'deadline',
        'description',
        'status',
    ];

    protected $casts = [
        'required_quantity' => 'decimal:2',
        'max_price' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'deadline' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function commodity(): BelongsTo
    {
        return $this->belongsTo(Commodity::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(RequestOffer::class, 'commodity_request_id');
    }

    public function activeOffers(): HasMany
    {
        return $this->hasMany(RequestOffer::class, 'commodity_request_id')->where('status', 'Menunggu');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['Aktif', 'Mendapat Penawaran']);
    }

    public function getFormattedMaxPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->max_price, 0, ',', '.');
    }

    public function getFormattedQuantityAttribute(): string
    {
        return number_format($this->required_quantity, 0, ',', '.') . ' ' . $this->unit;
    }
}
