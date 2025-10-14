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
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transform transition duration-300 hover:scale-105">
                    <div class="text-gray-700 font-medium uppercase text-sm">Total Assets</div>
                    <div class="text-2xl font-bold text-green-700 mt-2 animate-pulse">{{ $totalAssets }}</div>
                </div>

                <!-- Active Assets -->
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transform transition duration-300 hover:scale-105">
                    <div class="text-gray-700 font-medium uppercase text-sm">Active Assets</div>
                    <div class="text-2xl font-bold text-green-600 mt-2">{{ $activeAssets }}</div>
                </div>

                <!-- Maintenance Assets -->
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transform transition duration-300 hover:scale-105">
                    <div class="text-gray-700 font-medium uppercase text-sm">Maintenance</div>
                    <div class="text-2xl font-bold text-yellow-600 mt-2">{{ $maintenanceAssets }}</div>
                </div>

                <!-- Disposed Assets -->
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transform transition duration-300 hover:scale-105">
                    <div class="text-gray-700 font-medium uppercase text-sm">Disposed</div>
                    <div class="text-2xl font-bold text-red-600 mt-2">{{ $disposedAssets }}</div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-4 mt-6">
                <a href="{{ route('assets.create') }}"
                   class="bg-green-600 text-white px-4 py-2 rounded-lg shadow-md transform transition
                          duration-300 ease-in-out hover:bg-green-700 hover:scale-105 hover:shadow-lg">
                    Add New Asset
                </a>

                <a href="{{ route('assets.list') }}"
                   class="bg-green-500 text-white px-4 py-2 rounded-lg shadow-md transform transition
                          duration-300 ease-in-out hover:bg-green-600 hover:scale-105 hover:shadow-lg">
                    View Assets
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
