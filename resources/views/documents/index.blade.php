<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-green-900 leading-tight">
            Documents
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">

                <a href="{{ route('documents.create') }}"
                   class="block bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded text-center font-medium">
                    Upload Document
                </a>

                <a href="{{ route('documents.list') }}"
                   class="block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-center font-medium">
                    View Documents
                </a>

            </div>
        </div>
    </div>
</x-app-layout>
