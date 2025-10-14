<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Staff;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    public function create()
    {
        return view('admin.users.create');
    }

    // Show admin dashboard with users & tasks
    public function index()
    {
        $admins = User::orderBy('id', 'desc')->get();
        $staff = Staff::orderBy('id', 'desc')->get()->map(fn($s) => tap($s)->role = 'staff');
        $users = $admins->concat($staff);

        $tasks = Task::with(['staff', 'admin'])->orderBy('due_date', 'asc')->get();

        return view('admin.dashboard', compact('users', $tasks));
    }

    // Store a new admin/staff - CORRECTED VERSION
    public function store(Request $request)
    {
        // Validate common fields
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:staff,admin',
        ]);

        try {
            if ($request->role === 'admin') {
                // Validate unique email for admin
                $request->validate([
                    'email' => 'unique:users,email',
                ]);

                // Create admin user - Let the model mutator handle password hashing
                User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => $request->password, // Let the mutator hash it
                    'role' => 'admin',
                ]);
            } else {
                // Validate unique email for staff
                $request->validate([
                    'email' => 'unique:staff,email',
                ]);

                // Create staff user - Let the model mutator handle password hashing
                Staff::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => $request->password, // Let the mutator hash it
                    'status' => 'active',
                    'created_by' => Auth::id(),
                ]);
            }

            return redirect()->route('admin.users.create')->with('success', ucfirst($request->role) . ' created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()]);
        }

    }
   public function staffList(Request $request)
{
    // Get all active staff
    $staffQuery = Staff::where('status', 'active');
    $adminQuery = User::where('role', 'admin'); // Get admin users

    // Search functionality for both staff and admins
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;

        $staffQuery->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });

        $adminQuery->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    $staff = $staffQuery->orderBy('name')->get()->map(function($staff) {
        $staff->user_type = 'staff';
        $staff->role_display = 'Staff';
        return $staff;
    });

    $admins = $adminQuery->orderBy('name')->get()->map(function($admin) {
        $admin->user_type = 'admin';
        $admin->role_display = 'Admin';
        $admin->status = 'active'; // Admins are always active
        return $admin;
    });

    // Combine staff and admins
    $allUsers = $staff->concat($admins)->sortBy('name');

    return view('admin.staff.list', compact('allUsers'));
}
}
