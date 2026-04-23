<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'position',
        'department',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin()
    {
        return $this->role === 'system_admin' ||
               $this->position === 'Executive Director';
    }

    /**
     * Check if user is management
     */
    public function isManagement()
    {
        $managementPositions = [
            'Executive Director',
            'Head of Research',
            'Head of Advocacy and Campaigns',
            'M&E Specialist',
            'Fundraising and Partnership Manager',
            'Human Resource Manager'
        ];

        return in_array($this->position, $managementPositions) ||
               $this->role === 'system_admin';
    }

    /**
     * Check if user is in Finance department
     */
    public function isFinance()
    {
        return $this->department === 'Finance and Admin';
    }
}
