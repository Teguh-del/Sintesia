<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $farmer = User::where('role', 'petani')->first();
        $collector = User::where('role', 'pengepul')->first();
        $consumer = User::where('role', 'konsumen')->first();

        if (!$farmer || !$collector || !$consumer) {
            $this->command->warn('User Petani, Pengepul, atau Konsumen belum lengkap. Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        $products = Product::where('user_id', $farmer->id)->get();
        if ($products->isEmpty()) {
            $this->command->warn('Produk petani belum tersedia. Jalankan ProductSeeder terlebih dahulu.');
            return;
        }

        $jagungProduct = $products->where('unit', 'kg')->first() ?? $products->first();
        $otherProduct = $products->where('id', '!=', $jagungProduct->id)->first() ?? $jagungProduct;

        // Clean up previously seeded demo orders if any for idempotent seeding
        $demoOrderNumbers = [
            'ORD-' . date('Ymd') . '-P0001',
            'ORD-' . date('Ymd') . '-K0002',
            'ORD-' . date('Ymd') . '-S0003',
        ];
        Order::whereIn('order_number', $demoOrderNumbers)->delete();
        Transaction::whereIn('transaction_number', [
            'TRX-' . date('Ymd') . '-P0001',
            'TRX-' . date('Ymd') . '-K0002',
            'TRX-' . date('Ymd') . '-S0003',
        ])->delete();

        // ---------------------------------------------------------------------
        // 1. Order 1: Pengepul -> Petani (Status: 'Diproses')
        // ---------------------------------------------------------------------
        $qty1 = 500;
        $subtotal1 = $jagungProduct->price * $qty1;
        $shipping1 = 150000;
        $total1 = $subtotal1 + $shipping1;
        $orderNum1 = 'ORD-' . date('Ymd') . '-P0001';

        $order1 = Order::create([
            'order_number' => $orderNum1,
            'buyer_id' => $collector->id,
            'seller_id' => $farmer->id,
            'source_type' => 'direct_purchase',
            'status' => 'Diproses',
            'total_amount' => $total1,
            'shipping_address' => $collector->collectorProfile->address ?? 'Gudang Pusat CV Hasil Bumi, Kediri',
            'shipping_method' => 'Pengiriman / Kurir',
            'shipping_cost' => $shipping1,
            'payment_method' => 'Transfer Bank / Rekber SINTESA',
            'payment_status' => 'Belum Dibayar',
            'notes' => 'Truk armada angkut siap jemput di gerbang gudang petani pukul 09.00 WIB.',
            'confirmed_at' => now()->subHours(6),
            'processed_at' => now()->subHours(2),
            'created_at' => now()->subHours(8),
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $jagungProduct->id,
            'stock_id' => $jagungProduct->stock_id,
            'product_name' => $jagungProduct->name,
            'price' => $jagungProduct->price,
            'quantity' => $qty1,
            'unit' => $jagungProduct->unit,
            'subtotal' => $subtotal1,
        ]);

        Transaction::create([
            'transaction_number' => 'TRX-' . date('Ymd') . '-P0001',
            'order_id' => $order1->id,
            'buyer_id' => $collector->id,
            'seller_id' => $farmer->id,
            'amount' => $total1,
            'payment_method' => $order1->payment_method,
            'payment_status' => 'pending',
            'created_at' => now()->subHours(8),
        ]);

        Notification::create([
            'user_id' => $farmer->id,
            'title' => "Pesanan Dikonfirmasi (#{$order1->order_number})",
            'message' => "Anda telah mengonfirmasi pesanan dari {$collector->name} sebanyak {$qty1} {$jagungProduct->unit}.",
            'type' => 'order_confirmed',
            'data' => ['order_id' => $order1->id, 'order_number' => $order1->order_number],
            'is_read' => true,
            'read_at' => now()->subHours(5),
            'created_at' => now()->subHours(6),
        ]);

        // Reserve stock
        if ($jagungProduct->stock >= $qty1) {
            $jagungProduct->decrement('stock', $qty1);
        }
        if ($jagungProduct->stock_id) {
            $stock1 = Stock::find($jagungProduct->stock_id);
            if ($stock1 && $stock1->available_quantity >= $qty1) {
                $stock1->reserve($qty1);
            }
        }

        // ---------------------------------------------------------------------
        // 2. Order 2: Konsumen -> Petani (Status: 'Menunggu Konfirmasi')
        // ---------------------------------------------------------------------
        $qty2 = 15;
        $subtotal2 = $otherProduct->price * $qty2;
        $shipping2 = 0;
        $total2 = $subtotal2 + $shipping2;
        $orderNum2 = 'ORD-' . date('Ymd') . '-K0002';

        $order2 = Order::create([
            'order_number' => $orderNum2,
            'buyer_id' => $consumer->id,
            'seller_id' => $farmer->id,
            'source_type' => 'direct_purchase',
            'status' => 'Menunggu Konfirmasi',
            'total_amount' => $total2,
            'shipping_address' => $consumer->consumerProfile->address ?? 'Jl. Melati No. 42, Kediri',
            'shipping_method' => 'Ambil di Lokasi Petani',
            'shipping_cost' => $shipping2,
            'payment_method' => 'Tunai / COD saat Timbang',
            'payment_status' => 'Belum Dibayar',
            'notes' => 'Pilih komoditas yang segar petik hari ini ya Pak.',
            'created_at' => now()->subHours(1),
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $otherProduct->id,
            'stock_id' => $otherProduct->stock_id,
            'product_name' => $otherProduct->name,
            'price' => $otherProduct->price,
            'quantity' => $qty2,
            'unit' => $otherProduct->unit,
            'subtotal' => $subtotal2,
        ]);

        Transaction::create([
            'transaction_number' => 'TRX-' . date('Ymd') . '-K0002',
            'order_id' => $order2->id,
            'buyer_id' => $consumer->id,
            'seller_id' => $farmer->id,
            'amount' => $total2,
            'payment_method' => $order2->payment_method,
            'payment_status' => 'pending',
            'created_at' => now()->subHours(1),
        ]);

        Notification::create([
            'user_id' => $farmer->id,
            'title' => "Pesanan Baru Masuk (#{$order2->order_number})",
            'message' => "{$consumer->name} memesan {$qty2} {$otherProduct->unit} {$otherProduct->name} senilai Rp " . number_format($total2, 0, ',', '.') . ". Segera lakukan konfirmasi.",
            'type' => 'order_created',
            'data' => ['order_id' => $order2->id, 'order_number' => $order2->order_number],
            'is_read' => false,
            'created_at' => now()->subHours(1),
        ]);

        if ($otherProduct->stock >= $qty2) {
            $otherProduct->decrement('stock', $qty2);
        }
        if ($otherProduct->stock_id) {
            $stock2 = Stock::find($otherProduct->stock_id);
            if ($stock2 && $stock2->available_quantity >= $qty2) {
                $stock2->reserve($qty2);
            }
        }

        // ---------------------------------------------------------------------
        // 3. Order 3: Pengepul -> Petani (Status: 'Selesai' / Paid Transaction)
        // ---------------------------------------------------------------------
        $qty3 = 250;
        $subtotal3 = $jagungProduct->price * $qty3;
        $shipping3 = 50000;
        $total3 = $subtotal3 + $shipping3;
        $orderNum3 = 'ORD-' . date('Ymd') . '-S0003';

        $order3 = Order::create([
            'order_number' => $orderNum3,
            'buyer_id' => $collector->id,
            'seller_id' => $farmer->id,
            'source_type' => 'direct_purchase',
            'status' => 'Selesai',
            'total_amount' => $total3,
            'shipping_address' => $collector->collectorProfile->address ?? 'Gudang Pusat CV Hasil Bumi, Kediri',
            'shipping_method' => 'Ambil di Lokasi Petani',
            'shipping_cost' => $shipping3,
            'payment_method' => 'Transfer Bank / Rekber SINTESA',
            'payment_status' => 'Sudah Dibayar',
            'notes' => 'Pesanan telah diterima dan timbangan sesuai nota timbang.',
            'confirmed_at' => now()->subDays(2),
            'processed_at' => now()->subDays(1),
            'completed_at' => now()->subHours(12),
            'created_at' => now()->subDays(2),
        ]);

        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $jagungProduct->id,
            'stock_id' => $jagungProduct->stock_id,
            'product_name' => $jagungProduct->name,
            'price' => $jagungProduct->price,
            'quantity' => $qty3,
            'unit' => $jagungProduct->unit,
            'subtotal' => $subtotal3,
        ]);

        Transaction::create([
            'transaction_number' => 'TRX-' . date('Ymd') . '-S0003',
            'order_id' => $order3->id,
            'buyer_id' => $collector->id,
            'seller_id' => $farmer->id,
            'amount' => $total3,
            'payment_method' => $order3->payment_method,
            'payment_status' => 'paid',
            'paid_at' => now()->subHours(12),
            'created_at' => now()->subDays(2),
        ]);

        Notification::create([
            'user_id' => $collector->id,
            'title' => "Transaksi Selesai (#{$order3->order_number})",
            'message' => "Pesanan komoditas telah berhasil diselesaikan. Terima kasih telah bermitra di SINTESA.",
            'type' => 'order_completed',
            'data' => ['order_id' => $order3->id, 'order_number' => $order3->order_number],
            'is_read' => true,
            'read_at' => now()->subHours(10),
            'created_at' => now()->subHours(12),
        ]);

        if ($jagungProduct->stock >= $qty3) {
            $jagungProduct->decrement('stock', $qty3);
        }
        if ($jagungProduct->stock_id) {
            $stock3 = Stock::find($jagungProduct->stock_id);
            if ($stock3 && $stock3->available_quantity >= $qty3) {
                $stock3->reserve($qty3);
                $stock3->completeSale($qty3);
            }
        }
    }
}
