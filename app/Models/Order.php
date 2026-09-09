<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'buyer_id',
        'seller_id',
        'source_type',
        'status',
        'total_amount',
        'shipping_address',
        'shipping_method',
        'shipping_cost',
        'payment_method',
        'payment_status',
        'notes',
        'confirmed_at',
        'processed_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /* -------------------------------------------------------------------------- */
    /*                                Relationships                               */
    /* -------------------------------------------------------------------------- */

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class)->latestOfMany();
    }

    public function latestTransaction(): HasOne
    {
        return $this->hasOne(Transaction::class)->latestOfMany();
    }

    /* -------------------------------------------------------------------------- */
    /*                                   Scopes                                   */
    /* -------------------------------------------------------------------------- */

    public function scopeForBuyer(Builder $query, int $userId): Builder
    {
        return $query->where('buyer_id', $userId);
    }

    public function scopeForSeller(Builder $query, int $userId): Builder
    {
        return $query->where('seller_id', $userId);
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /* -------------------------------------------------------------------------- */
    /*                                  Helpers                                   */
    /* -------------------------------------------------------------------------- */

    public function getFormattedTotalAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getFormattedShippingCostAttribute(): string
    {
        return $this->shipping_cost > 0
            ? 'Rp ' . number_format($this->shipping_cost, 0, ',', '.')
            : 'Gratis / Termasuk';
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'Menunggu Konfirmasi' => 'bg-amber-100 text-amber-800 border-amber-200',
            'Dikonfirmasi' => 'bg-blue-100 text-blue-800 border-blue-200',
            'Diproses' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            'Selesai' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'Dibatalkan' => 'bg-rose-100 text-rose-800 border-rose-200',
            default => 'bg-slate-100 text-slate-800 border-slate-200',
        };
    }

    public function canBeCancelledByBuyer(): bool
    {
        return $this->status === 'Menunggu Konfirmasi';
    }

    public function canBeConfirmedByFarmer(): bool
    {
        return $this->status === 'Menunggu Konfirmasi';
    }

    public function canBeProcessedByFarmer(): bool
    {
        return $this->status === 'Dikonfirmasi';
    }

    public function canBeCompleted(): bool
    {
        return $this->status === 'Diproses';
    }
}
