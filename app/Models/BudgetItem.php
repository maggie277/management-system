<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetItem extends Model
{
    use HasFactory;

    protected $table = 'budget_items';

    protected $fillable = [
        'budget_id',
        'section',
        'objective',
        'activity',
        'description',
        'cost',
        'number',
        'frequency',
        'unit',
        'unit_cost',
        'currency',
        'exchange_rate_item',
        'calculated_total',
        'total_amount_zmw',
        'total_amount_usd',
        'year_1',
        'year_2',
        'year_3',
        'note',
        'sort_order',
    ];

    protected $casts = [
        'number' => 'integer',
        'frequency' => 'integer',
        'unit_cost' => 'decimal:2',
        'calculated_total' => 'decimal:2',
        'total_amount_zmw' => 'decimal:2',
        'total_amount_usd' => 'decimal:2',
        'year_1' => 'decimal:2',
        'year_2' => 'decimal:2',
        'year_3' => 'decimal:2',
        'exchange_rate_item' => 'decimal:4',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    /**
     * Calculate total based on: Number × Unit Cost × Frequency
     */
    public function calculateTotal($exchangeRate = null)
    {
        $this->calculated_total = $this->number * $this->unit_cost * $this->frequency;

        // Convert to ZMW based on currency
        if ($this->currency === 'USD' && $exchangeRate) {
            $this->total_amount_zmw = $this->calculated_total * $exchangeRate;
        } else {
            $this->total_amount_zmw = $this->calculated_total;
        }

        // Calculate USD using budget's exchange rate
        if ($this->budget && $this->budget->exchange_rate > 0) {
            $this->total_amount_usd = $this->total_amount_zmw / $this->budget->exchange_rate;
        }

        return $this;
    }
}
