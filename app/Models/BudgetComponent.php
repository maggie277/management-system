<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetComponent extends Model
{
    protected $fillable = [
        'budget_id',
        'component_type',
        'section',
        'title',
        'description',
        'position'
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function activities()
    {
        return $this->hasMany(BudgetActivity::class)->orderBy('position');
    }
}
