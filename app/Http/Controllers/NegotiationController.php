<?php

namespace App\Http\Controllers;

use App\Models\PriceOffer;
use App\Models\Product;
use App\Services\NegotiationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NegotiationController extends Controller
{
    protected NegotiationService $negotiationService;

    public function __construct(NegotiationService $negotiationService)
    {
        $this->negotiationService = $negotiationService;
    }

    /**
     * Buyer views their negotiations.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $status = $request->query('status');

        $query = PriceOffer::with(['product.commodity', 'seller.farmerProfile'])
            ->where('buyer_id', $user->id);

        if ($status) {
            $query->where('status', $status);
        }

        $offers = $query->latest()->paginate(10)->withQueryString();

        $counts = [
            'all' => PriceOffer::where('buyer_id', $user->id)->count(),
            'pending' => PriceOffer::where('buyer_id', $user->id)->where('status', 'Menunggu')->count(),
            'counter' => PriceOffer::where('buyer_id', $user->id)->where('status', 'Counter Offer')->count(),
            'completed' => PriceOffer::where('buyer_id', $user->id)->where('status', 'Selesai')->count(),
            'rejected' => PriceOffer::where('buyer_id', $user->id)->where('status', 'Ditolak')->count(),
        ];

        return view('negotiations.index', compact('offers', 'status', 'counts'));
    }

    /**
     * Store new price offer from marketplace.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'offered_price' => ['required', 'numeric', 'min:1'],
            'shipping_method' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'offered_price.required' => 'Masukkan harga penawaran Anda.',
            'quantity.required' => 'Masukkan kuantitas komoditas.',
        ]);

        $product = Product::findOrFail($request->input('product_id'));

        try {
            $offer = $this->negotiationService->createOffer(Auth::user(), $product, $request->all());

            return back()->with('success', "Tawaran harga sebesar Rp " . number_format($offer->offered_price, 0, ',', '.') . " berhasil dikirimkan ke Petani. Pantau status tawaran di menu Negosiasi.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Buyer accepts counter offer from farmer.
     */
    public function acceptCounter(PriceOffer $offer): RedirectResponse
    {
        $this->authorize('accept', $offer);

        try {
            $order = $this->negotiationService->acceptCounterOffer($offer, Auth::user());

            return redirect()->route('orders.show', $order)
                ->with('success', "Tawaran balik berhasil disepakati! Pesanan resmi #{$order->order_number} telah dibuat.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Buyer rejects counter offer.
     */
    public function reject(Request $request, PriceOffer $offer): RedirectResponse
    {
        $this->authorize('reject', $offer);

        try {
            $this->negotiationService->rejectOffer($offer, Auth::user(), $request->input('reason'));

            return back()->with('success', 'Tawaran negosiasi berhasil ditolak/dibatalkan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
