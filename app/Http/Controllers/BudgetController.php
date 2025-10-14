<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Donor;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    /**
     * Display a listing of budgets.
     */
    public function index()
    {
        $budgets = Budget::with(['donor', 'expenses'])->get();
        return view('budgets.index', compact('budgets'));
    }

    /**
     * Show the form for creating a new budget.
     */
    public function create()
    {
        $donors = Donor::all();
        return view('budgets.create', compact('donors'));
    }

    /**
     * Store a newly created budget in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'donor_id' => 'required|exists:donors,id',
            'amount' => 'required|numeric|min:0',
            'threshold' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:10',
            'description' => 'nullable|string',
        ]);

        Budget::create($validated);

        return redirect()->route('budgets.index')
                         ->with('success', 'Budget created successfully.');
    }

    /**
     * Show the form for editing the specified budget.
     */
    public function edit(Budget $budget)
    {
        $budget->load('expenses', 'donor');
        $donors = Donor::all();
        return view('budgets.edit', compact('budget', 'donors'));
    }

    /**
     * Update the specified budget in storage.
     */
    public function update(Request $request, Budget $budget)
    {
        $validated = $request->validate([
            'donor_id' => 'required|exists:donors,id',
            'amount' => 'required|numeric|min:0',
            'threshold' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:10',
            'description' => 'nullable|string',
        ]);

        // Update budget details (without saving 'remaining' to DB)
        $budget->update($validated);

        // Load expenses if needed in the view (optional)
        $budget->load('expenses');

        return redirect()->route('budgets.index')
                         ->with('success', 'Budget updated successfully.');
    }

    /**
     * Remove the specified budget from storage.
     */
    public function destroy(Budget $budget)
    {
        $budget->delete();

        return redirect()->route('budgets.index')
                         ->with('success', 'Budget deleted.');
    }
}
