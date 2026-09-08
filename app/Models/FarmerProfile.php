<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'farm_name',
        'farm_area_hectares',
        'primary_commodity',
        'address',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'farm_area_hectares' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
