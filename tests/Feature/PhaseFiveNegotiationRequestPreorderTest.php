<?php

namespace Tests\Feature;

use App\Models\Commodity;
use App\Models\CommodityRequest;
use App\Models\Order;
use App\Models\Preorder;
use App\Models\PreorderItem;
use App\Models\PriceOffer;
use App\Models\Product;
use App\Models\RequestOffer;
use App\Models\User;
use Tests\TestCase;

class PhaseFiveNegotiationRequestPreorderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure farmer products and linked stocks have sufficient inventory
        $farmer = User::where('role', 'petani')->first();
        if ($farmer) {
            $products = Product::where('user_id', $farmer->id)->get();
            foreach ($products as $p) {
                $p->update(['stock' => 3000]);
                if ($p->stock_id) {
                    \App\Models\Stock::where('id', $p->stock_id)->update(['available_quantity' => 3000]);
                }
            }
        }
    }

    /**
     * 1. Test buyer can submit a price offer on a product.
     */
    public function test_buyer_can_submit_price_offer_on_product(): void
    {
        $collector = User::where('role', 'pengepul')->first();
        $farmer = User::where('role', 'petani')->first();
        $product = Product::where('user_id', $farmer->id)->where('status', 'active')->first();

        $this->assertNotNull($collector);
        $this->assertNotNull($product);

        $offeredPrice = round($product->price * 0.85);
        $qty = max(10, (float) $product->min_order);

        $response = $this->actingAs($collector)->post('/offers', [
            'product_id' => $product->id,
            'quantity' => $qty,
            'offered_price' => $offeredPrice,
            'shipping_method' => 'Ambil di Lokasi Petani',
            'notes' => 'Uji coba negosiasi harga Phase 5.',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('price_offers', [
            'product_id' => $product->id,
            'buyer_id' => $collector->id,
            'seller_id' => $farmer->id,
            'quantity' => $qty,
            'offered_price' => $offeredPrice,
            'status' => 'Menunggu',
        ]);
    }

    /**
     * 2. Test farmer can accept buyer's price offer -> creates official Order.
     */
    public function test_farmer_can_accept_buyer_price_offer_creating_official_order(): void
    {
        $collector = User::where('role', 'pengepul')->first();
        $farmer = User::where('role', 'petani')->first();
        $product = Product::where('user_id', $farmer->id)->where('status', 'active')->first();

        $qty = max(20, (float) $product->min_order);
        $offeredPrice = round($product->price * 0.90);

        $offer = PriceOffer::create([
            'product_id' => $product->id,
            'buyer_id' => $collector->id,
            'seller_id' => $farmer->id,
            'quantity' => $qty,
            'offered_price' => $offeredPrice,
            'original_price' => $product->price,
            'shipping_method' => 'Ambil di Lokasi Petani',
            'status' => 'Menunggu',
        ]);

        $response = $this->actingAs($farmer)->post("/farmer/negotiations/{$offer->id}/accept");

        $response->assertStatus(302);

        // Verify offer status updated
        $offer->refresh();
        $this->assertEquals('Selesai', $offer->status);

        // Verify Order created with source_type = negotiation
        $this->assertDatabaseHas('orders', [
            'buyer_id' => $collector->id,
            'seller_id' => $farmer->id,
            'source_type' => 'negotiation',
            'status' => 'Menunggu Konfirmasi',
            'total_amount' => $qty * $offeredPrice,
        ]);
    }

    /**
     * 3. Test farmer can counter offer and buyer can accept the counter offer.
     */
    public function test_farmer_can_counter_offer_and_buyer_can_accept_it(): void
    {
        $consumer = User::where('role', 'konsumen')->first();
        $farmer = User::where('role', 'petani')->first();
        $product = Product::where('user_id', $farmer->id)->where('status', 'active')->first();

        $qty = max(15, (float) $product->min_order);
        $offeredPrice = round($product->price * 0.75);
        $counterPrice = round($product->price * 0.88);

        $offer = PriceOffer::create([
            'product_id' => $product->id,
            'buyer_id' => $consumer->id,
            'seller_id' => $farmer->id,
            'quantity' => $qty,
            'offered_price' => $offeredPrice,
            'original_price' => $product->price,
            'shipping_method' => 'Pengiriman / Kurir',
            'status' => 'Menunggu',
        ]);

        // Farmer sends counter offer
        $counterResponse = $this->actingAs($farmer)->post("/farmer/negotiations/{$offer->id}/counter", [
            'counter_price' => $counterPrice,
            'notes' => 'Harga terbaik dari kami.',
        ]);

        $counterResponse->assertStatus(302);
        $offer->refresh();
        $this->assertEquals('Counter Offer', $offer->status);
        $this->assertEquals($counterPrice, (float) $offer->counter_price);

        // Buyer accepts counter offer
        $acceptResponse = $this->actingAs($consumer)->post("/offers/{$offer->id}/accept-counter");

        $acceptResponse->assertStatus(302);
        $offer->refresh();
        $this->assertEquals('Selesai', $offer->status);

        // Verify Order created at counter price
        $this->assertDatabaseHas('orders', [
            'buyer_id' => $consumer->id,
            'seller_id' => $farmer->id,
            'source_type' => 'negotiation',
            'total_amount' => $qty * $counterPrice,
        ]);
    }

    /**
     * 4. Test buyer (Pengepul / Konsumen) can create commodity request.
     */
    public function test_buyer_can_create_commodity_request(): void
    {
        $collector = User::where('role', 'pengepul')->first();
        $commodity = Commodity::first();

        $response = $this->actingAs($collector)->post('/requests', [
            'commodity_id' => $commodity->id,
            'title' => 'Kebutuhan Pasokan Jagung Pakan Ternak 5 Ton',
            'required_quantity' => 5000,
            'unit' => 'kg',
            'max_price' => 6500,
            'location' => 'Kandang Sentral Sleman',
            'deadline' => now()->addDays(20)->toDateString(),
            'description' => 'Jagung kering pipil kadar air max 14%.',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('commodity_requests', [
            'user_id' => $collector->id,
            'commodity_id' => $commodity->id,
            'title' => 'Kebutuhan Pasokan Jagung Pakan Ternak 5 Ton',
            'required_quantity' => 5000,
            'max_price' => 6500,
            'status' => 'Aktif',
        ]);
    }

    /**
     * 5. Test farmer cannot create commodity request (PRD Rule).
     */
    public function test_farmer_cannot_create_commodity_request(): void
    {
        $farmer = User::where('role', 'petani')->first();
        $commodity = Commodity::first();

        $response = $this->actingAs($farmer)->post('/requests', [
            'commodity_id' => $commodity->id,
            'title' => 'Permintaan Tidak Valid oleh Petani',
            'required_quantity' => 500,
            'unit' => 'kg',
            'max_price' => 10000,
            'location' => 'Desa Sleman',
            'deadline' => now()->addDays(10)->toDateString(),
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error');
    }

    /**
     * 6. Test farmer can submit supply offer for commodity request.
     */
    public function test_farmer_can_submit_supply_offer_for_commodity_request(): void
    {
        $consumer = User::where('role', 'konsumen')->first();
        $farmer = User::where('role', 'petani')->first();
        $commodity = Commodity::first();

        $request = CommodityRequest::create([
            'user_id' => $consumer->id,
            'commodity_id' => $commodity->id,
            'title' => 'Butuh Pasokan Tomat Acara Syukuran',
            'required_quantity' => 100,
            'unit' => 'kg',
            'max_price' => 16000,
            'location' => 'Dapur Katering Berkah',
            'deadline' => now()->addDays(5)->toDateString(),
            'status' => 'Aktif',
        ]);

        $response = $this->actingAs($farmer)->post("/farmer/requests/{$request->id}/offer", [
            'offered_quantity' => 100,
            'offered_price' => 15000,
            'shipping_method' => 'Pengiriman / Kurir',
            'notes' => 'Bisa diantar pagi hari pukul 06.00.',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('request_offers', [
            'commodity_request_id' => $request->id,
            'farmer_id' => $farmer->id,
            'offered_quantity' => 100,
            'offered_price' => 15000,
            'status' => 'Menunggu',
        ]);

        $request->refresh();
        $this->assertEquals('Mendapat Penawaran', $request->status);
    }

    /**
     * 7. Test buyer accepts supply offer -> Order created with source_type = commodity_request.
     */
    public function test_buyer_accepts_supply_offer_creating_official_order(): void
    {
        $consumer = User::where('role', 'konsumen')->first();
        $farmer = User::where('role', 'petani')->first();
        $commodity = Commodity::first();

        $request = CommodityRequest::create([
            'user_id' => $consumer->id,
            'commodity_id' => $commodity->id,
            'title' => 'Pengadaan Cabai Rawit Restoran',
            'required_quantity' => 50,
            'unit' => 'kg',
            'max_price' => 45000,
            'location' => 'Resto Sedap Rasa',
            'deadline' => now()->addDays(8)->toDateString(),
            'status' => 'Mendapat Penawaran',
        ]);

        $offer = RequestOffer::create([
            'commodity_request_id' => $request->id,
            'farmer_id' => $farmer->id,
            'offered_quantity' => 50,
            'offered_price' => 43000,
            'shipping_method' => 'Ambil di Lokasi Petani',
            'status' => 'Menunggu',
        ]);

        $response = $this->actingAs($consumer)->post("/requests/offers/{$offer->id}/accept");

        $response->assertStatus(302);

        // Verify offer accepted & request fulfilled
        $offer->refresh();
        $request->refresh();
        $this->assertEquals('Diterima', $offer->status);
        $this->assertEquals('Dipenuhi', $request->status);

        // Verify Order created
        $this->assertDatabaseHas('orders', [
            'buyer_id' => $consumer->id,
            'seller_id' => $farmer->id,
            'source_type' => 'commodity_request',
            'status' => 'Menunggu Konfirmasi',
            'total_amount' => 50 * 43000,
        ]);
    }

    /**
     * 8. Test farmer can create preorder campaign.
     */
    public function test_farmer_can_create_preorder_campaign(): void
    {
        $farmer = User::where('role', 'petani')->first();
        $commodity = Commodity::first();

        $response = $this->actingAs($farmer)->post('/farmer/preorders', [
            'commodity_id' => $commodity->id,
            'title' => 'Pre-Order Bawang Merah Super Masa Panen Desember',
            'estimated_production' => 2000,
            'price' => 28000,
            'unit' => 'kg',
            'min_order' => 20,
            'estimated_harvest_date' => now()->addDays(40)->toDateString(),
            'location' => 'Lahan Agro Bantul',
            'description' => 'Bibit varietas bauji dengan mutu tinggi.',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('preorders', [
            'user_id' => $farmer->id,
            'commodity_id' => $commodity->id,
            'title' => 'Pre-Order Bawang Merah Super Masa Panen Desember',
            'estimated_production' => 2000,
            'preorder_available_quantity' => 2000,
            'price' => 28000,
            'status' => 'Dibuka',
        ]);
    }

    /**
     * 9. Test buyer books preorder quota with capacity deduction.
     */
    public function test_buyer_books_preorder_quota_with_capacity_deduction(): void
    {
        $collector = User::where('role', 'pengepul')->first();
        $farmer = User::where('role', 'petani')->first();
        $commodity = Commodity::first();

        $preorder = Preorder::create([
            'user_id' => $farmer->id,
            'commodity_id' => $commodity->id,
            'title' => 'Pre-Order Jagung Panen Raya',
            'slug' => 'pre-order-jagung-' . \Illuminate\Support\Str::random(8),
            'price' => 8000,
            'estimated_production' => 1000,
            'preorder_available_quantity' => 1000,
            'min_order' => 50,
            'unit' => 'kg',
            'estimated_harvest_date' => now()->addDays(30)->toDateString(),
            'location' => 'Seyegan, Sleman',
            'status' => 'Dibuka',
        ]);

        $response = $this->actingAs($collector)->post("/preorders/{$preorder->slug}/book", [
            'quantity' => 300,
            'shipping_method' => 'Ambil di Lokasi Petani',
            'shipping_address' => 'Gudang Pengepul Bantul',
            'notes' => 'Booking 300kg.',
        ]);

        $response->assertStatus(302);

        // Verify booking item created
        $this->assertDatabaseHas('preorder_items', [
            'preorder_id' => $preorder->id,
            'buyer_id' => $collector->id,
            'quantity' => 300,
            'total_amount' => 300 * 8000,
            'status' => 'Menunggu Panen',
        ]);

        // Verify preorder capacity decremented
        $preorder->refresh();
        $this->assertEquals(700, (float) $preorder->preorder_available_quantity);
    }

    /**
     * 10. Test preorder booking fails if exceeding available capacity.
     */
    public function test_preorder_booking_fails_if_exceeds_available_capacity(): void
    {
        $consumer = User::where('role', 'konsumen')->first();
        $farmer = User::where('role', 'petani')->first();
        $commodity = Commodity::first();

        $preorder = Preorder::create([
            'user_id' => $farmer->id,
            'commodity_id' => $commodity->id,
            'title' => 'Pre-Order Terbatas',
            'slug' => 'pre-order-terbatas-' . \Illuminate\Support\Str::random(8),
            'price' => 20000,
            'estimated_production' => 100,
            'preorder_available_quantity' => 100,
            'min_order' => 5,
            'unit' => 'kg',
            'estimated_harvest_date' => now()->addDays(20)->toDateString(),
            'location' => 'Sleman',
            'status' => 'Dibuka',
        ]);

        // Attempt to book 150 kg (available only 100 kg)
        $response = $this->actingAs($consumer)->post("/preorders/{$preorder->slug}/book", [
            'quantity' => 150,
            'shipping_method' => 'Pengiriman / Kurir',
            'shipping_address' => 'Alamat Pengiriman',
        ]);

        // Form validation max rule rejects it
        $response->assertStatus(302);
        $response->assertSessionHasErrors('quantity');

        // Verify available capacity untouched
        $preorder->refresh();
        $this->assertEquals(100, (float) $preorder->preorder_available_quantity);
    }

    /**
     * 11. Test farmer marks preorder ready auto-converting booked items to official orders.
     */
    public function test_farmer_marks_preorder_ready_auto_converting_to_order(): void
    {
        $collector = User::where('role', 'pengepul')->first();
        $farmer = User::where('role', 'petani')->first();
        $commodity = Commodity::first();

        $preorder = Preorder::create([
            'user_id' => $farmer->id,
            'commodity_id' => $commodity->id,
            'title' => 'Pre-Order Panen Cepat',
            'slug' => 'pre-order-panen-cepat-' . \Illuminate\Support\Str::random(8),
            'price' => 10000,
            'estimated_production' => 500,
            'preorder_available_quantity' => 300,
            'min_order' => 10,
            'unit' => 'kg',
            'estimated_harvest_date' => now()->addDays(5)->toDateString(),
            'location' => 'Sleman',
            'status' => 'Menunggu Panen',
        ]);

        $item = PreorderItem::create([
            'preorder_id' => $preorder->id,
            'buyer_id' => $collector->id,
            'quantity' => 200,
            'price_per_unit' => 10000,
            'total_amount' => 2000000,
            'shipping_address' => 'Gudang Pengepul',
            'shipping_method' => 'Ambil di Lokasi Petani',
            'status' => 'Menunggu Panen',
        ]);

        // Farmer updates status to 'Siap Diproses'
        $response = $this->actingAs($farmer)->post("/farmer/preorders/{$preorder->id}/status", [
            'status' => 'Siap Diproses',
        ]);

        $response->assertStatus(302);

        $item->refresh();
        $this->assertNotNull($item->order_id);
        $this->assertEquals('Dikonfirmasi', $item->status);

        // Verify Order created in orders table
        $this->assertDatabaseHas('orders', [
            'id' => $item->order_id,
            'buyer_id' => $collector->id,
            'seller_id' => $farmer->id,
            'source_type' => 'preorder',
            'status' => 'Dikonfirmasi',
            'total_amount' => 2000000,
        ]);
    }

    /**
     * 12. Test buyer can cancel preorder booking and capacity is restored.
     */
    public function test_buyer_can_cancel_preorder_restoring_capacity(): void
    {
        $consumer = User::where('role', 'konsumen')->first();
        $farmer = User::where('role', 'petani')->first();
        $commodity = Commodity::first();

        $preorder = Preorder::create([
            'user_id' => $farmer->id,
            'commodity_id' => $commodity->id,
            'title' => 'Pre-Order Uji Batal',
            'slug' => 'pre-order-uji-batal-' . \Illuminate\Support\Str::random(8),
            'price' => 15000,
            'estimated_production' => 500,
            'preorder_available_quantity' => 450,
            'min_order' => 10,
            'unit' => 'kg',
            'estimated_harvest_date' => now()->addDays(25)->toDateString(),
            'location' => 'Sleman',
            'status' => 'Dibuka',
        ]);

        $item = PreorderItem::create([
            'preorder_id' => $preorder->id,
            'buyer_id' => $consumer->id,
            'quantity' => 50,
            'price_per_unit' => 15000,
            'total_amount' => 750000,
            'shipping_address' => 'Rumah Konsumen',
            'shipping_method' => 'Pengiriman / Kurir',
            'status' => 'Menunggu Panen',
        ]);

        $response = $this->actingAs($consumer)->post("/my-preorders/{$item->id}/cancel");

        $response->assertStatus(302);

        $item->refresh();
        $preorder->refresh();

        $this->assertEquals('Dibatalkan', $item->status);
        $this->assertEquals(500, (float) $preorder->preorder_available_quantity);
    }
}
