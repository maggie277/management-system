<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>CTPD - Staff Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-green-50 font-sans antialiased">

<div class="flex min-h-screen">
    <aside class="w-64 bg-green-50 shadow-md p-4">
        <nav class="space-y-2">
            <a href="{{ route('staff.dashboard') }}" class="block px-4 py-2 bg-green-200 text-green-900 rounded hover:bg-green-300">Dashboard</a>
            <a href="{{ route('staff.tasks.index') }}" class="block px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800">Tasks</a>
        </nav>
    </aside>

    <main class="flex-1 p-6">
        @isset($header)
            <div class="mb-6 text-2xl font-semibold text-green-900">{{ $header }}</div>
        @endisset

        <div class="bg-white shadow-sm rounded-lg p-6">
            {{ $slot }}
        </div>
    </main>
</div>
</body>
</html>
