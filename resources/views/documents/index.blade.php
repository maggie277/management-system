<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-green-900 leading-tight">
            Documents Dashboard
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Buttons --}}
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('documents.create') }}"
               class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded shadow-md transition transform hover:-translate-y-1 hover:scale-105">
                Upload Document
            </a>

            <a href="{{ route('documents.list') }}"
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow-md transition transform hover:-translate-y-1 hover:scale-105">
                View Documents
            </a>
        </div>

        {{-- Stats Cards --}}
        @php
            $totalDocuments = \App\Models\Document::count();
            $totalPDF = \App\Models\Document::where('file_path', 'like', '%.pdf')->count();
            $totalWord = \App\Models\Document::where('file_path', 'like', '%.doc%')->count();
            $totalExcel = \App\Models\Document::where('file_path', 'like', '%.xlsx')->count();
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Documents Card -->
            <a href="{{ route('documents.list') }}"
               class="block bg-white rounded-lg shadow p-6 border-l-4 border-green-700 transform transition hover:scale-105 hover:shadow-lg cursor-pointer">
                <h3 class="text-lg font-medium text-green-900 mb-2">Total Documents</h3>
                <p class="text-3xl font-bold text-gray-800">{{ $totalDocuments }}</p>
            </a>

            <!-- PDFs Card -->
            <a href="{{ route('documents.list') }}?type=pdf"
               class="block bg-white rounded-lg shadow p-6 border-l-4 border-blue-600 transform transition hover:scale-105 hover:shadow-lg cursor-pointer">
                <h3 class="text-lg font-medium text-green-900 mb-2">PDFs</h3>
                <p class="text-3xl font-bold text-gray-800">{{ $totalPDF }}</p>
            </a>

            <!-- Word Docs Card -->
            <a href="{{ route('documents.list') }}?type=word"
               class="block bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500 transform transition hover:scale-105 hover:shadow-lg cursor-pointer">
                <h3 class="text-lg font-medium text-green-900 mb-2">Word Docs</h3>
                <p class="text-3xl font-bold text-gray-800">{{ $totalWord }}</p>
            </a>

            <!-- Excel Sheets Card -->
            <a href="{{ route('documents.list') }}?type=excel"
               class="block bg-white rounded-lg shadow p-6 border-l-4 border-purple-600 transform transition hover:scale-105 hover:shadow-lg cursor-pointer">
                <h3 class="text-lg font-medium text-green-900 mb-2">Excel Sheets</h3>
                <p class="text-3xl font-bold text-gray-800">{{ $totalExcel }}</p>
            </a>
        </div>

        {{-- Latest Documents Table --}}
        <div class="bg-white shadow rounded-lg p-6 overflow-x-auto mt-6">
            <h3 class="text-xl font-semibold text-green-900 mb-4">Latest Documents</h3>

            @php
                $latestDocs = \App\Models\Document::with('uploadedBy')->latest()->take(5)->get();
            @endphp

            @if($latestDocs->isEmpty())
                <p class="text-gray-500">No documents uploaded yet.</p>
            @else
                <table class="min-w-full border">
                    <thead class="bg-green-100 text-green-900">
                        <tr>
                            <th class="py-2 px-4 border-b text-left">Title</th>
                            <th class="py-2 px-4 border-b text-left">Uploaded By</th>
                            <th class="py-2 px-4 border-b text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($latestDocs as $doc)
                            <tr class="hover:bg-green-50 transition">
                                <td class="py-2 px-4 border-b">{{ $doc->title }}</td>
                                <td class="py-2 px-4 border-b">{{ $doc->uploadedBy->name ?? 'N/A' }}</td>
                                <td class="py-2 px-4 border-b flex space-x-2">
                                    <a href="{{ route('documents.download', $doc->id) }}"
                                       class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-sm">
                                        Download
                                    </a>
                                    <a href="{{ route('documents.edit', $doc->id) }}"
                                       class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded text-sm">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-app-layout>
