<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-green-900 leading-tight">
            All Documents
        </h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded-lg border-l-4 border-green-500 animate-fade-in">
                {{ session('success') }}
            </div>
        @endif

        <!-- Document Cards / Table -->
        @if($documents->isEmpty())
            <p class="text-green-900 text-center">No documents uploaded yet.</p>
        @else
            <div class="overflow-x-auto bg-white shadow-lg rounded-lg p-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-green-100 text-green-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Uploaded By</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($documents as $doc)
                        <tr class="hover:bg-green-50 transition">
                            <td class="px-6 py-3 text-green-900 font-medium">{{ $doc->title }}</td>
                            <td class="px-6 py-3 text-green-900">{{ $doc->description ?? '-' }}</td>
                            <td class="px-6 py-3 text-green-900">{{ $doc->uploadedBy->name ?? 'N/A' }}</td>
                            <td class="px-6 py-3 space-x-2 flex">
                                <a href="{{ asset('storage/'.$doc->file_path) }}"
                                   target="_blank"
                                   class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg shadow-sm transition">
                                    View
                                </a>
                                <a href="{{ route('documents.edit', $doc->id) }}"
                                   class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded-lg shadow-sm transition">
                                    Edit
                                </a>
                                <form action="{{ route('documents.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg shadow-sm transition">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.5s ease forwards;
        }
    </style>
</x-app-layout>
