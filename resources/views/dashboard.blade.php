<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Welcome Section -->
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <h1 class="text-2xl font-semibold text-gray-900">Welcome to CTPD Dashboard</h1>
                <p class="text-gray-700 mt-2">
                    Use the sidebar to access your documents, assets, and other modules.
                </p>
            </div>

            <!-- Dashboard Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Documents Card -->
                <div class="bg-green-100 border-l-4 border-green-700 shadow rounded-lg p-6">
                    <h2 class="font-semibold text-lg text-green-900">Documents</h2>
                    <p class="mt-2 text-gray-700">View and manage all your documents.</p>
                    <a href="{{ route('documents.index') }}"
                       class="mt-4 inline-block px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800">
                        Go to Documents
                    </a>
                </div>

                <!-- Assets Card -->
                <div class="bg-green-100 border-l-4 border-green-700 shadow rounded-lg p-6">
                    <h2 class="font-semibold text-lg text-green-900">Assets</h2>
                    <p class="mt-2 text-gray-700">View and manage all assets.</p>
                    <a href="{{ route('assets.index') }}"
                       class="mt-4 inline-block px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800">
                        Go to Assets
                    </a>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
