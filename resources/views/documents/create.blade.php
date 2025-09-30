<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-green-900 leading-tight">
            Upload Document
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <!-- Success Message -->
                @if(session('success'))
                    <div class="bg-green-100 text-green-800 p-2 mb-4 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Upload Form -->
                <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label for="title" class="block text-green-900 font-semibold">Title</label>
                        <input type="text" name="title" id="title" class="w-full p-2 border rounded" required>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-green-900 font-semibold">Description</label>
                        <textarea name="description" id="description" class="w-full p-2 border rounded"></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="file" class="block text-green-900 font-semibold">Select File</label>
                        <input type="file" name="file" id="file" class="w-full" required>
                    </div>

                    <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded font-medium">
                        Upload
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
