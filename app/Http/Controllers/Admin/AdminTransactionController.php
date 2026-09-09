<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTransactionController extends Controller
{
    /**
     * Display all orders and digital transactions across the platform.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $paymentStatus = $request->query('payment_status');
        $search = $request->query('search');

        $query = Order::with(['buyer', 'seller.farmerProfile', 'items', 'transaction'])
            ->orderBy('created_at', 'desc');

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($paymentStatus && $paymentStatus !== 'all') {
            $query->where('payment_status', $paymentStatus);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('buyer', fn($bq) => $bq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('seller', fn($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        // Metrics for summary
        $totalGmv = Order::where('status', '!=', 'Dibatalkan')->sum('total_amount');
        $totalOrders = Order::count();
        $completedOrders = Order::where('status', 'Selesai')->count();
        $pendingOrders = Order::where('status', 'Menunggu Konfirmasi')->count();

        $statusCounts = [
            'all' => $totalOrders,
            'Menunggu Konfirmasi' => $pendingOrders,
            'Dikonfirmasi' => Order::where('status', 'Dikonfirmasi')->count(),
            'Diproses' => Order::where('status', 'Diproses')->count(),
            'Selesai' => $completedOrders,
            'Dibatalkan' => Order::where('status', 'Dibatalkan')->count(),
        ];

        return view('admin.transactions.index', compact(
            'orders',
            'status',
            'paymentStatus',
            'search',
            'totalGmv',
            'totalOrders',
            'completedOrders',
            'pendingOrders',
            'statusCounts'
        ));
    }

    /**
     * Display full invoice & transaction audit detail.
     */
    public function show(Order $order): View
    {
        $order->load(['buyer.consumerProfile', 'buyer.collectorProfile', 'seller.farmerProfile', 'items', 'transaction']);

        return view('admin.transactions.show', compact('order'));
    }
}
