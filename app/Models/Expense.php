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
        'expense_date',
        'notes'
    ];

    protected $casts = [
        'expense_date' => 'datetime',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }
}
