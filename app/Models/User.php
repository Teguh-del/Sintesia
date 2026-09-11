<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isPetani(): bool
    {
        return $this->role === 'petani';
    }

    public function isPengepul(): bool
    {
        return $this->role === 'pengepul';
    }

    public function isKonsumen(): bool
    {
        return $this->role === 'konsumen';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function farmerProfile(): HasOne
    {
        return $this->hasOne(FarmerProfile::class);
    }

    public function collectorProfile(): HasOne
    {
        return $this->hasOne(CollectorProfile::class);
    }

    public function consumerProfile(): HasOne
    {
        return $this->hasOne(ConsumerProfile::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function harvests(): HasMany
    {
        return $this->hasMany(Harvest::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function buyerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function sellerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->latest();
    }

    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(Notification::class)->where('is_read', false)->latest();
    }

    public function commodityRequests(): HasMany
    {
        return $this->hasMany(CommodityRequest::class);
    }

    public function buyerPriceOffers(): HasMany
    {
        return $this->hasMany(PriceOffer::class, 'buyer_id');
    }

    public function sellerPriceOffers(): HasMany
    {
        return $this->hasMany(PriceOffer::class, 'seller_id');
    }

    public function preorders(): HasMany
    {
        return $this->hasMany(Preorder::class);
    }

    public function preorderItems(): HasMany
    {
        return $this->hasMany(PreorderItem::class, 'buyer_id');
    }

    public function getAddressAttribute(): ?string
    {
        return $this->farmerProfile?->address
            ?? $this->collectorProfile?->address
            ?? $this->consumerProfile?->address;
    }

    /**
     * Get user dashboard route based on role.
     */
    public function getDashboardRoute(): string
    {
        return match ($this->role) {
            'petani' => route('farmer.dashboard'),
            'pengepul' => route('collector.dashboard'),
            'konsumen' => route('consumer.dashboard'),
            'admin' => route('admin.dashboard'),
            default => route('home'),
        };
    }
}
