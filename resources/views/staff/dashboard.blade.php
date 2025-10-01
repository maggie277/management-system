<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Staff Dashboard - CTPD</title>

    <!-- Tailwind CSS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Or use CDN fallback -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> -->
</head>
<body class="font-sans antialiased bg-white">

    <!-- Top Navbar -->
    <nav class="bg-green-700 shadow-md px-6 py-4 flex justify-between items-center text-white">
        <div>
            <a href="{{ route('staff.dashboard') }}" class="text-xl font-bold">CTPD - Staff</a>
        </div>
        <div class="flex items-center space-x-4">
            <span>{{ $staff->name }}</span>
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
                <a href="{{ route('staff.dashboard') }}"
                   class="block px-4 py-2 bg-green-200 text-green-900 rounded hover:bg-green-300">
                    Dashboard
                </a>
                <a href="#"
                   class="block px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800">
                    Tasks
                </a>
                <a href="#"
                   class="block px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800">
                    Deadlines
                </a>
                <a href="#"
                   class="block px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800">
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
                <div class="bg-white p-4 rounded shadow">
                    <h2 class="font-semibold mb-2">Assigned Tasks</h2>
                    <p>(Tasks placeholder)</p>
                </div>

                <div class="bg-white p-4 rounded shadow">
                    <h2 class="font-semibold mb-2">Upcoming Deadlines</h2>
                    <p>(Deadlines placeholder)</p>
                </div>

                <div class="bg-white p-4 rounded shadow">
                    <h2 class="font-semibold mb-2">Pending Items</h2>
                    <p>(Pending items placeholder)</p>
                </div>
            </div>
        </main>

    </div>

</body>
</html>
