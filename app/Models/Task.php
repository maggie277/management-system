<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Staff;
use App\Models\User;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
    'description',
    'status',
    'due_date',
    'assigned_to',
    'assigned_type',
    'assigned_by',
    'priority', // staff or admin
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    /**
     * Relation when assigned to a staff member
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'assigned_to');
    }

    /**
     * Relation when assigned to an admin
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Convenience accessor to get the actual assigned user
     * Usage in Blade: $task->assigned_user->name
     */
    public function getAssignedUserAttribute()
    {
        return $this->assigned_type === 'admin' ? $this->admin : $this->staff;
    }
}
