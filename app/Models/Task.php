<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'assigned_to',
    ];
    protected $casts = [
    'due_date' => 'datetime',
];


    public function staff()
    {
        return $this->belongsTo(Staff::class, 'assigned_to');
    }
}
