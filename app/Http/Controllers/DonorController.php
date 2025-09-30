<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Illuminate\Http\Request;

class DonorController extends Controller
{
    // Show all donors
    public function index()
    {
        $donors = Donor::all();
        return view('donors.index', compact('donors'));
    }

    // Show form to create new donor
    public function create()
    {
        return view('donors.create');
    }

    // Save new donor
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        Donor::create($request->all());

        return redirect()->route('donors.index')->with('success', 'Donor created successfully.');
    }

    // Show single donor
    public function show(Donor $donor)
    {
        return view('donors.show', compact('donor'));
    }

    // Show form to edit donor
    public function edit(Donor $donor)
    {
        return view('donors.edit', compact('donor'));
    }

    // Update donor info
    public function update(Request $request, Donor $donor)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $donor->update($request->all());

        return redirect()->route('donors.index')->with('success', 'Donor updated successfully.');
    }

    // Delete donor
    public function destroy(Donor $donor)
    {
        $donor->delete();
        return redirect()->route('donors.index')->with('success', 'Donor deleted successfully.');
    }
}
