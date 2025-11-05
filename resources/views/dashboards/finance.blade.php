<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 fw-bold text-dark mb-0">
            <i class="bi bi-cash-coin me-2"></i>Finance Dashboard
        </h2>
    </x-slot>

    <div class="container-fluid py-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h3>Welcome, {{ $user->name }}!</h3>
                <p class="text-muted">Finance and Administration</p>
                <p>This is the Finance dashboard for {{ $user->position }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
