<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-green-900">Budgets & Expenses Overview</h1>
    </x-slot>

    <div class="container mt-6 space-y-6" x-data="{ open: null }">
        <div class="flex justify-between">
            <a href="{{ route('budgets.create') }}" class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 transition">
                Add Budget
            </a>
            <a href="{{ route('expenses.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                Add Expense
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded shadow-sm animate-fade-in">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto bg-white rounded shadow-md">
            <table class="min-w-full border border-gray-200 text-sm">
                <thead class="bg-green-100 text-green-900">
                    <tr>
                        <th class="px-4 py-2 border">Donor</th>
                        <th class="px-4 py-2 border">Description</th>
                        <th class="px-4 py-2 border">Budget Amount</th>
                        <th class="px-4 py-2 border">Total Expenses</th>
                        <th class="px-4 py-2 border">Remaining</th>
                        <th class="px-4 py-2 border">Currency</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($budgets as $budget)
                    <tr class="hover:bg-green-50 transition" @click="open === {{ $budget->id }} ? open = null : open = {{ $budget->id }}">
                        <td class="px-4 py-2 border">{{ $budget->donor->name }}</td>
                        <td class="px-4 py-2 border">{{ $budget->description }}</td>
                        <td class="px-4 py-2 border">{{ number_format($budget->amount, 2) }}</td>
                        <td class="px-4 py-2 border text-red-600">{{ number_format($budget->total_expenses, 2) }}</td>
                        <td class="px-4 py-2 border font-semibold text-green-700">{{ number_format($budget->remaining, 2) }}</td>
                        <td class="px-4 py-2 border">{{ $budget->currency }}</td>
                        <td class="px-4 py-2 border space-x-2">
                            <a href="{{ route('budgets.edit', $budget) }}" class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600">Edit</a>
                            <form action="{{ route('budgets.destroy', $budget) }}" method="POST" class="inline-block">
                                @csrf @method('DELETE')
                                <button class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600" onclick="return confirm('Delete this budget?')">Delete</button>
                            </form>
                        </td>
                    </tr>

                    {{-- Expenses Row --}}
                    <tr x-show="open === {{ $budget->id }}" x-transition>
                        <td colspan="7" class="px-6 py-3 bg-gray-50 border">
                            <h3 class="font-semibold text-green-800 mb-2">Expenses for {{ $budget->description }}</h3>
                            <table class="w-full border border-gray-200 text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-2 py-1 border">Title</th>
                                        <th class="px-2 py-1 border">Amount</th>
                                        <th class="px-2 py-1 border">Date</th>
                                        <th class="px-2 py-1 border">Notes</th>
                                        <th class="px-2 py-1 border">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($budget->expenses as $expense)
                                    <tr>
                                        <td class="px-2 py-1 border">{{ $expense->title }}</td>
                                        <td class="px-2 py-1 border">{{ number_format($expense->amount, 2) }}</td>
                                        <td class="px-2 py-1 border">{{ $expense->expense_date->format('Y-m-d') }}</td>
                                        <td class="px-2 py-1 border">{{ $expense->notes }}</td>
                                        <td class="px-2 py-1 border space-x-1">
                                            <a href="{{ route('expenses.edit', $expense) }}" class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600">Edit</a>
                                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline-block">
                                                @csrf @method('DELETE')
                                                <button class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600" onclick="return confirm('Delete this expense?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
