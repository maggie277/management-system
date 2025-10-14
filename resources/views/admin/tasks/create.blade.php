<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-green-900 leading-tight tracking-wide">
            Assign Task
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg border border-red-300">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-200">
                <form action="{{ route('admin.tasks.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Assign To -->
                    <div>
                        <label class="block text-green-900 font-medium mb-2">Assign To</label>
                        <select name="assigned_to" required
                                class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300">
                            <option value="">Select Employee</option>
                            <optgroup label="Staff Members">
                                @foreach($staffMembers as $member)
                                    <option value="{{ $member->id }}"
                                        {{ $selectedStaff && $selectedStaff->id == $member->id ? 'selected' : '' }}>
                                        {{ $member->name }} (Staff)
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Administrators">
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}">
                                        {{ $admin->name }} (Admin)
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <!-- Task Title -->
                    <div>
                        <label class="block text-green-900 font-medium mb-2">Task Title</label>
                        <input type="text" name="title" value="{{ old('title') }}"
                               class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300"
                               placeholder="Enter task title" required>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-green-900 font-medium mb-2">Description</label>
                        <textarea name="description" rows="4"
                                  class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300"
                                  placeholder="Enter task description">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Due Date -->
                        <div>
                            <label class="block text-green-900 font-medium mb-2">Due Date</label>
                            <input type="date" name="due_date" value="{{ old('due_date') }}"
                                   class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300">
                        </div>

                        <!-- Priority -->
                        <div>
                            <label class="block text-green-900 font-medium mb-2">Priority</label>
                            <select name="priority"
                                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300">
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : 'selected' }}>Medium</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                            </select>
                        </div>
                    </div>

                    <!-- Assigned Type -->
                    <div>
                        <label class="block text-green-900 font-medium mb-2">Assign As</label>
                        <select name="assigned_type" required
                                class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300">
                            <option value="staff">Staff Task</option>
                            <option value="admin">Admin Task</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-green-900 font-medium mb-2">Status</label>
                        <select name="status"
                                class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300">
                            <option value="pending" selected>Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-4 pt-6">
                        <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg shadow-lg transform transition duration-300 hover:scale-105 hover:shadow-2xl font-medium">
                            Assign Task
                        </button>
                        <a href="{{ route('admin.staff.list') }}"
                           class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg shadow-lg transform transition duration-300 hover:scale-105 hover:shadow-2xl font-medium">
                            Back to Employees
                        </a>
                        <a href="{{ route('admin.tasks.index') }}"
                           class="bg-blue-500 hover:bg-blue-600 text-white px-8 py-3 rounded-lg shadow-lg transform transition duration-300 hover:scale-105 hover:shadow-2xl font-medium">
                            View All Tasks
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
