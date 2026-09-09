<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Models\PriceOffer;
use App\Services\NegotiationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FarmerNegotiationController extends Controller
{
    protected NegotiationService $negotiationService;

    public function __construct(NegotiationService $negotiationService)
    {
        $this->negotiationService = $negotiationService;
    }

    /**
     * Farmer views incoming price negotiations.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $status = $request->query('status');

        $query = PriceOffer::with(['product.commodity', 'buyer.consumerProfile', 'buyer.collectorProfile'])
            ->where('seller_id', $user->id);

        if ($status) {
            $query->where('status', $status);
        }

        $offers = $query->latest()->paginate(10)->withQueryString();

        $counts = [
            'all' => PriceOffer::where('seller_id', $user->id)->count(),
            'pending' => PriceOffer::where('seller_id', $user->id)->where('status', 'Menunggu')->count(),
            'counter' => PriceOffer::where('seller_id', $user->id)->where('status', 'Counter Offer')->count(),
            'completed' => PriceOffer::where('seller_id', $user->id)->where('status', 'Selesai')->count(),
            'rejected' => PriceOffer::where('seller_id', $user->id)->where('status', 'Ditolak')->count(),
        ];

        return view('farmer.negotiations.index', compact('offers', 'status', 'counts'));
    }

    /**
     * Farmer accepts buyer's offer.
     */
    public function accept(PriceOffer $offer): RedirectResponse
    {
        $this->authorize('accept', $offer);

        try {
            $order = $this->negotiationService->acceptOffer($offer, Auth::user());

            return redirect()->route('farmer.orders.show', $order)
                ->with('success', "Tawaran harga berhasil disepakati! Pesanan masuk baru #{$order->order_number} telah dibuat.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Farmer submits a counter offer.
     */
    public function counter(Request $request, PriceOffer $offer): RedirectResponse
    {
        $this->authorize('counter', $offer);

        $request->validate([
            'counter_price' => ['required', 'numeric', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'counter_price.required' => 'Masukkan nominal tawaran harga balik Anda.',
        ]);

        try {
            $this->negotiationService->counterOffer(
                $offer,
                Auth::user(),
                (float) $request->input('counter_price'),
                $request->input('notes')
            );

            return back()->with('success', "Tawaran balik sebesar Rp " . number_format($request->input('counter_price'), 0, ',', '.') . " telah dikirimkan ke pembeli.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Farmer rejects buyer's offer.
     */
    public function reject(Request $request, PriceOffer $offer): RedirectResponse
    {
        $this->authorize('reject', $offer);

        try {
            $this->negotiationService->rejectOffer($offer, Auth::user(), $request->input('reason'));

            return back()->with('success', 'Tawaran negosiasi harga berhasil ditolak.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
