<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-green-900">Edit Donor</h1>
    </x-slot>

    <div class="container mt-4">
        <form action="{{ route('donors.update', $donor) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block font-medium text-green-900 mb-1">Name</label>
                <input type="text" name="name" value="{{ $donor->name }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <div>
                <label for="email" class="block font-medium text-green-900 mb-1">Email</label>
                <input type="email" name="email" value="{{ $donor->email }}" class="w-full border border-gray-300 rounded px-3 py-2">
            </div>

            <div>
                <label for="phone" class="block font-medium text-green-900 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ $donor->phone }}" class="w-full border border-gray-300 rounded px-3 py-2">
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800">Update Donor</button>
                <a href="{{ route('donors.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
