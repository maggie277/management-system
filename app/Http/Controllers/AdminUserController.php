<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    // Show the create user form
    public function create()
    {
        return view('create-user'); // resources/views/create-user.blade.php
    }

    // Store the new user (admin or staff)
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:staff,admin',
        ]);

        // Check which type of user is being created
        if ($request->role === 'admin') {
            // Admins go to users table
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'admin',
            ]);
        } else {
            // Staff go to staff table
            Staff::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'created_by' => Auth::id(), // currently logged-in admin ID
                'status' => 'active',
            ]);
        }

        return redirect()->back()->with('status', ucfirst($request->role) . ' created successfully!');
    }
}
