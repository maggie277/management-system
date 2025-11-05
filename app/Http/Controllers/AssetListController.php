<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class AssetListController extends Controller
{
    /**
     * Display the comprehensive asset list with depreciation
     */
    public function index()
    {
        $assets = Asset::with('assignedUser')
            ->orderBy('name')
            ->get();

        $currentYear = date('Y');
        $years = range(2011, $currentYear);

        return view('assets.list', compact('assets', 'years', 'currentYear'));
    }

    /**
     * Show the form for creating a new asset with comprehensive fields
     */
    public function create()
    {
        return view('assets.create-comprehensive');
    }

    /**
     * Store a newly created comprehensive asset
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type_make' => 'nullable|string|max:255',
            'cost' => 'required|numeric|min:0',
            'date_of_purchase' => 'required|date',
            'serial_number' => 'nullable|string|unique:assets,serial_number',
            'chasis_no' => 'nullable|string',
            'ctpd_asset_code' => 'nullable|string|max:100',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:active,maintenance,retired,lost,disposed',
            'depreciation_rate' => 'required|numeric|min:0|max:100',
            'depreciation_method' => 'required|in:straight_line,reducing_balance',
            'location' => 'nullable|string|max:255',
            'remark' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        // Calculate initial net book value
        $validated['net_book_value'] = $validated['cost'];
        $validated['accumulated_depreciation'] = 0;

        $asset = Asset::create($validated);

        return redirect()->route('assets.list.index')
            ->with('success', 'Asset created successfully with depreciation tracking.');
    }

    /**
     * Calculate depreciation for all assets
     */
    public function calculateDepreciation()
    {
        $assets = Asset::where('status', 'active')->get();
        $currentYear = date('Y');

        foreach ($assets as $asset) {
            if ($asset->date_of_purchase && $asset->cost && $asset->depreciation_rate) {
                $purchaseYear = $asset->date_of_purchase->year;
                $yearsOfService = $currentYear - $purchaseYear;

                if ($yearsOfService > 0) {
                    $annualDepreciation = $asset->calculateAnnualDepreciation();
                    $accumulatedDepreciation = $annualDepreciation * $yearsOfService;
                    $netBookValue = max(0, $asset->cost - $accumulatedDepreciation);

                    $asset->update([
                        'accumulated_depreciation' => $accumulatedDepreciation,
                        'net_book_value' => $netBookValue,
                    ]);
                }
            }
        }

        return redirect()->route('assets.list.index')
            ->with('success', 'Depreciation calculated for all assets.');
    }
}
