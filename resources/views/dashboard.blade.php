@extends('layouts.app')

@section('title', 'Dashboard - CTPD Management System')

@section('content')
<style>
:root {
    --primary-green: #198754;
    --light-green: #f3fef6;
    --hover-green: #157347;
    --card-shadow: 0 3px 10px rgba(25, 135, 84, 0.1);
    --card-shadow-hover: 0 10px 20px rgba(25, 135, 84, 0.2);
}

.stat-card {
    transition: all 0.35s ease;
    border-radius: 1rem;
    border: 1px solid rgba(25, 135, 84, 0.15);
    background: linear-gradient(145deg, #ffffff, var(--light-green));
    box-shadow: var(--card-shadow);
}

.stat-card:hover {
    transform: translateY(-6px) scale(1.02);
    box-shadow: var(--card-shadow-hover);
    background: linear-gradient(145deg, #e9fff0, #ffffff);
}

.stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-green);
    letter-spacing: 0.5px;
}

.stat-label {
    color: #3a3a3a;
    font-weight: 500;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 1px;
}

.card-header {
    border-bottom: none;
    background: linear-gradient(to right, #ffffff, var(--light-green));
}

.hover-brighten {
    transition: all 0.3s ease;
}

.hover-brighten:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 6px rgba(25, 135, 84, 0.3);
}

.welcome-card {
    background: linear-gradient(135deg, #ffffff 0%, var(--light-green) 100%);
    border: 1px solid rgba(25, 135, 84, 0.15);
    border-radius: 1rem;
    box-shadow: var(--card-shadow);
}

.meeting-card, .task-card {
    border-radius: 1rem;
    border: 1px solid rgba(25, 135, 84, 0.15);
    background: linear-gradient(145deg, #ffffff, #f8fef9);
    box-shadow: var(--card-shadow);
    transition: all 0.3s ease;
}

.meeting-card:hover, .task-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--card-shadow-hover);
}

.meeting-item, .task-item {
    border-left: 3px solid var(--primary-green);
    transition: all 0.2s ease;
}

.meeting-item:hover, .task-item:hover {
    background-color: rgba(25, 135, 84, 0.05);
    transform: translateX(5px);
}

.badge-status {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
}

.btn-success {
    background-color: var(--primary-green);
    border-color: var(--primary-green);
}

.btn-success:hover {
    background-color: var(--hover-green);
    border-color: var(--hover-green);
}

.btn-outline-success:hover {
    background-color: var(--primary-green);
    color: #fff;
}

.text-success {
    color: var(--primary-green) !important;
}

.border-success {
    border-color: var(--primary-green) !important;
}

.status-pending {
    background-color: #6c757d;
}

.status-in_progress {
    background-color: #0d6efd;
}

.status-review {
    background-color: #fd7e14;
}

.status-completed {
    background-color: var(--primary-green);
}

.empty-state {
    color: #6c757d;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
}
</style>

<div class="container-fluid py-4">
    <!-- Welcome Card -->
    <div class="card welcome-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="card-title text-success fw-bold">Welcome, {{ Auth::user()->name }}!</h3>
                    <p class="text-muted mb-0">
                        <i class="bi bi-person-badge me-2"></i>{{ Auth::user()->position }} - {{ Auth::user()->department }}
                    </p>

                    <!-- View All Tasks Button -->
                    <div class="mt-3">
                        <a href="{{ route('tasks.index') }}" class="btn btn-success me-2 hover-brighten">
                            <i class="bi bi-list-task me-2"></i>View All Tasks
                        </a>
                        <a href="{{ route('calendar.index') }}" class="btn btn-outline-success hover-brighten">
                            <i class="bi bi-calendar me-2"></i>View Calendar
                        </a>
                    </div>
                </div>
                <div class="col-md-4 text-center d-none d-md-block">
                    <i class="bi bi-graph-up-arrow display-4 text-success opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card text-center p-4">
                <div class="stat-value">{{ $todayMeetings->count() + $upcomingMeetings->count() }}</div>
                <div class="stat-label">Total Meetings</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card text-center p-4">
                <div class="stat-value">{{ $todayMeetings->count() }}</div>
                <div class="stat-label">Today's Meetings</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card text-center p-4">
                <div class="stat-value">{{ $upcomingMeetings->count() }}</div>
                <div class="stat-label">Upcoming Meetings</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card text-center p-4">
                <div class="stat-value">{{ $pendingTasks->count() }}</div>
                <div class="stat-label">Pending Tasks</div>
            </div>
        </div>
    </div>

    <!-- Meeting Notifications -->
    <div class="row mt-4">
        <!-- Today's Meetings -->
        <div class="col-lg-6 mb-4">
            <div class="card meeting-card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title mb-0 fw-bold text-success">
                        <i class="bi bi-calendar-day me-2"></i>Today's Meetings
                    </h5>
                    <span class="badge bg-success rounded-pill">{{ $todayMeetings->count() }}</span>
                </div>
                <div class="card-body">
                    @if($todayMeetings->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($todayMeetings as $meeting)
                                <div class="list-group-item px-0 py-3 meeting-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-camera-video-fill text-success me-2"></i>
                                                <h6 class="mb-0 fw-bold">{{ $meeting->title }}</h6>
                                            </div>
                                            <div class="d-flex align-items-center flex-wrap mb-2">
                                                <span class="badge bg-success me-2 mb-1 badge-status">
                                                    <i class="bi bi-clock me-1"></i>{{ $meeting->start->format('g:i A') }}
                                                    @if($meeting->end)
                                                        - {{ $meeting->end->format('g:i A') }}
                                                    @endif
                                                </span>
                                                @if($meeting->location)
                                                    <span class="badge bg-secondary me-2 mb-1 badge-status">
                                                        <i class="bi bi-geo-alt me-1"></i>{{ $meeting->location }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if($meeting->description)
                                                <p class="text-muted small mb-0">{{ Str::limit($meeting->description, 100) }}</p>
                                            @endif
                                        </div>
                                        <div class="ms-3">
                                            <a href="{{ route('calendar.index') }}" class="btn btn-outline-success btn-sm hover-brighten">
                                                <i class="bi bi-eye me-1"></i>View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 empty-state">
                            <i class="bi bi-calendar-x"></i>
                            <h5 class="text-muted">No meetings today</h5>
                            <p class="text-muted">You have no meetings scheduled for today.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Upcoming Meetings -->
        <div class="col-lg-6 mb-4">
            <div class="card meeting-card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title mb-0 fw-bold text-success">
                        <i class="bi bi-clock me-2"></i>Upcoming Meetings
                    </h5>
                    <span class="badge bg-warning text-dark rounded-pill">{{ $upcomingMeetings->count() }}</span>
                </div>
                <div class="card-body">
                    @if($upcomingMeetings->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($upcomingMeetings as $meeting)
                                <div class="list-group-item px-0 py-3 meeting-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-calendar-event text-warning me-2"></i>
                                                <h6 class="mb-0 fw-bold">{{ $meeting->title }}</h6>
                                            </div>
                                            <div class="d-flex align-items-center flex-wrap mb-2">
                                                <span class="badge bg-info me-2 mb-1 badge-status">
                                                    <i class="bi bi-calendar me-1"></i>{{ $meeting->start->format('M j, Y') }}
                                                </span>
                                                <span class="badge bg-success me-2 mb-1 badge-status">
                                                    <i class="bi bi-clock me-1"></i>{{ $meeting->start->format('g:i A') }}
                                                </span>
                                                @if($meeting->location)
                                                    <span class="badge bg-secondary me-2 mb-1 badge-status">
                                                        <i class="bi bi-geo-alt me-1"></i>{{ Str::limit($meeting->location, 20) }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if($meeting->description)
                                                <p class="text-muted small mb-0">{{ Str::limit($meeting->description, 80) }}</p>
                                            @endif
                                        </div>
                                        <div class="ms-3">
                                            <a href="{{ route('calendar.index') }}" class="btn btn-outline-success btn-sm hover-brighten">
                                                <i class="bi bi-eye me-1"></i>View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 empty-state">
                            <i class="bi bi-calendar-check"></i>
                            <h5 class="text-muted">No upcoming meetings</h5>
                            <p class="text-muted">You have no meetings scheduled for the upcoming days.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Task Notifications -->
    <div class="row mt-2">
        <div class="col-12">
            <div class="card task-card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title mb-0 fw-bold text-success">Task Notifications</h5>
                    <span class="badge bg-success rounded-pill">{{ $pendingTasks->count() }} pending</span>
                </div>
                <div class="card-body">
                    @if($pendingTasks->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($pendingTasks as $task)
                                <div class="list-group-item px-0 py-3 task-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-bell-fill text-warning me-2"></i>
                                                <h6 class="mb-0 fw-bold">{{ $task->title }}</h6>
                                            </div>
                                            @if($task->description)
                                                <p class="text-muted small mb-2">{{ Str::limit($task->description, 150) }}</p>
                                            @endif
                                            <div class="d-flex align-items-center flex-wrap">
                                                <span class="badge bg-{{ $task->priority == 'high' ? 'danger' : ($task->priority == 'medium' ? 'warning' : 'secondary') }} me-2 mb-1 badge-status">
                                                    {{ ucfirst($task->priority) }} Priority
                                                </span>
                                                <span class="badge bg-primary me-2 mb-1 badge-status">
                                                    Assigned by: {{ $task->assigner->name }}
                                                </span>
                                                @if($task->due_date)
                                                    <small class="text-muted mb-1">
                                                        <i class="bi bi-calendar me-1"></i>Due: {{ $task->due_date->format('M d, Y') }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <button class="btn btn-outline-success btn-sm hover-brighten update-status-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#statusUpdateModal"
                                                    data-task-id="{{ $task->id }}"
                                                    data-task-title="{{ $task->title }}"
                                                    data-current-status="{{ $task->status }}"
                                                    title="Update Status">
                                                <i class="bi bi-chat-left-text me-1"></i>Update Status
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 empty-state">
                            <i class="bi bi-check-circle"></i>
                            <h5 class="text-muted">No pending tasks</h5>
                            <p class="text-muted">You're all caught up! No tasks require your attention at the moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-success fw-bold">Update Task Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusUpdateForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="task_id" id="status_task_id">
                    <div class="mb-3">
                        <label class="form-label">Task: <span id="status_task_title" class="fw-bold text-success"></span></label>
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
                    <button type="submit" class="btn btn-success btn-sm">Submit Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Simple and reliable JavaScript for modal functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard loaded successfully');

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
@endpush
