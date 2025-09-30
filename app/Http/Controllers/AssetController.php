<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;

class AssetController extends Controller
{
    public function index()
    {
        return view('assets.index');
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
            'status' => 'required|in:active,maintenance,disposed',
        ]);

        Asset::create($request->all());

        return redirect()->route('assets.list')->with('success', 'Asset added successfully.');
    }

    public function list()
    {
        $assets = Asset::latest()->get();
        return view('assets.list', compact('assets'));
    }

    public function edit($id)
    {
        $asset = Asset::findOrFail($id);
        return view('assets.edit', compact('asset'));
    }

    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'serial_number' => "nullable|string|max:255|unique:assets,serial_number,$id",
            'location' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'value' => 'nullable|numeric',
            'status' => 'required|in:active,maintenance,disposed',
        ]);

        $asset->update($request->all());

        return redirect()->route('assets.list')->with('success', 'Asset updated successfully.');
    }

    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();

        return redirect()->back()->with('success', 'Asset deleted successfully.');
    }
}
