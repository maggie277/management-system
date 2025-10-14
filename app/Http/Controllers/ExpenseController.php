<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Budget;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function create()
    {
        $budgets = Budget::with('donor')->get();
        return view('expenses.create', compact('budgets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'budget_id' => 'required|exists:budgets,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $expense = Expense::create($validated);

        // ✅ No need to save 'remaining' column — it's calculated dynamically
        // But we can still ensure data integrity by reloading relationships
        $budget = $expense->budget;
        $budget->load('expenses');

        return redirect()->route('budgets.index')
            ->with('success', 'Expense added successfully.');
    }

    public function edit(Expense $expense)
    {
        $budgets = Budget::with('donor')->get();
        return view('expenses.edit', compact('expense', 'budgets'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'budget_id' => 'required|exists:budgets,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $expense->update($validated);

        // ✅ Recalculate totals dynamically (no DB column needed)
        $budget = $expense->budget;
        $budget->load('expenses');

        return redirect()->route('budgets.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $budget = $expense->budget; // store reference before delete
        $expense->delete();

        // ✅ Reload relationships to reflect updated expenses
        $budget->load('expenses');

        return redirect()->route('budgets.index')
            ->with('success', 'Expense deleted successfully.');
    }
}
