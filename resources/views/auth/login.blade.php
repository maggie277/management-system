<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTPD Portal Login</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-green-50 flex items-center justify-center min-h-screen">

<div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-md border border-green-100">
    <h2 class="text-2xl font-bold text-green-800 mb-6 text-center">CTPD Portal Login</h2>

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-green-900 mb-1">Email</label>
            <input type="email" name="email" required
                   class="w-full border border-green-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 outline-none"
                   value="{{ old('email') }}">
        </div>

        <div>
            <label class="block text-sm font-medium text-green-900 mb-1">Password</label>
            <input type="password" name="password" required
                   class="w-full border border-green-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-green-900 mb-1">Role</label>
            <select name="role" required class="w-full border border-green-300 rounded-lg px-3 py-2">
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
            </select>
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
