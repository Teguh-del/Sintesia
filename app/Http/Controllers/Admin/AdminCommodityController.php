<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commodity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminCommodityController extends Controller
{
    /**
     * Display listing of master commodities.
     */
    public function index(): View
    {
        $commodities = Commodity::withCount(['products', 'harvests', 'stocks'])
            ->orderBy('name', 'asc')
            ->paginate(15);

        return view('admin.commodities.index', compact('commodities'));
    }

    /**
     * Store a newly created commodity in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:commodities,name',
            'category' => 'required|string|max:100',
            'unit' => 'required|string|max:50',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:50',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = true;
        $validated['icon'] = !empty($validated['icon']) ? $validated['icon'] : 'sprout';

        Commodity::create($validated);

        return redirect()->route('admin.commodities.index')
            ->with('success', "Komoditas master '{$validated['name']}' berhasil ditambahkan.");
    }

    /**
     * Update the specified commodity in storage.
     */
    public function update(Request $request, Commodity $commodity): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:commodities,name,' . $commodity->id,
            'category' => 'required|string|max:100',
            'unit' => 'required|string|max:50',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:50',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        if (empty($validated['icon'])) {
            unset($validated['icon']);
        }

        $commodity->update($validated);

        return redirect()->route('admin.commodities.index')
            ->with('success', "Komoditas master '{$commodity->name}' berhasil diperbarui.");
    }

    /**
     * Toggle active status of commodity.
     */
    public function toggleStatus(Commodity $commodity): RedirectResponse
    {
        $commodity->is_active = !$commodity->is_active;
        $commodity->save();

        $statusText = $commodity->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Komoditas {$commodity->name} berhasil {$statusText}.");
    }
}
