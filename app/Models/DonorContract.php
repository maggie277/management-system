<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonorContract extends Model
{
    use HasFactory;

    protected $table = 'documents';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->mergeFillable([
            'donor_name',
            'contract_value',
            'currency',
            'project_name',
            'reporting_requirements'
        ]);
    }

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('donor_contracts', function ($builder) {
            $builder->where('document_type', 'donor_contract');
        });

        static::creating(function ($document) {
            $document->document_type = 'donor_contract';
            $document->department = 'programs';
        });
    }

    public function getContractValueFormattedAttribute()
    {
        return $this->currency . ' ' . number_format($this->contract_value, 2);
    }
}
