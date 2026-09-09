<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PriceOffer;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NegotiationService
{
    /**
     * Buyer submits price offer on a marketplace product.
     */
    public function createOffer(User $buyer, Product $product, array $data): PriceOffer
    {
        if ($product->user_id === $buyer->id) {
            throw new \InvalidArgumentException('Anda tidak dapat menawar produk dari kebun Anda sendiri.');
        }

        if (!$product->allow_negotiation) {
            throw new \InvalidArgumentException('Produk ini tidak membuka fitur negosiasi harga.');
        }

        if ($product->stock <= 0 || $product->status !== 'active') {
            throw new \InvalidArgumentException('Produk ini sedang tidak tersedia untuk dinegosiasikan.');
        }

        $quantity = (float) $data['quantity'];
        if ($quantity < $product->min_order) {
            throw new \InvalidArgumentException("Jumlah penawaran minimal adalah {$product->min_order} {$product->unit}.");
        }

        if ($quantity > $product->stock) {
            throw new \InvalidArgumentException("Jumlah penawaran melebihi stok yang tersedia ({$product->stock} {$product->unit}).");
        }

        $offeredPrice = (float) $data['offered_price'];
        if ($offeredPrice <= 0) {
            throw new \InvalidArgumentException('Harga penawaran harus lebih dari Rp 0.');
        }

        return DB::transaction(function () use ($buyer, $product, $quantity, $offeredPrice, $data) {
            $offer = PriceOffer::create([
                'product_id' => $product->id,
                'buyer_id' => $buyer->id,
                'seller_id' => $product->user_id,
                'quantity' => $quantity,
                'offered_price' => $offeredPrice,
                'original_price' => $product->price,
                'shipping_method' => $data['shipping_method'] ?? 'Ambil di Lokasi Petani',
                'notes' => $data['notes'] ?? null,
                'status' => 'Menunggu',
            ]);

            // Notify farmer
            Notification::create([
                'user_id' => $product->user_id,
                'title' => "Tawaran Harga Masuk (#NEGO-{$offer->id})",
                'message' => "{$buyer->name} mengajukan tawaran harga Rp " . number_format($offeredPrice, 0, ',', '.') . "/{$product->unit} untuk {$quantity} {$product->unit} {$product->name}.",
                'type' => 'negotiation_created',
                'data' => [
                    'offer_id' => $offer->id,
                    'product_id' => $product->id,
                ],
            ]);

            return $offer;
        });
    }

    /**
     * Farmer accepts buyer's offer -> converts to an Order.
     */
    public function acceptOffer(PriceOffer $offer, User $farmer): Order
    {
        if ($offer->seller_id !== $farmer->id) {
            throw new \InvalidArgumentException('Anda tidak memiliki otorisasi untuk menerima tawaran ini.');
        }

        if ($offer->status !== 'Menunggu') {
            throw new \InvalidArgumentException("Tawaran dengan status '{$offer->status}' tidak dapat diterima.");
        }

        return DB::transaction(function () use ($offer) {
            $product = Product::where('id', $offer->product_id)->lockForUpdate()->firstOrFail();
            if ($product->stock < $offer->quantity) {
                throw new \InvalidArgumentException("Stok produk tidak mencukupi untuk memenuhi kesepakatan ini.");
            }

            $stock = null;
            if ($product->stock_id) {
                $stock = Stock::where('id', $product->stock_id)->lockForUpdate()->first();
                if ($stock && $stock->available_quantity < $offer->quantity) {
                    throw new \InvalidArgumentException("Stok gudang riil tidak mencukupi kuantitas negosiasi.");
                }
            }

            $agreedPrice = (float) $offer->offered_price;
            $subtotal = $agreedPrice * (float) $offer->quantity;
            $totalAmount = $subtotal;

            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            while (Order::where('order_number', $orderNumber)->exists()) {
                $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            }

            $order = Order::create([
                'order_number' => $orderNumber,
                'buyer_id' => $offer->buyer_id,
                'seller_id' => $offer->seller_id,
                'source_type' => 'negotiation',
                'status' => 'Menunggu Konfirmasi',
                'total_amount' => $totalAmount,
                'shipping_address' => $offer->buyer->consumerProfile->address ?? ($offer->buyer->collectorProfile->address ?? 'Alamat Pembeli'),
                'shipping_method' => $offer->shipping_method,
                'shipping_cost' => 0,
                'payment_method' => 'Transfer Bank / Rekber SINTESA',
                'payment_status' => 'Belum Dibayar',
                'notes' => 'Hasil negosiasi harga disepakati Rp ' . number_format($agreedPrice, 0, ',', '.') . '/' . $product->unit . '. ' . ($offer->notes ?? ''),
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'stock_id' => $stock ? $stock->id : null,
                'product_name' => $product->name,
                'price' => $agreedPrice,
                'quantity' => $offer->quantity,
                'unit' => $product->unit,
                'subtotal' => $subtotal,
            ]);

            $trxNumber = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            Transaction::create([
                'transaction_number' => $trxNumber,
                'order_id' => $order->id,
                'buyer_id' => $offer->buyer_id,
                'seller_id' => $offer->seller_id,
                'amount' => $totalAmount,
                'payment_method' => $order->payment_method,
                'payment_status' => 'pending',
            ]);

            $product->decrement('stock', $offer->quantity);
            if ($stock) {
                $stock->reserve((float) $offer->quantity);
            }

            $offer->update(['status' => 'Selesai']);

            Notification::create([
                'user_id' => $offer->buyer_id,
                'title' => "Tawaran Disepakati! (#{$order->order_number})",
                'message' => "Petani telah menyepakati tawaran harga Rp " . number_format($agreedPrice, 0, ',', '.') . "/{$product->unit} untuk {$product->name}. Pesanan resmi telah diterbitkan.",
                'type' => 'negotiation_accepted',
                'data' => ['order_id' => $order->id, 'offer_id' => $offer->id],
            ]);

            return $order;
        });
    }

    /**
     * Farmer makes a counter offer.
     */
    public function counterOffer(PriceOffer $offer, User $farmer, float $counterPrice, ?string $notes = null): bool
    {
        if ($offer->seller_id !== $farmer->id) {
            throw new \InvalidArgumentException('Anda tidak memiliki otorisasi untuk memberikan tawaran balik.');
        }

        if ($offer->status !== 'Menunggu') {
            throw new \InvalidArgumentException("Hanya tawaran berstatus 'Menunggu' yang dapat diberi tawaran balik.");
        }

        if ($counterPrice <= 0) {
            throw new \InvalidArgumentException('Harga tawaran balik harus lebih dari Rp 0.');
        }

        $offer->update([
            'counter_price' => $counterPrice,
            'notes' => $notes ?? $offer->notes,
            'status' => 'Counter Offer',
        ]);

        Notification::create([
            'user_id' => $offer->buyer_id,
            'title' => "Tawaran Balik dari Petani (#NEGO-{$offer->id})",
            'message' => "Petani menawarkan harga balik Rp " . number_format($counterPrice, 0, ',', '.') . "/{$offer->product->unit} untuk {$offer->product->name}.",
            'type' => 'counter_offer',
            'data' => ['offer_id' => $offer->id],
        ]);

        return true;
    }

    /**
     * Buyer accepts farmer's counter offer -> converts to an Order.
     */
    public function acceptCounterOffer(PriceOffer $offer, User $buyer): Order
    {
        if ($offer->buyer_id !== $buyer->id) {
            throw new \InvalidArgumentException('Anda tidak memiliki otorisasi untuk menerima tawaran balik ini.');
        }

        if ($offer->status !== 'Counter Offer' || !$offer->counter_price) {
            throw new \InvalidArgumentException('Tawaran balik tidak valid atau sudah tidak aktif.');
        }

        return DB::transaction(function () use ($offer) {
            $product = Product::where('id', $offer->product_id)->lockForUpdate()->firstOrFail();
            if ($product->stock < $offer->quantity) {
                throw new \InvalidArgumentException("Stok produk tidak mencukupi untuk memenuhi pesanan ini.");
            }

            $stock = null;
            if ($product->stock_id) {
                $stock = Stock::where('id', $product->stock_id)->lockForUpdate()->first();
                if ($stock && $stock->available_quantity < $offer->quantity) {
                    throw new \InvalidArgumentException("Stok gudang riil tidak mencukupi kuantitas pesanan.");
                }
            }

            $agreedPrice = (float) $offer->counter_price;
            $subtotal = $agreedPrice * (float) $offer->quantity;
            $totalAmount = $subtotal;

            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            while (Order::where('order_number', $orderNumber)->exists()) {
                $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            }

            $order = Order::create([
                'order_number' => $orderNumber,
                'buyer_id' => $offer->buyer_id,
                'seller_id' => $offer->seller_id,
                'source_type' => 'negotiation',
                'status' => 'Menunggu Konfirmasi',
                'total_amount' => $totalAmount,
                'shipping_address' => $offer->buyer->consumerProfile->address ?? ($offer->buyer->collectorProfile->address ?? 'Alamat Pembeli'),
                'shipping_method' => $offer->shipping_method,
                'shipping_cost' => 0,
                'payment_method' => 'Transfer Bank / Rekber SINTESA',
                'payment_status' => 'Belum Dibayar',
                'notes' => 'Hasil kesepakatan Counter Offer Rp ' . number_format($agreedPrice, 0, ',', '.') . '/' . $product->unit . '.',
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'stock_id' => $stock ? $stock->id : null,
                'product_name' => $product->name,
                'price' => $agreedPrice,
                'quantity' => $offer->quantity,
                'unit' => $product->unit,
                'subtotal' => $subtotal,
            ]);

            $trxNumber = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            Transaction::create([
                'transaction_number' => $trxNumber,
                'order_id' => $order->id,
                'buyer_id' => $offer->buyer_id,
                'seller_id' => $offer->seller_id,
                'amount' => $totalAmount,
                'payment_method' => $order->payment_method,
                'payment_status' => 'pending',
            ]);

            $product->decrement('stock', $offer->quantity);
            if ($stock) {
                $stock->reserve((float) $offer->quantity);
            }

            $offer->update(['status' => 'Selesai']);

            Notification::create([
                'user_id' => $offer->seller_id,
                'title' => "Tawaran Balik Diterima! (#{$order->order_number})",
                'message' => "Pembeli menyepakati harga tawaran balik Anda Rp " . number_format($agreedPrice, 0, ',', '.') . "/{$product->unit}. Pesanan resmi telah dibuat.",
                'type' => 'negotiation_accepted',
                'data' => ['order_id' => $order->id, 'offer_id' => $offer->id],
            ]);

            return $order;
        });
    }

    /**
     * Reject offer by farmer or buyer.
     */
    public function rejectOffer(PriceOffer $offer, User $actor, ?string $reason = null): bool
    {
        if ($offer->seller_id !== $actor->id && $offer->buyer_id !== $actor->id && !$actor->isAdmin()) {
            throw new \InvalidArgumentException('Anda tidak memiliki otorisasi untuk menolak tawaran ini.');
        }

        $offer->update([
            'status' => 'Ditolak',
            'notes' => $reason ? ($offer->notes . " (Ditolak: {$reason})") : $offer->notes,
        ]);

        $recipientId = ($actor->id === $offer->seller_id) ? $offer->buyer_id : $offer->seller_id;
        $actorRole = ($actor->id === $offer->seller_id) ? 'Petani' : 'Pembeli';

        Notification::create([
            'user_id' => $recipientId,
            'title' => "Tawaran Harga Ditolak (#NEGO-{$offer->id})",
            'message' => "Tawaran negosiasi untuk {$offer->product->name} telah ditolak oleh {$actorRole}." . ($reason ? " Alasan: {$reason}" : ""),
            'type' => 'negotiation_rejected',
            'data' => ['offer_id' => $offer->id],
        ]);

        return true;
    }
}
