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
        'description',
    ];

    /**
     * A budget belongs to a donor.
     */
    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    /**
     * A budget has many expenses.
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
