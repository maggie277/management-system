<x-staff-layout>
    <x-slot name="header">
        Pending Tasks
    </x-slot>

    <div class="bg-white p-6 shadow rounded">
        @if($tasks->isEmpty())
            <p class="text-gray-600">No pending tasks found.</p>
        @else
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-4 py-2">Title</th>
                        <th class="border border-gray-300 px-4 py-2">Description</th>
                        <th class="border border-gray-300 px-4 py-2">Due Date</th>
                        <th class="border border-gray-300 px-4 py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">{{ $task->title }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $task->description }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $task->due_date }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ ucfirst($task->status) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-staff-layout>
