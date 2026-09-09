<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Models\Commodity;
use App\Models\Preorder;
use App\Services\PreorderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FarmerPreorderController extends Controller
{
    protected PreorderService $preorderService;

    public function __construct(PreorderService $preorderService)
    {
        $this->preorderService = $preorderService;
    }

    /**
     * Farmer views their pre-order campaigns.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $status = $request->query('status');

        $query = Preorder::with(['commodity', 'items.buyer'])
            ->where('user_id', $user->id);

        if ($status) {
            $query->where('status', $status);
        }

        $preorders = $query->latest()->paginate(10)->withQueryString();

        $counts = [
            'all' => Preorder::where('user_id', $user->id)->count(),
            'open' => Preorder::where('user_id', $user->id)->where('status', 'Dibuka')->count(),
            'waiting' => Preorder::where('user_id', $user->id)->where('status', 'Menunggu Panen')->count(),
            'processing' => Preorder::where('user_id', $user->id)->whereIn('status', ['Siap Diproses', 'Diproses'])->count(),
            'completed' => Preorder::where('user_id', $user->id)->where('status', 'Selesai')->count(),
        ];

        return view('farmer.preorders.index', compact('preorders', 'status', 'counts'));
    }

    /**
     * Show form to create a new pre-order campaign.
     */
    public function create(): View
    {
        $commodities = Commodity::where('is_active', true)->orderBy('name')->get();
        return view('farmer.preorders.create', compact('commodities'));
    }

    /**
     * Store new pre-order campaign.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Preorder::class);

        $request->validate([
            'commodity_id' => ['required', 'exists:commodities,id'],
            'title' => ['required', 'string', 'max:255'],
            'estimated_production' => ['required', 'numeric', 'min:1'],
            'price' => ['required', 'numeric', 'min:1'],
            'unit' => ['required', 'string', 'max:20'],
            'min_order' => ['required', 'numeric', 'min:1'],
            'estimated_harvest_date' => ['required', 'date', 'after_or_equal:today'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'max:2048'],
        ], [
            'commodity_id.required' => 'Pilih jenis komoditas panen.',
            'title.required' => 'Judul kampanye pre-order wajib diisi.',
            'estimated_production.required' => 'Estimasi hasil panen wajib diisi.',
            'price.required' => 'Harga pre-order per satuan wajib diisi.',
            'estimated_harvest_date.required' => 'Perkiraan tanggal panen wajib diisi.',
            'estimated_harvest_date.after_or_equal' => 'Tanggal panen tidak boleh di masa lalu.',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('preorders', 'public');
            $data['image'] = '/storage/' . $path;
        }

        try {
            $preorder = $this->preorderService->createPreorder(Auth::user(), $data);

            return redirect()->route('farmer.preorders.show', $preorder)
                ->with('success', "Kampanye Pre-Order '{$preorder->title}' berhasil dibuka!");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Farmer views pre-order detail and incoming bookings.
     */
    public function show(Preorder $preorder): View
    {
        $this->authorize('update', $preorder);

        $preorder->load(['commodity', 'items.buyer.consumerProfile', 'items.buyer.collectorProfile', 'items.order']);

        return view('farmer.preorders.show', compact('preorder'));
    }

    /**
     * Update pre-order status (e.g. advance to Siap Diproses / Diproses / Selesai).
     */
    public function updateStatus(Request $request, Preorder $preorder): RedirectResponse
    {
        $this->authorize('update', $preorder);

        $request->validate([
            'status' => ['required', 'in:Dibuka,Menunggu Panen,Siap Diproses,Diproses,Selesai,Dibatalkan'],
        ]);

        try {
            $newStatus = $request->input('status');
            $this->preorderService->updateStatus($preorder, Auth::user(), $newStatus);

            $msg = "Status kampanye pre-order berhasil diperbarui ke '{$newStatus}'.";
            if (in_array($newStatus, ['Siap Diproses', 'Diproses'])) {
                $msg .= " Pemesanan kuota yang masuk telah otomatis dikonversi menjadi Pesanan Resmi!";
            }

            return back()->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
