<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'budget_id',
        'section',
        'objective',
        'activity',
        'component',
        'description_cost_category',
        'description_cost_item',
        'number',
        'frequency',
        'unit',
        'unit_cost',
        'total_amount_zmw',
        'total_amount_usd',
        'revised_year_1',
        'revised_year_2',
        'revised_year_3',
        'comments',
        'sort_order',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'total_amount_zmw' => 'decimal:2',
        'total_amount_usd' => 'decimal:2',
        'revised_year_1' => 'decimal:2',
        'revised_year_2' => 'decimal:2',
        'revised_year_3' => 'decimal:2',
        'number' => 'integer',
    ];

    /**
     * Get the budget that owns the item.
     */
    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    /**
     * Calculate total amount based on number, frequency, and unit cost.
     */
    public function calculateTotal()
    {
        $this->total_amount_zmw = $this->number * $this->frequency * $this->unit_cost;
        if ($this->budget && $this->budget->exchange_rate > 0) {
            $this->total_amount_usd = $this->total_amount_zmw / $this->budget->exchange_rate;
        }
        return $this;
    }

    /**
     * Get formatted unit cost.
     */
    public function getFormattedUnitCostAttribute()
    {
        return 'ZMW ' . number_format($this->unit_cost, 2);
    }

    /**
     * Get formatted total ZMW.
     */
    public function getFormattedTotalZMWAttribute()
    {
        return 'ZMW ' . number_format($this->total_amount_zmw, 2);
    }

    /**
     * Get formatted total USD.
     */
    public function getFormattedTotalUSDAttribute()
    {
        return '$' . number_format($this->total_amount_usd, 2);
    }
}
