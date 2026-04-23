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
    max-height: 250px; /* Increased height */
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
                                                        $showEntries = 3; // Show last 3 entries by default
                                                        $showAll = request()->get('show_all') == $task->id;
                                                    @endphp

                                                    @if(!empty($statusHistory))
                                                        @if($showAll)
                                                            <!-- Show all entries -->
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

                                                            <!-- Show Less button -->
                                                            @if($totalEntries > $showEntries)
                                                                <div class="text-center mt-2">
                                                                    <a href="{{ request()->fullUrlWithQuery(['show_all' => null]) }}" class="btn btn-outline-secondary btn-sm history-toggle">
                                                                        <i class="bi bi-chevron-up me-1"></i>Show Less ({{ $totalEntries }} entries)
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        @else
                                                            <!-- Show limited entries -->
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

                                                            <!-- Show More button -->
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
                                                    <!-- Status Update Button -->
                                                    <button class="btn btn-outline-primary btn-sm update-status-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#statusUpdateModal"
                                                            data-task-id="{{ $task->id }}"
                                                            data-task-title="{{ $task->title }}"
                                                            data-current-status="{{ $task->status }}"
                                                            title="Update Status">
                                                        <i class="bi bi-chat-left-text"></i>
                                                    </button>

                                                    <!-- Quick Complete Button -->
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

                                                    <!-- Delete Button -->
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

<script>
// Simple and reliable JavaScript for modal functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('Tasks page loaded successfully');

    // Handle status update buttons
    document.querySelectorAll('.update-status-btn').forEach(button => {
        button.addEventListener('click', function() {
            const taskId = this.getAttribute('data-task-id');
            const taskTitle = this.getAttribute('data-task-title');
            const currentStatus = this.getAttribute('data-current-status');

            console.log('Setting up modal for task:', taskId, taskTitle, currentStatus);

            // Update the modal form fields
            document.getElementById('status_task_id').value = taskId;
            document.getElementById('status_task_title').textContent = taskTitle;
            document.getElementById('current_status').textContent = currentStatus.replace('_', ' ');
            document.getElementById('current_status').className = 'badge status-' + currentStatus;

            // Set the form action
            const form = document.getElementById('statusUpdateForm');
            form.action = '/tasks/' + taskId + '/status-update';

            console.log('Form action set to:', form.action);
        });
    });

    // Add form submission logging for debugging
    document.getElementById('statusUpdateForm')?.addEventListener('submit', function(e) {
        console.log('Status update form submitted');
        console.log('Task ID:', document.getElementById('status_task_id').value);
        console.log('Form action:', this.action);
    });
});
</script>
@endsection
