<?php

namespace Database\Seeders;

use App\Models\Commodity;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $farmer = User::where('role', 'petani')->first();

        if (!$farmer) {
            $this->command->warn('Petani tidak ditemukan. Pastikan UserSeeder sudah dijalankan terlebih dahulu.');
            return;
        }

        $commodities = Commodity::all()->keyBy('name');

        $sampleProducts = [
            // 1. Jagung
            [
                'commodity_name' => 'Jagung',
                'name' => 'Jagung Hibrida Bisi-18 Super',
                'slug' => 'jagung-hibrida-bisi-18-super',
                'description' => "Jagung hibrida varietas Bisi-18 berkualitas super dengan biji padat dan kadar air rendah (<14%). Cocok untuk pakan ternak berkualitas tinggi maupun bahan baku industri pakan dan olahan pangan. Dipanen langsung dari lahan subur Pujon Malang.",
                'price' => 7500,
                'stock' => 3500,
                'unit' => 'kg',
                'min_order' => 50,
                'quality' => 'Grade A (Super)',
                'harvest_date' => now()->subDays(5)->format('Y-m-d'),
                'location' => 'Kec. Pujon, Kab. Malang, Jawa Timur',
                'latitude' => -7.8423,
                'longitude' => 112.4632,
                'status' => 'active',
                'allow_negotiation' => true,
                'images' => [
                    'https://images.unsplash.com/photo-1551754655-cd27e38d2076?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1598170845058-32b9d6a5da37?w=800&auto=format&fit=crop&q=80',
                ]
            ],
            [
                'commodity_name' => 'Jagung',
                'name' => 'Jagung Manis Segar Kupas',
                'slug' => 'jagung-manis-segar-kupas',
                'description' => "Jagung manis segar petik pagi hari dengan tingkat kemanisan (Brix) tinggi. Biji kuning bersih, renyah, dan cocok untuk konsumsi rumah tangga, hotel, restoran, dan katering.",
                'price' => 8500,
                'stock' => 750,
                'unit' => 'kg',
                'min_order' => 5,
                'quality' => 'Grade A (Super)',
                'harvest_date' => now()->subDays(1)->format('Y-m-d'),
                'location' => 'Kec. Dau, Kab. Malang, Jawa Timur',
                'latitude' => -7.9351,
                'longitude' => 112.5714,
                'status' => 'active',
                'allow_negotiation' => true,
                'images' => [
                    'https://images.unsplash.com/photo-1551754655-cd27e38d2076?w=800&auto=format&fit=crop&q=80',
                ]
            ],

            // 2. Cabai
            [
                'commodity_name' => 'Cabai',
                'name' => 'Cabai Rawit Merah Segar (Ori 212)',
                'slug' => 'cabai-rawit-merah-segar-ori-212',
                'description' => "Cabai rawit merah varietas Ori 212 asli lereng pegunungan. Tingkat kepedasan sangat tajam, kulit tebal, dan memiliki daya simpan hingga 10 hari tanpa pendingin. Dipetik dalam kondisi merah merona 90%.",
                'price' => 45000,
                'stock' => 450,
                'unit' => 'kg',
                'min_order' => 5,
                'quality' => 'Grade A (Super)',
                'harvest_date' => now()->subDays(2)->format('Y-m-d'),
                'location' => 'Kec. Ngantang, Kab. Malang, Jawa Timur',
                'latitude' => -7.8286,
                'longitude' => 112.3804,
                'status' => 'active',
                'allow_negotiation' => true,
                'images' => [
                    'https://images.unsplash.com/photo-1588252303782-cb80119abd6d?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=800&auto=format&fit=crop&q=80',
                ]
            ],
            [
                'commodity_name' => 'Cabai',
                'name' => 'Cabai Merah Keriting Grade A',
                'slug' => 'cabai-merah-keriting-grade-a',
                'description' => "Cabai merah keriting pilihan, panjang rata-rata 13-15 cm, tidak layu dan bebas patek / antraknosa. Pasokan utama untuk kebutuhan pedagang grosir pasar induk dan pabrik bumbu olahan.",
                'price' => 38000,
                'stock' => 600,
                'unit' => 'kg',
                'min_order' => 10,
                'quality' => 'Grade A (Super)',
                'harvest_date' => now()->subDays(3)->format('Y-m-d'),
                'location' => 'Kec. Bumiaji, Kota Batu, Jawa Timur',
                'latitude' => -7.8312,
                'longitude' => 112.5432,
                'status' => 'active',
                'allow_negotiation' => true,
                'images' => [
                    'https://images.unsplash.com/photo-1588252303782-cb80119abd6d?w=800&auto=format&fit=crop&q=80',
                ]
            ],

            // 3. Tomat
            [
                'commodity_name' => 'Tomat',
                'name' => 'Tomat Buah Segar Dataran Tinggi',
                'slug' => 'tomat-buah-segar-dataran-tinggi',
                'description' => "Tomat buah segar varietas Servo dengan daging tebal, padat, dan warna merah merata. Dipetik saat matang pohon sehingga rasa asam manisnya seimbang dan kandungan likopen optimal.",
                'price' => 12000,
                'stock' => 1200,
                'unit' => 'kg',
                'min_order' => 10,
                'quality' => 'Grade A (Super)',
                'harvest_date' => now()->subDays(1)->format('Y-m-d'),
                'location' => 'Kec. Pujon, Kab. Malang, Jawa Timur',
                'latitude' => -7.8465,
                'longitude' => 112.4701,
                'status' => 'active',
                'allow_negotiation' => true,
                'images' => [
                    'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?w=800&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1546470427-e26264be0b11?w=800&auto=format&fit=crop&q=80',
                ]
            ],
            [
                'commodity_name' => 'Tomat',
                'name' => 'Tomat Ceri Hidroponik Organik',
                'slug' => 'tomat-ceri-hidroponik-organik',
                'description' => "Tomat ceri manis yang dibudidayakan secara hidroponik ramah lingkungan tanpa pestisida kimia sintetis. Sangat lezat untuk salad dan konsumsi segar sehat.",
                'price' => 25000,
                'stock' => 250,
                'unit' => 'kg',
                'min_order' => 2,
                'quality' => 'Organik Premium',
                'harvest_date' => now()->format('Y-m-d'),
                'location' => 'Kec. Bumiaji, Kota Batu, Jawa Timur',
                'latitude' => -7.8201,
                'longitude' => 112.5321,
                'status' => 'active',
                'allow_negotiation' => false,
                'images' => [
                    'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?w=800&auto=format&fit=crop&q=80',
                ]
            ],

            // 4. Kelapa
            [
                'commodity_name' => 'Kelapa',
                'name' => 'Kelapa Tua Butiran Siap Santan',
                'slug' => 'kelapa-tua-butiran-siap-santan',
                'description' => "Kelapa tua butiran pilihan dengan daging buah tebal dan kadar minyak kelapa tinggi. Sangat cocok untuk pedagang santan, pembuatan minyak kelapa tradisional, dan industri kuliner.",
                'price' => 9000,
                'stock' => 800,
                'unit' => 'butir',
                'min_order' => 20,
                'quality' => 'Grade A (Super)',
                'harvest_date' => now()->subDays(6)->format('Y-m-d'),
                'location' => 'Kec. Rogojampi, Kab. Banyuwangi, Jawa Timur',
                'latitude' => -8.3045,
                'longitude' => 114.2982,
                'status' => 'active',
                'allow_negotiation' => true,
                'images' => [
                    'https://images.unsplash.com/photo-1544378730-8b5104b18790?w=800&auto=format&fit=crop&q=80',
                ]
            ],

            // 5. Padi / Beras
            [
                'commodity_name' => 'Padi',
                'name' => 'Beras Pandan Wangi Organik Super',
                'slug' => 'beras-pandan-wangi-organik-super',
                'description' => "Beras pandan wangi asli hasil panen sawah organik beririgasi mata air pegunungan. Pulen, beraroma wangi alami khas pandan tanpa pemutih maupun pengawet sintetis.",
                'price' => 17500,
                'stock' => 2000,
                'unit' => 'kg',
                'min_order' => 10,
                'quality' => 'Organik Premium',
                'harvest_date' => now()->subDays(10)->format('Y-m-d'),
                'location' => 'Kec. Sawangan, Kab. Magelang, Jawa Tengah',
                'latitude' => -7.5432,
                'longitude' => 110.3214,
                'status' => 'active',
                'allow_negotiation' => true,
                'images' => [
                    'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=800&auto=format&fit=crop&q=80',
                ]
            ],
        ];

        foreach ($sampleProducts as $item) {
            $commodity = $commodities->get($item['commodity_name']);
            if (!$commodity) {
                continue;
            }

            $images = $item['images'];
            unset($item['commodity_name'], $item['images']);

            $item['user_id'] = $farmer->id;
            $item['commodity_id'] = $commodity->id;

            $product = Product::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );

            // Seed images
            $product->images()->delete();
            foreach ($images as $index => $imgUrl) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imgUrl,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }
    }
}
