<x-staff-layout>
    <x-slot name="header">Tasks</x-slot>

    <div class="mb-4 flex justify-between items-center">
        <h2 class="text-xl font-semibold">Tasks</h2>
        <a href="{{ route('staff.tasks.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Add Task
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <table class="w-full table-auto bg-white shadow rounded">
        <thead class="bg-green-100">
            <tr>
                <th class="px-4 py-2">Title</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Due Date</th>
                <th class="px-4 py-2">Actions</th> <!-- New column -->
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $task)
            <tr class="border-b">
                <td class="px-4 py-2">{{ $task->title }}</td>
                <td class="px-4 py-2">{{ ucfirst($task->status) }}</td>
                <td class="px-4 py-2">{{ $task->due_date ?? 'N/A' }}</td>
                <td class="px-4 py-2">
                    <a href="{{ route('staff.tasks.edit', $task->id) }}"
                       class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                        Edit
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center py-4 text-gray-500">No tasks found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</x-staff-layout>
