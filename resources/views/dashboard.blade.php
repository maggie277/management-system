<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-green-900 leading-tight">Dashboard Overview</h2>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto px-6 space-y-8">
        <!-- Top Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-yellow-100 border-l-4 border-yellow-600 p-4 rounded-lg text-center shadow">
                <p class="text-sm text-gray-600">Donors</p>
                <h3 id="donorCount" class="text-2xl font-bold text-yellow-700">{{ $totals['donors'] ?? 0 }}</h3>
            </div>

            <div class="bg-blue-100 border-l-4 border-blue-600 p-4 rounded-lg text-center shadow">
                <p class="text-sm text-gray-600">Assets</p>
                <h3 id="assetCount" class="text-2xl font-bold text-blue-700">{{ $totals['assets'] ?? 0 }}</h3>
            </div>

            <div class="bg-gray-100 border-l-4 border-gray-600 p-4 rounded-lg text-center shadow">
                <p class="text-sm text-gray-600">Documents</p>
                <h3 id="docCount" class="text-2xl font-bold text-gray-700">{{ $totals['documents'] ?? 0 }}</h3>
            </div>

            <div class="bg-green-100 border-l-4 border-green-700 p-4 rounded-lg text-center shadow">
                <p class="text-sm text-gray-600">All Employees</p>
                <h3 id="employeesCount" class="text-2xl font-bold text-green-800">{{ $totals['staff'] ?? 0 }}</h3>
            </div>
        </div>

        <!-- System Overview Chart -->
        <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
            <div class="bg-white p-6 rounded-xl shadow">
                <h3 class="text-green-900 font-semibold mb-4">System Overview</h3>
                <canvas id="overviewChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const overviewCtx = document.getElementById('overviewChart');
        let overviewChart;

        function renderCharts(data) {
            if (overviewChart) overviewChart.destroy();

            overviewChart = new Chart(overviewCtx, {
                type: 'bar',
                data: {
                    labels: ['Donors', 'Assets', 'Documents', 'All Employees'],
                    datasets: [{
                        label: 'Count',
                        data: [data.donors, data.assets, data.documents, data.staff],
                        backgroundColor: ['#facc15', '#3b82f6', '#6b7280', '#22c55e']
                    }]
                },
                options: {
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        function updateStats() {
            fetch('/dashboard/stats')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('donorCount').textContent = data.donors;
                    document.getElementById('assetCount').textContent = data.assets;
                    document.getElementById('docCount').textContent = data.documents;
                    document.getElementById('employeesCount').textContent = data.staff;
                    renderCharts(data);
                });
        }

        updateStats();
        setInterval(updateStats, 15000); // Refresh every 15s
    </script>
</x-app-layout>
