<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold text-green-900 tracking-wide animate-fadeIn">
            Donors
        </h1>
    </x-slot>

    <div class="max-w-7xl mx-auto mt-8 px-6 animate-fadeInSlow">
        <!-- Add Donor Button -->
        <div class="flex justify-end mb-6">
            <a href="{{ route('donors.create') }}"
               class="bg-green-700 text-white px-5 py-2.5 rounded-lg shadow hover:bg-green-800 hover:scale-105 transition-all duration-300">
                + Add Donor
            </a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div id="successMessage"
                 class="bg-green-100 text-green-800 border-l-4 border-green-600 px-4 py-3 rounded mb-6 shadow-md animate-slideDown">
                {{ session('success') }}
            </div>

            <script>
                setTimeout(() => {
                    const msg = document.getElementById('successMessage');
                    if (msg) {
                        msg.classList.add('opacity-0', 'transition', 'duration-700');
                        setTimeout(() => msg.remove(), 700);
                    }
                }, 4000);
            </script>
        @endif

        <!-- Donor Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
            <table class="min-w-full border-collapse">
                <thead class="bg-green-100 text-green-900 uppercase text-sm font-semibold">
                    <tr>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Email Address</th>
                        <th class="px-6 py-3 text-left">Phone Number</th>
                        <th class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-green-900">
                    @forelse($donors as $donor)
                        <tr class="hover:bg-green-50 transition-all duration-300 transform hover:scale-[1.01]">
                            <td class="px-6 py-3">{{ $donor->name }}</td>
                            <td class="px-6 py-3">{{ $donor->email ?? '-' }}</td>
                            <td class="px-6 py-3">{{ $donor->phone ?? '-' }}</td>
                            <td class="px-6 py-3 text-center space-x-2">
                                <a href="{{ route('donors.edit', $donor) }}"
                                   class="bg-yellow-500 text-white px-3 py-1.5 rounded-md shadow hover:bg-yellow-600 hover:scale-105 transition-all duration-300">
                                    Edit
                                </a>

                                <form action="{{ route('donors.destroy', $donor) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Are you sure you want to delete this donor?')"
                                            class="bg-red-600 text-white px-3 py-1.5 rounded-md shadow hover:bg-red-700 hover:scale-105 transition-all duration-300">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-6 text-gray-500 italic">
                                No donors found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Animations -->
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInSlow {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.8s ease-out; }
        .animate-fadeInSlow { animation: fadeInSlow 1.2s ease-out; }
        .animate-slideDown { animation: slideDown 0.6s ease-out; }
    </style>
</x-app-layout>
