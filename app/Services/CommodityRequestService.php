<?php

namespace App\Services;

use App\Models\CommodityRequest;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\RequestOffer;
use App\Models\Stock;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CommodityRequestService
{
    /**
     * Buyer creates a new commodity request.
     */
    public function createRequest(User $buyer, array $data): CommodityRequest
    {
        if (!$buyer->isPengepul() && !$buyer->isKonsumen() && !$buyer->isAdmin()) {
            throw new \InvalidArgumentException('Hanya Pengepul dan Konsumen yang dapat memposting permintaan komoditas.');
        }

        return DB::transaction(function () use ($buyer, $data) {
            $request = CommodityRequest::create([
                'user_id' => $buyer->id,
                'commodity_id' => $data['commodity_id'],
                'title' => $data['title'],
                'required_quantity' => (float) $data['required_quantity'],
                'unit' => $data['unit'] ?? 'kg',
                'max_price' => (float) $data['max_price'],
                'location' => $data['location'],
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'deadline' => $data['deadline'],
                'description' => $data['description'] ?? null,
                'status' => 'Aktif',
            ]);

            // Notify all relevant farmers who produce this commodity
            $farmers = User::where('role', 'petani')->get();
            foreach ($farmers as $farmer) {
                Notification::create([
                    'user_id' => $farmer->id,
                    'title' => "Permintaan Komoditas Baru ({$request->commodity->name})",
                    'message' => "{$buyer->name} membutuhkan {$request->required_quantity} {$request->unit} {$request->commodity->name} di {$request->location}. Batas harga: Rp " . number_format($request->max_price, 0, ',', '.') . "/{$request->unit}.",
                    'type' => 'commodity_request_created',
                    'data' => [
                        'request_id' => $request->id,
                        'commodity_id' => $request->commodity_id,
                    ],
                ]);
            }

            return $request;
        });
    }

    /**
     * Farmer responds to commodity request with an offer.
     */
    public function createOffer(User $farmer, CommodityRequest $request, array $data): RequestOffer
    {
        if (!$farmer->isPetani() && !$farmer->isAdmin()) {
            throw new \InvalidArgumentException('Hanya Petani terdaftar yang dapat mengajukan penawaran komoditas.');
        }

        if (!in_array($request->status, ['Aktif', 'Mendapat Penawaran'])) {
            throw new \InvalidArgumentException("Permintaan komoditas berstatus '{$request->status}' sudah tidak menerima penawaran.");
        }

        $offeredPrice = (float) $data['offered_price'];
        $offeredQty = (float) $data['offered_quantity'];

        if ($offeredQty <= 0 || $offeredPrice <= 0) {
            throw new \InvalidArgumentException('Kuantitas dan harga penawaran harus lebih dari 0.');
        }

        return DB::transaction(function () use ($farmer, $request, $offeredQty, $offeredPrice, $data) {
            $offer = RequestOffer::create([
                'commodity_request_id' => $request->id,
                'farmer_id' => $farmer->id,
                'product_id' => $data['product_id'] ?? null,
                'stock_id' => $data['stock_id'] ?? null,
                'offered_quantity' => $offeredQty,
                'offered_price' => $offeredPrice,
                'shipping_method' => $data['shipping_method'] ?? 'Ambil di Lokasi Petani',
                'notes' => $data['notes'] ?? null,
                'status' => 'Menunggu',
            ]);

            // Update request status to 'Mendapat Penawaran'
            if ($request->status === 'Aktif') {
                $request->update(['status' => 'Mendapat Penawaran']);
            }

            // Notify buyer
            Notification::create([
                'user_id' => $request->user_id,
                'title' => "Tawaran Pasokan Baru (#REQ-{$request->id})",
                'message' => "Petani {$farmer->name} mengajukan penawaran pasokan {$offeredQty} {$request->unit} dengan harga Rp " . number_format($offeredPrice, 0, ',', '.') . "/{$request->unit}.",
                'type' => 'request_offer_created',
                'data' => [
                    'request_id' => $request->id,
                    'offer_id' => $offer->id,
                ],
            ]);

            return $offer;
        });
    }

    /**
     * Buyer accepts a farmer's offer -> generates Order.
     */
    public function acceptOffer(RequestOffer $offer, User $buyer): Order
    {
        $request = $offer->commodityRequest;

        if ($request->user_id !== $buyer->id && !$buyer->isAdmin()) {
            throw new \InvalidArgumentException('Anda tidak memiliki otorisasi untuk menerima penawaran ini.');
        }

        if ($offer->status !== 'Menunggu') {
            throw new \InvalidArgumentException("Penawaran dengan status '{$offer->status}' tidak dapat diterima.");
        }

        return DB::transaction(function () use ($offer, $request, $buyer) {
            $totalAmount = (float) ($offer->offered_quantity * $offer->offered_price);

            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            while (Order::where('order_number', $orderNumber)->exists()) {
                $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            }

            $order = Order::create([
                'order_number' => $orderNumber,
                'buyer_id' => $buyer->id,
                'seller_id' => $offer->farmer_id,
                'source_type' => 'commodity_request',
                'status' => 'Menunggu Konfirmasi',
                'total_amount' => $totalAmount,
                'shipping_address' => $request->location,
                'shipping_method' => $offer->shipping_method,
                'shipping_cost' => 0,
                'payment_method' => 'Transfer Bank / Rekber SINTESA',
                'payment_status' => 'Belum Dibayar',
                'notes' => "Pemenuhan permintaan komoditas '{$request->title}'. " . ($offer->notes ?? ''),
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $offer->product_id,
                'stock_id' => $offer->stock_id,
                'product_name' => "Komoditas {$request->commodity->name} (Permintaan #{$request->id})",
                'price' => $offer->offered_price,
                'quantity' => $offer->offered_quantity,
                'unit' => $request->unit,
                'subtotal' => $totalAmount,
            ]);

            Transaction::create([
                'transaction_number' => 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
                'order_id' => $order->id,
                'buyer_id' => $buyer->id,
                'seller_id' => $offer->farmer_id,
                'amount' => $totalAmount,
                'payment_method' => $order->payment_method,
                'payment_status' => 'pending',
            ]);

            // Mark offer accepted & request fulfilled
            $offer->update(['status' => 'Diterima']);
            $request->update(['status' => 'Dipenuhi']);

            // Reject other pending offers on this request
            $request->offers()->where('id', '!=', $offer->id)->where('status', 'Menunggu')->update(['status' => 'Ditolak']);

            Notification::create([
                'user_id' => $offer->farmer_id,
                'title' => "Penawaran Disetujui! (#{$order->order_number})",
                'message' => "Selamat! Pembeli menyetujui penawaran pasokan Anda untuk permintaan '{$request->title}'. Pesanan resmi telah dibuat.",
                'type' => 'request_offer_accepted',
                'data' => ['order_id' => $order->id, 'request_id' => $request->id],
            ]);

            return $order;
        });
    }

    /**
     * Buyer rejects a farmer's offer.
     */
    public function rejectOffer(RequestOffer $offer, User $buyer): bool
    {
        $request = $offer->commodityRequest;
        if ($request->user_id !== $buyer->id && !$buyer->isAdmin()) {
            throw new \InvalidArgumentException('Anda tidak memiliki otorisasi untuk menolak penawaran ini.');
        }

        $offer->update(['status' => 'Ditolak']);

        Notification::create([
            'user_id' => $offer->farmer_id,
            'title' => "Penawaran Pasokan Ditolak",
            'message' => "Penawaran pasokan Anda untuk permintaan '{$request->title}' belum disetujui pembeli.",
            'type' => 'request_offer_rejected',
            'data' => ['request_id' => $request->id, 'offer_id' => $offer->id],
        ]);

        return true;
    }

    /**
     * Close request by buyer.
     */
    public function closeRequest(CommodityRequest $request, User $buyer): bool
    {
        if ($request->user_id !== $buyer->id && !$buyer->isAdmin()) {
            throw new \InvalidArgumentException('Anda tidak memiliki otorisasi untuk menutup permintaan ini.');
        }

        $request->update(['status' => 'Ditutup']);
        $request->offers()->where('status', 'Menunggu')->update(['status' => 'Dibatalkan']);

        return true;
    }
}
