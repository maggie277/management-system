<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-green-900 leading-tight">
            Assets Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Cards for Asset Counts -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Assets -->
                <a href="{{ route('assets.list') }}"
                   class="block bg-gradient-to-br from-white to-green-50 p-6 rounded-lg shadow-md border border-green-100
                          hover:shadow-lg transform transition duration-300 hover:scale-105 cursor-pointer group
                          hover:border-green-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-gray-600 font-medium uppercase text-sm tracking-wide">Total Assets</div>
                            <div class="text-3xl font-bold text-green-700 mt-2">{{ $totalAssets }}</div>
                            <div class="text-xs text-gray-500 mt-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                Click to view all assets
                            </div>
                        </div>
                        <div class="bg-green-100 p-2 rounded-full group-hover:bg-green-200 transition-colors duration-300">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                    </div>
                </a>

                <!-- Active Assets -->
                <a href="{{ route('assets.list', ['status' => 'active']) }}"
                   class="block bg-gradient-to-br from-white to-green-50 p-6 rounded-lg shadow-md border border-green-100
                          hover:shadow-lg transform transition duration-300 hover:scale-105 cursor-pointer group
                          hover:border-green-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-gray-600 font-medium uppercase text-sm tracking-wide">Active Assets</div>
                            <div class="text-3xl font-bold text-green-600 mt-2">{{ $activeAssets }}</div>
                            <div class="text-xs text-gray-500 mt-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                Click to view active assets
                            </div>
                        </div>
                        <div class="bg-green-100 p-2 rounded-full group-hover:bg-green-200 transition-colors duration-300">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </a>

                <!-- Maintenance Assets -->
                <a href="{{ route('assets.list', ['status' => 'maintenance']) }}"
                   class="block bg-gradient-to-br from-white to-yellow-50 p-6 rounded-lg shadow-md border border-yellow-100
                          hover:shadow-lg transform transition duration-300 hover:scale-105 cursor-pointer group
                          hover:border-yellow-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-gray-600 font-medium uppercase text-sm tracking-wide">Maintenance</div>
                            <div class="text-3xl font-bold text-yellow-600 mt-2">{{ $maintenanceAssets }}</div>
                            <div class="text-xs text-gray-500 mt-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                Click to view maintenance assets
                            </div>
                        </div>
                        <div class="bg-yellow-100 p-2 rounded-full group-hover:bg-yellow-200 transition-colors duration-300">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                </a>

                <!-- Disposed Assets -->
                <a href="{{ route('assets.list', ['status' => 'disposed']) }}"
                   class="block bg-gradient-to-br from-white to-red-50 p-6 rounded-lg shadow-md border border-red-100
                          hover:shadow-lg transform transition duration-300 hover:scale-105 cursor-pointer group
                          hover:border-red-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-gray-600 font-medium uppercase text-sm tracking-wide">Disposed</div>
                            <div class="text-3xl font-bold text-red-600 mt-2">{{ $disposedAssets }}</div>
                            <div class="text-xs text-gray-500 mt-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                Click to view disposed assets
                            </div>
                        </div>
                        <div class="bg-red-100 p-2 rounded-full group-hover:bg-red-200 transition-colors duration-300">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 mt-8">
                <a href="{{ route('assets.create') }}"
                   class="bg-green-600 text-white px-6 py-3 rounded-lg shadow-md transform transition
                          duration-300 ease-in-out hover:bg-green-700 hover:scale-105 hover:shadow-lg
                          flex items-center gap-2 justify-center font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add New Asset
                </a>

                <a href="{{ route('assets.list', ['view' => 'folders']) }}"
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md transform transition
                          duration-300 ease-in-out hover:bg-blue-700 hover:scale-105 hover:shadow-lg
                          flex items-center gap-2 justify-center font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                    </svg>
                    Browse by Folders
                </a>

                <a href="{{ route('assets.list') }}"
                   class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-md transform transition
                          duration-300 ease-in-out hover:bg-green-600 hover:scale-105 hover:shadow-lg
                          flex items-center gap-2 justify-center font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    View All Assets
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
