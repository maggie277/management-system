<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\CalendarEvent;
use App\Models\WeeklyTaskPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with(['assigner', 'assignee'])
            ->where('assigned_to', Auth::id())
            ->orWhere('assigned_by', Auth::id())
            ->latest()
            ->paginate(10);

        $users = User::where('id', '!=', Auth::id())->get();

        // Get weekly task plans for current week
        $currentWeekStart = now()->startOfWeek();
        $currentWeekEnd = now()->endOfWeek();

        $weeklyPlans = WeeklyTaskPlan::with('user')
            ->where('week_start', $currentWeekStart->format('Y-m-d'))
            ->orderBy('created_at', 'desc')
            ->get();

        // Get user's own weekly plan (FIXED: Added this line)
        $myWeeklyPlan = WeeklyTaskPlan::where('user_id', Auth::id())
            ->where('week_start', $currentWeekStart->format('Y-m-d'))
            ->first();

        // Get pending tasks for the week
        $pendingTasksCount = Task::where('assigned_to', Auth::id())
            ->where('status', '!=', 'completed')
            ->count();

        // Pass $myWeeklyPlan to the view (FIXED: Added $myWeeklyPlan to compact)
        return view('tasks.index', compact('tasks', 'users', 'weeklyPlans', 'pendingTasksCount', 'currentWeekStart', 'currentWeekEnd', 'myWeeklyPlan'));
    }

    public function dashboard()
    {
        $user = Auth::user();

        // Task statistics
        $taskStats = [
            'total' => Task::where('assigned_to', Auth::id())->count(),
            'pending' => Task::where('assigned_to', Auth::id())->where('status', 'pending')->count(),
            'completed' => Task::where('assigned_to', Auth::id())->where('status', 'completed')->count(),
            'review' => Task::where('assigned_to', Auth::id())->where('status', 'review')->count(),
        ];

        // Pending tasks assigned to current user (not completed)
        $pendingTasks = Task::with('assigner')
            ->where('assigned_to', Auth::id())
            ->where('status', '!=', 'completed')
            ->latest()
            ->get();

        // Get today's meetings
        $todayMeetings = CalendarEvent::where('user_id', $user->id)
            ->whereDate('start', today())
            ->orderBy('start', 'asc')
            ->get();

        // Get upcoming meetings (next 7 days)
        $upcomingMeetings = CalendarEvent::where('user_id', $user->id)
            ->where('start', '>', now())
            ->where('start', '<=', now()->addDays(7))
            ->whereDate('start', '!=', today()) // Exclude today's meetings
            ->orderBy('start', 'asc')
            ->get();

        // All users for assigning tasks
        $users = User::where('id', '!=', Auth::id())->get();

        // Get all weekly plans for supervisor view
        $currentWeekStart = now()->startOfWeek();
        $allWeeklyPlans = WeeklyTaskPlan::with('user')
            ->where('week_start', $currentWeekStart->format('Y-m-d'))
            ->orderBy('created_at', 'desc')
            ->get();

        // Get user's own weekly plan
        $myWeeklyPlan = WeeklyTaskPlan::where('user_id', Auth::id())
            ->where('week_start', $currentWeekStart->format('Y-m-d'))
            ->first();

        return view('dashboard', compact(
            'taskStats',
            'pendingTasks',
            'users',
            'todayMeetings',
            'upcomingMeetings',
            'user',
            'allWeeklyPlans',
            'myWeeklyPlan',
            'currentWeekStart'
        ));
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
            'status' => 'pending',
            'status_history' => [
                [
                    'user_type' => 'assigner',
                    'user_id' => Auth::id(),
                    'message' => 'Task created and assigned',
                    'timestamp' => now()->toDateTimeString(),
                    'new_status' => 'pending'
                ]
            ]
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

        if ($task->assigned_to !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->update([
            'status' => $request->status,
            'completed_at' => $request->status === 'completed' ? now() : null
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Task $task)
    {
        if ($task->assigned_by !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized to delete this task.');
        }

        $task->delete();
        return redirect()->back()->with('success', 'Task deleted successfully!');
    }

    public function addStatusUpdate(Request $request, Task $task)
    {
        Log::info('=== STATUS UPDATE START ===');
        Log::info('Task ID: ' . $task->id);
        Log::info('User ID: ' . Auth::id());
        Log::info('Request data:', $request->all());

        $request->validate([
            'message' => 'required|string|max:1000',
            'new_status' => 'nullable|in:pending,in_progress,review,completed'
        ]);

        // Check if user is either assigner or assignee
        if ($task->assigned_to !== Auth::id() && $task->assigned_by !== Auth::id()) {
            Log::warning('Unauthorized access attempt');
            return redirect()->back()->with('error', 'Unauthorized to update this task.');
        }

        // Determine user type
        $userType = $task->assigned_by === Auth::id() ? 'assigner' : 'assignee';
        Log::info('User type: ' . $userType);

        // Get current status history or initialize empty array
        $statusHistory = $task->status_history ?? [];
        Log::info('Current history count: ' . count($statusHistory));

        // Add new status update
        $newEntry = [
            'user_type' => $userType,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'timestamp' => now()->toDateTimeString(),
            'new_status' => $request->new_status
        ];

        $statusHistory[] = $newEntry;
        Log::info('New entry added:', $newEntry);

        // Update task
        $updateData = ['status_history' => $statusHistory];

        if ($request->new_status) {
            $updateData['status'] = $request->new_status;
            Log::info('Updating status to: ' . $request->new_status);

            if ($request->new_status === 'completed') {
                $updateData['completed_at'] = now();
                Log::info('Setting completed_at timestamp');
            }
        }

        Log::info('Final update data:', $updateData);

        try {
            $task->update($updateData);
            Log::info('Task updated successfully');
            Log::info('=== STATUS UPDATE END ===');

            return redirect()->route('tasks.index')->with('success', 'Status update added successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating task: ' . $e->getMessage());
            Log::info('=== STATUS UPDATE FAILED ===');
            return redirect()->back()->with('error', 'Error updating task: ' . $e->getMessage());
        }
    }

    public function quickComplete(Task $task)
    {
        Log::info('Quick complete for task: ' . $task->id);

        if ($task->assigned_to !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $statusHistory = $task->status_history ?? [];

        $statusHistory[] = [
            'user_type' => 'assignee',
            'user_id' => Auth::id(),
            'message' => 'Task marked as completed',
            'timestamp' => now()->toDateTimeString(),
            'new_status' => 'completed'
        ];

        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
            'status_history' => $statusHistory
        ]);

        Log::info('Quick complete successful');
        return response()->json(['success' => true]);
    }

    // NEW METHODS FOR WEEKLY PLANNING

    public function storeWeeklyPlan(Request $request)
    {
        $request->validate([
            'planned_tasks' => 'required|array|min:1',
            'planned_tasks.*' => 'string|max:500',
            'week_start' => 'required|date'
        ]);

        $weekStart = $request->week_start;

        // Check if user already has a plan for this week
        $existingPlan = WeeklyTaskPlan::where('user_id', Auth::id())
            ->where('week_start', $weekStart)
            ->first();

        if ($existingPlan) {
            return redirect()->back()->with('error', 'You already have a weekly plan for this week. You can edit it instead.');
        }

        WeeklyTaskPlan::create([
            'user_id' => Auth::id(),
            'week_start' => $weekStart,
            'planned_tasks' => $request->planned_tasks,
            'status' => 'planned'
        ]);

        return redirect()->back()->with('success', 'Weekly plan submitted successfully! Supervisors can now see your planned tasks.');
    }

    public function updateWeeklyPlan(Request $request, WeeklyTaskPlan $weeklyPlan)
    {
        // Only the plan owner can update
        if ($weeklyPlan->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized to update this plan.');
        }

        $request->validate([
            'planned_tasks' => 'required|array|min:1',
            'planned_tasks.*' => 'string|max:500',
        ]);

        $weeklyPlan->update([
            'planned_tasks' => $request->planned_tasks,
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Weekly plan updated successfully!');
    }

    public function getWeeklyPlan($userId = null)
    {
        $weekStart = request()->get('week_start', now()->startOfWeek()->format('Y-m-d'));
        $targetUserId = $userId ?? Auth::id();

        $plan = WeeklyTaskPlan::where('user_id', $targetUserId)
            ->where('week_start', $weekStart)
            ->first();

        return response()->json($plan);
    }

    public function getAllWeeklyPlans()
    {
        $weekStart = request()->get('week_start', now()->startOfWeek()->format('Y-m-d'));

        $plans = WeeklyTaskPlan::with('user')
            ->where('week_start', $weekStart)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($plans);
    }
}
