<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-green-900">Edit Task</h1>
    </x-slot>

    <div class="py-10 max-w-3xl mx-auto px-6 space-y-6">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="p-3 bg-green-100 text-green-700 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-3 bg-red-100 text-red-700 rounded mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Edit Task Form -->
        <form method="POST" action="{{ route('admin.tasks.update', $task->id) }}" class="bg-white shadow rounded-xl p-6 space-y-4">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div>
                <label class="block text-green-800 font-medium mb-1">Title</label>
                <input type="text" name="title" value="{{ old('title', $task->title) }}"
                    class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-green-800 font-medium mb-1">Description</label>
                <textarea name="description" rows="3"
                    class="w-full border border-gray-300 rounded px-3 py-2">{{ old('description', $task->description) }}</textarea>
            </div>

            <!-- Due Date -->
            <div>
                <label class="block text-green-800 font-medium mb-1">Due Date</label>
                <input type="date" name="due_date" value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2">
            </div>

            <!-- Status -->
            <div>
                <label class="block text-green-800 font-medium mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="pending" {{ old('status', $task->status)=='pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ old('status', $task->status)=='completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            <!-- Assigned User -->
            <div>
                <label class="block text-green-800 font-medium mb-1">Assign To</label>
                <select name="assigned_to" class="w-full border border-gray-300 rounded px-3 py-2">
                    <optgroup label="Admins">
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}"
                                {{ $task->assigned_type=='admin' && $task->assigned_to==$admin->id ? 'selected' : '' }}>
                                {{ $admin->name }}
                            </option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Staff">
                        @foreach($staff as $s)
                            <option value="{{ $s->id }}"
                                {{ $task->assigned_type=='staff' && $task->assigned_to==$s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>
            </div>

            <!-- Assigned Type -->
            <input type="hidden" name="assigned_type" value="{{ $task->assigned_type }}">

            <!-- Submit -->
            <button type="submit"
                class="w-full bg-green-700 text-white py-2 rounded hover:bg-green-800 transition">
                Update Task
            </button>
        </form>
    </div>
</x-app-layout>
