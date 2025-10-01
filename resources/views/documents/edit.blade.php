<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-green-900 leading-tight">
            Edit Document
        </h2>
    </x-slot>

    <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-lg shadow">
        <form action="{{ route('documents.update', $document->id) }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="mb-4">
                <label class="block text-green-900 font-medium mb-1" for="title">Title</label>
                <input type="text" name="title" id="title" value="{{ $document->title }}" class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-green-900 font-medium mb-1" for="description">Description</label>
                <textarea name="description" id="description" class="w-full border rounded px-3 py-2">{{ $document->description }}</textarea>
            </div>

            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded">
                Update Document
            </button>
        </form>
    </div>
</x-app-layout>
