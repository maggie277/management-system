<x-app-layout>
    <x-slot name="header">
        Staff Management
    </x-slot>

    <div class="space-y-4">
        <a href="{{ route('admin.staff.create') }}" class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800">Add New Staff</a>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto mt-4">
            <table class="min-w-full bg-white border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2">ID</th>
                        <th class="border px-4 py-2">Name</th>
                        <th class="border px-4 py-2">Email</th>
                        <th class="border px-4 py-2">Status</th>
                        <th class="border px-4 py-2">Created By</th>
                        <th class="border px-4 py-2">Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($staff as $s)
                        <tr>
                            <td class="border px-4 py-2">{{ $s->id }}</td>
                            <td class="border px-4 py-2">{{ $s->name }}</td>
                            <td class="border px-4 py-2">{{ $s->email }}</td>
                            <td class="border px-4 py-2">{{ ucfirst($s->status ?? 'active') }}</td>
                            <td class="border px-4 py-2">{{ $s->creator->name ?? 'N/A' }}</td>
                            <td class="border px-4 py-2">{{ $s->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
