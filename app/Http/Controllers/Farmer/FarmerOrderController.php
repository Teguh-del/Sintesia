<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FarmerOrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display listing of incoming orders for the farmer.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $status = $request->query('status');
        $search = $request->query('search');

        $query = Order::with(['buyer.consumerProfile', 'buyer.collectorProfile', 'items.product'])
            ->where('seller_id', $user->id);

        if ($status && in_array($status, ['Menunggu Konfirmasi', 'Dikonfirmasi', 'Diproses', 'Selesai', 'Dibatalkan'])) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('items', function ($iq) use ($search) {
                        $iq->where('product_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('buyer', function ($bq) use ($search) {
                        $bq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        // Status counts
        $counts = [
            'all' => Order::where('seller_id', $user->id)->count(),
            'pending' => Order::where('seller_id', $user->id)->where('status', 'Menunggu Konfirmasi')->count(),
            'confirmed' => Order::where('seller_id', $user->id)->where('status', 'Dikonfirmasi')->count(),
            'processing' => Order::where('seller_id', $user->id)->where('status', 'Diproses')->count(),
            'completed' => Order::where('seller_id', $user->id)->where('status', 'Selesai')->count(),
            'cancelled' => Order::where('seller_id', $user->id)->where('status', 'Dibatalkan')->count(),
        ];

        return view('farmer.orders.index', compact('orders', 'status', 'search', 'counts'));
    }

    /**
     * Show incoming order detail.
     */
    public function show(Order $order): View
    {
        $user = Auth::user();

        if ($order->seller_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $order->load(['buyer.consumerProfile', 'buyer.collectorProfile', 'items.product.stock', 'transactions']);

        return view('farmer.orders.show', compact('order', 'user'));
    }

    /**
     * Farmer confirms incoming order.
     */
    public function confirm(Order $order): RedirectResponse
    {
        $user = Auth::user();

        try {
            $this->orderService->confirmOrder($order, $user);

            return back()->with('success', "Pesanan #{$order->order_number} berhasil dikonfirmasi! Silakan siapkan komoditas untuk diproses.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Farmer starts processing order.
     */
    public function process(Order $order): RedirectResponse
    {
        $user = Auth::user();

        try {
            $this->orderService->processOrder($order, $user);

            return back()->with('success', "Status pesanan #{$order->order_number} diperbarui menjadi 'Diproses'.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Farmer completes order handover.
     */
    public function complete(Order $order): RedirectResponse
    {
        $user = Auth::user();

        try {
            $this->orderService->completeOrder($order, $user);

            return back()->with('success', "Pesanan #{$order->order_number} berhasil diselesaikan. Penjualan telah tercatat dalam inventaris.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Farmer rejects incoming order with reason.
     */
    public function reject(Request $request, Order $order): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ], [
            'reason.required' => 'Mohon berikan alasan penolakan pesanan kepada pembeli.',
        ]);

        try {
            $this->orderService->cancelOrder($order, $user, $request->input('reason'));

            return back()->with('success', "Pesanan #{$order->order_number} berhasil ditolak. Kuantitas stok telah dikembalikan ke stok aktif Anda.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
