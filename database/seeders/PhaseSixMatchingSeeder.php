<?php

namespace Database\Seeders;

use App\Models\Commodity;
use App\Models\FarmerProfile;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use App\Services\HarvestStockService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PhaseSixMatchingSeeder extends Seeder
{
    /**
     * Seed distinct farmers, harvests, stocks, and products across Yogyakarta & surrounding areas
     * to facilitate diverse multi-criteria scoring in SINTESA Match.
     */
    public function run(): void
    {
        $service = app(HarvestStockService::class);
        $commodities = Commodity::all()->keyBy('name');

        // 1. Farmer in Sleman: Pak Joko (Kelompok Tani Sleman Makmur)
        $farmerSleman = User::updateOrCreate(
            ['email' => 'petani.sleman@sintesa.id'],
            [
                'name' => 'Pak Joko Susilo',
                'password' => Hash::make('password'),
                'role' => 'petani',
                'phone' => '081233445566',
                'is_active' => true,
            ]
        );

        FarmerProfile::updateOrCreate(
            ['user_id' => $farmerSleman->id],
            [
                'farm_name' => 'Kelompok Tani Sleman Makmur',
                'farm_area_hectares' => 3.20,
                'primary_commodity' => 'Cabai',
                'address' => 'Jl. Kaliurang Km 12, Ngaglik, Sleman, DI Yogyakarta',
                'latitude' => -7.7123000,
                'longitude' => 110.3854000,
            ]
        );

        // Product Sleman 1: Cabai Merah Besar Sleman
        if (isset($commodities['Cabai'])) {
            $cabai = $commodities['Cabai'];
            $harvestSlemanCabai = $service->recordHarvest($farmerSleman, [
                'commodity_id' => $cabai->id,
                'quantity' => 1200,
                'unit' => 'kg',
                'harvest_date' => now()->subDays(2)->format('Y-m-d'),
                'quality' => 'Grade A (Super)',
                'location' => 'Ngaglik, Sleman, DI Yogyakarta',
                'notes' => 'Panen cabai merah segar petik pagi varietas TM99 lereng Merapi.',
            ]);

            $productSleman = Product::updateOrCreate(
                ['slug' => 'cabai-merah-besar-sleman-segar'],
                [
                    'user_id' => $farmerSleman->id,
                    'commodity_id' => $cabai->id,
                    'stock_id' => $harvestSlemanCabai->stock?->id,
                    'name' => 'Cabai Merah Besar Sleman Segar',
                    'description' => 'Cabai merah besar segar langsung dipetik dari lereng Gunung Merapi Sleman. Biji padat, kulit mulus mengkilap, dan pedas segar.',
                    'price' => 28000,
                    'stock' => 1200,
                    'unit' => 'kg',
                    'min_order' => 10,
                    'quality' => 'Grade A (Super)',
                    'harvest_date' => now()->subDays(2)->format('Y-m-d'),
                    'location' => 'Ngaglik, Sleman, DI Yogyakarta',
                    'latitude' => -7.7123000,
                    'longitude' => 110.3854000,
                    'status' => 'active',
                    'allow_negotiation' => true,
                ]
            );

            ProductImage::create([
                'product_id' => $productSleman->id,
                'image_path' => 'https://images.unsplash.com/photo-1588252303782-cb80119abd6d?w=800&auto=format&fit=crop&q=80',
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }

        // 2. Farmer in Bantul: Bu Wartini (Tani Berkah Imogiri)
        $farmerBantul = User::updateOrCreate(
            ['email' => 'petani.bantul@sintesa.id'],
            [
                'name' => 'Bu Wartini',
                'password' => Hash::make('password'),
                'role' => 'petani',
                'phone' => '081277889900',
                'is_active' => true,
            ]
        );

        FarmerProfile::updateOrCreate(
            ['user_id' => $farmerBantul->id],
            [
                'farm_name' => 'Tani Berkah Imogiri',
                'farm_area_hectares' => 2.80,
                'primary_commodity' => 'Jagung',
                'address' => 'Desa Karangtalun, Imogiri, Bantul, DI Yogyakarta',
                'latitude' => -7.8921000,
                'longitude' => 110.3412000,
            ]
        );

        // Product Bantul 1: Jagung Manis Madu Imogiri
        if (isset($commodities['Jagung'])) {
            $jagung = $commodities['Jagung'];
            $harvestBantulJagung = $service->recordHarvest($farmerBantul, [
                'commodity_id' => $jagung->id,
                'quantity' => 2500,
                'unit' => 'kg',
                'harvest_date' => now()->subDays(1)->format('Y-m-d'),
                'quality' => 'Grade A (Super)',
                'location' => 'Imogiri, Bantul, DI Yogyakarta',
                'notes' => 'Panen jagung manis madu kualitas istimewa brix tinggi.',
            ]);

            $productBantul = Product::updateOrCreate(
                ['slug' => 'jagung-manis-madu-imogiri'],
                [
                    'user_id' => $farmerBantul->id,
                    'commodity_id' => $jagung->id,
                    'stock_id' => $harvestBantulJagung->stock?->id,
                    'name' => 'Jagung Manis Madu Imogiri',
                    'description' => 'Jagung manis madu segar petik sore dari persawahan subur Bantul. Rasa manis alami sangat legit, cocok untuk konsumsi segar dan industri.',
                    'price' => 7800,
                    'stock' => 2500,
                    'unit' => 'kg',
                    'min_order' => 25,
                    'quality' => 'Grade A (Super)',
                    'harvest_date' => now()->subDays(1)->format('Y-m-d'),
                    'location' => 'Imogiri, Bantul, DI Yogyakarta',
                    'latitude' => -7.8921000,
                    'longitude' => 110.3412000,
                    'status' => 'active',
                    'allow_negotiation' => true,
                ]
            );

            ProductImage::create([
                'product_id' => $productBantul->id,
                'image_path' => 'https://images.unsplash.com/photo-1551754655-cd27e38d2076?w=800&auto=format&fit=crop&q=80',
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }

        // 3. Farmer in Kulon Progo: Pak Nyoman Subekti (Kebun Agro Menoreh)
        $farmerKulonProgo = User::updateOrCreate(
            ['email' => 'petani.kulonprogo@sintesa.id'],
            [
                'name' => 'Pak Nyoman Subekti',
                'password' => Hash::make('password'),
                'role' => 'petani',
                'phone' => '081399887766',
                'is_active' => true,
            ]
        );

        FarmerProfile::updateOrCreate(
            ['user_id' => $farmerKulonProgo->id],
            [
                'farm_name' => 'Kebun Agro Menoreh',
                'farm_area_hectares' => 4.50,
                'primary_commodity' => 'Kelapa',
                'address' => 'Pegunungan Menoreh, Samigaluh, Kulon Progo, DI Yogyakarta',
                'latitude' => -7.8601000,
                'longitude' => 110.1583000,
            ]
        );

        // Product Kulon Progo: Kelapa Daging Tebal Menoreh
        if (isset($commodities['Kelapa'])) {
            $kelapa = $commodities['Kelapa'];
            $harvestKelapa = $service->recordHarvest($farmerKulonProgo, [
                'commodity_id' => $kelapa->id,
                'quantity' => 1500,
                'unit' => 'butir',
                'harvest_date' => now()->subDays(4)->format('Y-m-d'),
                'quality' => 'Grade A (Super)',
                'location' => 'Samigaluh, Kulon Progo, DI Yogyakarta',
                'notes' => 'Panen kelapa tua butiran daging santan tebal dan gurih.',
            ]);

            $productKulonProgo = Product::updateOrCreate(
                ['slug' => 'kelapa-tua-daging-tebal-menoreh'],
                [
                    'user_id' => $farmerKulonProgo->id,
                    'commodity_id' => $kelapa->id,
                    'stock_id' => $harvestKelapa->stock?->id,
                    'name' => 'Kelapa Tua Daging Tebal Menoreh',
                    'description' => 'Kelapa tua kualitas super dengan ketebalan daging optimal dan kadar minyak santan tinggi. Dipetik langsung dari pohon varietas lokal Menoreh.',
                    'price' => 5500,
                    'stock' => 1500,
                    'unit' => 'butir',
                    'min_order' => 50,
                    'quality' => 'Grade A (Super)',
                    'harvest_date' => now()->subDays(4)->format('Y-m-d'),
                    'location' => 'Samigaluh, Kulon Progo, DI Yogyakarta',
                    'latitude' => -7.8601000,
                    'longitude' => 110.1583000,
                    'status' => 'active',
                    'allow_negotiation' => true,
                ]
            );

            ProductImage::create([
                'product_id' => $productKulonProgo->id,
                'image_path' => 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?w=800&auto=format&fit=crop&q=80',
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }
    }
}
