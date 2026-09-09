<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of orders for the authenticated buyer (Pengepul / Konsumen).
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $status = $request->query('status');
        $search = $request->query('search');

        $query = Order::with(['seller.farmerProfile', 'items.product'])
            ->where('buyer_id', $user->id);

        if ($status && in_array($status, ['Menunggu Konfirmasi', 'Dikonfirmasi', 'Diproses', 'Selesai', 'Dibatalkan'])) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('items', function ($iq) use ($search) {
                        $iq->where('product_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('seller', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        // Status counts for tabs
        $counts = [
            'all' => Order::where('buyer_id', $user->id)->count(),
            'pending' => Order::where('buyer_id', $user->id)->where('status', 'Menunggu Konfirmasi')->count(),
            'confirmed' => Order::where('buyer_id', $user->id)->where('status', 'Dikonfirmasi')->count(),
            'processing' => Order::where('buyer_id', $user->id)->where('status', 'Diproses')->count(),
            'completed' => Order::where('buyer_id', $user->id)->where('status', 'Selesai')->count(),
            'cancelled' => Order::where('buyer_id', $user->id)->where('status', 'Dibatalkan')->count(),
        ];

        return view('orders.index', compact('orders', 'status', 'search', 'counts'));
    }

    /**
     * Store a newly created order from marketplace direct purchase.
     */
    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $product = Product::findOrFail($request->validated('product_id'));

        try {
            $order = $this->orderService->createDirectOrder(
                $user,
                $product,
                $request->validated()
            );

            return redirect()->route('orders.show', $order)
                ->with('success', "Pesanan #{$order->order_number} berhasil dibuat! Notifikasi telah dikirimkan ke petani.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified order details (digital invoice & status timeline).
     */
    public function show(Order $order): View
    {
        $user = Auth::user();

        // Authorization check: only buyer, seller, or admin can view
        if ($order->buyer_id !== $user->id && $order->seller_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki otorisasi untuk melihat detail pesanan ini.');
        }

        $order->load(['buyer.consumerProfile', 'buyer.collectorProfile', 'seller.farmerProfile', 'items.product', 'transactions']);

        return view('orders.show', compact('order', 'user'));
    }

    /**
     * Buyer cancels pending order.
     */
    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ], [
            'reason.required' => 'Mohon sertakan alasan pembatalan pesanan.',
        ]);

        try {
            $this->orderService->cancelOrder($order, $user, $request->input('reason'));

            return back()->with('success', "Pesanan #{$order->order_number} telah berhasil dibatalkan dan stok dikembalikan.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Buyer confirms order received (completes order).
     */
    public function receive(Order $order): RedirectResponse
    {
        $user = Auth::user();

        try {
            $this->orderService->completeOrder($order, $user);

            return back()->with('success', "Konfirmasi penerimaan berhasil! Transaksi #{$order->order_number} dinyatakan selesai.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
