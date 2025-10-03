<x-staff-layout>
    <x-slot name="header">Edit Task</x-slot>

    <form action="{{ route('staff.tasks.update', $task->id) }}" method="POST" class="bg-white p-6 shadow rounded">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="title" class="block text-gray-700 font-medium mb-2">Title</label>
            <input type="text" name="title" id="title" value="{{ old('title', $task->title) }}"
                   class="w-full border-gray-300 rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label for="status" class="block text-gray-700 font-medium mb-2">Status</label>
            <select name="status" id="status" class="w-full border-gray-300 rounded px-3 py-2">
                <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="due_date" class="block text-gray-700 font-medium mb-2">Due Date</label>
            <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $task->due_date) }}"
                   class="w-full border-gray-300 rounded px-3 py-2">
        </div>

        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Update Task
        </button>
    </form>
</x-staff-layout>
