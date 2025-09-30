<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-green-900">Donors</h1>
    </x-slot>

    <div class="container mt-4">
        <a href="{{ route('donors.create') }}" class="bg-green-700 text-white px-4 py-2 rounded mb-4 inline-block hover:bg-green-800">
            Add Donor
        </a>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <table class="table-auto w-full border border-gray-300">
            <thead>
                <tr class="bg-green-200 text-green-900">
                    <th class="px-4 py-2 border">Name</th>
                    <th class="px-4 py-2 border">Email</th>
                    <th class="px-4 py-2 border">Phone</th>
                    <th class="px-4 py-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($donors as $donor)
                    <tr class="text-green-900">
                        <td class="px-4 py-2 border">{{ $donor->name }}</td>
                        <td class="px-4 py-2 border">{{ $donor->email }}</td>
                        <td class="px-4 py-2 border">{{ $donor->phone }}</td>
                        <td class="px-4 py-2 border space-x-2">
                            <a href="{{ route('donors.edit', $donor) }}" class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600">Edit</a>

                            <form action="{{ route('donors.destroy', $donor) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600" onclick="return confirm('Delete this donor?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
