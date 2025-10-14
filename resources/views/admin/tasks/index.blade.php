<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-green-900">Admin Tasks</h1>
    </x-slot>

    <div class="py-10 max-w-6xl mx-auto px-6 space-y-6">
        <!-- ✅ Add Task Button -->
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-green-800">Tasks</h2>
            <a href="{{ route('admin.tasks.create') }}"
               class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
               Assign New Task
            </a>
        </div>

        <!-- ✅ Success Message -->
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- ✅ Tasks Table -->
        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-green-100 text-green-900">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-semibold">#</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold">Title</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold">Status</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold">Due Date</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold">Assigned To</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($tasks as $index => $task)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $task->title }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ ucfirst($task->status) }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">
                                {{ $task->due_date ? $task->due_date->format('Y-m-d') : 'N/A' }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-700">
                                {{ optional($task->assigned_user)->name ?? 'N/A' }}
                                <span class="ml-1 px-1 py-0.5 text-xs rounded-full {{ $task->assigned_type === 'admin' ? 'bg-green-700 text-white' : 'bg-blue-600 text-white' }}">
                                    {{ $task->assigned_type }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm space-x-2">
                                <a href="{{ route('admin.tasks.edit', $task->id) }}"
                                   class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition">
                                   Edit
                                </a>
                                <form action="{{ route('admin.tasks.destroy', $task->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 transition"
                                        onclick="return confirm('Are you sure you want to delete this task?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-gray-500">No tasks found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
