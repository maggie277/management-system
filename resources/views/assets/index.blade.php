<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-green-900 leading-tight">
            Assets Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h1 class="text-2xl font-semibold text-green-900 mb-4">
                    Asset Management
                </h1>

                <div class="flex space-x-4">
                    <a href="{{ route('assets.create') }}"
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                        Add New Asset
                    </a>
                    <a href="{{ route('assets.list') }}"
                       class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                        View Assets
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
