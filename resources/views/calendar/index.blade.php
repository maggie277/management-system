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
                <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="card-title mb-0 text-success">
                        <i class="bi bi-calendar-check me-2"></i>Meeting Calendar
                    </h5>
                    <div class="d-flex gap-2 mt-2 mt-sm-0">
                        <!-- Month Selector Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-outline-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-calendar-month me-1"></i>Jump to Month
                            </button>
                            <div class="dropdown-menu p-2" style="min-width: 250px;">
                                <div class="row g-1">
                                    @php
                                        $months = [
                                            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                                            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                                            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                                        ];
                                        $currentYear = $year ?? date('Y');
                                    @endphp
                                    @for($y = $currentYear - 2; $y <= $currentYear + 2; $y++)
                                        <div class="col-12 mb-2">
                                            <strong class="text-success">{{ $y }}</strong>
                                        </div>
                                        @foreach($months as $monthNum => $monthName)
                                            <div class="col-4">
                                                <a href="{{ route('calendar.index', ['year' => $y, 'month' => $monthNum]) }}"
                                                   class="btn btn-sm w-100 mb-1 {{ ($year == $y && $month == $monthNum) ? 'btn-success' : 'btn-outline-secondary' }}"
                                                   style="font-size: 0.75rem;">
                                                    {{ $monthName }}
                                                </a>
                                            </div>
                                        @endforeach
                                        @if($y < $currentYear + 2)<div class="col-12"><hr class="my-1"></div>@endif
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <!-- Year Navigation -->
                        <div class="btn-group">
                            <a href="{{ route('calendar.index', ['year' => $year - 1, 'month' => $month]) }}"
                               class="btn btn-outline-success btn-sm">
                                <i class="bi bi-chevron-double-left"></i> {{ $year - 1 }}
                            </a>
                            <a href="{{ route('calendar.index', ['year' => $year + 1, 'month' => $month]) }}"
                               class="btn btn-outline-success btn-sm">
                                {{ $year + 1 }} <i class="bi bi-chevron-double-right"></i>
                            </a>
                        </div>

                        <!-- Month Navigation - Using prevDate and nextDate from controller -->
                        <div class="btn-group">
                            <a href="{{ route('calendar.index', ['year' => $prevDate->year, 'month' => $prevDate->month]) }}"
                               class="btn btn-outline-success btn-sm">
                                <i class="bi bi-chevron-left"></i> Previous
                            </a>
                            <a href="{{ route('calendar.index') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-calendar3"></i> Today
                            </a>
                            <a href="{{ route('calendar.index', ['year' => $nextDate->year, 'month' => $nextDate->month]) }}"
                               class="btn btn-outline-success btn-sm">
                                Next <i class="bi bi-chevron-right"></i>
                            </a>
                        </div>

                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addEventModal">
                            <i class="bi bi-plus-circle me-1"></i>Add Meeting
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Current Month/Year Display -->
                    <div class="row mb-4">
                        <div class="col text-center">
                            <h3 class="text-success mb-0">
                                {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}
                            </h3>
                        </div>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="calendar-table">
                        <table class="table table-bordered calendar-grid">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center p-3" style="width: 14.28%">Sun</th>
                                    <th class="text-center p-3" style="width: 14.28%">Mon</th>
                                    <th class="text-center p-3" style="width: 14.28%">Tue</th>
                                    <th class="text-center p-3" style="width: 14.28%">Wed</th>
                                    <th class="text-center p-3" style="width: 14.28%">Thu</th>
                                    <th class="text-center p-3" style="width: 14.28%">Fri</th>
                                    <th class="text-center p-3" style="width: 14.28%">Sat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $currentDateObj = \Carbon\Carbon::createFromDate($year, $month, 1);
                                    $firstDayOfMonth = $currentDateObj->copy()->startOfMonth();
                                    $startDayOfWeek = $firstDayOfMonth->dayOfWeek; // 0 = Sunday
                                    $daysInMonth = $currentDateObj->daysInMonth;
                                    $today = now()->format('Y-m-d');
                                    $prevMonthDays = $currentDateObj->copy()->subMonth()->daysInMonth;
                                @endphp

                                @for($week = 0; $week < 6; $week++)
                                    <tr>
                                        @for($weekday = 0; $weekday < 7; $weekday++)
                                            @php
                                                $dayNumber = ($week * 7) + $weekday - $startDayOfWeek + 1;
                                                $isCurrentMonth = $dayNumber >= 1 && $dayNumber <= $daysInMonth;

                                                // For previous month days
                                                if(!$isCurrentMonth && $dayNumber < 1) {
                                                    $displayDay = $prevMonthDays + $dayNumber;
                                                    $dateStr = $currentDateObj->copy()->subMonth()->setDay($displayDay)->format('Y-m-d');
                                                }
                                                // For next month days
                                                elseif(!$isCurrentMonth && $dayNumber > $daysInMonth) {
                                                    $displayDay = $dayNumber - $daysInMonth;
                                                    $dateStr = $currentDateObj->copy()->addMonth()->setDay($displayDay)->format('Y-m-d');
                                                }
                                                // Current month
                                                else {
                                                    $displayDay = $dayNumber;
                                                    $dateStr = $currentDateObj->copy()->setDay($dayNumber)->format('Y-m-d');
                                                }

                                                $isToday = $dateStr === $today;
                                                $dayEvents = $isCurrentMonth ? $monthEvents->filter(function($event) use ($dateStr) {
                                                    return $event->start->format('Y-m-d') === $dateStr;
                                                }) : collect();
                                            @endphp

                                            <td class="calendar-day {{ $isToday ? 'today' : '' }} {{ !$isCurrentMonth ? 'other-month' : '' }}"
                                                style="height: 120px; vertical-align: top; position: relative;"
                                                data-date="{{ $dateStr }}">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="day-number {{ $isToday ? 'today-number' : '' }}">
                                                        {{ $displayDay }}
                                                    </div>
                                                    @if($isCurrentMonth && $dayEvents->count() > 0)
                                                        <small class="text-muted">{{ $dayEvents->count() }} events</small>
                                                    @endif
                                                </div>
                                                <div class="day-events mt-1" style="max-height: 85px; overflow-y: auto;">
                                                    @foreach($dayEvents->take(3) as $event)
                                                        <div class="event-item small text-truncate mb-1 p-1 rounded position-relative"
                                                             style="background: #e8f5e9; border-left: 3px solid #4caf50; cursor: pointer;"
                                                             data-event-id="{{ $event->id }}"
                                                             data-event-title="{{ $event->title }}"
                                                             title="{{ $event->title }} ({{ $event->start->format('g:i A') }})">
                                                            <strong>{{ $event->start->format('g:i') }}</strong>
                                                            {{ Str::limit($event->title, 12) }}
                                                        </div>
                                                    @endforeach
                                                    @if($dayEvents->count() > 3)
                                                        <small class="text-success" style="cursor: pointer;" onclick="viewAllEvents('{{ $dateStr }}')">
                                                            +{{ $dayEvents->count() - 3 }} more...
                                                        </small>
                                                    @endif
                                                    @if($isCurrentMonth && $dayEvents->count() === 0)
                                                        <small class="text-muted">No events</small>
                                                    @endif
                                                </div>
                                                <!-- Quick add button -->
                                                <button class="btn btn-link btn-sm p-0 mt-1 quick-add-btn text-success"
                                                        data-date="{{ $dateStr }}"
                                                        style="font-size: 0.7rem; opacity: 0.6;"
                                                        title="Quick add meeting">
                                                    <i class="bi bi-plus-circle"></i> Add
                                                </button>
                                            </td>
                                        @endfor
                                    </tr>
                                    @if($week * 7 + 7 - $startDayOfWeek >= $daysInMonth)
                                        @break
                                    @endif
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
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 text-success">
                        <i class="bi bi-calendar-day me-2"></i>Today's Events
                    </h6>
                    <span class="badge bg-success">{{ now()->format('M j, Y') }}</span>
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
                                                    <br><small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $event->location }}</small>
                                                @endif
                                            </td>
                                            <td class="small">
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-success btn-sm simple-edit-btn"
                                                            data-event-id="{{ $event->id }}"
                                                            title="Edit Meeting">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <a href="{{ route('calendar.events.delete', $event->id) }}"
                                                       class="btn btn-outline-danger btn-sm"
                                                       onclick="return confirm('Are you sure you want to delete \'{{ $event->title }}\'?')"
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
                    <h6 class="card-title mb-0 text-success">
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
                                                    <br><small class="text-muted"><i class="bi bi-geo-alt"></i> {{ Str::limit($event->location, 20) }}</small>
                                                @endif
                                            </td>
                                            <td class="small">
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-success btn-sm simple-edit-btn"
                                                            data-event-id="{{ $event->id }}"
                                                            title="Edit Meeting">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <a href="{{ route('calendar.events.delete', $event->id) }}"
                                                       class="btn btn-outline-danger btn-sm"
                                                       onclick="return confirm('Are you sure you want to delete \'{{ $event->title }}\'?')"
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
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-success" id="modalTitle">Add New Meeting</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('calendar.store') }}" method="POST" id="eventForm">
                @csrf
                <input type="hidden" name="event_id" id="eventId">
                <div class="modal-body pt-0">
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold">Meeting Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" id="eventTitle" placeholder="Enter meeting title" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-dark fw-semibold">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="start_date" id="eventDate" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label text-dark fw-semibold">Start <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="start_time" id="startTime" value="09:00" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label text-dark fw-semibold">End</label>
                                <input type="time" class="form-control" name="end_time" id="endTime" value="10:00">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold">Location</label>
                        <input type="text" class="form-control" name="location" id="eventLocation" placeholder="e.g., Conference Room, Zoom Meeting">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold">Description</label>
                        <textarea class="form-control" name="description" id="eventDescription" rows="3" placeholder="Meeting agenda or details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm" id="saveButton">Save Meeting</button>
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
                <h6 class="modal-title text-success">Meeting Actions</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <h6 id="eventActionTitle" class="mb-3 text-dark"></h6>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success btn-sm" id="modalEditBtn">
                        <i class="bi bi-pencil me-1"></i>Edit Meeting
                    </button>
                    <a href="#" class="btn btn-outline-danger btn-sm" id="deleteEventLink">
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

// Quick add event for a specific date
function quickAddEvent(date) {
    waitForBootstrap(() => {
        resetFormForNewEvent();
        document.getElementById('eventDate').value = date;
        const modal = new bootstrap.Modal(document.getElementById('addEventModal'));
        modal.show();
    });
}

// View all events for a specific date
function viewAllEvents(date) {
    alert(`View all events for ${date}\nThis feature is coming soon!`);
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
    waitForBootstrap(() => {
        // Add click listeners to all simple edit buttons
        document.querySelectorAll('.simple-edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                editEvent(this.getAttribute('data-event-id'));
            });
        });

        // Handle edit button in actions modal
        document.getElementById('modalEditBtn').addEventListener('click', function() {
            if (currentEventId) {
                editEvent(currentEventId);
            }
        });

        // Reset form when Add Meeting button is clicked
        document.querySelectorAll('[data-bs-target="#addEventModal"]').forEach(btn => {
            btn.addEventListener('click', resetFormForNewEvent);
        });

        // Add click listeners to calendar event items
        document.querySelectorAll('.event-item').forEach(item => {
            item.addEventListener('click', function() {
                openEventActions(this.getAttribute('data-event-id'), this.getAttribute('data-event-title'));
            });
        });

        // Add quick add button listeners
        document.querySelectorAll('.quick-add-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                quickAddEvent(this.getAttribute('data-date'));
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
    background-color: #e8f5e9 !important;
}

.calendar-day.today .today-number {
    background-color: #4caf50 !important;
    color: white !important;
    display: inline-block;
    width: 28px;
    height: 28px;
    line-height: 28px;
    text-align: center;
    border-radius: 50%;
}

.calendar-day.other-month {
    background-color: #f8f9fa;
    color: #6c757d;
}

.day-number {
    font-size: 0.9rem;
    font-weight: 600;
    padding: 2px 6px;
}

.event-item {
    font-size: 0.7rem;
    line-height: 1.2;
    transition: all 0.2s;
}

.event-item:hover {
    background: #c8e6c9 !important;
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

.quick-add-btn {
    opacity: 0.5;
    transition: opacity 0.2s;
}

.quick-add-btn:hover {
    opacity: 1;
}

.dropdown-menu .btn-outline-secondary:hover {
    background-color: #198754 !important;
    color: white !important;
}

/* Green focus ring for form inputs */
.form-control:focus,
.form-select:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}
</style>
@endsection
