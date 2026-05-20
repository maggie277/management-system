<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyTaskPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'week_start',
        'planned_tasks',
        'status',
        'completed_tasks'
    ];

    protected $casts = [
        'week_start' => 'date',
        'planned_tasks' => 'array',
        'completed_tasks' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
