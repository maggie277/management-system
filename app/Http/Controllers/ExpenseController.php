<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Budget;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    // Show all expenses
    public function index()
    {
        $expenses = Expense::with('budget.donor')->get();
        return view('expenses.index', compact('expenses'));
    }

    // Show form to create new expense
    public function create()
    {
        $budgets = Budget::all();
        return view('expenses.create', compact('budgets'));
    }

    // Save new expense
    public function store(Request $request)
    {
        $request->validate([
            'budget_id'    => 'required|exists:budgets,id',
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0',
            'notes'        => 'nullable|string',
            'expense_date' => 'required|date',
        ]);

        Expense::create($request->all());

        return redirect()->route('expenses.index')->with('success', 'Expense created successfully.');
    }

    // Show single expense
    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    // Show form to edit expense
    public function edit(Expense $expense)
    {
        $budgets = Budget::all();
        return view('expenses.edit', compact('expense', 'budgets'));
    }

    // Update expense
    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'budget_id'    => 'required|exists:budgets,id',
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0',
            'notes'        => 'nullable|string',
            'expense_date' => 'required|date',
        ]);

        $expense->update($request->all());

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    // Delete expense
    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }
}
