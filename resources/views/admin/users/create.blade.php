<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-green-900 leading-tight tracking-wide">
            Create New User
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg sm:rounded-xl p-8 border border-gray-200">

                <!-- Success Message -->
                @if(session('success'))
                    <div id="successMessage" class="bg-green-100 text-green-800 p-4 rounded mb-6 shadow-inner">
                        {{ session('success') }}
                    </div>
                    <script>
                        setTimeout(() => {
                            const msg = document.getElementById('successMessage');
                            if(msg){
                                msg.classList.add('opacity-0', 'transition', 'duration-700');
                                setTimeout(() => msg.remove(), 700);
                            }
                        }, 4000);
                    </script>
                @endif

                <!-- Errors -->
                @if($errors->any())
                    <div class="bg-red-100 text-red-800 p-4 rounded mb-6 shadow-inner">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div class="relative">
                            <label class="block text-green-900 font-medium mb-1">Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300"
                                   placeholder="Enter user name">
                            @error('name')
                                <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="relative">
                            <label class="block text-green-900 font-medium mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300"
                                   placeholder="Enter email address">
                            @error('email')
                                <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="relative">
                            <label class="block text-green-900 font-medium mb-1">Password</label>
                            <input type="password" name="password" required
                                   class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300"
                                   placeholder="Enter password">
                            @error('password')
                                <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Role -->
                        <div class="relative">
                            <label class="block text-green-900 font-medium mb-1">Role</label>
                            <select name="role" required
                                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300">
                                <option value="">Select Role</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                            </select>
                            @error('role')
                                <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="flex gap-4 pt-6">
                        <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg shadow-lg transform transition duration-300 hover:scale-105 hover:shadow-2xl">
                            Create User
                        </button>
                        <a href="{{ route('dashboard') }}"
                           class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg shadow-lg transform transition duration-300 hover:scale-105 hover:shadow-2xl">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
