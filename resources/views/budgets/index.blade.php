<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-green-900">Budgets</h1>
    </x-slot>

    <div class="container mt-4">
        <div class="mb-4">
            <a href="{{ route('budgets.create') }}" class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800">
                Add Budget
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded">
                <thead class="bg-green-200 text-green-900">
                    <tr>
                        <th class="px-4 py-2 border">Donor</th>
                        <th class="px-4 py-2 border">Amount</th>
                        <th class="px-4 py-2 border">Threshold</th>
                        <th class="px-4 py-2 border">Currency</th>
                        <th class="px-4 py-2 border">Description</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($budgets as $budget)
                    <tr class="text-green-900">
                        <td class="px-4 py-2 border">{{ $budget->donor->name }}</td>
                        <td class="px-4 py-2 border">{{ number_format($budget->amount, 2) }}</td>
                        <td class="px-4 py-2 border">{{ $budget->threshold ? number_format($budget->threshold, 2) : '-' }}</td>
                        <td class="px-4 py-2 border">{{ $budget->currency }}</td>
                        <td class="px-4 py-2 border">{{ $budget->description }}</td>
                        <td class="px-4 py-2 border space-x-2">
                            <a href="{{ route('budgets.edit', $budget) }}" class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600">Edit</a>
                            <form action="{{ route('budgets.destroy', $budget) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600" onclick="return confirm('Delete this budget?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
