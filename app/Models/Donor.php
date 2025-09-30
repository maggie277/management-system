<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
    ];

    /**
     * A donor can have many budgets.
     */
    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }
}
