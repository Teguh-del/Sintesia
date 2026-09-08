<?php

namespace Database\Seeders;

use App\Models\Commodity;
use App\Models\Product;
use App\Models\User;
use App\Services\HarvestStockService;
use Illuminate\Database\Seeder;

class HarvestStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $farmer = User::where('role', 'petani')->first();

        if (!$farmer) {
            $this->command->warn('Petani tidak ditemukan. Pastikan UserSeeder sudah dijalankan.');
            return;
        }

        $service = app(HarvestStockService::class);
        $commodities = Commodity::all()->keyBy('name');

        $harvestRecords = [
            [
                'commodity_name' => 'Jagung',
                'quantity' => 4000,
                'unit' => 'kg',
                'harvest_date' => now()->subDays(5)->format('Y-m-d'),
                'quality' => 'Grade A (Super)',
                'location' => 'Blok B Lahan Subur, Kec. Pujon, Kab. Malang',
                'notes' => 'Panen jagung Bisi-18 kemarau berkadar air rendah 13.5%, bulir padat kuning keemasan.',
                'product_slug' => 'jagung-hibrida-bisi-18-super',
            ],
            [
                'commodity_name' => 'Jagung',
                'quantity' => 900,
                'unit' => 'kg',
                'harvest_date' => now()->subDays(1)->format('Y-m-d'),
                'quality' => 'Grade A (Super)',
                'location' => 'Lahan Irigasi Teknis, Kec. Dau, Kab. Malang',
                'notes' => 'Jagung manis segar petik pagi hari saat embun masih ada.',
                'product_slug' => 'jagung-manis-segar-kupas',
            ],
            [
                'commodity_name' => 'Cabai',
                'quantity' => 500,
                'unit' => 'kg',
                'harvest_date' => now()->subDays(2)->format('Y-m-d'),
                'quality' => 'Grade A (Super)',
                'location' => 'Lahan Lereng Gunung, Kec. Ngantang, Kab. Malang',
                'notes' => 'Cabai rawit merah petik merah 95%, bebas patek, daya simpan tinggi.',
                'product_slug' => 'cabai-rawit-merah-segar-ori-212',
            ],
            [
                'commodity_name' => 'Cabai',
                'quantity' => 700,
                'unit' => 'kg',
                'harvest_date' => now()->subDays(3)->format('Y-m-d'),
                'quality' => 'Grade A (Super)',
                'location' => 'Kebun Bumiaji, Kota Batu, Jawa Timur',
                'notes' => 'Cabai merah keriting panjang rata-rata 14 cm, padat dan mengkilap.',
                'product_slug' => 'cabai-merah-keriting-grade-a',
            ],
            [
                'commodity_name' => 'Tomat',
                'quantity' => 1500,
                'unit' => 'kg',
                'harvest_date' => now()->subDays(1)->format('Y-m-d'),
                'quality' => 'Grade A (Super)',
                'location' => 'Kec. Pujon, Kab. Malang, Jawa Timur',
                'notes' => 'Tomat buah Servo dataran tinggi, kulit tebal tahan guncangan transportasi.',
                'product_slug' => 'tomat-buah-segar-dataran-tinggi',
            ],
            [
                'commodity_name' => 'Kelapa',
                'quantity' => 1000,
                'unit' => 'butir',
                'harvest_date' => now()->subDays(6)->format('Y-m-d'),
                'quality' => 'Grade A (Super)',
                'location' => 'Perkebunan Rakyat, Kec. Rogojampi, Kab. Banyuwangi',
                'notes' => 'Kelapa tua kupas sabut, santan kental gurih berbobot rata-rata 1.2 kg.',
                'product_slug' => 'kelapa-tua-butiran-siap-santan',
            ],
            [
                'commodity_name' => 'Padi',
                'quantity' => 2500,
                'unit' => 'kg',
                'harvest_date' => now()->subDays(10)->format('Y-m-d'),
                'quality' => 'Organik Premium',
                'location' => 'Sawah Berundak Mata Air, Kec. Sawangan, Kab. Magelang',
                'notes' => 'Beras pandan wangi organik murni, digiling dingin tanpa pewangi buatan.',
                'product_slug' => 'beras-pandan-wangi-organik-super',
            ],
        ];

        foreach ($harvestRecords as $data) {
            $commodity = $commodities->get($data['commodity_name']);
            if (!$commodity) {
                continue;
            }

            $productSlug = $data['product_slug'] ?? null;
            unset($data['commodity_name'], $data['product_slug']);

            $data['commodity_id'] = $commodity->id;

            // Record harvest and automatic stock creation via HarvestStockService
            $harvest = $service->recordHarvest($farmer, $data);

            // If a corresponding product exists, link it to this stock batch!
            if ($productSlug && $harvest->stock) {
                Product::where('slug', $productSlug)->update([
                    'stock_id' => $harvest->stock->id,
                ]);
            }
        }
    }
}
