<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Models\Preorder;
use App\Models\PreorderItem;
use App\Services\PreorderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PreorderController extends Controller
{
    protected PreorderService $preorderService;

    public function __construct(PreorderService $preorderService)
    {
        $this->preorderService = $preorderService;
    }

    /**
     * Browse active pre-order campaigns.
     */
    public function index(Request $request): View
    {
        $commodityId = $request->query('commodity_id');
        $search = $request->query('q');

        $query = Preorder::with(['commodity', 'farmer.farmerProfile'])
            ->whereIn('status', ['Dibuka', 'Menunggu Panen']);

        if ($commodityId) {
            $query->where('commodity_id', $commodityId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $preorders = $query->orderBy('estimated_harvest_date')->paginate(12)->withQueryString();
        $commodities = Commodity::where('is_active', true)->orderBy('name')->get();

        return view('preorders.index', compact('preorders', 'commodities', 'commodityId', 'search'));
    }

    /**
     * View detail of a pre-order campaign.
     */
    public function show(Preorder $preorder): View
    {
        $preorder->load(['commodity', 'farmer.farmerProfile']);
        return view('preorders.show', compact('preorder'));
    }

    /**
     * Buyer books quota for a pre-order campaign.
     */
    public function book(Request $request, Preorder $preorder): RedirectResponse
    {
        $this->authorize('book', $preorder);

        $request->validate([
            'quantity' => ['required', 'numeric', 'min:' . ($preorder->min_order ?: 1), 'max:' . $preorder->preorder_available_quantity],
            'shipping_method' => ['required', 'string'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'quantity.required' => 'Masukkan jumlah kuota yang ingin Anda pesan.',
            'quantity.min' => "Pemesanan minimal adalah {$preorder->min_order} {$preorder->unit}.",
            'quantity.max' => "Pemesanan melebihi sisa kuota yang tersedia ({$preorder->preorder_available_quantity} {$preorder->unit}).",
            'shipping_method.required' => 'Pilih metode pengiriman.',
            'shipping_address.required' => 'Alamat pengiriman wajib diisi.',
        ]);

        try {
            $item = $this->preorderService->bookPreorder(Auth::user(), $preorder, $request->all());

            return redirect()->route('preorders.my')
                ->with('success', "Pemesanan kuota pre-order sebanyak {$item->quantity} {$preorder->unit} berhasil dipesan! Pantau status masa panen di halaman ini.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Buyer views their pre-order bookings.
     */
    public function myBookings(Request $request): View
    {
        $user = Auth::user();
        $status = $request->query('status');

        $query = PreorderItem::with(['preorder.commodity', 'preorder.farmer.farmerProfile', 'order'])
            ->where('buyer_id', $user->id);

        if ($status) {
            $query->where('status', $status);
        }

        $items = $query->latest()->paginate(10)->withQueryString();

        $counts = [
            'all' => PreorderItem::where('buyer_id', $user->id)->count(),
            'pending' => PreorderItem::where('buyer_id', $user->id)->where('status', 'Menunggu Panen')->count(),
            'converted' => PreorderItem::where('buyer_id', $user->id)->where('status', 'Dikonfirmasi')->count(),
            'cancelled' => PreorderItem::where('buyer_id', $user->id)->where('status', 'Dibatalkan')->count(),
        ];

        return view('preorders.my', compact('items', 'status', 'counts'));
    }

    /**
     * Cancel a booking.
     */
    public function cancelBooking(PreorderItem $item): RedirectResponse
    {
        try {
            $this->preorderService->cancelBooking($item, Auth::user());

            return back()->with('success', 'Pesanan kuota pre-order berhasil dibatalkan dan kuota dikembalikan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
