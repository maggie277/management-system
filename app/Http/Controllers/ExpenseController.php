<?php
// app/Http/Controllers/ExpenseController.php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Expense;
use App\Models\BudgetItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with(['budget', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Calculate summary statistics
        $stats = [
            'total_expenses' => Expense::whereIn('status', ['approved', 'paid'])->sum('amount_zmw'),
            'pending_count' => Expense::where('status', 'pending')->count(),
            'pending_amount' => Expense::where('status', 'pending')->sum('amount_zmw'),
            'approved_count' => Expense::where('status', 'approved')->count(),
            'paid_count' => Expense::where('status', 'paid')->count(),
            'rejected_count' => Expense::where('status', 'rejected')->count(),
            'total_count' => Expense::count(),
        ];

        // Add paid_this_month safely
        if (Schema::hasColumn('expenses', 'paid_at')) {
            $stats['paid_this_month'] = Expense::where('status', 'paid')
                ->whereYear('paid_at', now()->year)
                ->whereMonth('paid_at', now()->month)
                ->sum('amount_zmw');
        } else {
            $stats['paid_this_month'] = Expense::where('status', 'paid')
                ->whereYear('expense_date', now()->year)
                ->whereMonth('expense_date', now()->month)
                ->sum('amount_zmw');
        }

        return view('expenses.index', compact('expenses', 'stats'));
    }

    public function create(Request $request)
    {
        $budgets = Budget::approved()
            ->orderBy('project_code')
            ->get();

        return view('expenses.create', compact('budgets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'budget_id' => 'required|exists:budgets,id',
            'budget_item_id' => 'nullable|exists:budget_items,id',
            'description' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'purpose' => 'nullable|string',
            'amount_zmw' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'payment_method' => 'nullable|string|max:50',
            'vendor_payee' => 'nullable|string|max:200',
            'receipt_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $budget = Budget::findOrFail($validated['budget_id']);

        // Update budget totals to get current remaining
        $budget->updateTotals();

        // Check if budget has enough remaining funds
        if ($budget->remaining_budget_zmw < $validated['amount_zmw']) {
            return back()->withErrors(['amount_zmw' => 'Insufficient budget remaining. Available: ' .
                number_format($budget->remaining_budget_zmw, 2)])->withInput();
        }

        // Calculate USD amount using exchange rate
        $amountUsd = $validated['amount_zmw'] / $budget->exchange_rate;

        DB::beginTransaction();
        try {
            $expense = Expense::create([
                'expense_number' => Expense::generateExpenseNumber(),
                'budget_id' => $validated['budget_id'],
                'budget_item_id' => $validated['budget_item_id'] ?? null,
                'description' => $validated['description'],
                'category' => $validated['category'],
                'purpose' => $validated['purpose'],
                'amount_zmw' => $validated['amount_zmw'],
                'amount_usd' => $amountUsd,
                'expense_date' => $validated['expense_date'],
                'payment_method' => $validated['payment_method'],
                'vendor_payee' => $validated['vendor_payee'],
                'receipt_number' => $validated['receipt_number'],
                'notes' => $validated['notes'],
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('expenses.show', $expense)
                ->with('success', 'Expense created successfully. Pending approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating expense: ' . $e->getMessage());
            return back()->with('error', 'Error creating expense: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Expense $expense)
    {
        $expense->load(['budget', 'budgetItem', 'createdBy', 'approvedBy', 'paidBy', 'rejectedBy']);

        $budget = $expense->budget;
        $utilization = null;

        if ($budget) {
            $budget->updateTotals();

            $expensesByCategory = $budget->expenses()
                ->whereIn('status', ['approved', 'paid'])
                ->select('category', DB::raw('SUM(amount_zmw) as total'))
                ->groupBy('category')
                ->get();

            $utilization = [
                'total_expenses' => $budget->total_expenses_zmw,
                'remaining' => $budget->remaining_budget_zmw,
                'percentage_used' => $budget->percentage_used,
                'expenses_by_category' => $expensesByCategory,
                'recent_expenses' => $budget->expenses()
                    ->whereIn('status', ['approved', 'paid'])
                    ->where('id', '!=', $expense->id)
                    ->orderBy('expense_date', 'desc')
                    ->limit(5)
                    ->get()
            ];
        }

        return view('expenses.show', compact('expense', 'utilization'));
    }

    public function edit(Expense $expense)
    {
        // Only allow editing of draft or pending expenses
        if (!in_array($expense->status, ['draft', 'pending'])) {
            return redirect()->route('expenses.show', $expense)
                ->with('error', 'This expense cannot be edited as it has already been ' . $expense->status);
        }

        $budgets = Budget::approved()->orderBy('project_code')->get();

        // Load budget items for the selected budget
        $budgetItems = collect();
        if ($expense->budget_id) {
            $budgetItems = BudgetItem::where('budget_id', $expense->budget_id)
                ->orderBy('section')
                ->orderBy('sort_order')
                ->get();
        }

        return view('expenses.edit', compact('expense', 'budgets', 'budgetItems'));
    }

    public function update(Request $request, Expense $expense)
    {
        // Only allow updating draft or pending expenses
        if (!in_array($expense->status, ['draft', 'pending'])) {
            return redirect()->route('expenses.show', $expense)
                ->with('error', 'This expense cannot be edited as it has already been ' . $expense->status);
        }

        $validated = $request->validate([
            'budget_id' => 'required|exists:budgets,id',
            'budget_item_id' => 'nullable|exists:budget_items,id',
            'description' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'purpose' => 'nullable|string',
            'amount_zmw' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'payment_method' => 'nullable|string|max:50',
            'vendor_payee' => 'nullable|string|max:200',
            'receipt_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $budget = Budget::findOrFail($validated['budget_id']);

        // If amount changed, check remaining budget
        $amountDifference = $validated['amount_zmw'] - $expense->amount_zmw;
        if ($amountDifference > 0 && $expense->status === 'pending') {
            $budget->updateTotals();
            // Calculate available remaining including the original expense amount
            $availableRemaining = $budget->remaining_budget_zmw + $expense->amount_zmw;
            if ($availableRemaining < $validated['amount_zmw']) {
                return back()->withErrors(['amount_zmw' => 'Insufficient budget remaining. Available: ' .
                    number_format($availableRemaining, 2)])->withInput();
            }
        }

        $amountUsd = $validated['amount_zmw'] / $budget->exchange_rate;

        DB::beginTransaction();
        try {
            $expense->update([
                'budget_id' => $validated['budget_id'],
                'budget_item_id' => $validated['budget_item_id'] ?? null,
                'description' => $validated['description'],
                'category' => $validated['category'],
                'purpose' => $validated['purpose'],
                'amount_zmw' => $validated['amount_zmw'],
                'amount_usd' => $amountUsd,
                'expense_date' => $validated['expense_date'],
                'payment_method' => $validated['payment_method'],
                'vendor_payee' => $validated['vendor_payee'],
                'receipt_number' => $validated['receipt_number'],
                'notes' => $validated['notes'],
                'updated_by' => auth()->id(),
            ]);

            // Update budget totals if expense was already approved/paid
            if (in_array($expense->status, ['approved', 'paid'])) {
                $budget->updateTotals();
            }

            DB::commit();

            return redirect()->route('expenses.show', $expense)
                ->with('success', 'Expense updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating expense: ' . $e->getMessage());
            return back()->with('error', 'Error updating expense: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Get budget items grouped by section for dropdown.
     */
    public function getBudgetItems(Budget $budget)
    {
        try {
            $items = $budget->items()->orderBy('section')->orderBy('sort_order')->get();

            $groupedItems = [];

            foreach ($items as $item) {
                $section = $item->section ?: 'General Items';

                if (!isset($groupedItems[$section])) {
                    $groupedItems[$section] = [];
                }

                $groupedItems[$section][] = [
                    'id' => $item->id,
                    'description' => $item->description,
                    'cost' => $item->cost,
                    'total_amount_zmw' => $item->total_amount_zmw,
                    'total_amount_usd' => $item->total_amount_usd,
                    'section' => $item->section,
                    'objective' => $item->objective,
                    'activity' => $item->activity,
                ];
            }

            return response()->json($groupedItems);

        } catch (\Exception $e) {
            Log::error('Error loading budget items: ' . $e->getMessage());
            return response()->json(['error' => 'Error loading budget items'], 500);
        }
    }

    /**
     * Get budget remaining amount and statistics.
     */
    public function getBudgetRemaining(Budget $budget)
    {
        try {
            // Update totals to ensure they're current
            $budget->updateTotals();

            return response()->json([
                'total_budget' => $budget->total_budget_zmw,
                'total_expenses' => $budget->total_expenses_zmw,
                'remaining' => $budget->remaining_budget_zmw,
                'exchange_rate' => $budget->exchange_rate,
                'percentage_used' => $budget->percentage_used,
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading budget info: ' . $e->getMessage());
            return response()->json(['error' => 'Error loading budget info'], 500);
        }
    }

    public function approve(Expense $expense)
    {
        if ($expense->status !== 'pending') {
            return redirect()->route('expenses.show', $expense)
                ->with('error', 'Only pending expenses can be approved.');
        }

        DB::beginTransaction();
        try {
            $budget = $expense->budget;
            $budget->updateTotals();

            if ($budget->remaining_budget_zmw < $expense->amount_zmw) {
                return redirect()->route('expenses.show', $expense)
                    ->with('error', 'Cannot approve: Insufficient budget remaining. Available: ' .
                        number_format($budget->remaining_budget_zmw, 2));
            }

            $expense->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'updated_by' => auth()->id(),
            ]);

            $budget->updateTotals();

            DB::commit();

            return redirect()->route('expenses.show', $expense)
                ->with('success', 'Expense approved successfully. Budget updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving expense: ' . $e->getMessage());
            return redirect()->route('expenses.show', $expense)
                ->with('error', 'Error approving expense: ' . $e->getMessage());
        }
    }

    public function markAsPaid(Expense $expense)
    {
        if ($expense->status !== 'approved') {
            return redirect()->route('expenses.show', $expense)
                ->with('error', 'Only approved expenses can be marked as paid.');
        }

        DB::beginTransaction();
        try {
            $expense->update([
                'status' => 'paid',
                'paid_by' => auth()->id(),
                'paid_at' => now(),
                'updated_by' => auth()->id(),
            ]);

            $expense->budget->updateTotals();

            DB::commit();

            return redirect()->route('expenses.show', $expense)
                ->with('success', 'Expense marked as paid.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error marking expense as paid: ' . $e->getMessage());
            return redirect()->route('expenses.show', $expense)
                ->with('error', 'Error updating expense: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Expense $expense)
    {
        if ($expense->status !== 'pending') {
            return redirect()->route('expenses.show', $expense)
                ->with('error', 'Only pending expenses can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            $expense->update([
                'status' => 'rejected',
                'rejected_by' => auth()->id(),
                'rejected_at' => now(),
                'rejection_reason' => $validated['rejection_reason'],
                'updated_by' => auth()->id(),
            ]);

            $expense->budget->updateTotals();

            DB::commit();

            return redirect()->route('expenses.show', $expense)
                ->with('success', 'Expense rejected.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting expense: ' . $e->getMessage());
            return redirect()->route('expenses.show', $expense)
                ->with('error', 'Error rejecting expense: ' . $e->getMessage());
        }
    }

    public function destroy(Expense $expense)
    {
        if (!in_array($expense->status, ['draft', 'pending', 'rejected'])) {
            return redirect()->route('expenses.show', $expense)
                ->with('error', 'This expense cannot be deleted as it has already been ' . $expense->status);
        }

        DB::beginTransaction();
        try {
            $budget = $expense->budget;
            $expense->delete();

            if ($budget) {
                $budget->updateTotals();
            }

            DB::commit();

            return redirect()->route('expenses.index')
                ->with('success', 'Expense deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting expense: ' . $e->getMessage());
            return redirect()->route('expenses.show', $expense)
                ->with('error', 'Error deleting expense: ' . $e->getMessage());
        }
    }
}
