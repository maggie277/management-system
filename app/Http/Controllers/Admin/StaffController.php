<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    // Show all staff
    public function index()
    {
        $staff = Staff::all();
        return view('staff.index', compact('staff'));
    }

    // Show create form
    public function create()
    {
        return view('staff.create');
    }

    // Store new staff
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email',
            'password' => 'required|min:8',
        ]);

        Staff::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'created_by' => auth()->id() ?? 1, // fallback admin ID
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'Staff created successfully.');
    }
}
