<x-app-layout>
    <x-slot name="header">
        Expenses
    </x-slot>

    <div class="space-y-4">
        <a href="{{ route('expenses.create') }}" class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 inline-block">
            Add Expense
        </a>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-2 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">Budget</th>
                        <th class="px-4 py-2 border">Donor</th>
                        <th class="px-4 py-2 border">Title</th>
                        <th class="px-4 py-2 border">Amount</th>
                        <th class="px-4 py-2 border">Expense Date</th>
                        <th class="px-4 py-2 border">Notes</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $expense)
                    <tr class="text-center">
                        <td class="px-4 py-2 border">{{ $expense->budget->description ?? 'N/A' }}</td>
                        <td class="px-4 py-2 border">{{ $expense->budget->donor->name ?? 'N/A' }}</td>
                        <td class="px-4 py-2 border">{{ $expense->title }}</td>
                        <td class="px-4 py-2 border">{{ number_format($expense->amount, 2) }}</td>
                        <td class="px-4 py-2 border">{{ $expense->expense_date->format('Y-m-d') }}</td>
                        <td class="px-4 py-2 border">{{ $expense->notes }}</td>
                        <td class="px-4 py-2 border space-x-1">
                            <a href="{{ route('expenses.edit', $expense) }}" class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600">Edit</a>
                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600" onclick="return confirm('Delete this expense?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
