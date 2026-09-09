<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Transaction;
use App\Models\User;
use Tests\TestCase;

class PhaseFourOrderTransactionTest extends TestCase
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
                    Stock::where('id', $p->stock_id)->update(['available_quantity' => 3000]);
                }
            }
        }
    }

    /**
     * Test buyer (Collector / Consumer) can create direct purchase order.
     */
    public function test_buyer_can_create_direct_order(): void
    {
        $consumer = User::where('role', 'konsumen')->first();
        $farmer = User::where('role', 'petani')->first();
        $product = Product::where('user_id', $farmer->id)->where('stock', '>=', 100)->first();

        $this->assertNotNull($consumer);
        $this->assertNotNull($product);

        $initialProductStock = (float) $product->stock;
        $orderQty = max(50, (float) $product->min_order);

        $response = $this->actingAs($consumer)->post('/orders', [
            'product_id' => $product->id,
            'quantity' => $orderQty,
            'shipping_method' => 'Ambil di Lokasi Petani',
            'shipping_address' => 'Jl. Uji Coba No. 1, Kediri',
            'payment_method' => 'Tunai / COD saat Timbang',
            'notes' => 'Pengujian pesanan langsung Phase 4.',
        ]);

        $response->assertStatus(302);

        // Verify order created in database
        $this->assertDatabaseHas('orders', [
            'buyer_id' => $consumer->id,
            'seller_id' => $farmer->id,
            'status' => 'Menunggu Konfirmasi',
            'payment_status' => 'Belum Dibayar',
        ]);

        $order = Order::where('buyer_id', $consumer->id)->latest('id')->first();
        $this->assertNotNull($order);

        // Verify Order Items
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $orderQty,
        ]);

        // Verify Transaction record created
        $this->assertDatabaseHas('transactions', [
            'order_id' => $order->id,
            'buyer_id' => $consumer->id,
            'seller_id' => $farmer->id,
            'payment_status' => 'pending',
        ]);

        // Verify notification sent to farmer
        $this->assertDatabaseHas('notifications', [
            'user_id' => $farmer->id,
            'type' => 'order_created',
        ]);

        // Verify product stock decremented
        $product->refresh();
        $this->assertEquals($initialProductStock - $orderQty, (float) $product->stock);
    }

    /**
     * Test farmer cannot buy their own product (self-purchase prevention).
     */
    public function test_farmer_cannot_purchase_own_product(): void
    {
        $farmer = User::where('role', 'petani')->first();
        $product = Product::where('user_id', $farmer->id)->first();

        $this->assertNotNull($farmer);
        $this->assertNotNull($product);

        $response = $this->actingAs($farmer)->post('/orders', [
            'product_id' => $product->id,
            'quantity' => max(50, (float) $product->min_order),
            'shipping_method' => 'Ambil di Lokasi Petani',
            'shipping_address' => 'Kebun sendiri',
            'payment_method' => 'Transfer Bank / Rekber SINTESA',
        ]);

        $response->assertSessionHas('error');
    }

    /**
     * Test cannot order exceeding product stock.
     */
    public function test_cannot_order_exceeding_stock(): void
    {
        $consumer = User::where('role', 'konsumen')->first();
        $farmer = User::where('role', 'petani')->first();
        $product = Product::where('user_id', $farmer->id)->first();

        $this->assertNotNull($consumer);
        $this->assertNotNull($product);

        $excessiveQty = (float) $product->stock + 50000;

        $response = $this->actingAs($consumer)->post('/orders', [
            'product_id' => $product->id,
            'quantity' => $excessiveQty,
            'shipping_method' => 'Ambil di Lokasi Petani',
            'shipping_address' => 'Jl. Tes Kediri',
            'payment_method' => 'Transfer Bank / Rekber SINTESA',
        ]);

        $response->assertSessionHas('error');
    }

    /**
     * Test farmer can view incoming orders list and detail.
     */
    public function test_farmer_can_view_incoming_orders(): void
    {
        $farmer = User::where('role', 'petani')->first();
        $this->assertNotNull($farmer);

        $response = $this->actingAs($farmer)->get('/farmer/orders');
        $response->assertStatus(200);
        $response->assertSee('Pesanan Masuk dari Pembeli');

        $order = Order::where('seller_id', $farmer->id)->first();
        if ($order) {
            $showResponse = $this->actingAs($farmer)->get('/farmer/orders/' . $order->id);
            $showResponse->assertStatus(200);
            $showResponse->assertSee($order->order_number);
        }
    }

    /**
     * Test farmer order lifecycle: Confirm -> Process -> Complete.
     */
    public function test_order_full_processing_lifecycle(): void
    {
        $farmer = User::where('role', 'petani')->first();
        $consumer = User::where('role', 'konsumen')->first();
        $product = Product::where('user_id', $farmer->id)->where('stock', '>=', 100)->first();

        $qty = max(50, (float) $product->min_order);

        // 1. Create order
        $orderService = app(\App\Services\OrderService::class);
        $order = $orderService->createDirectOrder($consumer, $product, [
            'quantity' => $qty,
            'shipping_method' => 'Ambil di Lokasi Petani',
            'shipping_address' => 'Kediri',
            'payment_method' => 'Transfer Bank / Rekber SINTESA',
        ]);

        $this->assertEquals('Menunggu Konfirmasi', $order->status);

        // 2. Farmer confirms order
        $confirmResponse = $this->actingAs($farmer)->post("/farmer/orders/{$order->id}/confirm");
        $confirmResponse->assertStatus(302);
        $order->refresh();
        $this->assertEquals('Dikonfirmasi', $order->status);

        // 3. Farmer processes order
        $processResponse = $this->actingAs($farmer)->post("/farmer/orders/{$order->id}/process");
        $processResponse->assertStatus(302);
        $order->refresh();
        $this->assertEquals('Diproses', $order->status);

        // 4. Buyer confirms order received (completes transaction)
        $completeResponse = $this->actingAs($consumer)->post("/orders/{$order->id}/receive");
        $completeResponse->assertStatus(302);
        $order->refresh();
        $this->assertEquals('Selesai', $order->status);
        $this->assertEquals('Sudah Dibayar', $order->payment_status);

        // Transaction updated to paid
        $this->assertDatabaseHas('transactions', [
            'order_id' => $order->id,
            'payment_status' => 'paid',
        ]);
    }

    /**
     * Test buyer can cancel order when status is 'Menunggu Konfirmasi' and stock is restored.
     */
    public function test_buyer_can_cancel_pending_order_and_stock_is_restored(): void
    {
        $farmer = User::where('role', 'petani')->first();
        $consumer = User::where('role', 'konsumen')->first();
        $product = Product::where('user_id', $farmer->id)->where('stock', '>=', 100)->first();

        $initialProductStock = (float) $product->stock;
        $qty = max(50, (float) $product->min_order);

        $orderService = app(\App\Services\OrderService::class);
        $order = $orderService->createDirectOrder($consumer, $product, [
            'quantity' => $qty,
            'shipping_method' => 'Ambil di Lokasi Petani',
            'shipping_address' => 'Kediri',
            'payment_method' => 'Transfer Bank / Rekber SINTESA',
        ]);

        $product->refresh();
        $this->assertEquals($initialProductStock - $qty, (float) $product->stock);

        // Buyer cancels order
        $cancelResponse = $this->actingAs($consumer)->post("/orders/{$order->id}/cancel", [
            'reason' => 'Perubahan kuantitas kebutuhan acara keluarga.',
        ]);

        $cancelResponse->assertStatus(302);
        $order->refresh();
        $this->assertEquals('Dibatalkan', $order->status);
        $this->assertEquals('Perubahan kuantitas kebutuhan acara keluarga.', $order->cancellation_reason);

        // Verify product stock is restored
        $product->refresh();
        $this->assertEquals($initialProductStock, (float) $product->stock);
    }

    /**
     * Test farmer can reject order with reason and stock is restored.
     */
    public function test_farmer_can_reject_order_and_stock_is_restored(): void
    {
        $farmer = User::where('role', 'petani')->first();
        $collector = User::where('role', 'pengepul')->first();
        $product = Product::where('user_id', $farmer->id)->where('stock', '>=', 100)->first();

        $initialStock = (float) $product->stock;
        $qty = max(50, (float) $product->min_order);

        $orderService = app(\App\Services\OrderService::class);
        $order = $orderService->createDirectOrder($collector, $product, [
            'quantity' => $qty,
            'shipping_method' => 'Ambil di Lokasi Petani',
            'shipping_address' => 'Gudang Pengepul',
            'payment_method' => 'Transfer Bank / Rekber SINTESA',
        ]);

        // Farmer rejects order
        $rejectResponse = $this->actingAs($farmer)->post("/farmer/orders/{$order->id}/reject", [
            'reason' => 'Kadar air komoditas belum mencapai standar petik pembeli.',
        ]);

        $rejectResponse->assertStatus(302);
        $order->refresh();
        $this->assertEquals('Dibatalkan', $order->status);

        // Product stock restored
        $product->refresh();
        $this->assertEquals($initialStock, (float) $product->stock);
    }

    /**
     * Test authorization boundaries: third party cannot view or manipulate order.
     */
    public function test_third_party_cannot_view_or_cancel_order(): void
    {
        $farmer = User::where('role', 'petani')->first();
        $consumer = User::where('role', 'konsumen')->first();
        $collector = User::where('role', 'pengepul')->first();
        $product = Product::where('user_id', $farmer->id)->where('stock', '>=', 100)->first();

        $qty = max(50, (float) $product->min_order);

        $orderService = app(\App\Services\OrderService::class);
        $order = $orderService->createDirectOrder($consumer, $product, [
            'quantity' => $qty,
            'shipping_method' => 'Ambil di Lokasi Petani',
            'shipping_address' => 'Kediri',
            'payment_method' => 'Transfer Bank / Rekber SINTESA',
        ]);

        // Unrelated collector tries to view order
        $unauthorizedView = $this->actingAs($collector)->get("/orders/{$order->id}");
        $this->assertEquals(403, $unauthorizedView->status());

        // Unrelated collector tries to cancel order
        $unauthorizedCancel = $this->actingAs($collector)->post("/orders/{$order->id}/cancel", [
            'reason' => 'Illegal cancel attempt',
        ]);
        $this->assertEquals(403, $unauthorizedCancel->status());
    }
}
