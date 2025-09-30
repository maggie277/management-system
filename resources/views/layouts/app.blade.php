<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CTPD</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white">

    <!-- Top Navbar -->
    <nav class="bg-green-700 shadow-md px-6 py-4 flex justify-between items-center text-white">
        <div>
            <a href="{{ route('dashboard') }}" class="text-xl font-bold">CTPD</a>
        </div>
        <div class="flex items-center space-x-4">
            <span>{{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
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
                <a href="{{ route('dashboard') }}"
                   class="block px-4 py-2 bg-green-200 text-green-900 rounded hover:bg-green-300">
                    Dashboard
                </a>
                <a href="{{ route('documents.index') }}"
                   class="block px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800">
                    Documents
                </a>
                <a href="{{ route('assets.index') }}"
                   class="block px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800">
                    Assets
                </a>
                <a href="{{ route('donors.index') }}"
                   class="block px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800">
                    Donors
                </a>
                <a href="{{ route('budgets.index') }}"
                   class="block px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800">
                    Budgets
                </a>
                <a href="{{ route('expenses.index') }}"
                   class="block px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800">
                    Expenses
                </a>

                <!-- Add more sidebar links here -->
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 bg-green-50">
            @isset($header)
                <div class="mb-6 text-2xl font-semibold text-green-900">
                    {{ $header }}
                </div>
            @endisset

            <div class="bg-white shadow-sm rounded-lg p-6">
                {{ $slot }}
            </div>
        </main>

    </div>
</body>
</html>
