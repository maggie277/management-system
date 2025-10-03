<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Staff Dashboard - CTPD</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white">

    <!-- Top Navbar -->
    <nav class="bg-green-700 shadow-md px-6 py-4 flex justify-between items-center text-white">
        <div>
            <a href="{{ route('staff.dashboard') }}" class="text-xl font-bold">CTPD - Staff Portal</a>
        </div>
        <div class="flex items-center space-x-4">
            <span>{{ $staff->name }}</span>

            <!-- Logout Form (POST only) -->
            <form method="POST" action="{{ route('staff.logout') }}">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                    Logout
                </button>
            </form>
        </div>
    </nav>

    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside class="w-64 bg-green-50 shadow-md p-4">
            <nav class="space-y-2">
                <a href="{{ route('staff.dashboard') }}" class="block px-4 py-2 bg-green-200 text-green-900 rounded hover:bg-green-300">
                    Dashboard
                </a>
                <a href="{{ route('staff.tasks.index') }}" class="block px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800">
                    Tasks
                </a>
                <a href="{{ route('staff.tasks.deadlines') }}" class="block px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800">
                    Deadlines
                </a>
                <a href="{{ route('staff.tasks.pending') }}" class="block px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800">
                    Pending Items
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 bg-green-50">
            <div class="mb-6 text-2xl font-semibold text-green-900">
                Welcome, {{ $staff->name }}
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <!-- Assigned Tasks -->
                <div class="bg-white p-4 rounded shadow">
                    <h2 class="font-semibold mb-2">Assigned Tasks</h2>
                    @if($assignedTasks->isEmpty())
                        <p>No tasks assigned.</p>
                    @else
                        <ul class="list-disc list-inside">
                            @foreach($assignedTasks as $task)
                                <li>
                                    {{ $task->title }}
                                    @if($task->status) <span class="text-sm text-gray-500">({{ $task->status }})</span> @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- Upcoming Deadlines -->
                <div class="bg-white p-4 rounded shadow">
                    <h2 class="font-semibold mb-2">Upcoming Deadlines</h2>
                    @if($upcomingDeadlines->isEmpty())
                        <p>No upcoming deadlines.</p>
                    @else
                        <ul class="list-disc list-inside">
                            @foreach($upcomingDeadlines as $task)
                                <li>
                                    {{ $task->title }} – due {{ $task->due_date->format('M d, Y') }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- Pending Items -->
                <div class="bg-white p-4 rounded shadow">
                    <h2 class="font-semibold mb-2">Pending Items</h2>
                    @if($pendingItems->isEmpty())
                        <p>No pending items.</p>
                    @else
                        <ul class="list-disc list-inside">
                            @foreach($pendingItems as $task)
                                <li>{{ $task->title }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>

            </div>
        </main>

    </div>

</body>
</html>
