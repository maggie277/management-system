<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // For date comparisons

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:staff');
    }

    public function index()
    {
        $staff = Auth::guard('staff')->user();

        // 1. Assigned Tasks – all tasks assigned to this staff
        $assignedTasks = $staff->tasks()->orderBy('due_date')->get();

        // 2. Upcoming Deadlines – tasks due today or in the future, not completed
        $upcomingDeadlines = $staff->tasks()
            ->where('status', '!=', 'completed')
            ->whereDate('due_date', '>=', Carbon::today())
            ->orderBy('due_date')
            ->get();

        // 3. Pending Items – tasks specifically marked as pending
        $pendingItems = $staff->tasks()
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->get();

        return view(
            'staff.dashboard',
            compact('staff', 'assignedTasks', 'upcomingDeadlines', 'pendingItems')
        );
    }
}
