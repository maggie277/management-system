<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'budgets';

    protected $fillable = [
        'project_title',
        'project_goal',
        'project_code',
        'contact_person',
        'contact_email',
        'contact_phone',
        'duration',
        'exchange_rate',
        'total_budget_zmw',
        'total_budget_usd',
        'total_year_1',
        'total_year_2',
        'total_year_3',
        'total_expenses_zmw',
        'total_expenses_usd',
        'remaining_budget_zmw',
        'remaining_budget_usd',
        'created_by',
        'updated_by',
        'status',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:2',
        'total_budget_zmw' => 'decimal:2',
        'total_budget_usd' => 'decimal:2',
        'total_year_1' => 'decimal:2',
        'total_year_2' => 'decimal:2',
        'total_year_3' => 'decimal:2',
        'total_expenses_zmw' => 'decimal:2',
        'total_expenses_usd' => 'decimal:2',
        'remaining_budget_zmw' => 'decimal:2',
        'remaining_budget_usd' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the budget items for this budget.
     */
    public function items()
    {
        return $this->hasMany(BudgetItem::class)->orderBy('sort_order');
    }

    /**
     * Get the expenses for this budget.
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Get the user who created the budget.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the budget.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Update all totals based on items and expenses.
     */
    public function updateTotals()
    {
        // Update totals from budget items
        $this->total_budget_zmw = $this->items->sum('total_amount_zmw');
        $this->total_budget_usd = $this->items->sum('total_amount_usd');
        $this->total_year_1 = $this->items->sum('year_1');
        $this->total_year_2 = $this->items->sum('year_2');
        $this->total_year_3 = $this->items->sum('year_3');

        // Update expense totals (only approved and paid expenses)
        $this->total_expenses_zmw = $this->expenses()
            ->whereIn('status', ['approved', 'paid'])
            ->sum('amount_zmw');
        $this->total_expenses_usd = $this->expenses()
            ->whereIn('status', ['approved', 'paid'])
            ->sum('amount_usd');

        // Calculate remaining budget
        $this->remaining_budget_zmw = $this->total_budget_zmw - $this->total_expenses_zmw;
        $this->remaining_budget_usd = $this->total_budget_usd - $this->total_expenses_usd;

        $this->saveQuietly();

        return $this;
    }

    /**
     * Get core program total.
     */
    public function getCoreProgramTotalAttribute()
    {
        return $this->items->where('section', 'A - CORE PROGRAM EXPENDITURE')->sum('total_amount_zmw');
    }

    /**
     * Get institutional support total.
     */
    public function getInstitutionalSupportTotalAttribute()
    {
        return $this->items->where('section', 'B - INSTITUTIONAL SUPPORT EXPENDITURE')->sum('total_amount_zmw');
    }

    /**
     * Get contingency total.
     */
    public function getContingencyTotalAttribute()
    {
        return $this->items->where('section', 'C - CONTINGENCY')->sum('total_amount_zmw');
    }

    /**
     * Get percentage of budget used.
     */
    public function getPercentageUsedAttribute()
    {
        if ($this->total_budget_zmw > 0) {
            return ($this->total_expenses_zmw / $this->total_budget_zmw) * 100;
        }
        return 0;
    }

    /**
     * Get formatted total budget ZMW.
     */
    public function getFormattedTotalZMWAttribute()
    {
        return 'ZMW ' . number_format($this->total_budget_zmw, 2);
    }

    /**
     * Get formatted total budget USD.
     */
    public function getFormattedTotalUSDAttribute()
    {
        return '$' . number_format($this->total_budget_usd, 2);
    }

    /**
     * Get formatted remaining budget ZMW.
     */
    public function getFormattedRemainingZMWAttribute()
    {
        return 'ZMW ' . number_format($this->remaining_budget_zmw, 2);
    }

    /**
     * Get formatted remaining budget USD.
     */
    public function getFormattedRemainingUSDAttribute()
    {
        return '$' . number_format($this->remaining_budget_usd, 2);
    }

    /**
     * Get formatted expenses ZMW.
     */
    public function getFormattedExpensesZMWAttribute()
    {
        return 'ZMW ' . number_format($this->total_expenses_zmw, 2);
    }

    /**
     * Get formatted expenses USD.
     */
    public function getFormattedExpensesUSDAttribute()
    {
        return '$' . number_format($this->total_expenses_usd, 2);
    }

    /**
     * Scope for approved budgets.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for pending budgets.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for draft budgets.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope for active budgets (not deleted).
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Scope for budgets with remaining funds.
     */
    public function scopeWithRemainingFunds($query)
    {
        return $query->whereRaw('remaining_budget_zmw > 0');
    }
}
