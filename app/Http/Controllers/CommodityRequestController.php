<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Models\CommodityRequest;
use App\Models\RequestOffer;
use App\Services\CommodityRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CommodityRequestController extends Controller
{
    protected CommodityRequestService $requestService;

    public function __construct(CommodityRequestService $requestService)
    {
        $this->requestService = $requestService;
    }

    /**
     * Buyer views their commodity requests.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $status = $request->query('status');

        $query = CommodityRequest::with(['commodity', 'offers.farmer.farmerProfile'])
            ->where('user_id', $user->id);

        if ($status) {
            $query->where('status', $status);
        }

        $requests = $query->latest()->paginate(10)->withQueryString();

        $counts = [
            'all' => CommodityRequest::where('user_id', $user->id)->count(),
            'active' => CommodityRequest::where('user_id', $user->id)->where('status', 'Aktif')->count(),
            'offers' => CommodityRequest::where('user_id', $user->id)->where('status', 'Mendapat Penawaran')->count(),
            'fulfilled' => CommodityRequest::where('user_id', $user->id)->where('status', 'Dipenuhi')->count(),
            'closed' => CommodityRequest::where('user_id', $user->id)->whereIn('status', ['Ditutup', 'Dibatalkan'])->count(),
        ];

        return view('requests.index', compact('requests', 'status', 'counts'));
    }

    /**
     * Show form to create a new commodity request.
     */
    public function create(): View
    {
        $commodities = Commodity::where('is_active', true)->orderBy('name')->get();
        return view('requests.create', compact('commodities'));
    }

    /**
     * Store new commodity request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'commodity_id' => ['required', 'exists:commodities,id'],
            'title' => ['required', 'string', 'max:255'],
            'required_quantity' => ['required', 'numeric', 'min:0.01'],
            'unit' => ['required', 'string', 'max:20'],
            'max_price' => ['required', 'numeric', 'min:1'],
            'location' => ['required', 'string', 'max:255'],
            'deadline' => ['required', 'date', 'after_or_equal:today'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'commodity_id.required' => 'Pilih komoditas yang Anda butuhkan.',
            'title.required' => 'Judul permintaan wajib diisi.',
            'required_quantity.required' => 'Kuantitas kebutuhan wajib diisi.',
            'max_price.required' => 'Batas harga maksimal wajib diisi.',
            'location.required' => 'Lokasi pengiriman/tujuan wajib diisi.',
            'deadline.required' => 'Batas waktu (deadline) wajib diisi.',
            'deadline.after_or_equal' => 'Batas waktu tidak boleh di masa lalu.',
        ]);

        try {
            $commodityRequest = $this->requestService->createRequest(Auth::user(), $request->all());

            return redirect()->route('requests.show', $commodityRequest)
                ->with('success', "Permintaan komoditas '{$commodityRequest->title}' berhasil dipublikasikan ke seluruh Petani terdaftar.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * View detail of a commodity request with incoming offers.
     */
    public function show(CommodityRequest $commodityRequest): View
    {
        $this->authorize('view', $commodityRequest);

        $commodityRequest->load(['commodity', 'user', 'offers.farmer.farmerProfile', 'offers.product']);

        return view('requests.show', compact('commodityRequest'));
    }

    /**
     * Buyer accepts a farmer's offer.
     */
    public function acceptOffer(RequestOffer $offer): RedirectResponse
    {
        $this->authorize('acceptOffer', $offer->commodityRequest);

        try {
            $order = $this->requestService->acceptOffer($offer, Auth::user());

            return redirect()->route('orders.show', $order)
                ->with('success', "Penawaran pasokan disetujui! Pesanan resmi #{$order->order_number} telah dibuat dan diteruskan ke petani.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Buyer rejects a farmer's offer.
     */
    public function rejectOffer(RequestOffer $offer): RedirectResponse
    {
        $this->authorize('acceptOffer', $offer->commodityRequest);

        try {
            $this->requestService->rejectOffer($offer, Auth::user());

            return back()->with('success', 'Penawaran pasokan dari petani telah ditolak.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Buyer closes their commodity request.
     */
    public function close(CommodityRequest $commodityRequest): RedirectResponse
    {
        $this->authorize('update', $commodityRequest);

        try {
            $this->requestService->closeRequest($commodityRequest, Auth::user());

            return back()->with('success', 'Permintaan komoditas berhasil ditutup.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
