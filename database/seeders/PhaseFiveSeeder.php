<?php

namespace Database\Seeders;

use App\Models\Commodity;
use App\Models\CommodityRequest;
use App\Models\Notification;
use App\Models\Preorder;
use App\Models\PreorderItem;
use App\Models\PriceOffer;
use App\Models\Product;
use App\Models\RequestOffer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PhaseFiveSeeder extends Seeder
{
    public function run(): void
    {
        $farmer = User::where('role', 'petani')->first();
        $collector = User::where('role', 'pengepul')->first();
        $consumer = User::where('role', 'konsumen')->first();

        if (!$farmer || !$collector || !$consumer) {
            return;
        }

        $cabai = Commodity::where('slug', 'cabai')->first() ?? Commodity::first();
        $tomat = Commodity::where('slug', 'tomat')->first() ?? Commodity::skip(1)->first();
        $jagung = Commodity::where('slug', 'jagung')->first() ?? Commodity::skip(2)->first();

        // 1. Seed Commodity Requests
        $req1 = CommodityRequest::create([
            'user_id' => $collector->id,
            'commodity_id' => $cabai->id,
            'title' => 'Kebutuhan Pasokan Cabai Rawit Merah Pengiriman Mingguan',
            'required_quantity' => 1000,
            'unit' => 'kg',
            'max_price' => 45000,
            'location' => 'Gudang Pusat Pengepul, Sleman, DI Yogyakarta',
            'deadline' => now()->addDays(10)->toDateString(),
            'description' => 'Dibutuhkan cabai rawit merah petik segar dengan kadar air rendah, kemasan karung berventilasi 50kg. Siap ditimbang langsung di gudang.',
            'status' => 'Mendapat Penawaran',
        ]);

        RequestOffer::create([
            'commodity_request_id' => $req1->id,
            'farmer_id' => $farmer->id,
            'offered_quantity' => 800,
            'offered_price' => 42000,
            'shipping_method' => 'Ambil di Lokasi Petani',
            'notes' => 'Pasokan dari kebun lereng Merapi, siap dipetik segar H-1 sebelum penyerahan.',
            'status' => 'Menunggu',
        ]);

        $req2 = CommodityRequest::create([
            'user_id' => $consumer->id,
            'commodity_id' => $tomat->id,
            'title' => 'Pengadaan Tomat Segar Kualitas Super untuk Katering',
            'required_quantity' => 80,
            'unit' => 'kg',
            'max_price' => 15000,
            'location' => 'Dapur Katering Berkah, Jl. Kaliurang KM 7, Sleman',
            'deadline' => now()->addDays(7)->toDateString(),
            'description' => 'Tomat merah mulus segar tanpa cacat, ukuran sedang seragam untuk kebutuhan olahan sayur dan salad acara.',
            'status' => 'Mendapat Penawaran',
        ]);

        RequestOffer::create([
            'commodity_request_id' => $req2->id,
            'farmer_id' => $farmer->id,
            'offered_quantity' => 80,
            'offered_price' => 13500,
            'shipping_method' => 'Pengiriman / Kurir',
            'notes' => 'Siap diantar pagi hari pukul 07.00 WIB dalam peti kayu beralas daun pisang.',
            'status' => 'Menunggu',
        ]);

        CommodityRequest::create([
            'user_id' => $collector->id,
            'commodity_id' => $jagung->id,
            'title' => 'Pasokan Jagung Manis Segar Partai Besar',
            'required_quantity' => 2500,
            'unit' => 'kg',
            'max_price' => 9000,
            'location' => 'Sentra Distribusi Pengepul Bantul',
            'deadline' => now()->addDays(14)->toDateString(),
            'description' => 'Mencari pasokan jagung manis kupas sebagian, tongkol padat dan biji rata untuk distribusi pasar induk.',
            'status' => 'Aktif',
        ]);

        // 2. Seed Price Offers / Negotiations
        $farmerProduct = Product::where('user_id', $farmer->id)->where('status', 'active')->first();
        if ($farmerProduct) {
            // Buyer (Collector) submits price offer
            PriceOffer::create([
                'product_id' => $farmerProduct->id,
                'buyer_id' => $collector->id,
                'seller_id' => $farmer->id,
                'quantity' => max(50, (float) $farmerProduct->min_order * 2),
                'offered_price' => round($farmerProduct->price * 0.88),
                'original_price' => $farmerProduct->price,
                'shipping_method' => 'Ambil di Lokasi Petani',
                'notes' => 'Tawaran untuk pengambilan langsung dengan armada pikap kami.',
                'status' => 'Menunggu',
            ]);

            // Buyer (Consumer) has counter offer from farmer
            PriceOffer::create([
                'product_id' => $farmerProduct->id,
                'buyer_id' => $consumer->id,
                'seller_id' => $farmer->id,
                'quantity' => max(10, (float) $farmerProduct->min_order),
                'offered_price' => round($farmerProduct->price * 0.80),
                'original_price' => $farmerProduct->price,
                'counter_price' => round($farmerProduct->price * 0.92),
                'shipping_method' => 'Pengiriman / Kurir',
                'notes' => 'Bisa dipertimbangkan untuk diskon volume ini, penawaran balik Rp ' . number_format(round($farmerProduct->price * 0.92), 0, ',', '.'),
                'status' => 'Counter Offer',
            ]);
        }

        // 3. Seed Pre-Orders
        $po1 = Preorder::create([
            'user_id' => $farmer->id,
            'commodity_id' => $jagung->id,
            'title' => 'Pre-Order Jagung Manis Hibrida Masa Panen Raya Oktober',
            'slug' => 'pre-order-jagung-manis-hibrida-' . Str::random(5),
            'description' => 'Varietas jagung manis hibrida unggul dengan rasa manis alami brix tinggi. Cocok untuk industri kuliner, rebusan, dan pasar swalayan. Panen serentak diproyeksikan akhir bulan.',
            'price' => 8500,
            'estimated_production' => 3000,
            'preorder_available_quantity' => 2500,
            'min_order' => 50,
            'unit' => 'kg',
            'estimated_harvest_date' => now()->addDays(28)->toDateString(),
            'location' => 'Lahan Blok C, Desa Margodadi, Seyegan, Sleman',
            'image' => '/assets/images/commodities/jagung.jpg',
            'status' => 'Dibuka',
        ]);

        PreorderItem::create([
            'preorder_id' => $po1->id,
            'buyer_id' => $collector->id,
            'quantity' => 500,
            'price_per_unit' => 8500,
            'total_amount' => 500 * 8500,
            'shipping_address' => 'Gudang Sentral Pengepul Sleman',
            'shipping_method' => 'Ambil di Lokasi Petani',
            'notes' => 'Alokasi awal kuota pengiriman minggu pertama panen.',
            'status' => 'Menunggu Panen',
        ]);

        $po2 = Preorder::create([
            'user_id' => $farmer->id,
            'commodity_id' => $cabai->id,
            'title' => 'Pre-Order Cabai Rawit Merah Dataran Tinggi Musim Panen Depan',
            'slug' => 'pre-order-cabai-rawit-merah-dataran-tinggi-' . Str::random(5),
            'description' => 'Budidaya cabai rawit merah semi-organik di dataran tinggi. Tingkat kepedasan maksimal, daya tahan simpan hingga 8 hari pasca panen.',
            'price' => 38000,
            'estimated_production' => 1200,
            'preorder_available_quantity' => 1050,
            'min_order' => 15,
            'unit' => 'kg',
            'estimated_harvest_date' => now()->addDays(35)->toDateString(),
            'location' => 'Kebun Agro Merapi, Cangkringan, Sleman',
            'image' => '/assets/images/commodities/cabai.jpg',
            'status' => 'Dibuka',
        ]);

        PreorderItem::create([
            'preorder_id' => $po2->id,
            'buyer_id' => $consumer->id,
            'quantity' => 150,
            'price_per_unit' => 38000,
            'total_amount' => 150 * 38000,
            'shipping_address' => 'Resto & Katering Berkah Yogyakarta',
            'shipping_method' => 'Pengiriman / Kurir',
            'notes' => 'Mohon dipacking per 10kg karung plastik jaring.',
            'status' => 'Menunggu Panen',
        ]);
    }
}
