<?php

namespace Database\Seeders;

use App\Models\Commodity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CommoditySeeder extends Seeder
{
    public function run(): void
    {
        $commodities = [
            [
                'name' => 'Jagung',
                'slug' => Str::slug('Jagung'),
                'category' => 'Tanaman Pangan',
                'unit' => 'kg',
                'description' => 'Jagung pipil kering dan jagung manis berkualitas langsung dari lahan petani.',
                'icon' => 'wheat',
                'is_active' => true,
            ],
            [
                'name' => 'Cabai',
                'slug' => Str::slug('Cabai'),
                'category' => 'Hortikultura',
                'unit' => 'kg',
                'description' => 'Cabai rawit merah dan cabai merah keriting segar dengan tingkat kepedasan optimal.',
                'icon' => 'flame',
                'is_active' => true,
            ],
            [
                'name' => 'Tomat',
                'slug' => Str::slug('Tomat'),
                'category' => 'Hortikultura',
                'unit' => 'kg',
                'description' => 'Tomat segar pilihan, tekstur padat dan kaya nutrisi.',
                'icon' => 'apple',
                'is_active' => true,
            ],
            [
                'name' => 'Kelapa',
                'slug' => Str::slug('Kelapa'),
                'category' => 'Perkebunan',
                'unit' => 'butir',
                'description' => 'Kelapa tua dan kelapa muda segar langsung dari perkebunan rakyat.',
                'icon' => 'circle',
                'is_active' => true,
            ],
            [
                'name' => 'Padi',
                'slug' => Str::slug('Padi'),
                'category' => 'Tanaman Pangan',
                'unit' => 'kg',
                'description' => 'Gabah kering panen (GKP) dan beras pilihan hasil panen petani lokal.',
                'icon' => 'sprout',
                'is_active' => true,
            ],
        ];

        foreach ($commodities as $commodity) {
            Commodity::updateOrCreate(
                ['slug' => $commodity['slug']],
                $commodity
            );
        }
    }
}
