<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Donor;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    // Show all budgets
    public function index()
    {
        $budgets = Budget::with('donor')->get();
        return view('budgets.index', compact('budgets'));
    }

    // Show form to create new budget
    public function create()
    {
        $donors = Donor::all();
        return view('budgets.create', compact('donors'));
    }

    // Save new budget
    public function store(Request $request)
    {
        $request->validate([
            'donor_id'    => 'required|exists:donors,id',
            'amount'      => 'required|numeric|min:0',
            'threshold'   => 'nullable|numeric|min:0',
            'currency'    => 'required|string|max:5',
            'description' => 'nullable|string|max:255',
        ]);

        Budget::create($request->all());

        return redirect()->route('budgets.index')->with('success', 'Budget created successfully.');
    }

    // Show single budget
    public function show(Budget $budget)
    {
        return view('budgets.show', compact('budget'));
    }

    // Show form to edit budget
    public function edit(Budget $budget)
    {
        $donors = Donor::all();
        return view('budgets.edit', compact('budget', 'donors'));
    }

    // Update budget
    public function update(Request $request, Budget $budget)
    {
        $request->validate([
            'donor_id'    => 'required|exists:donors,id',
            'amount'      => 'required|numeric|min:0',
            'threshold'   => 'nullable|numeric|min:0',
            'currency'    => 'required|string|max:5',
            'description' => 'nullable|string|max:255',
        ]);

        $budget->update($request->all());

        return redirect()->route('budgets.index')->with('success', 'Budget updated successfully.');
    }

    // Delete budget
    public function destroy(Budget $budget)
    {
        $budget->delete();
        return redirect()->route('budgets.index')->with('success', 'Budget deleted successfully.');
    }
}
