<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-green-900 leading-tight tracking-wide">
            All Assets
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Success Message -->
            @if(session('success'))
                <div id="successMessage"
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

            <div class="bg-white shadow-lg sm:rounded-xl p-6 border border-gray-200">

                @if($assets->isEmpty())
                    <p class="text-gray-700 text-lg">No assets added yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-300 divide-y divide-gray-200">
                            <thead class="bg-green-100 text-green-900 font-semibold uppercase text-sm">
                                <tr>
                                    <th class="p-3 text-left">Name</th>
                                    <th class="p-3 text-left">Category</th>
                                    <th class="p-3 text-left">Serial</th>
                                    <th class="p-3 text-left">Location</th>
                                    <th class="p-3 text-left">Value</th>
                                    <th class="p-3 text-left">Status</th>
                                    <th class="p-3 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($assets as $asset)
                                    <tr class="transition duration-300 hover:bg-green-50 hover:shadow-md">
                                        <td class="p-3">{{ $asset->name }}</td>
                                        <td class="p-3">{{ $asset->category ?? '-' }}</td>
                                        <td class="p-3">{{ $asset->serial_number ?? '-' }}</td>
                                        <td class="p-3">{{ $asset->location ?? '-' }}</td>
                                        <td class="p-3">
                                            {{ $asset->currency ?? '$' }}{{ number_format($asset->value, 2) }}
                                        </td>
                                        <td class="p-3">
                                            @php
                                                $statusColors = [
                                                    'active' => 'bg-green-100 text-green-800',
                                                    'maintenance' => 'bg-yellow-100 text-yellow-800',
                                                    'disposed' => 'bg-red-100 text-red-800'
                                                ];
                                            @endphp
                                            <span class="px-2 py-1 rounded-full text-sm font-semibold {{ $statusColors[$asset->status] }}">
                                                {{ ucfirst($asset->status) }}
                                            </span>
                                        </td>
                                        <td class="p-3 flex space-x-2">
                                            <a href="{{ route('assets.edit', $asset->id) }}"
                                               class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded shadow hover:scale-105 transform transition duration-300">
                                               Edit
                                            </a>
                                            <form method="POST" action="{{ route('assets.destroy', $asset->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        onclick="return confirm('Are you sure?')"
                                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded shadow hover:scale-105 transform transition duration-300">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
