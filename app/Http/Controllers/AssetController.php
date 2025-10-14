<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;

class AssetController extends Controller
{
    public function index()
    {
        $totalAssets = Asset::count();
        $activeAssets = Asset::where('status', 'active')->count();
        $maintenanceAssets = Asset::where('status', 'maintenance')->count();
        $disposedAssets = Asset::where('status', 'disposed')->count();

        return view('assets.index', compact('totalAssets', 'activeAssets', 'maintenanceAssets', 'disposedAssets'));
    }

    public function create()
    {
        return view('assets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'serial_number' => 'nullable|string|max:255|unique:assets,serial_number',
            'location' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'value' => 'nullable|numeric',
            'currency' => 'nullable|string|max:5',
            'status' => 'required|in:active,maintenance,disposed',
        ]);

        Asset::create($request->only([
            'name', 'category', 'description', 'serial_number', 'location', 'purchase_date', 'value', 'status', 'currency'
        ]));

        return redirect()->route('assets.list')->with('success', 'Asset added successfully.');
    }

    public function list()
    {
        $assets = Asset::latest()->get();
        return view('assets.list', compact('assets'));
    }

    public function edit(Asset $asset)
    {
        return view('assets.edit', compact('asset'));
    }

    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'serial_number' => "nullable|string|max:255|unique:assets,serial_number,{$asset->id}",
            'location' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'value' => 'nullable|numeric',
            'currency' => 'nullable|string|max:5',
            'status' => 'required|in:active,maintenance,disposed',
        ]);

        $asset->update($request->only([
            'name', 'category', 'description', 'serial_number', 'location', 'purchase_date', 'value', 'status', 'currency'
        ]));

        return redirect()->route('assets.list')->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return redirect()->back()->with('success', 'Asset deleted successfully.');
    }
}
