<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold text-green-900 animate-fadeIn"> Add Donor</h1>
    </x-slot>

    <div class="container mt-8 max-w-2xl mx-auto animate-slideUp">
        <div class="bg-white shadow-lg rounded-2xl p-8 border border-green-100 transition-transform transform hover:scale-[1.01] hover:shadow-2xl">
            <form action="{{ route('donors.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-lg font-medium text-green-900 mb-2">Name</label>
                    <input type="text" name="name"
                        class="w-full border border-green-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-600 focus:outline-none transition duration-200"
                        placeholder="Enter donor name" required>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-lg font-medium text-green-900 mb-2">Email</label>
                    <input type="email" name="email"
                        class="w-full border border-green-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-600 focus:outline-none transition duration-200"
                        placeholder="Enter donor email">
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-lg font-medium text-green-900 mb-2">Phone</label>
                    <input type="text" name="phone"
                        class="w-full border border-green-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-600 focus:outline-none transition duration-200"
                        placeholder="Enter donor phone number">
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('donors.index') }}"
                        class="bg-gray-400 text-white px-5 py-2 rounded-xl hover:bg-gray-500 transition-all duration-200 transform hover:-translate-y-1 hover:shadow-lg">
                        Cancel
                    </a>
                    <button type="submit"
                        class="bg-green-700 text-white px-5 py-2 rounded-xl hover:bg-green-800 transition-all duration-200 transform hover:-translate-y-1 hover:shadow-lg">
                        Save Donor
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tailwind Animations -->
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fadeIn {
            animation: fadeIn 0.6s ease-out forwards;
        }

        .animate-slideUp {
            animation: slideUp 0.8s ease-out forwards;
        }
    </style>
</x-app-layout>
