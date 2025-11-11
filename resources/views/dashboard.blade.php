@extends('layouts.app')

@section('title', 'Dashboard - CTPD Management System')

@section('content')
<div class="container-fluid py-4">
    <!-- Welcome Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h3 class="card-title">Welcome, {{ Auth::user()->name }}!</h3>
            <p class="text-muted mb-0">
                {{ Auth::user()->position }} - {{ Auth::user()->department }}
            </p>

            <!-- Task Stats -->
            <div class="row mt-4">
                <div class="col-md-3 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <i class="bi bi-list-task fs-1"></i>
                            <h5>Total Tasks</h5>
                            <h3>{{ $taskStats['total'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <i class="bi bi-clock fs-1"></i>
                            <h5>Pending</h5>
                            <h3>{{ $taskStats['pending'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <i class="bi bi-check-circle fs-1"></i>
                            <h5>Completed</h5>
                            <h3>{{ $taskStats['completed'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <i class="bi bi-star fs-1"></i>
                            <h5>Under Review</h5>
                            <h3>{{ $taskStats['review'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- My Tasks -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">My Tasks</h5>
                </div>
                <div class="card-body">
                    @if($myTasks->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($myTasks as $task)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $task->title }}</h6>
                                            <p class="text-muted small mb-1">{{ Str::limit($task->description, 100) }}</p>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-{{ $task->priority == 'high' ? 'danger' : ($task->priority == 'medium' ? 'warning' : 'secondary') }} me-2">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                                <span class="badge bg-{{ $task->status == 'completed' ? 'success' : ($task->status == 'in_progress' ? 'primary' : 'warning') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                </span>
                                                @if($task->due_date)
                                                    <small class="text-muted ms-2">
                                                        Due: {{ $task->due_date->format('M d, Y') }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                @if($task->status == 'pending')
                                                    <li><a class="dropdown-item" href="#" onclick="updateTaskStatus({{ $task->id }}, 'in_progress')">Start Task</a></li>
                                                @endif
                                                @if($task->status == 'in_progress')
                                                    <li><a class="dropdown-item" href="#" onclick="updateTaskStatus({{ $task->id }}, 'review')">Mark for Review</a></li>
                                                @endif
                                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewTaskModal" onclick="viewTask({{ $task->id }})">View Details</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    @if($task->assigner)
                                        <small class="text-muted">Assigned by: {{ $task->assigner->name }}</small>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">No tasks assigned to you.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Assigned Tasks & Quick Actions -->
        <div class="col-lg-6">
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignTaskModal">
                            <i class="bi bi-plus-circle me-2"></i>Assign New Task
                        </button>
                        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-list-task me-2"></i>View All Tasks
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tasks I Assigned -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Tasks I Assigned</h5>
                </div>
                <div class="card-body">
                    @if($assignedTasks->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($assignedTasks as $task)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $task->title }}</h6>
                                            <p class="small mb-1">To: {{ $task->assignee->name }}</p>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-{{ $task->status == 'completed' ? 'success' : ($task->status == 'in_progress' ? 'primary' : 'warning') }} me-2">
                                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                </span>
                                                @if($task->status == 'review')
                                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#reviewTaskModal" onclick="prepareReview({{ $task->id }})">
                                                        Give Review
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">You haven't assigned any tasks yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Task Modal -->
<div class="modal fade" id="assignTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign New Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Task Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assign To</label>
                        <select class="form-select" id="assigned_to" name="assigned_to" required>
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                @if($user->id != Auth::id())
                                    <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->position }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" class="form-control" id="due_date" name="due_date">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Review Task Modal -->
<div class="modal fade" id="reviewTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="reviewForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating</label>
                        <select class="form-select" id="rating" name="rating" required>
                            <option value="">Select Rating</option>
                            <option value="1">1 - Poor</option>
                            <option value="2">2 - Fair</option>
                            <option value="3">3 - Good</option>
                            <option value="4">4 - Very Good</option>
                            <option value="5">5 - Excellent</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="review" class="form-label">Review Comments</label>
                        <textarea class="form-control" id="review" name="review" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Task Modal -->
<div class="modal fade" id="viewTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Task Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="taskDetails">
                <!-- Task details will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateTaskStatus(taskId, status) {
    if (confirm('Are you sure you want to update this task status?')) {
        fetch(`/tasks/${taskId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: status })
        }).then(response => {
            if (response.ok) {
                location.reload();
            }
        });
    }
}

function prepareReview(taskId) {
    document.getElementById('reviewForm').action = `/tasks/${taskId}/review`;
}

function viewTask(taskId) {
    fetch(`/tasks/${taskId}`)
        .then(response => response.json())
        .then(task => {
            const details = `
                <h6>${task.title}</h6>
                <p>${task.description || 'No description provided.'}</p>
                <div class="row">
                    <div class="col-6">
                        <strong>Status:</strong>
                        <span class="badge bg-${task.status == 'completed' ? 'success' : (task.status == 'in_progress' ? 'primary' : 'warning')}">
                            ${task.status.replace('_', ' ')}
                        </span>
                    </div>
                    <div class="col-6">
                        <strong>Priority:</strong>
                        <span class="badge bg-${task.priority == 'high' ? 'danger' : (task.priority == 'medium' ? 'warning' : 'secondary')}">
                            ${task.priority}
                        </span>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-6">
                        <strong>Assigned by:</strong> ${task.assigner.name}
                    </div>
                    <div class="col-6">
                        <strong>Due Date:</strong> ${task.due_date ? new Date(task.due_date).toLocaleDateString() : 'Not set'}
                    </div>
                </div>
                ${task.review ? `
                <div class="mt-3">
                    <strong>Review:</strong>
                    <div class="alert alert-info mt-2">
                        <div>Rating: ${'★'.repeat(task.rating)}${'☆'.repeat(5-task.rating)}</div>
                        <div class="mt-1">${task.review}</div>
                    </div>
                </div>
                ` : ''}
            `;
            document.getElementById('taskDetails').innerHTML = details;
        });
}
</script>
@endpush
