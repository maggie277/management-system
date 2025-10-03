<x-staff-layout>
    <x-slot name="header">Add Task</x-slot>

    <div class="mb-4">
        <h2 class="text-xl font-semibold">Add Task</h2>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('staff.tasks.store') }}" method="POST" class="bg-white p-6 shadow rounded">
        @csrf

        <div class="mb-4">
            <label for="title" class="block text-gray-700 font-medium mb-2">Title</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}"
                   class="w-full border-gray-300 rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label for="status" class="block text-gray-700 font-medium mb-2">Status</label>
            <select name="status" id="status" class="w-full border-gray-300 rounded px-3 py-2">
                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="due_date" class="block text-gray-700 font-medium mb-2">Due Date</label>
            <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}"
                   class="w-full border-gray-300 rounded px-3 py-2">
        </div>

        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Save Task
        </button>
    </form>
</x-staff-layout>
