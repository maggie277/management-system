<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\Document;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeAccess();

        $query = Budget::with('createdBy')->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $budgets    = $query->get();
        $allBudgets = Budget::all();

        $totalExpensesAll = \App\Models\Expense::whereIn('status', ['approved', 'paid'])->sum('amount_zmw');

        $stats = [
            'total_budgets'    => $allBudgets->count(),
            'total_amount_zmw' => $allBudgets->sum('total_budget_zmw'),
            'total_amount_usd' => $allBudgets->sum('total_budget_usd'),
            'draft_count'      => $allBudgets->where('status', 'draft')->count(),
            'approved_count'   => $allBudgets->where('status', 'approved')->count(),
            'pending_count'    => $allBudgets->where('status', 'pending')->count(),
        ];

        $statusDistribution = [
            'draft'    => $allBudgets->where('status', 'draft')->count(),
            'approved' => $allBudgets->where('status', 'approved')->count(),
            'pending'  => $allBudgets->where('status', 'pending')->count(),
        ];

        return view('budgets.index', [
            'title'              => 'Budget Management',
            'budgets'            => $budgets,
            'stats'              => $stats,
            'totalExpensesAll'   => $totalExpensesAll,
            'statusDistribution' => $statusDistribution,
            'user'               => Auth::user(),
        ]);
    }

    public function create()
    {
        $this->authorizeAccess();
        return view('budgets.create', [
            'title' => 'Create New Detailed Budget',
            'user'  => Auth::user(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeAccess();

        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'project_title'    => 'required|string|max:255',
                'project_goal'     => 'required|string',
                'project_code'     => 'required|string|max:50|unique:budgets,project_code',
                'contact_person'   => 'required|string|max:255',
                'contact_email'    => 'required|email|max:255',
                'contact_phone'    => 'required|string|max:20',
                'duration'         => 'required|string|max:100',
                'exchange_rate'    => 'required|numeric|min:0',
                'total_budget_zmw' => 'required|numeric|min:0',
                'total_budget_usd' => 'required|numeric|min:0',
                'status'           => 'required|in:draft,pending,approved',
                'sections'         => 'nullable|array',
            ]);

            $validated['created_by'] = Auth::id();
            $validated['updated_by'] = Auth::id();

            $budget = Budget::create($validated);

            if ($request->has('sections') && is_array($request->sections)) {
                $this->processBudgetData($budget, $request->sections);
            }

            DB::commit();

            // ── Save document AFTER commit so a document failure never
            //    rolls back the budget itself. ────────────────────────
            $this->saveBudgetAsDocument($budget);

            return redirect()->route('budgets.show', $budget->id)
                ->with('success', 'Budget created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating budget: ' . $e->getMessage());
            return redirect()->back()->withInput()
                ->with('error', 'Error creating budget: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorizeAccess();

        $budget = Budget::with([
            'items'    => fn($q) => $q->orderBy('sort_order'),
            'createdBy',
            'updatedBy',
            'expenses' => fn($q) => $q->orderBy('expense_date', 'desc'),
        ])->findOrFail($id);

        $groupedItems = $budget->items->groupBy('section');

        $sectionTotals = [];
        foreach ($groupedItems as $section => $items) {
            $sectionTotals[$section] = [
                'zmw' => $items->sum('total_amount_zmw'),
                'usd' => $items->sum('total_amount_usd'),
            ];
        }

        $expenses = $budget->expenses()
            ->with(['createdBy', 'approvedBy'])
            ->orderBy('expense_date', 'desc')
            ->get();

        $expensesByCategory = $budget->expenses()
            ->whereIn('status', ['approved', 'paid'])
            ->select('category', DB::raw('SUM(amount_zmw) as total'))
            ->groupBy('category')
            ->get();

        return view('budgets.show', [
            'title'              => 'Detailed Budget - ' . $budget->project_code,
            'budget'             => $budget,
            'groupedItems'       => $groupedItems,
            'sectionTotals'      => $sectionTotals,
            'expenses'           => $expenses,
            'expensesByCategory' => $expensesByCategory,
            'user'               => Auth::user(),
        ]);
    }

    public function edit($id)
    {
        $this->authorizeAccess();

        $budget = Budget::with(['items' => fn($q) => $q->orderBy('sort_order')])
            ->findOrFail($id);

        return view('budgets.edit', [
            'title'  => 'Edit Budget - ' . $budget->project_code,
            'budget' => $budget,
            'user'   => Auth::user(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->authorizeAccess();

        DB::beginTransaction();

        try {
            $budget = Budget::findOrFail($id);

            $validated = $request->validate([
                'project_title'    => 'required|string|max:255',
                'project_goal'     => 'required|string',
                'project_code'     => 'required|string|max:50|unique:budgets,project_code,' . $id,
                'contact_person'   => 'required|string|max:255',
                'contact_email'    => 'required|email|max:255',
                'contact_phone'    => 'required|string|max:20',
                'duration'         => 'required|string|max:100',
                'exchange_rate'    => 'required|numeric|min:0',
                'total_budget_zmw' => 'required|numeric|min:0',
                'total_budget_usd' => 'required|numeric|min:0',
                'status'           => 'required|in:draft,pending,approved',
                'sections'         => 'nullable|array',
            ]);

            $validated['updated_by'] = Auth::id();
            $budget->update($validated);
            $budget->items()->delete();

            if ($request->has('sections') && is_array($request->sections)) {
                $this->processBudgetData($budget, $request->sections);
            }

            DB::commit();

            // Update document after commit
            $this->updateBudgetDocument($budget);

            return redirect()->route('budgets.show', $budget->id)
                ->with('success', 'Budget updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating budget: ' . $e->getMessage());
            return redirect()->back()->withInput()
                ->with('error', 'Error updating budget: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorizeAccess();

        DB::beginTransaction();

        try {
            $budget = Budget::findOrFail($id);

            if ($budget->expenses()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete budget with existing expenses. Please delete expenses first.');
            }

            Document::where('reference_number', $budget->project_code)
                ->where('document_type', 'budget')
                ->delete();

            $budget->items()->delete();
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

    // ── Document helpers ──────────────────────────────────────────────────────

    /**
     * Save budget as a document record.
     * Called OUTSIDE any open transaction so failures are isolated.
     */
    private function saveBudgetAsDocument(Budget $budget): void
    {
        try {
            Log::info('[Budget->Doc] Starting for: ' . $budget->project_code);

            // Get or create the Budgets category
            $category = Category::firstOrCreate(
                ['name' => 'Budgets'],
                [
                    'description' => 'Project budgets and financial documents',
                    'slug'        => 'budgets',
                    'is_active'   => true,
                ]
            );

            Log::info('[Budget->Doc] Category ID: ' . $category->id);

            // Remove any stale duplicate first
            Document::where('reference_number', $budget->project_code)
                ->where('document_type', 'budget')
                ->delete();

            $doc = Document::create([
                'title'            => $budget->project_title,
                'description'      => $budget->project_goal,
                'file_name'        => $budget->project_code . '.pdf',
                'file_path'        => null,   // reference doc — no physical file
                'file_size'        => 0,
                'file_type'        => 'application/pdf',
                'department'       => 'finance',
                'document_type'    => 'budget',
                'reference_number' => $budget->project_code,
                'status'           => $budget->status,
                'version'          => '1.0',
                'created_by'       => Auth::id(),
                'category_id'      => $category->id,
                'folder_id'        => null,   // show directly in category, no sub-folder
                'fiscal_year'      => date('Y'),
                'budget_type'      => 'proposal',
                'total_amount'     => $budget->total_budget_zmw,
                'currency'         => 'ZMW',
                'effective_date'   => now()->toDateString(),
                'is_public'        => false,
                'is_active'        => true,
            ]);

            Log::info('[Budget->Doc] Created document ID: ' . $doc->id
                . ' | category_id: ' . $doc->category_id
                . ' | folder_id: ' . ($doc->folder_id ?? 'NULL'));

        } catch (\Exception $e) {
            // Never block the budget save — just log
            Log::error('[Budget->Doc] FAILED for ' . $budget->project_code . ': ' . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }

    /**
     * Update existing budget document, or create it if missing.
     * Called OUTSIDE any open transaction.
     */
    private function updateBudgetDocument(Budget $budget): void
    {
        try {
            $category = Category::firstOrCreate(
                ['name' => 'Budgets'],
                [
                    'description' => 'Project budgets and financial documents',
                    'slug'        => 'budgets',
                    'is_active'   => true,
                ]
            );

            $document = Document::where('reference_number', $budget->project_code)
                ->where('document_type', 'budget')
                ->first();

            if ($document) {
                $document->update([
                    'title'        => $budget->project_title,
                    'description'  => $budget->project_goal,
                    'status'       => $budget->status,
                    'total_amount' => $budget->total_budget_zmw,
                    'category_id'  => $category->id,
                    'folder_id'    => null,
                ]);
                Log::info('[Budget->Doc] Updated document ID: ' . $document->id);
            } else {
                $this->saveBudgetAsDocument($budget);
            }
        } catch (\Exception $e) {
            Log::error('[Budget->Doc] Update FAILED: ' . $e->getMessage());
        }
    }

    // ── Budget data processing ────────────────────────────────────────────────

    private function processBudgetData(Budget $budget, array $sections): void
    {
        $sortOrder = 0;

        foreach ($sections as $sectionData) {
            $sectionName = $sectionData['name'] ?? 'Unnamed Section';

            if (!isset($sectionData['objectives']) || !is_array($sectionData['objectives'])) {
                continue;
            }

            foreach ($sectionData['objectives'] as $objectiveData) {
                $objectiveDescription = $objectiveData['description'] ?? '';

                if (!isset($objectiveData['activities']) || !is_array($objectiveData['activities'])) {
                    continue;
                }

                foreach ($objectiveData['activities'] as $activityData) {
                    $activityDescription = $activityData['description'] ?? '';

                    if (!isset($activityData['items']) || !is_array($activityData['items'])) {
                        continue;
                    }

                    foreach ($activityData['items'] as $itemData) {
                        // Skip completely empty rows
                        if (empty($itemData['description']) && empty($itemData['cost'])
                            && (empty($itemData['calculated_total']) || $itemData['calculated_total'] == 0)) {
                            continue;
                        }

                        $unitCost  = floatval($itemData['unit_cost']  ?? 0);
                        $number    = intval($itemData['number']    ?? 1);
                        $frequency = intval($itemData['frequency'] ?? 1);
                        $unit      = intval($itemData['unit']      ?? 1);
                        $currency  = $itemData['currency'] ?? 'ZMW';

                        $calculatedTotal = $number * $frequency * $unit * $unitCost;

                        $year1 = floatval($itemData['year_1'] ?? 0);
                        $year2 = floatval($itemData['year_2'] ?? 0);
                        $year3 = floatval($itemData['year_3'] ?? 0);

                        if ($year1 == 0 && $year2 == 0 && $year3 == 0 && $calculatedTotal > 0) {
                            $equalShare = $calculatedTotal / 3;
                            $year1 = round($equalShare, 2);
                            $year2 = round($equalShare, 2);
                            $year3 = round($equalShare, 2);
                        }

                        $exchangeRateItem = floatval($itemData['exchange_rate_item'] ?? $budget->exchange_rate);

                        $totalZMW = $currency === 'ZMW'
                            ? $calculatedTotal
                            : $calculatedTotal * $exchangeRateItem;

                        $totalUSD = $budget->exchange_rate > 0
                            ? $totalZMW / $budget->exchange_rate
                            : 0;

                        BudgetItem::create([
                            'budget_id'          => $budget->id,
                            'section'            => $sectionName,
                            'objective'          => $objectiveDescription,
                            'activity'           => $activityDescription,
                            'description'        => $itemData['description'] ?? '',
                            'cost'               => $itemData['cost'] ?? '',
                            'number'             => $number,
                            'frequency'          => $frequency,
                            'unit'               => $unit,
                            'unit_cost'          => $unitCost,
                            'currency'           => $currency,
                            'exchange_rate_item' => $currency !== 'ZMW' ? $exchangeRateItem : null,
                            'calculated_total'   => $calculatedTotal,
                            'total_amount_zmw'   => $totalZMW,
                            'total_amount_usd'   => $totalUSD,
                            'year_1'             => $year1,
                            'year_2'             => $year2,
                            'year_3'             => $year3,
                            'note'               => $itemData['note'] ?? '',
                            'sort_order'         => $sortOrder++,
                        ]);
                    }
                }
            }
        }

        $budget->updateTotals();
    }

    // ── Other actions ─────────────────────────────────────────────────────────

    public function exportPdf($id)
    {
        $this->authorizeAccess();

        $budget = Budget::with(['items' => fn($q) => $q->orderBy('sort_order')])
            ->findOrFail($id);

        return view('budgets.pdf', compact('budget'));
    }

    public function duplicate($id)
    {
        $this->authorizeAccess();

        DB::beginTransaction();

        try {
            $original = Budget::with('items')->findOrFail($id);

            $newBudget                = $original->replicate();
            $newBudget->project_code  = $original->project_code . '-COPY-' . time();
            $newBudget->status        = 'draft';
            $newBudget->created_by    = Auth::id();
            $newBudget->updated_by    = Auth::id();
            $newBudget->created_at    = now();
            $newBudget->updated_at    = now();
            $newBudget->save();

            foreach ($original->items as $item) {
                $newItem            = $item->replicate();
                $newItem->budget_id = $newBudget->id;
                $newItem->save();
            }

            DB::commit();

            // Create document after commit
            $this->saveBudgetAsDocument($newBudget);

            return redirect()->route('budgets.edit', $newBudget->id)
                ->with('success', 'Budget duplicated successfully! Please update the project code and other details.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error duplicating budget: ' . $e->getMessage());
        }
    }

    public function changeStatus(Request $request, $id)
    {
        $this->authorizeAccess();

        $request->validate(['status' => 'required|in:draft,pending,approved']);

        $budget = Budget::findOrFail($id);
        $budget->update([
            'status'     => $request->status,
            'updated_by' => Auth::id(),
        ]);

        Document::where('reference_number', $budget->project_code)
            ->where('document_type', 'budget')
            ->update(['status' => $request->status]);

        return redirect()->back()
            ->with('success', 'Budget status updated to ' . ucfirst($request->status));
    }

    public function getStatistics()
    {
        $this->authorizeAccess();

        return response()->json([
            'total_budgets'    => Budget::count(),
            'total_amount_zmw' => Budget::sum('total_budget_zmw'),
            'total_amount_usd' => Budget::sum('total_budget_usd'),
            'draft_count'      => Budget::where('status', 'draft')->count(),
            'approved_count'   => Budget::where('status', 'approved')->count(),
            'pending_count'    => Budget::where('status', 'pending')->count(),
        ]);
    }

    public function getStatusData()
    {
        $this->authorizeAccess();

        return response()->json([
            'draft'    => Budget::where('status', 'draft')->count(),
            'approved' => Budget::where('status', 'approved')->count(),
            'pending'  => Budget::where('status', 'pending')->count(),
        ]);
    }

    public function search(Request $request)
    {
        $this->authorizeAccess();

        $query = Budget::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('project_title',  'like', "%{$search}%")
                  ->orWhere('project_code', 'like', "%{$search}%")
                  ->orWhere('project_goal', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        $budgets = $query->with('createdBy')->latest()->get();

        if ($request->ajax()) {
            return view('budgets.partials.budget_table', compact('budgets'));
        }

        return redirect()->route('budgets.index');
    }

    // ── Authorization ─────────────────────────────────────────────────────────

    private function authorizeAccess(): void
    {
        $user = Auth::user();

        $allowed = $user->role === 'system_admin'
            || $user->department === 'Finance and Admin'
            || $user->position === 'Executive Director'
            || $user->position === 'Fundraising and Partnership Manager';

        if (!$allowed) {
            abort(403, 'Unauthorized access to budgets.');
        }
    }
}
