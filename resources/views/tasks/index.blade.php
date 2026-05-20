@extends('layouts.app')

@section('content')
<style>
.excel-table {
    border: 1px solid #e0e0e0;
    font-size: 0.875rem;
}
.excel-table th {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    font-weight: 600;
    color: #495057;
    padding: 8px 12px;
    white-space: nowrap;
}
.excel-table td {
    border: 1px solid #dee2e6;
    padding: 8px 12px;
    vertical-align: middle;
}
.excel-table tr:hover {
    background-color: #f8f9fa;
}
.status-history {
    max-height: 250px;
    overflow-y: auto;
    font-size: 0.8rem;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 10px;
    background-color: #fafafa;
}
.status-entry {
    padding: 8px 12px;
    margin: 6px 0;
    border-radius: 6px;
    background-color: #f8f9fa;
    border-left: 4px solid #198754;
    line-height: 1.4;
}
.status-entry.assigner {
    border-left-color: #0d6efd;
    background-color: #e7f1ff;
}
.status-entry.assignee {
    border-left-color: #198754;
    background-color: #e8f5e8;
}
.status-message {
    word-wrap: break-word;
    white-space: pre-wrap;
}
.badge-excel {
    font-size: 0.75rem;
    padding: 4px 8px;
}
.priority-high { background-color: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
.priority-medium { background-color: #fff3e0; color: #ef6c00; border: 1px solid #ffe0b2; }
.priority-low { background-color: #e8f5e8; color: #2e7d32; border: 1px solid #c8e6c9; }
.status-pending { background-color: #f5f5f5; color: #757575; border: 1px solid #e0e0e0; }
.status-in_progress { background-color: #e3f2fd; color: #1565c0; border: 1px solid #bbdefb; }
.status-review { background-color: #fff8e1; color: #ff8f00; border: 1px solid #ffecb3; }
.status-completed { background-color: #e8f5e8; color: #2e7d32; border: 1px solid #c8e6c9; }

/* Scrollbar styling */
.status-history::-webkit-scrollbar {
    width: 6px;
}
.status-history::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}
.status-history::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}
.status-history::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Show more/less buttons */
.history-toggle {
    font-size: 0.75rem;
    padding: 4px 8px;
}

/* Weekly Plan Styles */
.weekly-plan-card {
    transition: all 0.2s ease;
}
.weekly-plan-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.team-plan-accordion .accordion-button:not(.collapsed) {
    background-color: #e8f5e8;
    color: #198754;
}
.team-plan-accordion .accordion-button:focus {
    box-shadow: none;
    border-color: #198754;
}
</style>

<div class="container-fluid">
    <div class="row justify-content-center mt-4">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold text-success">Tasks Management</h5>
                    <div>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-success btn-sm me-2">
                            <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                        </a>
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                            <i class="bi bi-plus-circle me-1"></i>Create Task
                        </button>
                    </div>
                </div>

                <div class="card-body bg-white p-0">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- ========= WEEKLY TASK PLANNING SECTION ========= -->
                    <div class="bg-white p-4 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0 fw-bold text-success">
                                    <i class="bi bi-calendar-week me-2"></i>My Weekly Plan
                                </h6>
                                <small class="text-muted">Week of {{ $currentWeekStart->format('M d, Y') }} - {{ $currentWeekEnd->format('M d, Y') }}</small>
                            </div>
                            <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#weeklyPlanModal">
                                <i class="bi bi-plus-circle me-1"></i>
                                {{ isset($myWeeklyPlan) && $myWeeklyPlan ? 'Edit Weekly Plan' : 'Submit Weekly Plan' }}
                            </button>
                        </div>

                        @if(isset($myWeeklyPlan) && $myWeeklyPlan && count($myWeeklyPlan->planned_tasks) > 0)
                            <div class="row g-2">
                                @foreach($myWeeklyPlan->planned_tasks as $index => $task)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card border-success bg-light weekly-plan-card">
                                            <div class="card-body py-2 px-3">
                                                <div class="d-flex align-items-start">
                                                    <span class="badge bg-success me-2 mt-1">{{ $index + 1 }}</span>
                                                    <span class="small">{{ $task }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-3 bg-light rounded">
                                <i class="bi bi-calendar2-week text-muted fs-4"></i>
                                <p class="text-muted small mb-0">No weekly plan submitted yet. Click the button above to plan your week!</p>
                            </div>
                        @endif

                        <!-- Supervisor View: See Everyone's Plans -->
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'supervisor')
                            <hr class="my-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 fw-bold text-success">
                                    <i class="bi bi-people me-2"></i>Team Weekly Plans
                                </h6>
                                <small class="text-muted">What everyone is working on this week</small>
                            </div>

                            @php
                                $teamPlans = $weeklyPlans->where('user_id', '!=', Auth::id());
                            @endphp

                            @if($teamPlans->count() > 0)
                                <div class="accordion team-plan-accordion" id="teamPlansAccordion">
                                    @foreach($teamPlans as $plan)
                                        <div class="accordion-item border-0 mb-2">
                                            <div class="accordion-header" id="heading{{ $plan->id }}">
                                                <button class="accordion-button collapsed bg-light rounded" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse{{ $plan->id }}"
                                                        style="background-color: #f8f9fa; font-size: 0.9rem;">
                                                    <div class="d-flex justify-content-between w-100 me-3">
                                                        <span>
                                                            <i class="bi bi-person-circle text-success me-2"></i>
                                                            <strong>{{ $plan->user->name }}</strong>
                                                        </span>
                                                        <span class="text-muted">
                                                            <i class="bi bi-list-check me-1"></i>{{ count($plan->planned_tasks) }} tasks planned
                                                        </span>
                                                    </div>
                                                </button>
                                            </div>
                                            <div id="collapse{{ $plan->id }}" class="accordion-collapse collapse"
                                                 data-bs-parent="#teamPlansAccordion">
                                                <div class="accordion-body pt-3">
                                                    <div class="row g-2">
                                                        @foreach($plan->planned_tasks as $index => $task)
                                                            <div class="col-12">
                                                                <div class="d-flex align-items-start">
                                                                    <span class="badge bg-secondary me-2 mt-1">{{ $index + 1 }}</span>
                                                                    <span class="small">{{ $task }}</span>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div class="mt-2 pt-2 border-top">
                                                        <small class="text-muted">
                                                            <i class="bi bi-clock me-1"></i>
                                                            Submitted: {{ $plan->created_at->format('M d, Y \a\t g:i A') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-3 bg-light rounded">
                                    <i class="bi bi-people text-muted fs-4"></i>
                                    <p class="text-muted small mb-0">No team members have submitted weekly plans yet.</p>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Tasks Table -->
                    @if($tasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered excel-table m-0">
                                <thead>
                                    <tr>
                                        <th>Task Title</th>
                                        <th>Description</th>
                                        <th>Assigned By</th>
                                        <th>Assigned To</th>
                                        <th>Priority</th>
                                        <th>Current Status</th>
                                        <th style="min-width: 300px;">Status History</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                        <tr>
                                            <td class="fw-semibold">{{ $task->title }}</td>
                                            <td style="max-width: 200px;">
                                                @if($task->description)
                                                    <span class="d-inline-block text-truncate" style="max-width: 180px;"
                                                          title="{{ $task->description }}">
                                                        {{ $task->description }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $task->assigner->name }}</small>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $task->assignee->name }}</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-excel priority-{{ $task->priority }}">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-excel status-{{ $task->status }}">
                                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                </span>
                                            </td>
                                            <td style="max-width: 350px;">
                                                <div class="status-history">
                                                    @php
                                                        $statusHistory = $task->status_history ?? [];
                                                        $totalEntries = count($statusHistory);
                                                        $showEntries = 3;
                                                        $showAll = request()->get('show_all') == $task->id;
                                                    @endphp

                                                    @if(!empty($statusHistory))
                                                        @if($showAll)
                                                            @foreach($statusHistory as $index => $history)
                                                                <div class="status-entry {{ $history['user_type'] }}">
                                                                    <strong>{{ $history['user_type'] == 'assigner' ? $task->assigner->name : $task->assignee->name }}:</strong>
                                                                    <span class="status-message">{{ $history['message'] }}</span>
                                                                    <br>
                                                                    <small class="text-muted">
                                                                        {{ \Carbon\Carbon::parse($history['timestamp'])->format('M j, Y g:i A') }}
                                                                        @if(isset($history['new_status']) && $history['new_status'])
                                                                            • Status: {{ ucfirst(str_replace('_', ' ', $history['new_status'])) }}
                                                                        @endif
                                                                    </small>
                                                                </div>
                                                            @endforeach
                                                            @if($totalEntries > $showEntries)
                                                                <div class="text-center mt-2">
                                                                    <a href="{{ request()->fullUrlWithQuery(['show_all' => null]) }}" class="btn btn-outline-secondary btn-sm history-toggle">
                                                                        <i class="bi bi-chevron-up me-1"></i>Show Less ({{ $totalEntries }} entries)
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        @else
                                                            @foreach(array_slice($statusHistory, -$showEntries) as $history)
                                                                <div class="status-entry {{ $history['user_type'] }}">
                                                                    <strong>{{ $history['user_type'] == 'assigner' ? $task->assigner->name : $task->assignee->name }}:</strong>
                                                                    <span class="status-message">{{ $history['message'] }}</span>
                                                                    <br>
                                                                    <small class="text-muted">
                                                                        {{ \Carbon\Carbon::parse($history['timestamp'])->format('M j, Y g:i A') }}
                                                                        @if(isset($history['new_status']) && $history['new_status'])
                                                                            • Status: {{ ucfirst(str_replace('_', ' ', $history['new_status'])) }}
                                                                        @endif
                                                                    </small>
                                                                </div>
                                                            @endforeach
                                                            @if($totalEntries > $showEntries)
                                                                <div class="text-center mt-2">
                                                                    <a href="{{ request()->fullUrlWithQuery(['show_all' => $task->id]) }}" class="btn btn-outline-primary btn-sm history-toggle">
                                                                        <i class="bi bi-chevron-down me-1"></i>Show More (+{{ $totalEntries - $showEntries }})
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @else
                                                        <span class="text-muted">No status updates yet</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($task->due_date)
                                                    @php
                                                        $dueDate = \Carbon\Carbon::parse($task->due_date);
                                                        $isOverdue = $dueDate->isPast() && $task->status !== 'completed';
                                                    @endphp
                                                    <small class="{{ $isOverdue ? 'text-danger fw-bold' : 'text-muted' }}">
                                                        {{ $dueDate->format('M d, Y') }}
                                                    </small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary btn-sm update-status-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#statusUpdateModal"
                                                            data-task-id="{{ $task->id }}"
                                                            data-task-title="{{ $task->title }}"
                                                            data-current-status="{{ $task->status }}"
                                                            title="Update Status">
                                                        <i class="bi bi-chat-left-text"></i>
                                                    </button>

                                                    @if($task->assigned_to === Auth::id() && $task->status !== 'completed')
                                                        <form action="{{ route('tasks.quick-complete', $task) }}" method="POST" class="d-inline ms-1">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-success btn-sm"
                                                                    onclick="return confirm('Mark this task as completed?')"
                                                                    title="Mark as Complete">
                                                                <i class="bi bi-check-circle"></i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if($task->assigned_by === Auth::id())
                                                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline ms-1">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                                    onclick="return confirm('Are you sure you want to delete this task?')"
                                                                    title="Delete Task">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center p-3 border-top">
                            <div class="text-muted small">
                                Showing {{ $tasks->firstItem() }} to {{ $tasks->lastItem() }} of {{ $tasks->total() }} tasks
                            </div>
                            <div>
                                {{ $tasks->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h5 class="text-muted mt-3">No tasks found</h5>
                            <p class="text-muted">You don't have any tasks assigned to you or created by you yet.</p>
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                                <i class="bi bi-plus-circle me-1"></i>Create Your First Task
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Task Modal -->
<div class="modal fade" id="createTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-success">Create New Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Task Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required
                               placeholder="Enter task title">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                                  placeholder="Enter task description (optional)"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assign To <span class="text-danger">*</span></label>
                        <select class="form-select" id="assigned_to" name="assigned_to" required>
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                <select class="form-select" id="priority" name="priority" required>
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" class="form-control" id="due_date" name="due_date"
                                       min="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm">Create Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary">Update Task Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusUpdateForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="task_id" id="status_task_id">
                    <div class="mb-3">
                        <label class="form-label">Task: <span id="status_task_title" class="fw-bold"></span></label>
                        <br>
                        <small class="text-muted">Current Status: <span id="current_status" class="badge status-pending"></span></small>
                    </div>
                    <div class="mb-3">
                        <label for="status_message" class="form-label">Status Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="status_message" name="message" rows="4" required
                                  placeholder="Describe the current status... (e.g., 'Working on the design', 'Waiting for feedback', 'Completed phase 1', etc.)"></textarea>
                        <small class="text-muted">This message will be added to the status history</small>
                    </div>
                    <div class="mb-3">
                        <label for="new_status" class="form-label">Update Status To</label>
                        <select class="form-select" id="new_status" name="new_status">
                            <option value="">Keep current status</option>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="review">Review</option>
                            <option value="completed">Completed</option>
                        </select>
                        <small class="text-muted">Optional: Change the overall task status</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Submit Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Weekly Plan Modal -->
<div class="modal fade" id="weeklyPlanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-success">
                    <i class="bi bi-calendar-week me-2"></i>
                    {{ isset($myWeeklyPlan) && $myWeeklyPlan ? 'Edit Weekly Plan' : 'Submit Weekly Plan' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            @if(isset($myWeeklyPlan) && $myWeeklyPlan)
                <!-- EDIT FORM - NO _method field, just POST -->
                <form action="{{ url('/weekly-plans/' . $myWeeklyPlan->id) }}" method="POST" id="weeklyPlanForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar-week text-success me-1"></i>
                                Week of {{ $currentWeekStart->format('M d, Y') }} - {{ $currentWeekEnd->format('M d, Y') }}
                            </label>
                            <input type="hidden" name="week_start" value="{{ $currentWeekStart->format('Y-m-d') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                What are you planning to work on this week?
                                <span class="text-danger">*</span>
                            </label>
                            <div id="tasksList">
                                @foreach($myWeeklyPlan->planned_tasks as $index => $task)
                                    <div class="input-group mb-2 task-item">
                                        <span class="input-group-text bg-light">{{ $index + 1 }}</span>
                                        <input type="text" class="form-control" name="planned_tasks[]"
                                               value="{{ $task }}" placeholder="Enter task description" required>
                                        <button type="button" class="btn btn-outline-danger remove-task" onclick="removeTask(this)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="addTask()">
                                <i class="bi bi-plus-circle me-1"></i>Add Another Task
                            </button>
                            <div class="form-text mt-2">
                                <i class="bi bi-info-circle text-success"></i>
                                List the specific tasks you plan to complete this week. This helps supervisors understand your workload before assigning new tasks.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-save me-1"></i>Update Weekly Plan
                        </button>
                    </div>
                </form>
            @else
                <!-- CREATE FORM -->
                <form action="{{ route('weekly-plans.store') }}" method="POST" id="weeklyPlanForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar-week text-success me-1"></i>
                                Week of {{ $currentWeekStart->format('M d, Y') }} - {{ $currentWeekEnd->format('M d, Y') }}
                            </label>
                            <input type="hidden" name="week_start" value="{{ $currentWeekStart->format('Y-m-d') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                What are you planning to work on this week?
                                <span class="text-danger">*</span>
                            </label>
                            <div id="tasksList">
                                <div class="input-group mb-2 task-item">
                                    <span class="input-group-text bg-light">1</span>
                                    <input type="text" class="form-control" name="planned_tasks[]"
                                           placeholder="Enter task description" required>
                                    <button type="button" class="btn btn-outline-danger remove-task" onclick="removeTask(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="addTask()">
                                <i class="bi bi-plus-circle me-1"></i>Add Another Task
                            </button>
                            <div class="form-text mt-2">
                                <i class="bi bi-info-circle text-success"></i>
                                List the specific tasks you plan to complete this week. This helps supervisors understand your workload before assigning new tasks.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-save me-1"></i>Save Weekly Plan
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<script>
let taskCounter = {{ isset($myWeeklyPlan) && $myWeeklyPlan ? count($myWeeklyPlan->planned_tasks) : 1 }};

function addTask() {
    taskCounter++;
    const tasksList = document.getElementById('tasksList');
    const newTask = document.createElement('div');
    newTask.className = 'input-group mb-2 task-item';
    newTask.innerHTML = `
        <span class="input-group-text bg-light">${taskCounter}</span>
        <input type="text" class="form-control" name="planned_tasks[]"
               placeholder="Enter task description" required>
        <button type="button" class="btn btn-outline-danger remove-task" onclick="removeTask(this)">
            <i class="bi bi-trash"></i>
        </button>
    `;
    tasksList.appendChild(newTask);
}

function removeTask(button) {
    button.closest('.task-item').remove();
    // Renumber remaining tasks
    document.querySelectorAll('#tasksList .task-item').forEach((item, idx) => {
        item.querySelector('.input-group-text').textContent = idx + 1;
    });
    taskCounter = document.querySelectorAll('#tasksList .task-item').length;
}

// Handle status update buttons
document.addEventListener('DOMContentLoaded', function() {
    // Handle status update buttons
    document.querySelectorAll('.update-status-btn').forEach(button => {
        button.addEventListener('click', function() {
            const taskId = this.getAttribute('data-task-id');
            const taskTitle = this.getAttribute('data-task-title');
            const currentStatus = this.getAttribute('data-current-status');

            document.getElementById('status_task_id').value = taskId;
            document.getElementById('status_task_title').textContent = taskTitle;
            document.getElementById('current_status').textContent = currentStatus.replace('_', ' ');
            document.getElementById('current_status').className = 'badge status-' + currentStatus;

            const form = document.getElementById('statusUpdateForm');
            form.action = '/tasks/' + taskId + '/status-update';
        });
    });

    // Validate weekly plan form before submission
    const weeklyForm = document.getElementById('weeklyPlanForm');
    if (weeklyForm) {
        weeklyForm.addEventListener('submit', function(e) {
            const tasks = document.querySelectorAll('#tasksList input[name="planned_tasks[]"]');
            let hasValidTask = false;
            let hasEmpty = false;

            tasks.forEach(task => {
                if (task.value.trim() !== '') {
                    hasValidTask = true;
                } else {
                    hasEmpty = true;
                    task.classList.add('is-invalid');
                }
            });

            if (!hasValidTask) {
                e.preventDefault();
                alert('Please add at least one task to your weekly plan.');
                return false;
            }

            if (hasEmpty) {
                e.preventDefault();
                alert('Please fill in all task descriptions or remove empty fields.');
                return false;
            }
        });
    }
});
</script>
@endsection
