<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource (Dashboard).
     */
    public function index()
    {
        $this->authorizeAccess();

        // Get all budgets with createdBy relationship
        $budgets = Budget::with('createdBy')->latest()->get();

        // Calculate dashboard statistics
        $stats = [
            'total_budgets' => $budgets->count(),
            'total_amount_zmw' => $budgets->sum('total_budget_zmw'),
            'total_amount_usd' => $budgets->sum('total_budget_usd'),
            'draft_count' => $budgets->where('status', 'draft')->count(),
            'approved_count' => $budgets->where('status', 'approved')->count(),
            'pending_count' => $budgets->where('status', 'pending')->count(),
        ];

        // Recent budgets (last 5)
        $recentBudgets = $budgets->take(5);

        // Budgets by status for chart
        $statusDistribution = [
            'draft' => $budgets->where('status', 'draft')->count(),
            'approved' => $budgets->where('status', 'approved')->count(),
            'pending' => $budgets->where('status', 'pending')->count(),
        ];

        return view('budgets.index', [
            'title' => 'Budget Management Dashboard',
            'budgets' => $budgets,
            'recentBudgets' => $recentBudgets,
            'stats' => $stats,
            'statusDistribution' => $statusDistribution,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorizeAccess();
        return view('budgets.create', [
            'title' => 'Create New Detailed Budget',
            'user' => Auth::user(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeAccess();

        DB::beginTransaction();

        try {
            // Validate basic budget information
            $validated = $request->validate([
                'project_title' => 'required|string|max:255',
                'project_goal' => 'required|string',
                'project_code' => 'required|string|max:50|unique:budgets,project_code',
                'contact_person' => 'required|string|max:255',
                'contact_email' => 'required|email|max:255',
                'contact_phone' => 'required|string|max:20',
                'duration' => 'required|string|max:100',
                'exchange_rate' => 'required|numeric|min:0',
                'total_budget_zmw' => 'required|numeric|min:0',
                'total_budget_usd' => 'required|numeric|min:0',
                'status' => 'required|in:draft,pending,approved',
            ]);

            // Add user information
            $validated['created_by'] = Auth::id();
            $validated['updated_by'] = Auth::id();

            // Create the main budget
            $budget = Budget::create($validated);

            // Process budget items if provided
            if ($request->has('budget_items')) {
                $this->processBudgetItems($budget, $request->budget_items);
            }

            DB::commit();

            return redirect()->route('budgets.show', $budget->id)
                ->with('success', 'Budget created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating budget: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource with detailed breakdown.
     */
    public function show($id)
    {
        $this->authorizeAccess();

        // Eager load all relationships
        $budget = Budget::with([
            'items' => function($query) {
                $query->orderBy('sort_order');
            },
            'createdBy',
            'updatedBy'
        ])->findOrFail($id);

        // Group items by section for display
        $groupedItems = $budget->items->groupBy('section');

        // Calculate section totals
        $sectionTotals = [];
        foreach ($groupedItems as $section => $items) {
            $sectionTotals[$section] = [
                'zmw' => $items->sum('total_amount_zmw'),
                'usd' => $items->sum('total_amount_usd'),
            ];
        }

        // Calculate summary statistics
        $summary = [
            'core_program' => $budget->items->where('section', 'A - CORE PROGRAM EXPENDITURE')->sum('total_amount_zmw'),
            'institutional_support' => $budget->items->where('section', 'B - INSTITUTIONAL SUPPORT EXPENDITURE')->sum('total_amount_zmw'),
            'contingency' => $budget->items->where('section', 'C - CONTINGENCY')->sum('total_amount_zmw'),
        ];

        return view('budgets.show', [
            'title' => 'Detailed Budget - ' . $budget->project_code,
            'budget' => $budget,
            'groupedItems' => $groupedItems,
            'sectionTotals' => $sectionTotals,
            'summary' => $summary,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $this->authorizeAccess();

        // Load budget with items ordered by sort_order
        $budget = Budget::with(['items' => function($query) {
            $query->orderBy('sort_order');
        }])->findOrFail($id);

        // Group items by section for easier editing
        $groupedItems = $budget->items->groupBy('section');

        return view('budgets.edit', [
            'title' => 'Edit Budget - ' . $budget->project_code,
            'budget' => $budget,
            'groupedItems' => $groupedItems,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->authorizeAccess();

        DB::beginTransaction();

        try {
            $budget = Budget::findOrFail($id);

            // Validate basic budget information
            $validated = $request->validate([
                'project_title' => 'required|string|max:255',
                'project_goal' => 'required|string',
                'project_code' => 'required|string|max:50|unique:budgets,project_code,' . $id,
                'contact_person' => 'required|string|max:255',
                'contact_email' => 'required|email|max:255',
                'contact_phone' => 'required|string|max:20',
                'duration' => 'required|string|max:100',
                'exchange_rate' => 'required|numeric|min:0',
                'total_budget_zmw' => 'required|numeric|min:0',
                'total_budget_usd' => 'required|numeric|min:0',
                'status' => 'required|in:draft,pending,approved',
            ]);

            // Update user information
            $validated['updated_by'] = Auth::id();

            // Update the budget
            $budget->update($validated);

            // Delete existing items
            $budget->items()->delete();

            // Process new budget items if provided
            if ($request->has('budget_items')) {
                $this->processBudgetItems($budget, $request->budget_items);
            }

            DB::commit();

            return redirect()->route('budgets.show', $budget->id)
                ->with('success', 'Budget updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating budget: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->authorizeAccess();

        DB::beginTransaction();

        try {
            $budget = Budget::findOrFail($id);

            // Delete all associated items first
            $budget->items()->delete();

            // Delete the budget
            $budget->delete();

            DB::commit();

            return redirect()->route('budgets.index')
                ->with('success', 'Budget deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Error deleting budget: ' . $e->getMessage());
        }
    }

    /**
     * Export budget to PDF.
     */
    public function exportPdf($id)
    {
        $this->authorizeAccess();

        $budget = Budget::with(['items' => function($query) {
            $query->orderBy('sort_order');
        }])->findOrFail($id);

        // You would typically use a PDF library here
        // For now, we'll return a view that can be printed

        return view('budgets.pdf', [
            'budget' => $budget,
            'groupedItems' => $budget->items->groupBy('section'),
        ]);
    }

    /**
     * Duplicate/Copy an existing budget.
     */
    public function duplicate($id)
    {
        $this->authorizeAccess();

        DB::beginTransaction();

        try {
            $original = Budget::with('items')->findOrFail($id);

            // Create new budget based on original
            $newBudget = $original->replicate();
            $newBudget->project_code = $original->project_code . '-COPY-' . time();
            $newBudget->status = 'draft';
            $newBudget->created_by = Auth::id();
            $newBudget->updated_by = Auth::id();
            $newBudget->created_at = now();
            $newBudget->updated_at = now();
            $newBudget->save();

            // Duplicate all items
            foreach ($original->items as $item) {
                $newItem = $item->replicate();
                $newItem->budget_id = $newBudget->id;
                $newItem->save();
            }

            DB::commit();

            return redirect()->route('budgets.edit', $newBudget->id)
                ->with('success', 'Budget duplicated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Error duplicating budget: ' . $e->getMessage());
        }
    }

    /**
     * Change budget status.
     */
    public function changeStatus(Request $request, $id)
    {
        $this->authorizeAccess();

        $request->validate([
            'status' => 'required|in:draft,pending,approved',
        ]);

        $budget = Budget::findOrFail($id);
        $budget->update([
            'status' => $request->status,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->back()
            ->with('success', 'Budget status updated to ' . ucfirst($request->status));
    }

    /**
     * Process budget items from request.
     */
    private function processBudgetItems(Budget $budget, array $itemsData)
    {
        foreach ($itemsData as $itemData) {
            // Skip empty rows
            if (empty($itemData['description_cost_item']) && empty($itemData['description_cost_category'])) {
                continue;
            }

            $budget->items()->create([
                'section' => $itemData['section'] ?? null,
                'objective' => $itemData['objective'] ?? null,
                'activity' => $itemData['activity'] ?? null,
                'component' => $itemData['component'] ?? null,
                'description_cost_category' => $itemData['description_cost_category'] ?? null,
                'description_cost_item' => $itemData['description_cost_item'] ?? null,
                'number' => $itemData['number'] ?? 1,
                'frequency' => $itemData['frequency'] ?? '',
                'unit' => $itemData['unit'] ?? '',
                'unit_cost' => $itemData['unit_cost'] ?? 0,
                'total_amount_zmw' => $itemData['total_amount_zmw'] ?? 0,
                'total_amount_usd' => $itemData['total_amount_usd'] ?? 0,
                'revised_year_1' => $itemData['revised_year_1'] ?? null,
                'revised_year_2' => $itemData['revised_year_2'] ?? null,
                'revised_year_3' => $itemData['revised_year_3'] ?? null,
                'comments' => $itemData['comments'] ?? null,
                'sort_order' => $itemData['sort_order'] ?? 0,
            ]);
        }
    }

    /**
     * Get budget statistics for dashboard.
     */
    public function getStatistics()
    {
        $this->authorizeAccess();

        $stats = [
            'total_budgets' => Budget::count(),
            'total_amount_zmw' => Budget::sum('total_budget_zmw'),
            'total_amount_usd' => Budget::sum('total_budget_usd'),
            'draft_count' => Budget::where('status', 'draft')->count(),
            'approved_count' => Budget::where('status', 'approved')->count(),
            'pending_count' => Budget::where('status', 'pending')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get budgets by status for chart.
     */
    public function getStatusData()
    {
        $this->authorizeAccess();

        $data = [
            'draft' => Budget::where('status', 'draft')->count(),
            'approved' => Budget::where('status', 'approved')->count(),
            'pending' => Budget::where('status', 'pending')->count(),
        ];

        return response()->json($data);
    }

    /**
     * Search/filter budgets.
     */
    public function search(Request $request)
    {
        $this->authorizeAccess();

        $query = Budget::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('project_title', 'like', "%{$search}%")
                  ->orWhere('project_code', 'like', "%{$search}%")
                  ->orWhere('project_goal', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('year')) {
            $query->whereYear('created_at', $request->year);
        }

        $budgets = $query->with('createdBy')->latest()->get();

        return view('budgets.partials.budget_table', [
            'budgets' => $budgets,
        ]);
    }

    /**
     * Authorization check for budget access.
     */
    private function authorizeAccess()
    {
        $user = Auth::user();

        $allowed = $user->role === 'system_admin' ||
                  $user->department === 'Finance and Admin' ||
                  $user->position === 'Executive Director' ||
                  $user->position === 'Fundraising and Partnership Manager';

        if (!$allowed) {
            abort(403, 'Unauthorized access to budgets.');
        }
    }
}
