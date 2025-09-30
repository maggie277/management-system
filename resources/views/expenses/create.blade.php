<x-app-layout>
    <x-slot name="header">
        Add Expense
    </x-slot>

    <div class="space-y-4">
        <form action="{{ route('expenses.store') }}" method="POST" class="bg-white shadow-md rounded p-6 space-y-4">
            @csrf

            <div>
                <label for="budget_id" class="block font-medium text-gray-700">Budget</label>
                <select name="budget_id" class="mt-1 block w-full border-gray-300 rounded" required>
                    <option value="">Select Budget</option>
                    @foreach($budgets as $budget)
                        <option value="{{ $budget->id }}">
                            {{ $budget->description }} ({{ $budget->donor->name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="title" class="block font-medium text-gray-700">Title</label>
                <input type="text" name="title" class="mt-1 block w-full border-gray-300 rounded" required>
            </div>

            <div>
                <label for="amount" class="block font-medium text-gray-700">Amount</label>
                <input type="number" step="0.01" name="amount" class="mt-1 block w-full border-gray-300 rounded" required>
            </div>

            <div>
                <label for="expense_date" class="block font-medium text-gray-700">Expense Date</label>
                <input type="date" name="expense_date" class="mt-1 block w-full border-gray-300 rounded" required>
            </div>

            <div>
                <label for="notes" class="block font-medium text-gray-700">Notes (optional)</label>
                <textarea name="notes" class="mt-1 block w-full border-gray-300 rounded"></textarea>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800">Save Expense</button>
                <a href="{{ route('expenses.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
