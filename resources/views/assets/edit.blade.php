<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-green-900 leading-tight tracking-wide">
            Edit Asset
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg sm:rounded-xl p-8 border border-gray-200">

                <!-- Success Message -->
                @if(session('success'))
                    <div
                        id="successMessage"
                        class="bg-green-100 text-green-800 p-4 rounded mb-6 shadow-inner animate-slideDown">
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

                <form method="POST" action="{{ route('assets.update', $asset->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Asset Name -->
                    <div class="relative">
                        <label class="block text-green-900 font-medium mb-1">Asset Name</label>
                        <input type="text" name="name" value="{{ old('name', $asset->name) }}"
                               class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300"
                               placeholder="Enter asset name" required>
                    </div>

                    <!-- Owner -->
                    <div class="relative">
                        <label class="block text-green-900 font-medium mb-1">Owner</label>
                        <input type="text" name="owner" value="{{ old('owner', $asset->owner) }}"
                               class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300"
                               placeholder="Enter owner name">
                    </div>

                    <!-- Category -->
                    <div class="relative">
                        <label class="block text-green-900 font-medium mb-1">Category</label>
                        <input type="text" name="category" value="{{ old('category', $asset->category) }}"
                               class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300"
                               placeholder="Enter category">
                    </div>

                    <!-- Description -->
                    <div class="relative">
                        <label class="block text-green-900 font-medium mb-1">Description</label>
                        <textarea name="description" rows="4"
                                  class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300"
                                  placeholder="Enter description">{{ old('description', $asset->description) }}</textarea>
                    </div>

                    <!-- Serial Number -->
                    <div class="relative">
                        <label class="block text-green-900 font-medium mb-1">Serial Number</label>
                        <input type="text" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}"
                               class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300"
                               placeholder="Enter serial number">
                    </div>

                    <!-- Location -->
                    <div class="relative">
                        <label class="block text-green-900 font-medium mb-1">Location</label>
                        <input type="text" name="location" value="{{ old('location', $asset->location) }}"
                               class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300"
                               placeholder="Enter location">
                    </div>

                    <!-- Purchase Date -->
                    <div class="relative">
                        <label class="block text-green-900 font-medium mb-1">Purchase Date</label>
                        <input type="date" name="purchase_date" value="{{ old('purchase_date', $asset->purchase_date) }}"
                               class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300">
                    </div>

                    <!-- Value -->
                    <div class="relative">
                        <label class="block text-green-900 font-medium mb-1">Value</label>
                        <input type="number" step="0.01" name="value" value="{{ old('value', $asset->value) }}"
                               class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300"
                               placeholder="Enter value">
                    </div>

                    <!-- Currency -->
                    <div class="relative">
                        <label class="block text-green-900 font-medium mb-1">Currency</label>
                        <input type="text" name="currency" value="{{ old('currency', $asset->currency) }}"
                               class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300"
                               placeholder="Enter currency symbol or code">
                    </div>

                    <!-- Status -->
                    <div class="relative">
                        <label class="block text-green-900 font-medium mb-1">Status</label>
                        <select name="status"
                                class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300" required>
                            <option value="active" {{ $asset->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="maintenance" {{ $asset->status == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="disposed" {{ $asset->status == 'disposed' ? 'selected' : '' }}>Disposed</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg shadow-lg transform transition duration-300 hover:scale-105 hover:shadow-2xl">
                        Update Asset
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
