<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-green-900 leading-tight">
            @if(isset($currentFolder))
                {{ $currentFolder->name }} Assets
            @else
                Asset Manager
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
                    <a href="{{ route('assets.list', ['view' => 'folders']) }}"
                       class="{{ request('view') == 'folders' || !request('view') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                         Folder View
                    </a>
                    <a href="{{ route('assets.list', ['view' => 'all']) }}"
                       class="{{ request('view') == 'all' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                         All Assets
                    </a>
                </nav>
            </div>
        </div>

        @if(request('view') == 'all')
            <!-- ALL ASSETS VIEW -->
            <!-- Controls Section for All Assets -->
            <div class="bg-white shadow-lg rounded-lg p-6 space-y-4">
                <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center">
                    <!-- Search -->
                    <form method="GET" action="{{ route('assets.list') }}" class="flex-1 w-full md:w-auto">
                        <input type="hidden" name="view" value="all">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Search all assets..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </form>

                    <!-- Sort and Actions -->
                    <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                        <!-- Status Filter -->
                        <form method="GET" action="{{ route('assets.list') }}" class="flex">
                            <input type="hidden" name="view" value="all">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                            <select name="status" onchange="this.form.submit()"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="disposed" {{ request('status') == 'disposed' ? 'selected' : '' }}>Disposed</option>
                            </select>
                        </form>

                        <!-- Sort Dropdown -->
                        <form method="GET" action="{{ route('assets.list') }}" class="flex">
                            <input type="hidden" name="view" value="all">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                            <select name="sort" onchange="this.form.submit()"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                                <option value="owner_asc" {{ request('sort') == 'owner_asc' ? 'selected' : '' }}>Owner A-Z</option>
                                <option value="owner_desc" {{ request('sort') == 'owner_desc' ? 'selected' : '' }}>Owner Z-A</option>
                                <option value="value_high" {{ request('sort') == 'value_high' ? 'selected' : '' }}>Value: High to Low</option>
                                <option value="value_low" {{ request('sort') == 'value_low' ? 'selected' : '' }}>Value: Low to High</option>
                            </select>
                        </form>

                        <!-- Add New Asset Button -->
                        <a href="{{ route('assets.create') }}"
                           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-sm transition flex items-center gap-2 justify-center text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Asset
                        </a>
                    </div>
                </div>

                <!-- Active Filters -->
                @if(request('search') || request('status') || request('sort') != 'latest')
                <div class="flex flex-wrap gap-2">
                    @if(request('search'))
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm flex items-center gap-1">
                        Search: "{{ request('search') }}"
                        <a href="{{ route('assets.list', array_merge(request()->except('search'), ['search' => '', 'view' => 'all'])) }}"
                           class="text-blue-600 hover:text-blue-800">
                            &times;
                        </a>
                    </span>
                    @endif
                    @if(request('status'))
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm flex items-center gap-1">
                        Status: {{ ucfirst(request('status')) }}
                        <a href="{{ route('assets.list', array_merge(request()->except('status'), ['status' => '', 'view' => 'all'])) }}"
                           class="text-green-600 hover:text-green-800">
                            &times;
                        </a>
                    </span>
                    @endif
                    @if(request('sort') && request('sort') != 'latest')
                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm flex items-center gap-1">
                        Sorted:
                        @switch(request('sort'))
                            @case('oldest') Oldest First @break
                            @case('name_asc') Name A-Z @break
                            @case('name_desc') Name Z-A @break
                            @case('owner_asc') Owner A-Z @break
                            @case('owner_desc') Owner Z-A @break
                            @case('value_high') Value High to Low @break
                            @case('value_low') Value Low to High @break
                        @endswitch
                        <a href="{{ route('assets.list', array_merge(request()->except('sort'), ['sort' => 'latest', 'view' => 'all'])) }}"
                           class="text-yellow-600 hover:text-yellow-800">
                            &times;
                        </a>
                    </span>
                    @endif
                    @if(request('search') || request('status') || request('sort') != 'latest')
                    <a href="{{ route('assets.list', ['view' => 'all']) }}"
                       class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm hover:bg-gray-200 transition">
                        Clear All
                    </a>
                    @endif
                </div>
                @endif
            </div>

            <!-- All Assets Content -->
            @if($assets->isEmpty())
                <div class="bg-white shadow-lg rounded-lg p-8 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <p class="text-green-900 text-lg mb-2">No assets found</p>
                    <p class="text-gray-600 mb-4">Try adjusting your search or add your first asset</p>
                    <a href="{{ route('assets.create') }}"
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 justify-center text-sm mx-auto w-fit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Your First Asset
                    </a>
                </div>
            @else
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-green-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        All Assets ({{ $assets->count() }})
                    </h3>

                    <!-- Responsive Table Container -->
                    <div class="overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-green-100 text-green-900">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Owner</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Category</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Location</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Value</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($assets as $asset)
                                        <tr class="transition duration-300 hover:bg-green-50">
                                            <td class="px-4 py-4">
                                                <div class="font-medium text-green-900 text-sm">{{ $asset->name }}</div>
                                                @if($asset->description)
                                                <div class="text-xs text-gray-600 mt-1">{{ Str::limit($asset->description, 40) }}</div>
                                                @endif
                                                @if($asset->serial_number)
                                                <div class="text-xs text-gray-500 font-mono mt-1">{{ $asset->serial_number }}</div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 text-green-900 text-sm">
                                                {{ $asset->owner ?? '-' }}
                                            </td>
                                            <td class="px-4 py-4 text-sm">
                                                @if($asset->category)
                                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                                    {{ $asset->category }}
                                                </span>
                                                @else
                                                <span class="text-gray-500 text-xs">-</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 text-green-900 text-sm">
                                                {{ $asset->location ?? '-' }}
                                            </td>
                                            <td class="px-4 py-4 text-sm">
                                                @if($asset->value)
                                                    <div class="font-semibold text-green-900">{{ $asset->currency ?? '$' }}{{ number_format($asset->value, 2) }}</div>
                                                    @if($asset->purchase_date)
                                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($asset->purchase_date)->format('M Y') }}</div>
                                                    @endif
                                                @else
                                                    <span class="text-gray-500">-</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4">
                                                @php
                                                    $statusColors = [
                                                        'active' => 'bg-green-100 text-green-800 border-green-200',
                                                        'maintenance' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                        'disposed' => 'bg-red-100 text-red-800 border-red-200'
                                                    ];
                                                @endphp
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold border {{ $statusColors[$asset->status] }}">
                                                    {{ ucfirst($asset->status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="flex flex-col sm:flex-row gap-2">
                                                    <a href="{{ route('assets.edit', $asset->id) }}"
                                                       class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded text-xs text-center transition whitespace-nowrap">
                                                        Edit
                                                    </a>
                                                    <form method="POST" action="{{ route('assets.destroy', $asset->id) }}" onsubmit="return confirm('Are you sure you want to delete this asset?');" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs w-full transition whitespace-nowrap">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Results Count -->
                    <div class="mt-4 text-sm text-gray-600">
                        Showing {{ $assets->count() }} asset(s)
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
                                <a href="{{ route('assets.list', ['view' => 'folders']) }}" class="text-green-600 hover:text-green-800 font-medium flex items-center">
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
                                            <a href="{{ route('assets.list', ['view' => 'folders', 'folder_id' => $parent->id]) }}" class="text-green-600 hover:text-green-800">
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
                    <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center">
                        <!-- Search -->
                        <form method="GET" action="{{ route('assets.list') }}" class="flex-1 w-full md:w-auto">
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
                        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                            <!-- Sort Dropdown -->
                            <form method="GET" action="{{ route('assets.list') }}" class="flex">
                                <input type="hidden" name="view" value="folders">
                                <input type="hidden" name="folder_id" value="{{ request('folder_id') }}">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <select name="sort" onchange="this.form.submit()"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm">
                                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest First</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                                    <option value="owner_asc" {{ request('sort') == 'owner_asc' ? 'selected' : '' }}>Owner A-Z</option>
                                    <option value="owner_desc" {{ request('sort') == 'owner_desc' ? 'selected' : '' }}>Owner Z-A</option>
                                    <option value="value_high" {{ request('sort') == 'value_high' ? 'selected' : '' }}>Value: High to Low</option>
                                    <option value="value_low" {{ request('sort') == 'value_low' ? 'selected' : '' }}>Value: Low to High</option>
                                </select>
                            </form>

                            <!-- Create Folder Button -->
                            <button onclick="document.getElementById('createFolderModal').classList.remove('hidden')"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm transition flex items-center gap-2 justify-center text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                New Folder
                            </button>

                            <!-- Add Asset Button -->
                            <a href="{{ route('assets.create') }}?folder_id={{ request('folder_id') }}&view=folders"
                               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-sm transition flex items-center gap-2 justify-center text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Asset
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Folder Contents - Assets in this folder -->
                @if($assets->isEmpty() && $subfolders->isEmpty())
                    <div class="bg-white shadow-lg rounded-lg p-8 text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <p class="text-green-900 text-lg mb-2">This folder is empty</p>
                        <p class="text-gray-600 mb-4">Add assets or create subfolders to get started</p>
                        <div class="flex gap-4 justify-center flex-col sm:flex-row">
                            <a href="{{ route('assets.create') }}?folder_id={{ request('folder_id') }}&view=folders"
                               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 justify-center text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Asset
                            </a>
                            <button onclick="document.getElementById('createFolderModal').classList.remove('hidden')"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 justify-center text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                 onclick="window.location='{{ route('assets.list', ['view' => 'folders', 'folder_id' => $folder->id]) }}'">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <div>
                                            <h4 class="font-medium text-green-900 group-hover:text-blue-700 truncate max-w-[150px]">
                                                {{ $folder->name }}
                                            </h4>
                                            <p class="text-sm text-gray-500">{{ $folder->assets_count ?? 0 }} items</p>
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

                    <!-- Assets in Current Folder -->
                    @if(!$assets->isEmpty())
                    <div class="bg-white shadow-lg rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-green-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Assets in '{{ $currentFolder->name }}' ({{ $assets->count() }})
                        </h3>

                        <!-- Responsive Table Container -->
                        <div class="overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-green-100 text-green-900">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Name</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Owner</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Category</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Location</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Value</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($assets as $asset)
                                            <tr class="transition duration-300 hover:bg-green-50">
                                                <td class="px-4 py-4">
                                                    <div class="font-medium text-green-900 text-sm">{{ $asset->name }}</div>
                                                    @if($asset->description)
                                                    <div class="text-xs text-gray-600 mt-1">{{ Str::limit($asset->description, 40) }}</div>
                                                    @endif
                                                    @if($asset->serial_number)
                                                    <div class="text-xs text-gray-500 font-mono mt-1">{{ $asset->serial_number }}</div>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-4 text-green-900 text-sm">
                                                    {{ $asset->owner ?? '-' }}
                                                </td>
                                                <td class="px-4 py-4 text-sm">
                                                    @if($asset->category)
                                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                                        {{ $asset->category }}
                                                    </span>
                                                    @else
                                                    <span class="text-gray-500 text-xs">-</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-4 text-green-900 text-sm">
                                                    {{ $asset->location ?? '-' }}
                                                </td>
                                                <td class="px-4 py-4 text-sm">
                                                    @if($asset->value)
                                                        <div class="font-semibold text-green-900">{{ $asset->currency ?? '$' }}{{ number_format($asset->value, 2) }}</div>
                                                        @if($asset->purchase_date)
                                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($asset->purchase_date)->format('M Y') }}</div>
                                                        @endif
                                                    @else
                                                        <span class="text-gray-500">-</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-4">
                                                    @php
                                                        $statusColors = [
                                                            'active' => 'bg-green-100 text-green-800 border-green-200',
                                                            'maintenance' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                            'disposed' => 'bg-red-100 text-red-800 border-red-200'
                                                        ];
                                                    @endphp
                                                    <span class="px-2 py-1 rounded-full text-xs font-semibold border {{ $statusColors[$asset->status] }}">
                                                        {{ ucfirst($asset->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-4">
                                                    <div class="flex flex-col sm:flex-row gap-2">
                                                        <a href="{{ route('assets.edit', $asset->id) }}"
                                                           class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded text-xs text-center transition whitespace-nowrap">
                                                            Edit
                                                        </a>
                                                        <form method="POST" action="{{ route('assets.destroy', $asset->id) }}" onsubmit="return confirm('Are you sure you want to delete this asset?');" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs w-full transition whitespace-nowrap">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                @endif

            @else
                <!-- FOLDER BROWSER VIEW - When no specific folder is selected -->
                <!-- Controls Section for Folder Browser -->
                <div class="bg-white shadow-lg rounded-lg p-6 space-y-4">
                    <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center">
                        <!-- Search -->
                        <form method="GET" action="{{ route('assets.list') }}" class="flex-1 w-full md:w-auto">
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
                        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                            <!-- Create Folder Button -->
                            <button onclick="document.getElementById('createFolderModal').classList.remove('hidden')"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm transition flex items-center gap-2 justify-center text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                New Folder
                            </button>

                            <!-- Add Asset Button -->
                            <a href="{{ route('assets.create') }}?view=folders"
                               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-sm transition flex items-center gap-2 justify-center text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Asset
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Folder Browser Content - Root Level Folders -->
                @if($subfolders->isEmpty())
                    <div class="bg-white shadow-lg rounded-lg p-8 text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-green-900 text-lg mb-2">No folders created yet</p>
                        <p class="text-gray-600 mb-4">Create your first folder to organize your assets</p>
                        <button onclick="document.getElementById('createFolderModal').classList.remove('hidden')"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 justify-center text-sm mx-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                 onclick="window.location='{{ route('assets.list', ['view' => 'folders', 'folder_id' => $folder->id]) }}'">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <div>
                                            <h4 class="font-medium text-green-900 group-hover:text-blue-700 truncate max-w-[150px]">
                                                {{ $folder->name }}
                                            </h4>
                                            <p class="text-sm text-gray-500">{{ $folder->assets_count ?? 0 }} items</p>
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
            <form action="{{ route('assets.create-folder') }}" method="POST">
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
            // Update to use asset-folders route
            form.action = `/asset-folders/${folderId}`;

            document.getElementById('editFolderModal').classList.remove('hidden');
        }
    </script>
</x-app-layout>
