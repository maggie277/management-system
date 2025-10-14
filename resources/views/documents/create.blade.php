<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-green-900 leading-tight">
            Upload Document
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-lg p-8 space-y-6 transition-transform transform hover:scale-105">

                <!-- Success Message -->
                @if(session('success'))
                    <div class="bg-green-100 text-green-800 p-3 rounded-lg border-l-4 border-green-500 animate-fade-in">
                        {{ session('success') }}
                    </div>
                @endif

                <h3 class="text-lg font-semibold text-green-900 mb-4">Upload a New Document</h3>

                <!-- Upload Form -->
                <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label for="title" class="block text-green-900 font-medium mb-1">Title</label>
                        <input type="text" name="title" id="title" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none transition" placeholder="Enter document title" required>
                    </div>

                    <div>
                        <label for="description" class="block text-green-900 font-medium mb-1">Description</label>
                        <textarea name="description" id="description" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none transition" rows="4" placeholder="Optional description..."></textarea>
                    </div>

                    <div>
                        <label for="file" class="block text-green-900 font-medium mb-1">Select File</label>
                        <input type="file" name="file" id="file" class="w-full text-gray-700 border border-gray-300 rounded-lg p-2 cursor-pointer hover:bg-green-50 transition" required>
                    </div>

                    <button type="submit" class="w-full bg-green-700 hover:bg-green-800 text-white font-semibold py-3 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:-translate-y-1">
                        Upload Document
                    </button>
                </form>

            </div>
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
</x-app-layout>
