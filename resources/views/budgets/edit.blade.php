<x-app-layout>
    <x-slot name="header">
        Edit Budget
    </x-slot>

    <form action="{{ route('budgets.update', $budget) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="donor_id" class="block font-medium text-sm text-gray-700">Donor</label>
            <select name="donor_id" class="border-gray-300 rounded-md shadow-sm w-full" required>
                @foreach($donors as $donor)
                    <option value="{{ $donor->id }}" {{ $budget->donor_id == $donor->id ? 'selected' : '' }}>
                        {{ $donor->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="amount" class="block font-medium text-sm text-gray-700">Amount</label>
            <input type="number" step="0.01" name="amount" value="{{ $budget->amount }}" class="border-gray-300 rounded-md shadow-sm w-full" required>
        </div>

        <div>
            <label for="threshold" class="block font-medium text-sm text-gray-700">Threshold (optional)</label>
            <input type="number" step="0.01" name="threshold" value="{{ $budget->threshold }}" class="border-gray-300 rounded-md shadow-sm w-full">
        </div>

        <div>
            <label for="currency" class="block font-medium text-sm text-gray-700">Currency</label>
            <input type="text" name="currency" value="{{ $budget->currency }}" class="border-gray-300 rounded-md shadow-sm w-full" required>
        </div>

        <div>
            <label for="description" class="block font-medium text-sm text-gray-700">Description (optional)</label>
            <textarea name="description" class="border-gray-300 rounded-md shadow-sm w-full">{{ $budget->description }}</textarea>
        </div>

        <div class="flex space-x-2">
            <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800">Update Budget</button>
            <a href="{{ route('budgets.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</a>
        </div>
    </form>
</x-app-layout>
