<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\User;   // Admin model
use App\Models\Staff;  // Staff model

class AdminTaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // admin guard (default)
    }

    /**
     * Show all tasks (admin can see everything)
     */
    public function index()
    {
        $tasks = Task::with(['staff', 'admin'])->orderBy('due_date', 'asc')->get();
        return view('admin.tasks.index', compact('tasks'));
    }

    /**
     * Show form to create a new task
     */
  public function create($staff = null)
{
    $staffMembers = Staff::where('status', 'active')->get();
    $admins = User::where('role', 'admin')->get();
    $selectedStaff = $staff ? Staff::find($staff) : null;

    return view('admin.tasks.create', compact('staffMembers', 'admins', 'selectedStaff'));
}
    /**
     *  Store a new task (assigned to staff or admin)
     */
    public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'due_date' => 'required|date',
        'assigned_to' => 'required|exists:staff,id',
        'priority' => 'required|in:low,medium,high',
    ]);

    Task::create([
        'title' => $request->title,
        'description' => $request->description,
        'due_date' => $request->due_date,
        'assigned_to' => $request->assigned_to,
        'assigned_type' => 'staff',
        'assigned_by' => Auth::id(),
        'priority' => $request->priority,
        'status' => 'pending',
    ]);

    return redirect()->route('admin.tasks.index')->with('success', 'Task assigned successfully!');
}
    /**
     * Edit a task
     */
    public function edit(Task $task)
    {
        $staff = Staff::all();
        $admins = User::all();
        return view('admin.tasks.edit', compact('task', 'staff', 'admins'));
    }

    /**
     * Update task
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'assigned_to' => 'required|integer',
            'assigned_type' => 'required|in:staff,admin',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task->update($request->only('title', 'description', 'due_date', 'status', 'assigned_to', 'assigned_type'));

        return redirect()->route('admin.tasks.index')->with('success', 'Task updated successfully!');
    }

    /**
     * Delete task
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('admin.tasks.index')->with('success', 'Task deleted successfully!');
    }
}
