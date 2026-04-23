@extends('layouts.app')

@section('title', 'Calendar - CTPD Management System')

@section('content')
<!-- Load Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<div class="container-fluid">
    <div class="row">
        <!-- Calendar Section -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar-check me-2"></i>Meeting Calendar
                    </h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addEventModal">
                        <i class="bi bi-plus-circle me-1"></i>Add Meeting
                    </button>
                </div>
                <div class="card-body">
                    <!-- Calendar Navigation -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="btn-group">
                                <a href="{{ route('calendar.index', ['month' => 'prev']) }}"
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-chevron-left"></i> Previous Month
                                </a>
                                <a href="{{ route('calendar.index', ['month' => 'next']) }}"
                                   class="btn btn-outline-primary btn-sm">
                                    Next Month <i class="bi bi-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <h4 class="text-primary mb-0">
                                {{ $currentDate->format('F Y') }}
                            </h4>
                        </div>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="calendar-table">
                        <table class="table table-bordered calendar-grid">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center p-3">Sun</th>
                                    <th class="text-center p-3">Mon</th>
                                    <th class="text-center p-3">Tue</th>
                                    <th class="text-center p-3">Wed</th>
                                    <th class="text-center p-3">Thu</th>
                                    <th class="text-center p-3">Fri</th>
                                    <th class="text-center p-3">Sat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $firstDay = $currentDate->copy()->startOfMonth()->dayOfWeek;
                                    $daysInMonth = $currentDate->daysInMonth;
                                    $today = now()->format('Y-m-d');
                                @endphp

                                @for($week = 0; $week < 6; $week++)
                                    <tr>
                                        @for($weekday = 0; $weekday < 7; $weekday++)
                                            @php
                                                $dayNumber = ($week * 7) + $weekday - $firstDay + 1;
                                                $isCurrentMonth = $dayNumber >= 1 && $dayNumber <= $daysInMonth;
                                                $dateStr = $isCurrentMonth ? $currentDate->copy()->setDay($dayNumber)->format('Y-m-d') : null;
                                                $isToday = $dateStr === $today;
                                                $dayEvents = $isCurrentMonth ? $monthEvents->filter(function($event) use ($dateStr) {
                                                    return $event->start->format('Y-m-d') === $dateStr;
                                                }) : collect();
                                            @endphp

                                            <td class="calendar-day {{ $isToday ? 'today' : '' }} {{ !$isCurrentMonth ? 'other-month' : '' }}"
                                                style="height: 120px; vertical-align: top; position: relative;">
                                                @if($isCurrentMonth)
                                                    <div class="day-number {{ $isToday ? 'text-white bg-primary rounded-circle d-inline-block px-2 py-1' : 'fw-bold' }}">
                                                        {{ $dayNumber }}
                                                    </div>
                                                    <div class="day-events mt-2">
                                                        @foreach($dayEvents as $event)
                                                            <div class="event-item small text-truncate mb-1 p-1 rounded position-relative"
                                                                 style="background: #e3f2fd; border-left: 3px solid #2196f3; cursor: pointer;"
                                                                 data-event-id="{{ $event->id }}"
                                                                 data-event-title="{{ $event->title }}"
                                                                 title="{{ $event->title }} ({{ $event->start->format('g:i A') }})">
                                                                <strong>{{ $event->start->format('g:i') }}</strong>
                                                                {{ Str::limit($event->title, 15) }}
                                                            </div>
                                                        @endforeach
                                                        @if($dayEvents->count() === 0)
                                                            <small class="text-muted">No events</small>
                                                        @endif
                                                    </div>
                                                @else
                                                    &nbsp;
                                                @endif
                                            </td>
                                        @endfor
                                    </tr>
                                    @if($dayNumber >= $daysInMonth) @break @endif
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Section -->
        <div class="col-lg-4">
            <!-- Today's Events -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-calendar-day me-2"></i>Today's Events
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($todayEvents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="small">Time</th>
                                        <th class="small">Meeting</th>
                                        <th class="small">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayEvents as $event)
                                        <tr>
                                            <td class="small">
                                                <strong>{{ $event->start->format('g:i A') }}</strong>
                                                @if($event->end)
                                                    <br><small class="text-muted">to {{ $event->end->format('g:i A') }}</small>
                                                @endif
                                            </td>
                                            <td class="small">
                                                <strong>{{ $event->title }}</strong>
                                                @if($event->location)
                                                    <br><small class="text-muted">{{ $event->location }}</small>
                                                @endif
                                                @if($event->description)
                                                    <br><small class="text-muted">{{ Str::limit($event->description, 30) }}</small>
                                                @endif
                                            </td>
                                            <td class="small">
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary btn-sm simple-edit-btn"
                                                            data-event-id="{{ $event->id }}"
                                                            title="Edit Meeting">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <a href="{{ route('calendar.events.delete', $event->id) }}"
                                                       class="btn btn-outline-danger btn-sm"
                                                       onclick="return confirm('Are you sure you want to delete \\'{{ $event->title }}\\'?')"
                                                       title="Delete Meeting">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-calendar-x display-6"></i>
                            <p class="mt-2 mb-0">No events scheduled for today</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upcoming Meetings Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-clock me-2"></i>Upcoming Meetings
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($upcomingEvents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="small">Date</th>
                                        <th class="small">Time</th>
                                        <th class="small">Meeting</th>
                                        <th class="small">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingEvents as $event)
                                        <tr>
                                            <td class="small">
                                                <strong>{{ $event->start->format('M j') }}</strong>
                                                <br><small class="text-muted">{{ $event->start->format('D') }}</small>
                                            </td>
                                            <td class="small">
                                                <strong>{{ $event->start->format('g:i A') }}</strong>
                                                @if($event->end)
                                                    <br><small class="text-muted">to {{ $event->end->format('g:i A') }}</small>
                                                @endif
                                            </td>
                                            <td class="small">
                                                <strong>{{ Str::limit($event->title, 25) }}</strong>
                                                @if($event->location)
                                                    <br><small class="text-muted">{{ $event->location }}</small>
                                                @endif
                                            </td>
                                            <td class="small">
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary btn-sm simple-edit-btn"
                                                            data-event-id="{{ $event->id }}"
                                                            title="Edit Meeting">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <a href="{{ route('calendar.events.delete', $event->id) }}"
                                                       class="btn btn-outline-danger btn-sm"
                                                       onclick="return confirm('Are you sure you want to delete \\'{{ $event->title }}\\'?')"
                                                       title="Delete Meeting">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-calendar-check display-6"></i>
                            <p class="mt-2 mb-0">No upcoming meetings</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Meeting</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('calendar.store') }}" method="POST" id="eventForm">
                @csrf
                <input type="hidden" name="event_id" id="eventId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Meeting Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" id="eventTitle" placeholder="Enter meeting title" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="start_date" id="eventDate" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Start Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="start_time" id="startTime" value="09:00" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">End Time</label>
                                <input type="time" class="form-control" name="end_time" id="endTime" value="10:00">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" name="location" id="eventLocation" placeholder="e.g., Conference Room, Zoom Meeting">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="eventDescription" rows="3" placeholder="Meeting agenda or details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveButton">Save Meeting</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Event Actions Modal -->
<div class="modal fade" id="eventActionsModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Meeting Actions</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <h6 id="eventActionTitle" class="mb-3"></h6>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary btn-sm" id="modalEditBtn">
                        <i class="bi bi-pencil me-1"></i>Edit Meeting
                    </button>
                    <a href="#" class="btn btn-danger btn-sm" id="deleteEventLink">
                        <i class="bi bi-trash me-1"></i>Delete Meeting
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- INLINE JAVASCRIPT -->
<script>
// Wait for Bootstrap to load
function waitForBootstrap(callback) {
    if (typeof bootstrap !== 'undefined') {
        callback();
    } else {
        setTimeout(() => waitForBootstrap(callback), 100);
    }
}

let currentEventId = null;

// Simple and reliable edit function
function editEvent(eventId) {
    // Wait for Bootstrap before showing modal
    waitForBootstrap(() => {
        // Show loading state
        document.getElementById('eventTitle').value = 'Loading...';

        // Close actions modal if open
        try {
            const actionsModal = bootstrap.Modal.getInstance(document.getElementById('eventActionsModal'));
            if (actionsModal) {
                actionsModal.hide();
            }
        } catch (e) {
            // No actions modal to close
        }

        // Fetch event data
        fetch(`/calendar/events/${eventId}/edit`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(event => {
                // Fill form with event data
                try {
                    document.getElementById('eventId').value = event.id;
                    document.getElementById('eventTitle').value = event.title;
                    document.getElementById('eventDate').value = event.start_date;
                    document.getElementById('startTime').value = event.start_time;
                    document.getElementById('endTime').value = event.end_time || '';
                    document.getElementById('eventLocation').value = event.location || '';
                    document.getElementById('eventDescription').value = event.description || '';

                    // Update form for editing
                    const form = document.getElementById('eventForm');
                    form.action = `/calendar/events/${event.id}`;

                    // Remove any existing method input
                    const existingMethod = form.querySelector('input[name="_method"]');
                    if (existingMethod) {
                        existingMethod.remove();
                    }

                    // Add PUT method for update
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PUT';
                    form.appendChild(methodInput);

                    // Update UI text
                    document.getElementById('saveButton').textContent = 'Update Meeting';
                    document.getElementById('modalTitle').textContent = 'Edit Meeting';

                    // Show the modal
                    const modal = new bootstrap.Modal(document.getElementById('addEventModal'));
                    modal.show();
                } catch (error) {
                    alert('Error setting up edit form: ' + error.message);
                }
            })
            .catch(error => {
                alert('Error loading meeting details. Please try again.');
            });
    });
}

// Open event actions modal
function openEventActions(eventId, eventTitle) {
    waitForBootstrap(() => {
        currentEventId = eventId;
        document.getElementById('eventActionTitle').textContent = eventTitle;

        // Update delete link
        const deleteLink = document.getElementById('deleteEventLink');
        deleteLink.href = `/calendar/events/${eventId}/delete`;
        deleteLink.onclick = function() {
            return confirm(`Are you sure you want to delete "${eventTitle}"?`);
        };

        const modal = new bootstrap.Modal(document.getElementById('eventActionsModal'));
        modal.show();
    });
}

// Reset form for new events
function resetFormForNewEvent() {
    document.getElementById('eventForm').reset();
    document.getElementById('eventForm').action = '{{ route("calendar.store") }}';
    document.getElementById('saveButton').textContent = 'Save Meeting';
    document.getElementById('modalTitle').textContent = 'Add New Meeting';
    document.getElementById('eventId').value = '';

    // Remove method input
    const methodInput = document.querySelector('input[name="_method"]');
    if (methodInput) {
        methodInput.remove();
    }

    // Set default values
    document.getElementById('eventDate').value = '{{ date('Y-m-d') }}';
    document.getElementById('startTime').value = '09:00';
    document.getElementById('endTime').value = '10:00';
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Wait for Bootstrap before setting up event listeners
    waitForBootstrap(() => {
        // Add click listeners to all simple edit buttons
        const editButtons = document.querySelectorAll('.simple-edit-btn');

        editButtons.forEach((button) => {
            button.addEventListener('click', function() {
                const eventId = this.getAttribute('data-event-id');
                editEvent(eventId);
            });
        });

        // Handle edit button in actions modal
        document.getElementById('modalEditBtn').addEventListener('click', function() {
            if (currentEventId) {
                editEvent(currentEventId);
            }
        });

        // Reset form when Add Meeting button is clicked
        const addButton = document.querySelector('[data-bs-target="#addEventModal"]');
        if (addButton) {
            addButton.addEventListener('click', resetFormForNewEvent);
        }

        // Add click listeners to calendar event items
        document.querySelectorAll('.event-item').forEach(item => {
            item.addEventListener('click', function() {
                const eventId = this.getAttribute('data-event-id');
                const eventTitle = this.getAttribute('data-event-title');
                openEventActions(eventId, eventTitle);
            });
        });
    });
});
</script>

<style>
.calendar-table {
    background: white;
    border-radius: 8px;
    overflow: hidden;
}

.calendar-grid {
    margin-bottom: 0;
}

.calendar-day {
    cursor: pointer;
    transition: background-color 0.2s;
}

.calendar-day:hover {
    background-color: #f8f9fa !important;
}

.calendar-day.today {
    background-color: #e7f3ff !important;
}

.calendar-day.today .day-number {
    background-color: #007bff !important;
    color: white !important;
}

.calendar-day.other-month {
    background-color: #f8f9fa;
    color: #6c757d;
}

.day-number {
    font-size: 0.9rem;
    font-weight: 600;
}

.event-item {
    font-size: 0.75rem;
    line-height: 1.2;
    transition: all 0.2s;
}

.event-item:hover {
    background: #bbdefb !important;
    transform: translateX(2px);
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.85rem;
}

.table-responsive {
    border-radius: 8px;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.btn-group-sm > a.btn {
    text-decoration: none;
    display: inline-block;
}
</style>
@endsection
