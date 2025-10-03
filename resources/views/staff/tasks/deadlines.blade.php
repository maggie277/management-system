<x-staff-layout>
    <x-slot name="header">Upcoming Deadlines</x-slot>

    @if($upcomingDeadlines->isEmpty())
        <p class="text-gray-500">No upcoming deadlines.</p>
    @else
        <ul class="list-disc list-inside">
            @foreach($upcomingDeadlines as $task)
                <li>
                    {{ $task->title }} – due {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}
                    @if($task->status) <span class="text-sm text-gray-500">({{ ucfirst($task->status) }})</span> @endif
                </li>
            @endforeach
        </ul>
    @endif
</x-staff-layout>
