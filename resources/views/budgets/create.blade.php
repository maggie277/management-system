<x-app-layout>
    <x-slot name="header">
        Add Budget
    </x-slot>

    <form action="{{ route('budgets.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="donor_id" class="block font-medium text-sm text-gray-700">Donor</label>
            <select name="donor_id" class="border-gray-300 rounded-md shadow-sm w-full" required>
                <option value="">Select Donor</option>
                @foreach($donors as $donor)
                    <option value="{{ $donor->id }}">{{ $donor->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="amount" class="block font-medium text-sm text-gray-700">Amount</label>
            <input type="number" step="0.01" name="amount" class="border-gray-300 rounded-md shadow-sm w-full" required>
        </div>

        <div>
            <label for="threshold" class="block font-medium text-sm text-gray-700">Threshold (optional)</label>
            <input type="number" step="0.01" name="threshold" class="border-gray-300 rounded-md shadow-sm w-full">
        </div>

        <div>
            <label for="currency" class="block font-medium text-sm text-gray-700">Currency</label>
            <input type="text" name="currency" value="USD" class="border-gray-300 rounded-md shadow-sm w-full" required>
        </div>

        <div>
            <label for="description" class="block font-medium text-sm text-gray-700">Description (optional)</label>
            <textarea name="description" class="border-gray-300 rounded-md shadow-sm w-full"></textarea>
        </div>

        <div class="flex space-x-2">
            <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800">Save Budget</button>
            <a href="{{ route('budgets.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</a>
        </div>
    </form>
</x-app-layout>
