<?php

namespace App\Helpers;

use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthHelper
{
    /**
     * Create user using DB facade (since User model mutator doesn't work)
     */
    public static function createUser($data)
    {
        return DB::table('users')->insertGetId([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'user',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Create staff using Eloquent (mutator works perfectly)
     */
    public static function createStaff($data)
    {
        $staff = new Staff();
        $staff->name = $data['name'];
        $staff->email = $data['email'];
        $staff->password = $data['password']; // Auto-hashed by mutator
        $staff->status = $data['status'] ?? 'active';
        $staff->created_by = $data['created_by'] ?? 1;
        $staff->save();
        return $staff;
    }
}
