<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTPD Staff Login</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-green-50 flex items-center justify-center min-h-screen">

<div class="w-full max-w-sm bg-white p-6 rounded-xl shadow-md border border-green-100">
    <h1 class="text-xl font-bold text-center mb-4 text-green-900">CTPD Staff Portal Login</h1>

    @if($errors->any())
        <div class="bg-red-100 text-red-800 p-2 rounded mb-3">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('staff.login.submit') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-green-800 font-medium mb-1">Email</label>
            <input type="email" name="email" required
                   class="w-full border border-green-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 outline-none"
                   value="{{ old('email') }}">
        </div>

        <div>
            <label class="block text-green-800 font-medium mb-1">Password</label>
            <input type="password" name="password" required
                   class="w-full border border-green-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 outline-none">
        </div>

        <button type="submit"
                class="w-full bg-green-700 hover:bg-green-800 text-white font-semibold py-2 rounded-lg transition">
            Login
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-green-700">© {{ date('Y') }} CTPD Management System</p>
</div>

</body>
</html>
