<x-app-layout>
    <x-slot name="header">
        Add New Staff
    </x-slot>

    <div class="space-y-4">
        <a href="{{ route('admin.staff.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Back to Staff List</a>

        @if($errors->any())
            <div class="bg-red-100 text-red-800 p-3 rounded">
                <ul>
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.staff.store') }}" method="POST" class="bg-white shadow-md rounded p-6 space-y-4">
            @csrf

            <div>
                <label for="name" class="block font-medium text-gray-700">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full border-gray-300 rounded" required>
            </div>

            <div>
                <label for="email" class="block font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full border-gray-300 rounded" required>
            </div>

            <div>
                <label for="password" class="block font-medium text-gray-700">Password</label>
                <input type="password" name="password" class="mt-1 block w-full border-gray-300 rounded" required>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800">Create Staff</button>
                <a href="{{ route('admin.staff.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
