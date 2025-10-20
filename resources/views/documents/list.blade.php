<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-green-900 leading-tight">
            @if(isset($currentFolder))
                {{ $currentFolder->name }}
            @else
                Document Manager
            @endif
        </h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded-lg border-l-4 border-green-500 animate-fade-in">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tab Navigation -->
        <div class="bg-white shadow-lg rounded-lg p-4">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('documents.list', ['view' => 'folders']) }}"
                       class="{{ request('view') == 'folders' || !request('view') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                         Folder View
                    </a>
                    <a href="{{ route('documents.list', ['view' => 'all']) }}"
                       class="{{ request('view') == 'all' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                         All Documents
                    </a>
                </nav>
            </div>
        </div>

        @if(request('view') == 'all')
            <!-- ALL DOCUMENTS VIEW -->
            <!-- Controls Section for All Documents -->
            <div class="bg-white shadow-lg rounded-lg p-6 space-y-4">
                <div class="flex flex-col md:flex-row gap-4 justify-between">
                    <!-- Search -->
                    <form method="GET" action="{{ route('documents.list') }}" class="flex-1">
                        <input type="hidden" name="view" value="all">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Search all documents..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </form>

                    <!-- Sort and Actions -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <!-- Sort Dropdown -->
                        <form method="GET" action="{{ route('documents.list') }}" class="flex">
                            <input type="hidden" name="view" value="all">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <select name="sort" onchange="this.form.submit()"
                                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Name A-Z</option>
                                <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Name Z-A</option>
                                <option value="type" {{ request('sort') == 'type' ? 'selected' : '' }}>File Type</option>
                            </select>
                        </form>

                        <!-- Upload Button -->
                        <a href="{{ route('documents.create') }}"
                           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-sm transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Upload
                        </a>
                    </div>
                </div>
            </div>

            <!-- All Documents Content -->
            @if($documents->isEmpty())
                <div class="bg-white shadow-lg rounded-lg p-12 text-center">
                    <svg class="w-20 h-20 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-green-900 text-xl mb-2">No documents found</p>
                    <p class="text-gray-600 mb-6">Try adjusting your search or upload your first document</p>
                    <a href="{{ route('documents.create') }}"
                       class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition flex items-center gap-2 justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Upload Your First Document
                    </a>
                </div>
            @else
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-green-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        All Documents ({{ $documents->count() }})
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-green-100 text-green-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Folder</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Modified</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($documents as $doc)
                                @php
                                    $fileExt = pathinfo($doc->file_path, PATHINFO_EXTENSION);
                                @endphp
                                <tr class="hover:bg-green-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if($fileExt === 'pdf')
                                                <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            @elseif(in_array($fileExt, ['doc', 'docx']))
                                                <svg class="w-6 h-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            @elseif(in_array($fileExt, ['xlsx', 'xls']))
                                                <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            @else
                                                <svg class="w-6 h-6 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            @endif
                                            <span class="text-green-900 font-medium">{{ $doc->title }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded text-sm font-semibold
                                            @if($fileExt === 'pdf') bg-red-100 text-red-800
                                            @elseif(in_array($fileExt, ['doc', 'docx'])) bg-blue-100 text-blue-800
                                            @elseif(in_array($fileExt, ['xlsx', 'xls'])) bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $fileExt }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-green-900">{{ $doc->description ?? '-' }}</td>
                                    <td class="px-6 py-4 text-green-900">
                                        @if($doc->folder)
                                            <a href="{{ route('documents.list', ['view' => 'folders', 'folder_id' => $doc->folder->id]) }}" class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm hover:bg-blue-200 transition">
                                                {{ $doc->folder->name }}
                                            </a>
                                        @else
                                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-sm">Unassigned</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-green-900">{{ $doc->updated_at->format('M j, Y') }}</td>
                                    <td class="px-6 py-4 space-x-2 flex">
                                        <a href="{{ asset('storage/'.$doc->file_path) }}"
                                           target="_blank"
                                           class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg shadow-sm transition">
                                            View
                                        </a>
                                        <a href="{{ route('documents.edit', $doc->id) }}"
                                           class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded-lg shadow-sm transition">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        @else
            <!-- FOLDER VIEW (Default) -->
            @if(isset($currentFolder) && request('folder_id'))
                <!-- FOLDER CONTENTS VIEW - When a specific folder is clicked -->
                <!-- Breadcrumb Navigation -->
                <div class="bg-white shadow-lg rounded-lg p-4">
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-2 text-sm">
                            <li>
                                <a href="{{ route('documents.list', ['view' => 'folders']) }}" class="text-green-600 hover:text-green-800 font-medium flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                    Home
                                </a>
                            </li>
                            @if(isset($currentFolder) && $currentFolder->parent)
                                @php
                                    $parents = [];
                                    $parent = $currentFolder->parent;
                                    while($parent) {
                                        $parents[] = $parent;
                                        $parent = $parent->parent;
                                    }
                                    $parents = array_reverse($parents);
                                @endphp
                                @foreach($parents as $parent)
                                    <li>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                            <a href="{{ route('documents.list', ['view' => 'folders', 'folder_id' => $parent->id]) }}" class="text-green-600 hover:text-green-800">
                                                {{ $parent->name }}
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                            @endif
                            @if(isset($currentFolder))
                                <li>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                        <span class="text-green-900 font-medium">{{ $currentFolder->name }}</span>
                                    </div>
                                </li>
                            @endif
                        </ol>
                    </nav>
                </div>

                <!-- Controls Section for Folder Contents -->
                <div class="bg-white shadow-lg rounded-lg p-6 space-y-4">
                    <div class="flex flex-col md:flex-row gap-4 justify-between">
                        <!-- Search -->
                        <form method="GET" action="{{ route('documents.list') }}" class="flex-1">
                            <input type="hidden" name="view" value="folders">
                            <input type="hidden" name="folder_id" value="{{ request('folder_id') }}">
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}"
                                       placeholder="Search in '{{ $currentFolder->name }}'..."
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </form>

                        <!-- Actions -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <!-- Sort Dropdown -->
                            <form method="GET" action="{{ route('documents.list') }}" class="flex">
                                <input type="hidden" name="view" value="folders">
                                <input type="hidden" name="folder_id" value="{{ request('folder_id') }}">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <select name="sort" onchange="this.form.submit()"
                                        class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest First</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                    <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Name A-Z</option>
                                    <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Name Z-A</option>
                                    <option value="type" {{ request('sort') == 'type' ? 'selected' : '' }}>File Type</option>
                                </select>
                            </form>

                            <!-- Create Folder Button -->
                            <button onclick="document.getElementById('createFolderModal').classList.remove('hidden')"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                New Folder
                            </button>

                            <!-- Upload Button -->
                            <a href="{{ route('documents.create') }}?folder_id={{ request('folder_id') }}&view=folders"
                               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-sm transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Upload
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Folder Contents - Documents in this folder -->
                @if($documents->isEmpty() && $subfolders->isEmpty())
                    <div class="bg-white shadow-lg rounded-lg p-12 text-center">
                        <svg class="w-20 h-20 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-green-900 text-xl mb-2">This folder is empty</p>
                        <p class="text-gray-600 mb-6">Upload documents or create subfolders to get started</p>
                        <div class="flex gap-4 justify-center">
                            <a href="{{ route('documents.create') }}?folder_id={{ request('folder_id') }}&view=folders"
                               class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Upload Files
                            </a>
                            <button onclick="document.getElementById('createFolderModal').classList.remove('hidden')"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                New Folder
                            </button>
                        </div>
                    </div>
                @else
                    <!-- Subfolders Section -->
                    @if(!$subfolders->isEmpty())
                    <div class="bg-white shadow-lg rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-green-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                            </svg>
                            Subfolders
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            @foreach($subfolders as $folder)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-blue-50 hover:border-blue-300 transition cursor-pointer group"
                                 onclick="window.location='{{ route('documents.list', ['view' => 'folders', 'folder_id' => $folder->id]) }}'">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <div>
                                            <h4 class="font-medium text-green-900 group-hover:text-blue-700 truncate max-w-[150px]">
                                                {{ $folder->name }}
                                            </h4>
                                            <p class="text-sm text-gray-500">{{ $folder->documents_count }} items</p>
                                        </div>
                                    </div>
                                    <button onclick="event.stopPropagation(); openEditFolderModal({{ $folder->id }}, '{{ addslashes($folder->name) }}', '{{ addslashes($folder->description ?? '') }}')"
                                            class="opacity-0 group-hover:opacity-100 bg-yellow-500 hover:bg-yellow-600 text-white p-1 rounded transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                </div>
                                @if($folder->description)
                                <p class="text-sm text-gray-600 mt-2 truncate">{{ $folder->description }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Documents in Current Folder -->
                    @if(!$documents->isEmpty())
                    <div class="bg-white shadow-lg rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-green-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Documents in '{{ $currentFolder->name }}' ({{ $documents->count() }})
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-green-100 text-green-900">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Modified</th>
                                        <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($documents as $doc)
                                    @php
                                        $fileExt = pathinfo($doc->file_path, PATHINFO_EXTENSION);
                                    @endphp
                                    <tr class="hover:bg-green-50 transition">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                @if($fileExt === 'pdf')
                                                    <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                @elseif(in_array($fileExt, ['doc', 'docx']))
                                                    <svg class="w-6 h-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                @elseif(in_array($fileExt, ['xlsx', 'xls']))
                                                    <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-6 h-6 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                @endif
                                                <span class="text-green-900 font-medium">{{ $doc->title }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 rounded text-sm font-semibold
                                                @if($fileExt === 'pdf') bg-red-100 text-red-800
                                                @elseif(in_array($fileExt, ['doc', 'docx'])) bg-blue-100 text-blue-800
                                                @elseif(in_array($fileExt, ['xlsx', 'xls'])) bg-green-100 text-green-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $fileExt }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-green-900">{{ $doc->description ?? '-' }}</td>
                                        <td class="px-6 py-4 text-green-900">{{ $doc->updated_at->format('M j, Y') }}</td>
                                        <td class="px-6 py-4 space-x-2 flex">
                                            <a href="{{ asset('storage/'.$doc->file_path) }}"
                                               target="_blank"
                                               class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg shadow-sm transition">
                                                View
                                            </a>
                                            <a href="{{ route('documents.edit', $doc->id) }}"
                                               class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded-lg shadow-sm transition">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                @endif

            @else
                <!-- FOLDER BROWSER VIEW - When no specific folder is selected -->
                <!-- Controls Section for Folder Browser -->
                <div class="bg-white shadow-lg rounded-lg p-6 space-y-4">
                    <div class="flex flex-col md:flex-row gap-4 justify-between">
                        <!-- Search -->
                        <form method="GET" action="{{ route('documents.list') }}" class="flex-1">
                            <input type="hidden" name="view" value="folders">
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}"
                                       placeholder="Search folders..."
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </form>

                        <!-- Actions -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <!-- Create Folder Button -->
                            <button onclick="document.getElementById('createFolderModal').classList.remove('hidden')"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                New Folder
                            </button>

                            <!-- Upload Button -->
                            <a href="{{ route('documents.create') }}?view=folders"
                               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-sm transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Upload
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Folder Browser Content - Root Level Folders -->
                @if($subfolders->isEmpty())
                    <div class="bg-white shadow-lg rounded-lg p-12 text-center">
                        <svg class="w-20 h-20 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-green-900 text-xl mb-2">No folders created yet</p>
                        <p class="text-gray-600 mb-6">Create your first folder to organize your documents</p>
                        <button onclick="document.getElementById('createFolderModal').classList.remove('hidden')"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition flex items-center gap-2 justify-center mx-auto">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Create Your First Folder
                        </button>
                    </div>
                @else
                    <!-- Root Folders Grid -->
                    <div class="bg-white shadow-lg rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-green-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                            </svg>
                            Folders
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            @foreach($subfolders as $folder)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-blue-50 hover:border-blue-300 transition cursor-pointer group"
                                 onclick="window.location='{{ route('documents.list', ['view' => 'folders', 'folder_id' => $folder->id]) }}'">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <div>
                                            <h4 class="font-medium text-green-900 group-hover:text-blue-700 truncate max-w-[150px]">
                                                {{ $folder->name }}
                                            </h4>
                                            <p class="text-sm text-gray-500">{{ $folder->documents_count }} items</p>
                                        </div>
                                    </div>
                                    <button onclick="event.stopPropagation(); openEditFolderModal({{ $folder->id }}, '{{ addslashes($folder->name) }}', '{{ addslashes($folder->description ?? '') }}')"
                                            class="opacity-0 group-hover:opacity-100 bg-yellow-500 hover:bg-yellow-600 text-white p-1 rounded transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                </div>
                                @if($folder->description)
                                <p class="text-sm text-gray-600 mt-2 truncate">{{ $folder->description }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        @endif
    </div>

    <!-- Create Folder Modal -->
    <div id="createFolderModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-green-900 mb-4">Create New Folder</h3>
            <form action="{{ route('documents.create-folder') }}" method="POST">
                @csrf
                <input type="hidden" name="parent_id" value="{{ request('folder_id') }}">
                <input type="hidden" name="view" value="folders">
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Folder Name</label>
                        <input type="text" name="name" id="name" required
                               class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3"
                                  class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('createFolderModal').classList.add('hidden')"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                        Create Folder
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Folder Modal -->
    <div id="editFolderModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-green-900 mb-4">Edit Folder</h3>
            <form id="editFolderForm" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="view" value="folders">
                <div class="space-y-4">
                    <div>
                        <label for="edit_folder_name" class="block text-sm font-medium text-gray-700">Folder Name</label>
                        <input type="text" name="name" id="edit_folder_name" required
                               class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label for="edit_folder_description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="edit_folder_description" rows="3"
                                  class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('editFolderModal').classList.add('hidden')"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                        Update Folder
                    </button>
                </div>
            </form>
        </div>
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

    <script>
        // Close modals when clicking outside
        document.getElementById('createFolderModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        document.getElementById('editFolderModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        // Function to open edit folder modal
        function openEditFolderModal(folderId, folderName, folderDescription) {
            document.getElementById('edit_folder_name').value = folderName;
            document.getElementById('edit_folder_description').value = folderDescription || '';

            const form = document.getElementById('editFolderForm');
            form.action = `/folders/${folderId}`;

            document.getElementById('editFolderModal').classList.remove('hidden');
        }
    </script>
</x-app-layout>
