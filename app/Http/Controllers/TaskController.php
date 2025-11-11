<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with(['assigner', 'assignee'])
            ->where('assigned_to', Auth::id())
            ->orWhere('assigned_by', Auth::id())
            ->latest()
            ->paginate(10);

        return view('tasks.index', compact('tasks'));
    }

    public function dashboard()
    {
        // Task statistics
        $taskStats = [
            'total' => Task::where('assigned_to', Auth::id())->count(),
            'pending' => Task::where('assigned_to', Auth::id())->where('status', 'pending')->count(),
            'completed' => Task::where('assigned_to', Auth::id())->where('status', 'completed')->count(),
            'review' => Task::where('assigned_to', Auth::id())->where('status', 'review')->count(),
        ];

        // Tasks assigned to current user
        $myTasks = Task::with('assigner')
            ->where('assigned_to', Auth::id())
            ->whereIn('status', ['pending', 'in_progress'])
            ->latest()
            ->take(5)
            ->get();

        // Tasks assigned by current user
        $assignedTasks = Task::with('assignee')
            ->where('assigned_by', Auth::id())
            ->whereIn('status', ['pending', 'in_progress', 'review'])
            ->latest()
            ->take(5)
            ->get();

        // All users for assigning tasks
        $users = User::where('id', '!=', Auth::id())->get();

        return view('dashboard', compact('taskStats', 'myTasks', 'assignedTasks', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);

        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_by' => Auth::id(),
            'assigned_to' => $request->assigned_to,
            'priority' => $request->priority,
            'due_date' => $request->due_date,
        ]);

        return redirect()->back()->with('success', 'Task assigned successfully!');
    }

    public function show(Task $task)
    {
        $task->load(['assigner', 'assignee']);
        return response()->json($task);
    }

    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,review'
        ]);

        // Check if user is authorized to update this task
        if ($task->assigned_to !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->update([
            'status' => $request->status,
            'completed_at' => $request->status === 'completed' ? now() : null
        ]);

        return response()->json(['success' => true]);
    }

    public function submitReview(Request $request, Task $task)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'review' => 'required|string'
        ]);

        // Check if user is the assigner of this task
        if ($task->assigned_by !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->update([
            'rating' => $request->rating,
            'review' => $request->review,
            'status' => 'completed'
        ]);

        return redirect()->back()->with('success', 'Review submitted successfully!');
    }

    public function destroy(Task $task)
    {
        // Only the assigner can delete the task
        if ($task->assigned_by !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized to delete this task.');
        }

        $task->delete();

        return redirect()->back()->with('success', 'Task deleted successfully!');
    }
}
