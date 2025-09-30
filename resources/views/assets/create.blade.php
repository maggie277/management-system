<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-green-900 leading-tight">
            Add New Asset
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

                <form method="POST" action="{{ route('assets.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-green-900 font-medium">Asset Name</label>
                        <input type="text" name="name" class="w-full border rounded p-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-green-900 font-medium">Category</label>
                        <input type="text" name="category" class="w-full border rounded p-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-green-900 font-medium">Description</label>
                        <textarea name="description" class="w-full border rounded p-2"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-green-900 font-medium">Serial Number</label>
                        <input type="text" name="serial_number" class="w-full border rounded p-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-green-900 font-medium">Location</label>
                        <input type="text" name="location" class="w-full border rounded p-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-green-900 font-medium">Purchase Date</label>
                        <input type="date" name="purchase_date" class="w-full border rounded p-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-green-900 font-medium">Value</label>
                        <input type="number" step="0.01" name="value" class="w-full border rounded p-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-green-900 font-medium">Status</label>
                        <select name="status" class="w-full border rounded p-2" required>
                            <option value="active">Active</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="disposed">Disposed</option>
                        </select>
                    </div>

                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                        Save Asset
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
