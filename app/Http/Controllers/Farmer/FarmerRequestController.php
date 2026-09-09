<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Models\Commodity;
use App\Models\CommodityRequest;
use App\Models\Product;
use App\Models\RequestOffer;
use App\Services\CommodityRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FarmerRequestController extends Controller
{
    protected CommodityRequestService $requestService;

    public function __construct(CommodityRequestService $requestService)
    {
        $this->requestService = $requestService;
    }

    /**
     * Farmer browses active commodity requests and their submitted offers.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $tab = $request->query('tab', 'open'); // 'open' or 'my_offers'
        $commodityId = $request->query('commodity_id');

        $commodities = Commodity::where('is_active', true)->orderBy('name')->get();

        if ($tab === 'my_offers') {
            $query = RequestOffer::with(['commodityRequest.commodity', 'commodityRequest.user', 'product'])
                ->where('farmer_id', $user->id);

            $offers = $query->latest()->paginate(10)->withQueryString();
            $requests = null;
        } else {
            $query = CommodityRequest::with(['commodity', 'user.consumerProfile', 'user.collectorProfile', 'offers'])
                ->whereIn('status', ['Aktif', 'Mendapat Penawaran'])
                ->whereDate('deadline', '>=', today());

            if ($commodityId) {
                $query->where('commodity_id', $commodityId);
            }

            $requests = $query->latest()->paginate(10)->withQueryString();
            $offers = null;
        }

        $counts = [
            'open' => CommodityRequest::whereIn('status', ['Aktif', 'Mendapat Penawaran'])->whereDate('deadline', '>=', today())->count(),
            'my_offers' => RequestOffer::where('farmer_id', $user->id)->count(),
            'my_offers_accepted' => RequestOffer::where('farmer_id', $user->id)->where('status', 'Diterima')->count(),
        ];

        return view('farmer.requests.index', compact('requests', 'offers', 'commodities', 'tab', 'commodityId', 'counts'));
    }

    /**
     * Farmer views details of a commodity request and submits an offer.
     */
    public function show(CommodityRequest $commodityRequest): View
    {
        $user = Auth::user();
        $commodityRequest->load(['commodity', 'user.consumerProfile', 'user.collectorProfile']);

        // Check if this farmer already submitted an offer for this request
        $existingOffer = RequestOffer::where('commodity_request_id', $commodityRequest->id)
            ->where('farmer_id', $user->id)
            ->first();

        // Farmer's available products for this commodity
        $farmerProducts = Product::where('user_id', $user->id)
            ->where('commodity_id', $commodityRequest->commodity_id)
            ->where('status', 'active')
            ->get();

        return view('farmer.requests.show', compact('commodityRequest', 'existingOffer', 'farmerProducts'));
    }

    /**
     * Farmer submits a supply offer for the request.
     */
    public function submitOffer(Request $request, CommodityRequest $commodityRequest): RedirectResponse
    {
        $this->authorize('offer', $commodityRequest);

        $request->validate([
            'offered_quantity' => ['required', 'numeric', 'min:0.01'],
            'offered_price' => ['required', 'numeric', 'min:1'],
            'product_id' => ['nullable', 'exists:products,id'],
            'shipping_method' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'offered_quantity.required' => 'Masukkan kuantitas pasokan yang Anda tawarkan.',
            'offered_price.required' => 'Masukkan harga penawaran per satuan.',
            'shipping_method.required' => 'Pilih metode pengiriman pasokan.',
        ]);

        try {
            $offer = $this->requestService->createOffer(Auth::user(), $commodityRequest, $request->all());

            return redirect()->route('farmer.requests.show', $commodityRequest)
                ->with('success', "Penawaran pasokan sebesar {$offer->offered_quantity} {$commodityRequest->unit} seharga Rp " . number_format($offer->offered_price, 0, ',', '.') . "/{$commodityRequest->unit} berhasil dikirim ke pembeli.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
