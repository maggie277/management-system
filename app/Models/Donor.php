<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'contact_number', 'email', 'contract_type', 'contract_start_date',
        'contract_end_date', 'total_budget', 'remaining_budget', 'budget_threshold',
        'is_budget_low', 'notes', 'document_path', 'is_active', 'responsible_person'
    ];

    protected $casts = [
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'total_budget' => 'decimal:2',
        'remaining_budget' => 'decimal:2',
        'budget_threshold' => 'decimal:2',
        'is_budget_low' => 'boolean',
        'is_active' => 'boolean',
    ];

    // The dates property is automatically handled by SoftDeletes trait

    public function getDonationAmountAttribute()
    {
        return $this->total_budget;
    }

    public function getIsContractActiveAttribute()
    {
        return $this->contract_end_date >= now();
    }

    public function getContractDurationAttribute()
    {
        $start = $this->contract_start_date;
        $end = $this->contract_end_date;

        return $start->diffInDays($end) . ' days';
    }
}
