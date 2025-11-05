<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetDocument extends Model
{
    use HasFactory;

    protected $table = 'documents';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->mergeFillable([
            'fiscal_year',
            'budget_type',
            'total_amount',
            'currency'
        ]);
    }

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('budgets', function ($builder) {
            $builder->where('document_type', 'budget');
        });

        static::creating(function ($document) {
            $document->document_type = 'budget';
            $document->department = 'finance';
        });
    }

    public function getBudgetTypeFormattedAttribute()
    {
        return match($this->budget_type) {
            'operational' => 'Operational Budget',
            'program' => 'Program Budget',
            'capital' => 'Capital Budget',
            'proposal' => 'Proposal Budget',
            default => 'Other'
        };
    }
}
