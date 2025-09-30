<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-green-900 leading-tight">
            All Assets
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if(session('success'))
                    <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if($assets->isEmpty())
                    <p class="text-gray-700">No assets added yet.</p>
                @else
                    <table class="min-w-full border border-gray-300">
                        <thead>
                        <tr class="bg-green-100 text-green-900 font-semibold">
                            <th class="p-2 border">Name</th>
                            <th class="p-2 border">Category</th>
                            <th class="p-2 border">Serial</th>
                            <th class="p-2 border">Location</th>
                            <th class="p-2 border">Value</th>
                            <th class="p-2 border">Status</th>
                            <th class="p-2 border">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($assets as $asset)
                            <tr class="text-gray-700">
                                <td class="p-2 border">{{ $asset->name }}</td>
                                <td class="p-2 border">{{ $asset->category }}</td>
                                <td class="p-2 border">{{ $asset->serial_number }}</td>
                                <td class="p-2 border">{{ $asset->location }}</td>
                                <td class="p-2 border">{{ $asset->value }}</td>
                                <td class="p-2 border">{{ ucfirst($asset->status) }}</td>
                                <td class="p-2 border flex space-x-2">
                                    <a href="{{ route('assets.edit', $asset->id) }}"
                                       class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded">
                                       Edit
                                    </a>
                                    <form method="POST" action="{{ route('assets.destroy', $asset->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Are you sure?')"
                                                class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
