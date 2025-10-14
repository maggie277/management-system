<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-green-900">Edit Budget & Expenses</h1>
    </x-slot>

    {{-- ================= UPDATE BUDGET FORM ================= --}}
    <div class="bg-white shadow-md rounded p-6 mb-6">
        <h2 class="text-lg font-semibold text-green-800 border-b pb-2 mb-4">Budget Details</h2>

        <form action="{{ route('budgets.update', $budget) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700">Donor</label>
                <select name="donor_id" class="w-full rounded border-gray-300" required>
                    @foreach($donors as $donor)
                        <option value="{{ $donor->id }}" {{ $budget->donor_id == $donor->id ? 'selected' : '' }}>
                            {{ $donor->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Amount</label>
                <input type="number" step="0.01" name="amount" value="{{ $budget->amount }}" class="w-full rounded border-gray-300" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Threshold (optional)</label>
                <input type="number" step="0.01" name="threshold" value="{{ $budget->threshold }}" class="w-full rounded border-gray-300">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Currency</label>
                <input type="text" name="currency" value="{{ $budget->currency }}" class="w-full rounded border-gray-300" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Description (optional)</label>
                <textarea name="description" class="w-full rounded border-gray-300">{{ $budget->description }}</textarea>
            </div>

            <div class="flex space-x-2 mt-4">
                <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800">Update Budget</button>
                <a href="{{ route('budgets.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</a>
            </div>
        </form>
    </div>

    {{-- ================= EXISTING EXPENSES ================= --}}
    <div class="bg-white shadow-md rounded p-6 mb-6">
        <h2 class="text-lg font-semibold text-green-800 border-b pb-2 mb-4">Existing Expenses</h2>
        <table class="w-full text-sm border border-gray-200">
            <thead class="bg-gray-100 text-gray-800">
                <tr>
                    <th class="px-3 py-2 border">Title</th>
                    <th class="px-3 py-2 border">Amount</th>
                    <th class="px-3 py-2 border">Date</th>
                    <th class="px-3 py-2 border">Notes</th>
                    <th class="px-3 py-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($budget->expenses as $expense)
                    <tr>
                        <td class="border px-3 py-2">{{ $expense->title }}</td>
                        <td class="border px-3 py-2">{{ number_format($expense->amount, 2) }}</td>
                        <td class="border px-3 py-2">{{ $expense->expense_date->format('Y-m-d') }}</td>
                        <td class="border px-3 py-2">{{ $expense->notes }}</td>
                        <td class="border px-3 py-2 text-center">
                            <a href="{{ route('expenses.edit', $expense) }}" class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600">Edit</a>
                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600" onclick="return confirm('Delete this expense?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-500 py-3">No expenses recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ================= ADD NEW EXPENSE ================= --}}
    <div class="bg-white shadow-md rounded p-6 mb-6">
        <h2 class="text-lg font-semibold text-green-800 border-b pb-2 mb-4">Add New Expense</h2>

        <form action="{{ route('expenses.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="budget_id" value="{{ $budget->id }}">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" class="w-full rounded border-gray-300" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Amount</label>
                    <input type="number" step="0.01" name="amount" class="w-full rounded border-gray-300" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Expense Date</label>
                    <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" class="w-full rounded border-gray-300" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Notes (optional)</label>
                    <input type="text" name="notes" class="w-full rounded border-gray-300">
                </div>
            </div>

            <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800">
                + Add Expense
            </button>
        </form>
    </div>
</x-app-layout>
