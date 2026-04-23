<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use HasFactory, SoftDeletes;

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
        'created_by',
        'updated_by',
        'status',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:2',
        'total_budget_zmw' => 'decimal:2',
        'total_budget_usd' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the budget items.
     */
    public function items()
    {
        return $this->hasMany(BudgetItem::class)->orderBy('sort_order');
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
     * Scope for budgets by year.
     */
    public function scopeByYear($query, $year)
    {
        return $query->whereYear('created_at', $year);
    }

    /**
     * Get the formatted total budget.
     */
    public function getFormattedTotalZMWAttribute()
    {
        return 'ZMW ' . number_format($this->total_budget_zmw, 2);
    }

    /**
     * Get the formatted total budget in USD.
     */
    public function getFormattedTotalUSDAttribute()
    {
        return '$' . number_format($this->total_budget_usd, 2);
    }
}
