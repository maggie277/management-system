<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_id',
        'amount',
        'threshold',
        'currency',
        'description'
    ];

    // Relationships
    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    // Computed totals
    public function getTotalExpensesAttribute()
    {
        return $this->expenses()->sum('amount');
    }

    public function getRemainingAttribute()
    {
        return $this->amount - $this->total_expenses;
    }
}
