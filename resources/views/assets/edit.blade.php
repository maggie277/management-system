<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-green-900 leading-tight">
            {{ __('Edit Asset') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if(session('success'))
                    <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('assets.update', $asset->id) }}">
                    @csrf
                    @method('PUT') {{-- Changed from PATCH to PUT --}}

                    <div class="mb-4">
                        <label class="block text-green-900">Asset Name</label>
                        <input type="text" name="name" value="{{ old('name', $asset->name) }}"
                               class="w-full border border-green-300 rounded p-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-green-900">Category</label>
                        <input type="text" name="category" value="{{ old('category', $asset->category) }}"
                               class="w-full border border-green-300 rounded p-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-green-900">Description</label>
                        <textarea name="description" class="w-full border border-green-300 rounded p-2">{{ old('description', $asset->description) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-green-900">Serial Number</label>
                        <input type="text" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}"
                               class="w-full border border-green-300 rounded p-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-green-900">Location</label>
                        <input type="text" name="location" value="{{ old('location', $asset->location) }}"
                               class="w-full border border-green-300 rounded p-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-green-900">Purchase Date</label>
                        <input type="date" name="purchase_date" value="{{ old('purchase_date', $asset->purchase_date) }}"
                               class="w-full border border-green-300 rounded p-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-green-900">Value</label>
                        <input type="number" step="0.01" name="value" value="{{ old('value', $asset->value) }}"
                               class="w-full border border-green-300 rounded p-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-green-900">Status</label>
                        <select name="status" class="w-full border border-green-300 rounded p-2" required>
                            <option value="active" {{ $asset->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="maintenance" {{ $asset->status == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="disposed" {{ $asset->status == 'disposed' ? 'selected' : '' }}>Disposed</option>
                        </select>
                    </div>

                    <button type="submit"
                            class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded">
                        Update Asset
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
