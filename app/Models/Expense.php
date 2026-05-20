<?php
// app/Models/Expense.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'budget_id',
        'budget_item_id',
        'expense_number',
        'description',
        'purpose',
        'amount_zmw',
        'amount_usd',
        'expense_date',
        'category',
        'payment_method',
        'vendor_payee',
        'status',
        'receipt_number',
        'receipt_file',
        'notes',
        'approved_by',
        'approved_at',
        'paid_by',
        'paid_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount_zmw' => 'decimal:2',
        'amount_usd' => 'decimal:2',
        'expense_date' => 'date',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'rejected_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the budget that this expense belongs to.
     */
    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    /**
     * Get the budget item that this expense belongs to.
     */
    public function budgetItem()
    {
        return $this->belongsTo(BudgetItem::class);
    }

    /**
     * Get the user who created the expense.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the expense.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who approved the expense.
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who marked the expense as paid.
     */
    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Get the user who rejected the expense.
     */
    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Generate a unique expense number.
     */
    public static function generateExpenseNumber()
    {
        $year = date('Y');
        $month = date('m');
        $lastExpense = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastExpense) {
            $lastNumber = intval(substr($lastExpense->expense_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'EXP-' . $year . $month . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Scope for pending expenses.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved expenses.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for paid expenses.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for rejected expenses.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Check if expense can be edited.
     */
    public function isEditable()
    {
        return in_array($this->status, ['draft', 'pending']);
    }

    /**
     * Check if expense can be approved.
     */
    public function isApprovable()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if expense can be marked as paid.
     */
    public function isPayable()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if expense can be rejected.
     */
    public function isRejectable()
    {
        return $this->status === 'pending';
    }

    /**
     * Get formatted amount ZMW.
     */
    public function getFormattedAmountZMWAttribute()
    {
        return 'ZMW ' . number_format($this->amount_zmw, 2);
    }

    /**
     * Get formatted amount USD.
     */
    public function getFormattedAmountUSDAttribute()
    {
        return '$' . number_format($this->amount_usd, 2);
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeAttribute()
    {
        switch ($this->status) {
            case 'pending':
                return '<span class="badge" style="background: #fff3cd; color: #856404;">Pending</span>';
            case 'approved':
                return '<span class="badge" style="background: #d4edda; color: #155724;">Approved</span>';
            case 'paid':
                return '<span class="badge" style="background: #d1ecf1; color: #0c5460;">Paid</span>';
            case 'rejected':
                return '<span class="badge" style="background: #f8d7da; color: #721c24;">Rejected</span>';
            default:
                return '<span class="badge" style="background: #e2e3e5; color: #383d41;">Draft</span>';
        }
    }
}
