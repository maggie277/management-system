<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-green-900">Create New User</h1>
    </x-slot>

    <div class="w-full max-w-md mx-auto bg-white p-6 rounded shadow mt-6">
        @if(session('status'))
            <div class="bg-green-100 text-green-800 p-2 rounded mb-4 text-center">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-2 rounded mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf

            <!-- Name -->
            <div>
                <label class="block text-green-800 font-medium mb-1">Name</label>
                <input type="text" name="name" class="w-full border border-gray-300 rounded px-3 py-2" required value="{{ old('name') }}">
            </div>

            <!-- Email -->
            <div>
                <label class="block text-green-800 font-medium mb-1">Email</label>
                <input type="email" name="email" class="w-full border border-gray-300 rounded px-3 py-2" required value="{{ old('email') }}">
            </div>

            <!-- Password -->
            <div>
                <label class="block text-green-800 font-medium mb-1">Password</label>
                <input type="password" name="password" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <!-- Role -->
            <div>
                <label class="block text-green-800 font-medium mb-1">Role</label>
                <select name="role" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="staff" {{ old('role')=='staff' ? 'selected' : '' }}>Staff</option>
                    <option value="admin" {{ old('role')=='admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            <!-- Submit -->
            <button type="submit" class="w-full bg-green-700 text-white py-2 rounded hover:bg-green-800 transition">
                Create User
            </button>
        </form>
    </div>
</x-app-layout>
