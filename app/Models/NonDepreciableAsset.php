<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NonDepreciableAsset extends Model
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
        'location',
        'remark',
        'assigned_to',
        'net_book_value'
    ];

    protected $casts = [
        'date_of_purchase' => 'date',
        'cost' => 'decimal:2',
        'net_book_value' => 'decimal:2',
    ];

    // Always set net book value to cost for non-depreciable assets
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($asset) {
            $asset->net_book_value = $asset->cost;
        });

        static::retrieved(function ($asset) {
            $asset->net_book_value = $asset->cost;
        });
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // No depreciation methods for non-depreciable assets
    public function getDepreciationForYear($year)
    {
        return 0;
    }

    public function calculateAccumulatedDepreciation($upToYear)
    {
        return 0;
    }

    public function calculateNetBookValue($forYear)
    {
        return $this->cost;
    }

    public function getDepreciationBreakdown()
    {
        return [];
    }

    public function wasActiveInYear($year)
    {
        if (!$this->date_of_purchase) {
            return false;
        }

        $purchaseYear = $this->date_of_purchase->year;
        return $year >= $purchaseYear;
    }
}
