<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Preorder;
use App\Models\PreorderItem;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PreorderService
{
    /**
     * Farmer creates a new preorder campaign.
     */
    public function createPreorder(User $farmer, array $data): Preorder
    {
        if (!$farmer->isPetani() && !$farmer->isAdmin()) {
            throw new \InvalidArgumentException('Hanya Petani terdaftar yang dapat membuka kampanye Pre-Order.');
        }

        $production = (float) $data['estimated_production'];
        if ($production <= 0) {
            throw new \InvalidArgumentException('Estimasi hasil produksi harus lebih dari 0.');
        }

        $price = (float) $data['price'];
        if ($price <= 0) {
            throw new \InvalidArgumentException('Harga pre-order harus lebih dari Rp 0.');
        }

        return DB::transaction(function () use ($farmer, $production, $price, $data) {
            $slug = Str::slug($data['title']) . '-' . Str::random(5);

            return Preorder::create([
                'user_id' => $farmer->id,
                'commodity_id' => $data['commodity_id'],
                'title' => $data['title'],
                'slug' => $slug,
                'description' => $data['description'] ?? null,
                'price' => $price,
                'estimated_production' => $production,
                'preorder_available_quantity' => $production,
                'min_order' => $data['min_order'] ?? 1,
                'unit' => $data['unit'] ?? 'kg',
                'estimated_harvest_date' => $data['estimated_harvest_date'],
                'location' => $data['location'],
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'image' => $data['image'] ?? null,
                'status' => 'Dibuka',
            ]);
        });
    }

    /**
     * Buyer books preorder quota.
     * Guaranteed atomic: cannot exceed preorder_available_quantity.
     */
    public function bookPreorder(User $buyer, Preorder $preorder, array $data): PreorderItem
    {
        if ($preorder->user_id === $buyer->id) {
            throw new \InvalidArgumentException('Anda tidak dapat memesan pre-order dari kebun Anda sendiri.');
        }

        if (!in_array($preorder->status, ['Dibuka', 'Menunggu Panen'])) {
            throw new \InvalidArgumentException("Pre-order dengan status '{$preorder->status}' tidak lagi menerima pemesanan kuota.");
        }

        $quantity = (float) $data['quantity'];
        if ($preorder->min_order > 0 && $quantity < $preorder->min_order) {
            throw new \InvalidArgumentException("Pemesanan minimal adalah {$preorder->min_order} {$preorder->unit}.");
        }

        return DB::transaction(function () use ($buyer, $preorder, $quantity, $data) {
            // Lock preorder row for atomic capacity check
            $locked = Preorder::where('id', $preorder->id)->lockForUpdate()->firstOrFail();

            if ($locked->preorder_available_quantity < $quantity) {
                throw new \InvalidArgumentException("Sisa kuota pre-order yang tersedia ({$locked->preorder_available_quantity} {$locked->unit}) tidak mencukupi permintaan Anda ({$quantity} {$locked->unit}).");
            }

            $pricePerUnit = (float) $locked->price;
            $totalAmount = $pricePerUnit * $quantity;

            $item = PreorderItem::create([
                'preorder_id' => $locked->id,
                'buyer_id' => $buyer->id,
                'quantity' => $quantity,
                'price_per_unit' => $pricePerUnit,
                'total_amount' => $totalAmount,
                'shipping_address' => $data['shipping_address'] ?? ($buyer->consumerProfile->address ?? ($buyer->collectorProfile->address ?? 'Alamat Pembeli')),
                'shipping_method' => $data['shipping_method'] ?? 'Ambil di Lokasi Petani',
                'notes' => $data['notes'] ?? null,
                'status' => 'Menunggu Panen',
            ]);

            // Decrement available capacity
            $locked->decrement('preorder_available_quantity', $quantity);

            // Notify farmer
            Notification::create([
                'user_id' => $locked->user_id,
                'title' => "Pesanan Pre-Order Masuk (#PO-{$item->id})",
                'message' => "{$buyer->name} memesan kuota {$quantity} {$locked->unit} pada kampanye '{$locked->title}'. Sisa kuota: " . ($locked->preorder_available_quantity - $quantity) . " {$locked->unit}.",
                'type' => 'preorder_booked',
                'data' => [
                    'preorder_id' => $locked->id,
                    'item_id' => $item->id,
                ],
            ]);

            return $item;
        });
    }

    /**
     * Farmer updates campaign status and converts booked items to official orders when processing.
     */
    public function updateStatus(Preorder $preorder, User $farmer, string $newStatus): bool
    {
        if ($preorder->user_id !== $farmer->id && !$farmer->isAdmin()) {
            throw new \InvalidArgumentException('Anda tidak memiliki otorisasi untuk mengubah status pre-order ini.');
        }

        $validStatuses = ['Dibuka', 'Menunggu Panen', 'Siap Diproses', 'Diproses', 'Selesai', 'Dibatalkan'];
        if (!in_array($newStatus, $validStatuses)) {
            throw new \InvalidArgumentException("Status '{$newStatus}' tidak valid.");
        }

        return DB::transaction(function () use ($preorder, $newStatus, $farmer) {
            $preorder->update(['status' => $newStatus]);

            // When status becomes 'Siap Diproses' or 'Diproses', convert pending booked items into official Orders
            if (in_array($newStatus, ['Siap Diproses', 'Diproses'])) {
                $items = $preorder->items()->whereNull('order_id')->where('status', 'Menunggu Panen')->get();
                foreach ($items as $item) {
                    $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(5));
                    $order = Order::create([
                        'order_number' => $orderNumber,
                        'buyer_id' => $item->buyer_id,
                        'seller_id' => $preorder->user_id,
                        'source_type' => 'preorder',
                        'status' => 'Dikonfirmasi',
                        'total_amount' => $item->total_amount,
                        'shipping_address' => $item->shipping_address,
                        'shipping_method' => $item->shipping_method,
                        'shipping_cost' => 0,
                        'payment_method' => 'Transfer Bank / Rekber SINTESA',
                        'payment_status' => 'Belum Dibayar',
                        'notes' => "Pesanan hasil realisasi panen Pre-Order: '{$preorder->title}'. " . ($item->notes ?? ''),
                        'confirmed_at' => now(),
                    ]);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => null,
                        'stock_id' => null,
                        'product_name' => "Pre-Order: {$preorder->title}",
                        'price' => $item->price_per_unit,
                        'quantity' => $item->quantity,
                        'unit' => $preorder->unit,
                        'subtotal' => $item->total_amount,
                    ]);

                    Transaction::create([
                        'transaction_number' => 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
                        'order_id' => $order->id,
                        'buyer_id' => $item->buyer_id,
                        'seller_id' => $preorder->user_id,
                        'amount' => $item->total_amount,
                        'payment_method' => $order->payment_method,
                        'payment_status' => 'pending',
                    ]);

                    $item->update([
                        'order_id' => $order->id,
                        'status' => 'Dikonfirmasi',
                    ]);

                    Notification::create([
                        'user_id' => $item->buyer_id,
                        'title' => "Panen Siap! Pre-Order Dikonversi ke Pesanan (#{$order->order_number})",
                        'message' => "Hasil panen '{$preorder->title}' telah siap! Pesanan Anda telah resmi diterbitkan dan siap diproses petani.",
                        'type' => 'preorder_ready',
                        'data' => ['order_id' => $order->id, 'preorder_id' => $preorder->id],
                    ]);
                }
            }

            return true;
        });
    }

    /**
     * Cancel a preorder booking and restore capacity.
     */
    public function cancelBooking(PreorderItem $item, User $actor, ?string $reason = null): bool
    {
        $preorder = $item->preorder;

        if ($item->buyer_id !== $actor->id && $preorder->user_id !== $actor->id && !$actor->isAdmin()) {
            throw new \InvalidArgumentException('Anda tidak memiliki otorisasi untuk membatalkan pesanan pre-order ini.');
        }

        if (in_array($item->status, ['Selesai', 'Dibatalkan'])) {
            throw new \InvalidArgumentException("Pemesanan dengan status '{$item->status}' tidak dapat dibatalkan.");
        }

        return DB::transaction(function () use ($item, $preorder) {
            $item->update(['status' => 'Dibatalkan']);
            $preorder->increment('preorder_available_quantity', (float) $item->quantity);

            Notification::create([
                'user_id' => $preorder->user_id,
                'title' => "Pesanan Pre-Order Dibatalkan (#PO-{$item->id})",
                'message' => "Pesanan kuota {$item->quantity} {$preorder->unit} telah dibatalkan. Kuota telah dikembalikan ke kapasitas pre-order.",
                'type' => 'preorder_cancelled',
                'data' => ['preorder_id' => $preorder->id],
            ]);

            return true;
        });
    }
}
