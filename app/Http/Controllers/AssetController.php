<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\NonDepreciableAsset;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assets = Asset::latest()->get();
        $nonDepreciableAssets = NonDepreciableAsset::latest()->get();

        $currentYear = date('Y');
        $years = \App\Models\DepreciationYear::getActiveYears();

        // Get counts for dashboard - Depreciable Assets
        $depreciableAssets = $assets->where('is_depreciable', true);
        $totalDepreciableAssets = $depreciableAssets->count();
        $activeDepreciableAssets = $depreciableAssets->where('status', 'active')->count();
        $maintenanceDepreciableAssets = $depreciableAssets->where('status', 'maintenance')->count();
        $inactiveDepreciableAssets = $depreciableAssets->whereIn('status', ['retired', 'lost', 'disposed'])->count();

        // Get counts for dashboard - Non-Depreciable Assets
        $nonDepreciableAssetsCount = $nonDepreciableAssets->count();
        $nonDepreciableActiveCount = $nonDepreciableAssets->where('status', 'active')->count();
        $nonDepreciableMaintenanceCount = $nonDepreciableAssets->where('status', 'maintenance')->count();
        $nonDepreciableInactiveCount = $nonDepreciableAssets->whereIn('status', ['retired', 'lost', 'disposed'])->count();

        // Combined totals
        $totalAssets = $totalDepreciableAssets + $nonDepreciableAssetsCount;

        return view('assets.index', compact(
            'assets',
            'currentYear',
            'years',
            'totalAssets',
            'totalDepreciableAssets',
            'activeDepreciableAssets',
            'maintenanceDepreciableAssets',
            'inactiveDepreciableAssets',
            'nonDepreciableAssetsCount',
            'nonDepreciableActiveCount',
            'nonDepreciableMaintenanceCount',
            'nonDepreciableInactiveCount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('assets.create');
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
            'depreciation_rate' => 'required|numeric|min:0|max:100',
            'depreciation_method' => 'required|in:straight_line,reducing_balance',
            'location' => 'nullable|string|max:255',
            'remark' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'is_depreciable' => 'required|boolean',
        ]);

        // Create asset first
        $asset = new Asset($validated);

        // Calculate depreciation values only if asset is depreciable
        if ($asset->is_depreciable) {
            $currentYear = date('Y');
            $asset->accumulated_depreciation = $asset->calculateAccumulatedDepreciation($currentYear);
            $asset->net_book_value = $asset->calculateNetBookValue($currentYear);
        } else {
            // For non-depreciable assets, set accumulated depreciation to 0 and net book value to cost
            $asset->accumulated_depreciation = 0;
            $asset->net_book_value = $asset->cost;
        }

        // Save the asset
        $asset->save();

        return redirect()->route('assets.index')
            ->with('success', 'Asset created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset)
    {
        $currentYear = date('Y');
        $years = \App\Models\DepreciationYear::getActiveYears();

        return view('assets.show', compact('asset', 'currentYear', 'years'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        return view('assets.edit', compact('asset'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        // Debug: Check if request is received
        \Log::info('Update request received for asset: ' . $asset->id);
        \Log::info('Request data: ', $request->all());

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
            'depreciation_rate' => 'required|numeric|min:0|max:100',
            'depreciation_method' => 'required|in:straight_line,reducing_balance',
            'location' => 'nullable|string|max:255',
            'remark' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'is_depreciable' => 'required|boolean',
        ]);

        \Log::info('Validated data: ', $validated);

        try {
            // Update the asset
            $asset->fill($validated);

            // Recalculate depreciation only if asset is depreciable
            if ($asset->is_depreciable) {
                $currentYear = date('Y');
                $asset->accumulated_depreciation = $asset->calculateAccumulatedDepreciation($currentYear);
                $asset->net_book_value = $asset->calculateNetBookValue($currentYear);
            } else {
                // For non-depreciable assets, set accumulated depreciation to 0 and net book value to cost
                $asset->accumulated_depreciation = 0;
                $asset->net_book_value = $asset->cost;
            }

            // Save the asset
            $asset->save();

            \Log::info('Asset updated successfully: ' . $asset->id);

            return redirect()->route('assets.show', $asset)
                ->with('success', 'Asset updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Error updating asset: ' . $e->getMessage());
            return back()->with('error', 'Error updating asset: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        $asset->delete();

        return redirect()->route('assets.index')
            ->with('success', 'Asset deleted successfully.');
    }
}
