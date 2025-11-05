<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NonDepreciableAsset;

class NonDepreciableAssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assets = NonDepreciableAsset::latest()->get();
        return view('non-depreciable-assets.index', compact('assets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('non-depreciable-assets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2011|max:' . (date('Y') + 1),
            'name' => 'required|string|max:255',
            'type_make' => 'nullable|string|max:255',
            'cost' => 'required|numeric|min:0',
            'date_of_purchase' => 'required|date',
            'serial_chasis_no' => 'required|string|max:255',
            'ctpd_asset_code' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:active,maintenance,retired,lost,disposed',
            'location' => 'nullable|string|max:255',
            'remark' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        // For non-depreciable assets, net book value always equals cost
        $validated['net_book_value'] = $validated['cost'];

        NonDepreciableAsset::create($validated);

        return redirect()->route('non-depreciable-assets.index')
            ->with('success', 'Non-depreciable asset created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(NonDepreciableAsset $nonDepreciableAsset)
    {
        return view('non-depreciable-assets.show', compact('nonDepreciableAsset'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NonDepreciableAsset $nonDepreciableAsset)
    {
        return view('non-depreciable-assets.edit', compact('nonDepreciableAsset'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NonDepreciableAsset $nonDepreciableAsset)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2011|max:' . (date('Y') + 1),
            'name' => 'required|string|max:255',
            'type_make' => 'nullable|string|max:255',
            'cost' => 'required|numeric|min:0',
            'date_of_purchase' => 'required|date',
            'serial_chasis_no' => 'required|string|max:255',
            'ctpd_asset_code' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:active,maintenance,retired,lost,disposed',
            'location' => 'nullable|string|max:255',
            'remark' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        // For non-depreciable assets, net book value always equals cost
        $validated['net_book_value'] = $validated['cost'];

        $nonDepreciableAsset->update($validated);

        return redirect()->route('non-depreciable-assets.show', $nonDepreciableAsset)
            ->with('success', 'Non-depreciable asset updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NonDepreciableAsset $nonDepreciableAsset)
    {
        $nonDepreciableAsset->delete();

        return redirect()->route('non-depreciable-assets.index')
            ->with('success', 'Non-depreciable asset deleted successfully.');
    }
}
