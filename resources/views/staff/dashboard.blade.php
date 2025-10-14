<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Staff Dashboard - CTPD</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Fade-in animation for cards */
        @keyframes fade-in {
            0% { opacity: 0; transform: translateY(15px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade { animation: fade-in 0.5s ease-out forwards; }
        .animate-fade.delay-1 { animation-delay: 0.1s; }
        .animate-fade.delay-2 { animation-delay: 0.2s; }
        .animate-fade.delay-3 { animation-delay: 0.3s; }
    </style>
</head>
<body class="font-sans antialiased bg-green-50 flex flex-col min-h-screen">

    <!-- Top Navbar -->
    <nav class="bg-green-700 shadow-md px-6 py-4 flex justify-between items-center text-white">
        <div>
            <a href="{{ route('staff.dashboard') }}" class="text-xl font-bold hover:text-green-100 transition-colors">CTPD - Staff Portal</a>
        </div>
        <div class="flex items-center space-x-4">
            <span class="font-medium">{{ $staff->name }}</span>

            <!-- Logout Form -->

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded shadow transition-transform transform hover:scale-105">
        Logout
    </button>
</form>
        </div>
    </nav>

    <div class="flex flex-1">

        <!-- Sidebar -->
        <aside class="w-64 bg-green-50 shadow-md p-4 rounded-r-xl">
            <nav class="space-y-2">
                <a href="{{ route('staff.dashboard') }}" class="block px-4 py-2 bg-green-200 text-green-900 rounded hover:bg-green-300 transition-all transform hover:scale-105">
                    Dashboard
                </a>
                <a href="{{ route('staff.tasks.index') }}" class="block px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800 transition-all transform hover:scale-105">
                    Tasks
                </a>
                <a href="{{ route('staff.tasks.deadlines') }}" class="block px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800 transition-all transform hover:scale-105">
                    Deadlines
                </a>
                <a href="{{ route('staff.tasks.pending') }}" class="block px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800 transition-all transform hover:scale-105">
                    Pending Items
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 flex flex-col">

            <div class="mb-6 text-2xl font-semibold text-green-900 animate-fade">
                Welcome, {{ $staff->name }}
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 flex-1">

                <!-- Assigned Tasks -->
                <div class="bg-white p-6 rounded-xl shadow hover:shadow-2xl transition-transform transform hover:scale-105 animate-fade delay-1">
                    <h2 class="font-semibold mb-4 text-green-800">Assigned Tasks</h2>
                    @if($assignedTasks->isEmpty())
                        <p class="text-gray-500">No tasks assigned.</p>
                    @else
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($assignedTasks as $task)
                                <li class="hover:text-green-700 transition-colors">
                                    {{ $task->title }}
                                    @if($task->status) <span class="text-sm text-gray-400">({{ $task->status }})</span> @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- Upcoming Deadlines -->
                <div class="bg-white p-6 rounded-xl shadow hover:shadow-2xl transition-transform transform hover:scale-105 animate-fade delay-2">
                    <h2 class="font-semibold mb-4 text-green-800">Upcoming Deadlines</h2>
                    @if($upcomingDeadlines->isEmpty())
                        <p class="text-gray-500">No upcoming deadlines.</p>
                    @else
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($upcomingDeadlines as $task)
                                <li class="hover:text-yellow-700 transition-colors">
                                    {{ $task->title }} – due {{ $task->due_date->format('M d, Y') }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- Pending Items -->
                <div class="bg-white p-6 rounded-xl shadow hover:shadow-2xl transition-transform transform hover:scale-105 animate-fade delay-3">
                    <h2 class="font-semibold mb-4 text-green-800">Pending Items</h2>
                    @if($pendingItems->isEmpty())
                        <p class="text-gray-500">No pending items.</p>
                    @else
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($pendingItems as $task)
                                <li class="hover:text-red-700 transition-colors">{{ $task->title }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>

            </div>

        </main>

    </div>

    <!-- Footer (same as admin) -->
    <footer class="bg-green-700 text-white py-4 text-center rounded-t-xl mt-6 shadow-inner">
        Powered by <strong>Fortress Hub Technologies Limited</strong>
    </footer>

</body>
</html>
