<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-green-900 leading-tight tracking-wide">
            All Employees
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Header with Button -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-semibold text-green-900">All Employees</h1>
                    <p class="text-gray-600 mt-1">Manage staff and admin users</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.tasks.create') }}"
                       class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg shadow-lg transform transition duration-300 hover:scale-105">
                        Assign New Task
                    </a>
                    <a href="{{ route('admin.users.create') }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-lg transform transition duration-300 hover:scale-105">
                        Create New User
                    </a>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="bg-white p-6 rounded-lg shadow-sm mb-6 border border-gray-200">
                <form method="GET" action="{{ route('admin.staff.list') }}">
                    <div class="flex gap-4">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search employees by name or email..."
                               class="flex-1 border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg shadow-lg transform transition duration-300 hover:scale-105">
                            Search
                        </button>
                        @if(request('search'))
                            <a href="{{ route('admin.staff.list') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg shadow-lg transform transition duration-300 hover:scale-105">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Employees List -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
                @if($allUsers->count() > 0)
                    <div class="grid gap-6 p-6">
                        @foreach($allUsers as $user)
                            <div class="border border-gray-200 rounded-lg p-6 hover:bg-gray-50 transition duration-300">
                                <div class="flex justify-between items-center">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h3 class="font-semibold text-lg text-gray-900">{{ $user->name }}</h3>
                                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                                {{ $user->user_type === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ $user->role_display }}
                                            </span>
                                        </div>
                                        <p class="text-gray-600">{{ $user->email }}</p>
                                        <div class="flex gap-6 mt-3 text-sm text-gray-500">
                                            <span class="capitalize px-3 py-1 rounded-full {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $user->status ?? 'active' }}
                                            </span>
                                            @if($user->user_type === 'staff')
                                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
                                                    {{ $user->tasks->count() }} tasks assigned
                                                </span>
                                            @else
                                                <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full">
                                                    Administrator
                                                </span>
                                            @endif
                                            <span class="text-gray-500">
                                                Created: {{ $user->created_at->format('M d, Y') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex gap-3">
                                        @if($user->user_type === 'staff')
                                            <a href="{{ route('admin.tasks.create', $user->id) }}"
                                               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow transform transition duration-300 hover:scale-105 text-sm">
                                                Assign Task
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center text-gray-500">
                        @if(request('search'))
                            <p class="text-lg font-medium">No employees found matching "{{ request('search') }}"</p>
                            <p class="text-sm mt-2">Try adjusting your search terms</p>
                        @else
                            <p class="text-lg font-medium">No employees found</p>
                            <p class="text-sm mt-2">Create user accounts to get started</p>
                            <a href="{{ route('admin.users.create') }}" class="inline-block mt-4 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg shadow transform transition duration-300 hover:scale-105">
                                Create User Account
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
