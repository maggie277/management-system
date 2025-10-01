<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-green-50 flex items-center justify-center min-h-screen">

<div class="w-full max-w-sm bg-white p-6 rounded shadow">
    <h1 class="text-xl font-bold text-center mb-4 text-green-900">Staff Portal Login</h1>

    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-2 rounded mb-3">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('staff.login.submit') }}">
        @csrf
        <div class="mb-3">
            <label class="block text-green-800 font-medium mb-1">Email</label>
            <input type="email" name="email" class="w-full border p-2 rounded" required>
        </div>
        <div class="mb-3">
            <label class="block text-green-800 font-medium mb-1">Password</label>
            <input type="password" name="password" class="w-full border p-2 rounded" required>
        </div>
        <button type="submit" class="w-full bg-green-700 text-white py-2 rounded">Login</button>
    </form>
</div>
</body>
</html>
