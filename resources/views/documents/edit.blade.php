<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-green-900 leading-tight tracking-wide">
            @if(isset($folder))
                Edit Folder: {{ $folder->name }}
            @else
                Edit Document: {{ $document->title }}
            @endif
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

        @if(isset($folder))
            <!-- Folder Edit Form -->
            <form action="{{ route('documents.update-folder', $folder->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')

                <!-- Folder Name -->
                <div class="relative">
                    <label class="block text-green-900 font-medium mb-1" for="folder_name">Folder Name</label>
                    <input type="text" name="name" value="{{ old('name', $folder->name) }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300"
                           placeholder="Enter folder name">
                </div>

                <!-- Folder Description -->
                <div class="relative">
                    <label class="block text-green-900 font-medium mb-1" for="folder_description">Description</label>
                    <textarea name="description" rows="4"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300"
                              placeholder="Enter folder description">{{ old('description', $folder->description) }}</textarea>
                </div>

                <!-- Parent Folder -->
                <div class="relative">
                    <label class="block text-green-900 font-medium mb-1" for="parent_id">Parent Folder (Optional)</label>
                    <select name="parent_id"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300">
                        <option value="">No Parent (Root)</option>
                        @foreach($allFolders as $f)
                            @if($f->id !== $folder->id) {{-- Prevent selecting self as parent --}}
                                <option value="{{ $f->id }}" {{ old('parent_id', $folder->parent_id) == $f->id ? 'selected' : '' }}>
                                    {{ $f->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <!-- Folder Info -->
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <h4 class="text-blue-900 font-medium mb-2">Folder Information</h4>
                    <p class="text-blue-700 text-sm">
                        Contains {{ $folder->documents_count }} document(s)
                    </p>
                    <p class="text-blue-700 text-sm mt-1">
                        Created: {{ $folder->created_at->format('M j, Y') }}
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-4">
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg shadow-lg transform transition duration-300 hover:scale-105 hover:shadow-2xl">
                        Update Folder
                    </button>
                    <a href="{{ route('documents.list') }}"
                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg shadow-lg transform transition duration-300 hover:scale-105 hover:shadow-2xl">
                        Cancel
                    </a>
                </div>
            </form>

        @else
            <!-- Document Edit Form -->
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

                <!-- Folder Selection -->
                <div class="relative">
                    <label class="block text-green-900 font-medium mb-1" for="folder_id">Folder</label>
                    <select name="folder_id"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300">
                        <option value="">Unassigned</option>
                        @foreach($folders as $f)
                            <option value="{{ $f->id }}" {{ old('folder_id', $document->folder_id) == $f->id ? 'selected' : '' }}>
                                {{ $f->name }}
                            </option>
                        @endforeach
                    </select>
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

                <!-- Document Info -->
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <h4 class="text-green-900 font-medium mb-2">Document Information</h4>
                    <p class="text-green-700 text-sm">
                        File type: {{ pathinfo($document->file_path, PATHINFO_EXTENSION) }}
                    </p>
                    <p class="text-green-700 text-sm mt-1">
                        Uploaded: {{ $document->created_at->format('M j, Y') }}
                    </p>
                    <p class="text-green-700 text-sm mt-1">
                        Uploaded by: {{ $document->uploadedBy->name ?? 'N/A' }}
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-4">
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg shadow-lg transform transition duration-300 hover:scale-105 hover:shadow-2xl">
                        Update Document
                    </button>
                    <a href="{{ route('documents.list') }}"
                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg shadow-lg transform transition duration-300 hover:scale-105 hover:shadow-2xl">
                        Cancel
                    </a>
                </div>
            </form>
        @endif
    </div>
</x-app-layout>
