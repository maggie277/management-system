<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-green-900 leading-tight tracking-wide">
            Edit Document: {{ $document->title }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto mt-10 p-8 bg-white rounded-xl shadow-lg border border-gray-200">

        <!-- Success Message -->
        @if(session('success'))
            <div id="successMessage" class="bg-green-100 text-green-800 p-4 rounded mb-6 shadow-inner animate-slideDown">
                {{ session('success') }}
            </div>
            <script>
                setTimeout(() => {
                    const msg = document.getElementById('successMessage');
                    if(msg){
                        msg.classList.add('opacity-0', 'transition', 'duration-700');
                        setTimeout(() => msg.remove(), 700);
                    }
                }, 4000);
            </script>
        @endif

        <!-- Error Messages -->
        @if($errors->any())
            <div class="bg-red-100 text-red-800 p-4 rounded mb-6 shadow-inner animate-slideDown">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('documents.update', $document->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Title -->
            <div class="relative">
                <label class="block text-green-900 font-medium mb-1" for="title">Title</label>
                <input type="text" name="title" value="{{ old('title', $document->title) }}" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300"
                       placeholder="Enter document title">
            </div>

            <!-- Description -->
            <div class="relative">
                <label class="block text-green-900 font-medium mb-1" for="description">Description</label>
                <textarea name="description" rows="4"
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300"
                          placeholder="Enter document description">{{ old('description', $document->description) }}</textarea>
            </div>

            <!-- Download/Open File -->
            <div class="mb-4">
                <a href="{{ route('documents.download', $document->id) }}"
                   class="inline-block bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg shadow-lg transform transition duration-300 hover:scale-105 hover:shadow-2xl">
                    Download File
                </a>
            </div>

            <!-- Replace File (Optional) -->
            <div class="relative">
                <label class="block text-green-900 font-medium mb-1" for="file">Replace File (optional)</label>
                <input type="file" name="file"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300">
            </div>

            <!-- Submit Button -->
            <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg shadow-lg transform transition duration-300 hover:scale-105 hover:shadow-2xl">
                Update Document
            </button>
        </form>
    </div>
</x-app-layout>
