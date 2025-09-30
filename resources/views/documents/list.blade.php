<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-green-900 leading-tight">
            All Documents
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto mt-10 p-6 bg-white rounded-lg shadow">
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-2 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($documents->isEmpty())
            <p class="text-green-900">No documents uploaded yet.</p>
        @else
            <table class="min-w-full bg-white border">
                <thead>
                    <tr class="bg-green-100 text-green-900">
                        <th class="py-2 px-4 border-b text-left">Title</th>
                        <th class="py-2 px-4 border-b text-left">Description</th>
                        <th class="py-2 px-4 border-b text-left">Uploaded By</th>
                        <th class="py-2 px-4 border-b text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documents as $doc)
                        <tr class="text-green-900">
                            <td class="py-2 px-4 border-b">{{ $doc->title }}</td>
                            <td class="py-2 px-4 border-b">{{ $doc->description }}</td>
                            <td class="py-2 px-4 border-b">{{ $doc->uploadedBy->name ?? 'N/A' }}</td>
                            <td class="py-2 px-4 border-b flex space-x-2">
                                <a href="{{ asset('storage/'.$doc->file_path) }}"
                                   class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded" target="_blank">
                                    View
                                </a>
                                <a href="{{ route('documents.edit', $doc->id) }}"
                                   class="bg-yellow-600 hover:bg-yellow-700 text-white px-2 py-1 rounded">
                                    Edit
                                </a>
                                <form action="{{ route('documents.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded">
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
</x-app-layout>
