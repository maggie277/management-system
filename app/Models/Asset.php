<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'year',
        'name',
        'type_make',
        'cost',
        'date_of_purchase',
        'serial_chasis_no',
        'ctpd_asset_code',
        'description',
        'status',
        'depreciation_rate',
        'depreciation_method',
        'location',
        'remark',
        'cost_of_disposal',
        'disposal_date',
        'disposal_reason',
        'assigned_to',
        'accumulated_depreciation',
        'net_book_value'
    ];

    protected $casts = [
        'date_of_purchase' => 'date',
        'disposal_date' => 'date',
        'cost' => 'decimal:2',
        'cost_of_disposal' => 'decimal:2',
        'depreciation_rate' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'net_book_value' => 'decimal:2',
    ];

    // Automatically calculate depreciation when model is retrieved
    protected static function boot()
    {
        parent::boot();

        static::retrieved(function ($asset) {
            $asset->recalculateDepreciation();
        });

        static::saving(function ($asset) {
            $asset->recalculateDepreciation();
        });
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Calculate annual depreciation
    public function calculateAnnualDepreciation()
    {
        if (!$this->cost || !$this->depreciation_rate || $this->status !== 'active') {
            return 0;
        }
        return ($this->cost * $this->depreciation_rate) / 100;
    }

    // Get depreciation for a specific year
    public function getDepreciationForYear($year)
    {
        if (!$this->date_of_purchase || !$this->cost || !$this->depreciation_rate || $this->status !== 'active') {
            return 0;
        }

        $purchaseYear = $this->date_of_purchase->year;

        // Only calculate depreciation for years after purchase year
        if ($year < $purchaseYear) {
            return 0;
        }

        // Check if asset is disposed and the year is after disposal
        if ($this->disposal_date && $year > $this->disposal_date->year) {
            return 0;
        }

        // Check if asset is already fully depreciated
        $accumulatedUpToPreviousYear = $this->calculateAccumulatedDepreciation($year - 1);
        if ($accumulatedUpToPreviousYear >= $this->cost) {
            return 0;
        }

        // For the first year, calculate partial depreciation
        if ($year == $purchaseYear) {
            $purchaseMonth = $this->date_of_purchase->month;
            $monthsUsed = 13 - $purchaseMonth;
            $annualDepreciation = $this->calculateAnnualDepreciation();
            $partialDepreciation = ($annualDepreciation * $monthsUsed) / 12;

            // Don't exceed remaining cost
            $remainingCost = $this->cost - $accumulatedUpToPreviousYear;
            return min($partialDepreciation, $remainingCost);
        }

        $annualDepreciation = $this->calculateAnnualDepreciation();
        $remainingCost = $this->cost - $accumulatedUpToPreviousYear;

        return min($annualDepreciation, $remainingCost);
    }

    // Calculate accumulated depreciation up to a specific year
    public function calculateAccumulatedDepreciation($upToYear)
    {
        if (!$this->date_of_purchase || !$this->cost || !$this->depreciation_rate || $this->status !== 'active') {
            return 0;
        }

        $purchaseYear = $this->date_of_purchase->year;
        if ($upToYear < $purchaseYear) {
            return 0;
        }

        $accumulated = 0;

        // Calculate depreciation for each year from purchase year to target year
        for ($year = $purchaseYear; $year <= $upToYear; $year++) {
            $yearDepreciation = $this->getDepreciationForYear($year);
            $accumulated += $yearDepreciation;

            // Stop if accumulated depreciation reaches or exceeds cost
            if ($accumulated >= $this->cost) {
                return $this->cost;
            }
        }

        return min($accumulated, $this->cost);
    }

    // Calculate net book value for a specific year
    public function calculateNetBookValue($forYear)
    {
        $accumulatedDepreciation = $this->calculateAccumulatedDepreciation($forYear);
        $netBookValue = max(0, $this->cost - $accumulatedDepreciation);

        // Ensure net book value doesn't go below 0
        return max(0, $netBookValue);
    }

    // Recalculate and save depreciation - FIXED VERSION
    public function recalculateDepreciation()
    {
        if (!$this->date_of_purchase || !$this->cost || !$this->depreciation_rate || $this->status !== 'active') {
            return;
        }

        $currentYear = 2025; // Force 2025 since we're in 2025

        // Always recalculate regardless of existing values
        $this->accumulated_depreciation = $this->calculateAccumulatedDepreciation($currentYear);
        $this->net_book_value = $this->calculateNetBookValue($currentYear);

        // Log for debugging
        \Log::info("Recalculated Asset {$this->id}: Cost={$this->cost}, Accumulated={$this->accumulated_depreciation}, NBV={$this->net_book_value}");
    }

    // Check if asset was active in a given year - ADDED BACK
    public function wasActiveInYear($year)
    {
        if (!$this->date_of_purchase) {
            return false;
        }

        $purchaseYear = $this->date_of_purchase->year;

        // If disposed, check if year is between purchase and disposal
        if ($this->disposal_date) {
            $disposalYear = $this->disposal_date->year;
            return $year >= $purchaseYear && $year <= $disposalYear;
        }

        // If not disposed, check if year is after purchase
        return $year >= $purchaseYear;
    }

    // Get depreciation breakdown for all years
    public function getDepreciationBreakdown()
    {
        if (!$this->date_of_purchase) {
            return [];
        }

        $purchaseYear = $this->date_of_purchase->year;
        $currentYear = 2025; // We're in 2025
        $breakdown = [];
        $accumulated = 0;

        for ($year = $purchaseYear; $year <= $currentYear; $year++) {
            $yearDepreciation = $this->getDepreciationForYear($year);
            $accumulated += $yearDepreciation;
            $netBookValue = max(0, $this->cost - $accumulated);

            $breakdown[] = [
                'year' => $year,
                'depreciation' => $yearDepreciation,
                'accumulated' => $accumulated,
                'net_book_value' => $netBookValue
            ];

            if ($accumulated >= $this->cost) {
                break;
            }
        }

        return $breakdown;
    }
}
