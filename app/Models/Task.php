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
        'assigned_by',
        'assigned_to',
        'status',
        'priority',
        'due_date',
        'review',
        'rating',
        'completed_at'
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    // Relationship with the user who assigned the task
    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    // Relationship with the user who the task is assigned to
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
