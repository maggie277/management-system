<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'goal',
        'project_id',
        'contact_person',
        'contact_email',
        'contact_phone',
        'country',
        'region',
        'institution',
        'grant_amount',
        'duration_months',
        'impact_indicators',
        'currency_rates',
        'budget_data',
        'rmf_data',
        'm_e_plan_data',
        'implementation_plan_data',
        'procurement_plan_data',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'impact_indicators' => 'array',
        'currency_rates' => 'array',
        'budget_data' => 'array',
        'rmf_data' => 'array',
        'm_e_plan_data' => 'array',
        'implementation_plan_data' => 'array',
        'procurement_plan_data' => 'array',
        'grant_amount' => 'decimal:2'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function budgetItems()
    {
        return $this->hasMany(ProjectBudgetItem::class);
    }

    public function objectives()
    {
        return $this->hasMany(ProjectObjective::class);
    }

    public function activities()
    {
        return $this->hasMany(ProjectActivity::class);
    }

    public function indicators()
    {
        return $this->hasMany(ProjectIndicator::class);
    }

    public function schedule()
    {
        return $this->hasMany(ProjectSchedule::class);
    }

    public function procurement()
    {
        return $this->hasMany(ProjectProcurement::class);
    }

    // Calculate totals
    public function getTotalBudgetZMW()
    {
        return $this->budgetItems()->sum('total_zmw');
    }

    public function getTotalBudgetUSD()
    {
        return $this->budgetItems()->sum('total_usd');
    }

    public function getYear1TotalZMW()
    {
        return $this->budgetItems()->sum('year_1_zmw');
    }

    public function getYear2TotalZMW()
    {
        return $this->budgetItems()->sum('year_2_zmw');
    }

    public function getYear3TotalZMW()
    {
        return $this->budgetItems()->sum('year_3_zmw');
    }
}
