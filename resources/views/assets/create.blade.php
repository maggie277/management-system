<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-green-900 leading-tight tracking-wide">
            Add New Asset
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg sm:rounded-xl p-8 border border-gray-200">

                <!-- Success Message -->
                @if(session('success'))
                    <div id="successMessage" class="bg-green-100 text-green-800 p-4 rounded mb-6 shadow-inner animate-slideDown">
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

                <!-- Errors -->
                @if($errors->any())
                    <div class="bg-red-100 text-red-800 p-4 rounded mb-6 shadow-inner animate-slideDown">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('assets.store') }}" class="space-y-6">
                    @csrf

                    @php
                        $fields = [
                            'name' => 'Asset Name',
                            'owner' => 'Asset Owner',
                            'category' => 'Category',
                            'description' => 'Description',
                            'serial_number' => 'Serial Number',
                            'location' => 'Location',
                            'purchase_date' => 'Purchase Date',
                            'value' => 'Value',
                            'currency' => 'Currency',
                        ];
                    @endphp

                    @foreach($fields as $key => $label)
                        <div class="relative">
                            <label class="block text-green-900 font-medium mb-1">{{ $label }}</label>

                            @if($key === 'description')
                                <textarea name="{{ $key }}" rows="4"
                                          class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300"
                                          placeholder="Enter {{ strtolower($label) }}">{{ old($key) }}</textarea>
                            @elseif($key === 'purchase_date')
                                <input type="date" name="{{ $key }}" value="{{ old($key) }}"
                                       class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300">
                            @elseif($key === 'value')
                                <input type="number" step="0.01" name="{{ $key }}" value="{{ old($key) }}"
                                       class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300"
                                       placeholder="Enter {{ strtolower($label) }}">
                            @else
                                <input type="text" name="{{ $key }}" value="{{ old($key) }}"
                                       class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300"
                                       placeholder="Enter {{ strtolower($label) }}">
                            @endif
                        </div>
                    @endforeach

                    <!-- Status -->
                    <div class="relative">
                        <label class="block text-green-900 font-medium mb-1">Status</label>
                        <select name="status"
                                class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:border-green-400 transition duration-300 hover:border-green-300" required>
                            <option value="active" {{ old('status')=='active'?'selected':'' }}>Active</option>
                            <option value="maintenance" {{ old('status')=='maintenance'?'selected':'' }}>Maintenance</option>
                            <option value="disposed" {{ old('status')=='disposed'?'selected':'' }}>Disposed</option>
                        </select>
                    </div>

                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg shadow-lg transform transition duration-300 hover:scale-105 hover:shadow-2xl">
                        Save Asset
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
