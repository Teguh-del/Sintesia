<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Create a new direct purchase order from marketplace product.
     * Guaranteed atomic with stock reservation.
     *
     * @throws \Exception
     */
    public function createDirectOrder(User $buyer, Product $product, array $data): Order
    {
        return DB::transaction(function () use ($buyer, $product, $data) {
            // 1. Lock product row to prevent race conditions
            $lockedProduct = Product::where('id', $product->id)->lockForUpdate()->firstOrFail();

            // Self-purchase prevention
            if ($lockedProduct->user_id === $buyer->id) {
                throw new \InvalidArgumentException('Anda tidak dapat membeli produk dari kebun Anda sendiri.');
            }

            $quantity = (float) $data['quantity'];

            // Validate minimum order
            if ($lockedProduct->min_order > 0 && $quantity < $lockedProduct->min_order) {
                throw new \InvalidArgumentException("Jumlah pesanan minimal adalah {$lockedProduct->min_order} {$lockedProduct->unit}.");
            }

            // Validate available stock on product
            if ($lockedProduct->stock < $quantity) {
                throw new \InvalidArgumentException("Stok produk tidak mencukupi. Tersisa: {$lockedProduct->stock} {$lockedProduct->unit}.");
            }

            // 2. Lock and validate corresponding real stock batch
            $stock = null;
            if ($lockedProduct->stock_id) {
                $stock = Stock::where('id', $lockedProduct->stock_id)->lockForUpdate()->first();
                if ($stock && $stock->available_quantity < $quantity) {
                    throw new \InvalidArgumentException("Ketersediaan stok riil di gudang ({$stock->available_quantity} {$stock->unit}) tidak mencukupi kuantitas pesanan.");
                }
            }

            // Calculate amounts
            $price = (float) $lockedProduct->price;
            $shippingCost = isset($data['shipping_cost']) ? (float) $data['shipping_cost'] : 0.0;
            $subtotal = $price * $quantity;
            $totalAmount = $subtotal + $shippingCost;

            // Generate unique order number: ORD-YYYYMMDD-XXXXX
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            while (Order::where('order_number', $orderNumber)->exists()) {
                $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            }

            // 3. Create Order
            $order = Order::create([
                'order_number' => $orderNumber,
                'buyer_id' => $buyer->id,
                'seller_id' => $lockedProduct->user_id,
                'source_type' => 'direct_purchase',
                'status' => 'Menunggu Konfirmasi',
                'total_amount' => $totalAmount,
                'shipping_address' => $data['shipping_address'] ?? ($buyer->consumerProfile->address ?? ($buyer->collectorProfile->address ?? 'Alamat pembeli')),
                'shipping_method' => $data['shipping_method'] ?? 'Ambil di Lokasi Petani',
                'shipping_cost' => $shippingCost,
                'payment_method' => $data['payment_method'] ?? 'Transfer Bank / Rekber SINTESA',
                'payment_status' => 'Belum Dibayar',
                'notes' => $data['notes'] ?? null,
            ]);

            // 4. Create Order Item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $lockedProduct->id,
                'stock_id' => $stock ? $stock->id : null,
                'product_name' => $lockedProduct->name,
                'price' => $price,
                'quantity' => $quantity,
                'unit' => $lockedProduct->unit,
                'subtotal' => $subtotal,
            ]);

            // 5. Create Transaction record
            $trxNumber = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            while (Transaction::where('transaction_number', $trxNumber)->exists()) {
                $trxNumber = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            }

            Transaction::create([
                'transaction_number' => $trxNumber,
                'order_id' => $order->id,
                'buyer_id' => $buyer->id,
                'seller_id' => $lockedProduct->user_id,
                'amount' => $totalAmount,
                'payment_method' => $order->payment_method,
                'payment_status' => 'pending',
            ]);

            // 6. Deduct and reserve stock atomically
            $lockedProduct->decrement('stock', $quantity);
            if ($stock) {
                $stock->reserve($quantity);
            }

            // 7. Send Real Notification to Seller (Farmer)
            Notification::create([
                'user_id' => $lockedProduct->user_id,
                'title' => "Pesanan Baru Masuk (#{$order->order_number})",
                'message' => "{$buyer->name} memesan {$quantity} {$lockedProduct->unit} {$lockedProduct->name} senilai Rp " . number_format($totalAmount, 0, ',', '.') . ". Mohon segera lakukan konfirmasi pesanan.",
                'type' => 'order_created',
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ],
            ]);

            return $order;
        });
    }

    /**
     * Farmer confirms incoming order.
     */
    public function confirmOrder(Order $order, User $farmer): bool
    {
        if ($order->seller_id !== $farmer->id) {
            throw new \InvalidArgumentException('Anda tidak memiliki otorisasi untuk pesanan ini.');
        }

        if ($order->status !== 'Menunggu Konfirmasi') {
            throw new \InvalidArgumentException("Pesanan dengan status '{$order->status}' tidak dapat dikonfirmasi.");
        }

        $order->update([
            'status' => 'Dikonfirmasi',
            'confirmed_at' => now(),
        ]);

        Notification::create([
            'user_id' => $order->buyer_id,
            'title' => "Pesanan Dikonfirmasi (#{$order->order_number})",
            'message' => "Petani {$farmer->name} telah mengonfirmasi pesanan Anda dan akan segera menyiapkan komoditas.",
            'type' => 'order_confirmed',
            'data' => ['order_id' => $order->id, 'order_number' => $order->order_number],
        ]);

        return true;
    }

    /**
     * Farmer marks order as being processed.
     */
    public function processOrder(Order $order, User $farmer): bool
    {
        if ($order->seller_id !== $farmer->id) {
            throw new \InvalidArgumentException('Anda tidak memiliki otorisasi untuk pesanan ini.');
        }

        if ($order->status !== 'Dikonfirmasi') {
            throw new \InvalidArgumentException("Pesanan dengan status '{$order->status}' tidak dapat diproses.");
        }

        $order->update([
            'status' => 'Diproses',
            'processed_at' => now(),
        ]);

        Notification::create([
            'user_id' => $order->buyer_id,
            'title' => "Pesanan Sedang Diproses (#{$order->order_number})",
            'message' => "Komoditas pesanan Anda sedang disiapkan dan dikemas oleh Petani {$farmer->name}.",
            'type' => 'order_processed',
            'data' => ['order_id' => $order->id, 'order_number' => $order->order_number],
        ]);

        return true;
    }

    /**
     * Complete order (by buyer receiving order or farmer completing handover).
     * Finalizes stock sale from ordered_quantity to sold_quantity.
     */
    public function completeOrder(Order $order, User $actor): bool
    {
        if ($order->buyer_id !== $actor->id && $order->seller_id !== $actor->id && !$actor->isAdmin()) {
            throw new \InvalidArgumentException('Anda tidak memiliki otorisasi untuk menyelesaikan pesanan ini.');
        }

        if ($order->status !== 'Diproses') {
            throw new \InvalidArgumentException("Hanya pesanan berstatus 'Diproses' yang dapat diselesaikan.");
        }

        return DB::transaction(function () use ($order) {
            $order->update([
                'status' => 'Selesai',
                'payment_status' => 'Sudah Dibayar',
                'completed_at' => now(),
            ]);

            // Update Transaction
            Transaction::where('order_id', $order->id)->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            // Complete sale on stocks
            foreach ($order->items as $item) {
                if ($item->stock_id) {
                    $stock = Stock::find($item->stock_id);
                    if ($stock) {
                        $stock->completeSale((float) $item->quantity);
                    }
                }
            }

            // Send notification to both parties
            Notification::create([
                'user_id' => $order->buyer_id,
                'title' => "Transaksi Selesai (#{$order->order_number})",
                'message' => "Pesanan Anda telah selesai. Terima kasih telah berbelanja komoditas segar di SINTESA.",
                'type' => 'order_completed',
                'data' => ['order_id' => $order->id, 'order_number' => $order->order_number],
            ]);

            Notification::create([
                'user_id' => $order->seller_id,
                'title' => "Transaksi Selesai (#{$order->order_number})",
                'message' => "Pesanan #{$order->order_number} telah selesai dan kuantitas stok telah resmi tercatat sebagai hasil panen terjual.",
                'type' => 'order_completed',
                'data' => ['order_id' => $order->id, 'order_number' => $order->order_number],
            ]);

            return true;
        });
    }

    /**
     * Cancel / Reject order.
     * Restores locked stock back to available quantity.
     */
    public function cancelOrder(Order $order, User $actor, string $reason): bool
    {
        // Authorization check: only buyer or seller or admin can cancel
        if ($order->buyer_id !== $actor->id && $order->seller_id !== $actor->id && !$actor->isAdmin()) {
            throw new \InvalidArgumentException('Anda tidak memiliki otorisasi untuk membatalkan pesanan ini.');
        }

        // Buyer can only cancel while status is 'Menunggu Konfirmasi'
        if ($order->buyer_id === $actor->id && $order->status !== 'Menunggu Konfirmasi') {
            throw new \InvalidArgumentException('Pesanan yang telah dikonfirmasi atau diproses tidak dapat dibatalkan sepihak.');
        }

        // Cannot cancel already completed or cancelled order
        if (in_array($order->status, ['Selesai', 'Dibatalkan'])) {
            throw new \InvalidArgumentException("Pesanan yang sudah {$order->status} tidak dapat dibatalkan.");
        }

        return DB::transaction(function () use ($order, $actor, $reason) {
            $order->update([
                'status' => 'Dibatalkan',
                'payment_status' => 'Dibatalkan',
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);

            // Update Transaction
            Transaction::where('order_id', $order->id)->update([
                'payment_status' => 'failed',
            ]);

            // Release reserved stock back to available stock
            foreach ($order->items as $item) {
                // Restore product stock
                if ($item->product_id) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock', (float) $item->quantity);
                    }
                }

                // Restore real stock batch
                if ($item->stock_id) {
                    $stock = Stock::find($item->stock_id);
                    if ($stock) {
                        $stock->release((float) $item->quantity);
                    }
                }
            }

            // Notify counterpart
            $targetUserId = ($actor->id === $order->buyer_id) ? $order->seller_id : $order->buyer_id;
            $actorRoleName = ($actor->id === $order->buyer_id) ? 'Pembeli' : 'Petani';

            Notification::create([
                'user_id' => $targetUserId,
                'title' => "Pesanan Dibatalkan (#{$order->order_number})",
                'message' => "Pesanan #{$order->order_number} telah dibatalkan oleh {$actorRoleName} dengan alasan: {$reason}. Kuantitas stok telah dikembalikan ke sistem.",
                'type' => 'order_cancelled',
                'data' => ['order_id' => $order->id, 'order_number' => $order->order_number],
            ]);

            return true;
        });
    }
}
