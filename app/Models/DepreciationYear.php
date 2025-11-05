<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepreciationYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'position',
        'background_color',
        'text_color',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get active years ordered by position
     */
    public static function getActiveYears()
    {
        return static::where('is_active', true)
            ->orderBy('position')
            ->get();
    }

    /**
     * Add a new year to the table
     */
    public static function addYear($year, $position = null)
    {
        // Check if year already exists
        if (static::where('year', $year)->exists()) {
            throw new \Exception('Year already exists in the table');
        }

        if ($position === null) {
            // Add at the end
            $position = static::max('position') + 1;
        } else {
            // Shift existing positions
            static::where('position', '>=', $position)->increment('position');
        }

        return static::create([
            'year' => $year,
            'position' => $position,
            'background_color' => '#f8f9fa',
            'text_color' => '#000000',
            'is_active' => true
        ]);
    }

    /**
     * Update positions based on new order
     */
    public static function updatePositions($yearIds)
    {
        foreach ($yearIds as $index => $yearId) {
            static::where('id', $yearId)->update(['position' => $index + 1]);
        }
    }
}
