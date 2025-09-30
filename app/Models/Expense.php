<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'budget_id',
        'title',
        'amount',
        'notes',
        'expense_date',
    ];

    // Cast expense_date to a Carbon instance
    protected $casts = [
        'expense_date' => 'datetime',
    ];

    /**
     * Each expense belongs to a budget.
     */
    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }
}
