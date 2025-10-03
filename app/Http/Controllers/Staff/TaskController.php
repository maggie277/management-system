<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:staff');
    }

    /**
     * List all tasks assigned to the logged-in staff
     */
    public function index()
    {
        $staff = Auth::guard('staff')->user();
        $tasks = $staff->tasks()->orderBy('due_date', 'asc')->get();

        return view('staff.tasks.index', compact('tasks'));
    }

    /**
     * Show form to create a new task
     */
    public function create()
    {
        return view('staff.tasks.create');
    }

    /**
     * Store a new task
     */
    public function store(Request $request)
    {
        $staff = Auth::guard('staff')->user();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'assigned_to' => $staff->id,
        ]);

        return redirect()->route('staff.tasks.index')->with('success', 'Task added successfully!');
    }

    /**
     * Show form to edit a task
     */
    public function edit(Task $task)
    {
        $staff = Auth::guard('staff')->user();

        if ($task->assigned_to != $staff->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('staff.tasks.edit', compact('task'));
    }

    /**
     * Update a task
     */
    public function update(Request $request, Task $task)
    {
        $staff = Auth::guard('staff')->user();

        if ($task->assigned_to != $staff->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task->update($request->only('title', 'description', 'due_date', 'status'));

        return redirect()->route('staff.tasks.index')->with('success', 'Task updated successfully!');
    }

    /**
     * Delete a task
     */
    public function destroy(Task $task)
    {
        $staff = Auth::guard('staff')->user();

        if ($task->assigned_to != $staff->id) {
            abort(403, 'Unauthorized action.');
        }

        $task->delete();

        return redirect()->route('staff.tasks.index')->with('success', 'Task deleted successfully!');
    }

    /**
     * Show upcoming deadlines (tasks due in next 7 days)
     */
    public function deadlines()
    {
        $staff = Auth::guard('staff')->user();

        $upcomingDeadlines = $staff->tasks()
            ->whereNotNull('due_date')
            ->whereDate('due_date', '>=', now())
            ->whereDate('due_date', '<=', now()->addDays(7))
            ->orderBy('due_date', 'asc')
            ->get();

        return view('staff.tasks.deadlines', compact('upcomingDeadlines'));
    }
    public function pending()
{
    // Get logged-in staff
    $staffId = auth()->guard('staff')->id();

    // Fetch only pending tasks for that staff
    $tasks = Task::where('assigned_to', $staffId)
                 ->where('status', 'pending')
                 ->orderBy('due_date', 'asc')
                 ->get();

    return view('staff.tasks.pending', compact('tasks'));
}

}
