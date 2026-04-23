<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        // Get current date
        $currentDate = now();

        // Handle month navigation
        if ($request->has('month') && $request->month === 'prev') {
            $currentDate = now()->subMonth();
        } elseif ($request->has('month') && $request->month === 'next') {
            $currentDate = now()->addMonth();
        }

        // If specific month/year provided
        if ($request->has('month') && is_numeric($request->month)) {
            $currentDate = now()->setMonth($request->month);
        }
        if ($request->has('year') && is_numeric($request->year)) {
            $currentDate = now()->setYear($request->year);
        }

        $month = $currentDate->month;
        $year = $currentDate->year;

        // Calculate previous and next months
        $prevDate = $currentDate->copy()->subMonth();
        $nextDate = $currentDate->copy()->addMonth();

        // Get events for the current month
        $monthEvents = CalendarEvent::where('user_id', Auth::id())
            ->whereYear('start', $year)
            ->whereMonth('start', $month)
            ->orderBy('start', 'asc')
            ->get();

        // Get upcoming events (next 5)
        $upcomingEvents = CalendarEvent::where('user_id', Auth::id())
            ->where('start', '>=', now())
            ->orderBy('start', 'asc')
            ->take(5)
            ->get();

        // Get today's events
        $todayEvents = CalendarEvent::where('user_id', Auth::id())
            ->whereDate('start', now()->toDateString())
            ->orderBy('start', 'asc')
            ->get();

        return view('calendar.index', compact(
            'monthEvents',
            'upcomingEvents',
            'todayEvents',
            'month',
            'year',
            'prevDate',
            'nextDate',
            'currentDate'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'nullable',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
        ]);

        // Combine date and time
        $startDateTime = $request->start_date . ' ' . $request->start_time . ':00';
        $endDateTime = $request->end_time ? $request->start_date . ' ' . $request->end_time . ':00' : null;

        $event = CalendarEvent::create([
            'title' => $request->title,
            'start' => $startDateTime,
            'end' => $endDateTime,
            'description' => $request->description,
            'location' => $request->location,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('calendar.index')->with('success', 'Meeting added successfully!');
    }

    public function edit(CalendarEvent $event)
    {
        // Check authorization
        if ($event->user_id !== Auth::id() && Auth::user()->role !== 'system_admin') {
            abort(403, 'Unauthorized');
        }

        return response()->json([
            'id' => $event->id,
            'title' => $event->title,
            'start_date' => $event->start->format('Y-m-d'),
            'start_time' => $event->start->format('H:i'),
            'end_time' => $event->end ? $event->end->format('H:i') : null,
            'location' => $event->location,
            'description' => $event->description,
        ]);
    }

    public function update(Request $request, CalendarEvent $event)
    {
        // Check authorization
        if ($event->user_id !== Auth::id() && Auth::user()->role !== 'system_admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'nullable',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
        ]);

        // Combine date and time
        $startDateTime = $request->start_date . ' ' . $request->start_time . ':00';
        $endDateTime = $request->end_time ? $request->start_date . ' ' . $request->end_time . ':00' : null;

        $event->update([
            'title' => $request->title,
            'start' => $startDateTime,
            'end' => $endDateTime,
            'description' => $request->description,
            'location' => $request->location,
        ]);

        return redirect()->route('calendar.index')->with('success', 'Meeting updated successfully!');
    }

    public function destroy(CalendarEvent $event)
    {
        // Check authorization
        if ($event->user_id !== Auth::id() && Auth::user()->role !== 'system_admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $event->delete();

        return redirect()->route('calendar.index')->with('success', 'Meeting deleted successfully!');
    }

    public function getEvents(Request $request)
    {
        try {
            $query = CalendarEvent::where('user_id', Auth::id());

            if ($request->has('date')) {
                $query->whereDate('start', $request->date);
            }

            if ($request->has('year') && $request->has('month')) {
                $query->whereYear('start', $request->year)
                      ->whereMonth('start', $request->month);
            }

            $events = $query->get()->map(function($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->start->toISOString(),
                    'end' => $event->end ? $event->end->toISOString() : null,
                    'description' => $event->description,
                    'location' => $event->location,
                ];
            });

            return response()->json($events);
        } catch (\Exception $e) {
            \Log::error('Error loading calendar events: ' . $e->getMessage());
            return response()->json([]);
        }
    }
}
