@extends('layouts.app')

@section('content')
<style>
.stat-card {
    transition: all 0.35s ease;
    border-radius: 1rem;
    border: 1px solid rgba(25, 135, 84, 0.15);
    background: linear-gradient(145deg, #ffffff, #f3fef6);
    box-shadow: 0 3px 10px rgba(25, 135, 84, 0.1);
}
.stat-card:hover {
    transform: translateY(-6px) scale(1.02);
    box-shadow: 0 10px 20px rgba(25, 135, 84, 0.2);
    background: linear-gradient(145deg, #e9fff0, #ffffff);
}
.stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #198754;
    letter-spacing: 0.5px;
}
.stat-label {
    color: #3a3a3a;
    font-weight: 500;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 1px;
}
.card-header {
    border-bottom: none;
}
.hover-brighten {
    transition: all 0.3s ease;
}
.hover-brighten:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 6px rgba(25, 135, 84, 0.3);
}
</style>

<div class="container-fluid">
    <div class="row justify-content-center mt-4">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold text-success">Assets Dashboard</h5>
                    <div>
                        <a href="{{ route('asset-register') }}" class="btn btn-success me-2 hover-brighten">
                            View Depreciable Assets
                        </a>
                        <a href="{{ route('non-depreciable-assets.index') }}" class="btn btn-success me-2 hover-brighten">
                            View Non-Depreciable Assets
                        </a>
                        <a href="{{ route('assets.create') }}" class="btn btn-outline-success hover-brighten">
                            Add Depreciable Asset
                        </a>
                    </div>
                </div>

                <div class="card-body bg-white">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($assets->count() > 0 || $nonDepreciableAssetsCount > 0)
                    <!-- Depreciable Assets Stats -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="fw-bold text-success mb-3">
                                <i class="bi bi-graph-up me-2"></i>Depreciable Assets
                            </h6>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card text-center p-4">
                                <div class="stat-value">{{ $assets->count() }}</div>
                                <div class="stat-label">Total Depreciable</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card text-center p-4">
                                <div class="stat-value">{{ $assets->where('status', 'active')->count() }}</div>
                                <div class="stat-label">Active</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card text-center p-4">
                                <div class="stat-value">{{ $assets->where('status', 'maintenance')->count() }}</div>
                                <div class="stat-label">Maintenance</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card text-center p-4">
                                <div class="stat-value">{{ $assets->whereIn('status', ['retired', 'lost', 'disposed'])->count() }}</div>
                                <div class="stat-label">Inactive</div>
                            </div>
                        </div>
                    </div>

                    <!-- Non-Depreciable Assets Stats -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="fw-bold text-success mb-3">
                                <i class="bi bi-diamond me-2"></i>Non-Depreciable Assets
                            </h6>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card text-center p-4">
                                <div class="stat-value">{{ $nonDepreciableAssetsCount }}</div>
                                <div class="stat-label">Total Non-Depreciable</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card text-center p-4">
                                <div class="stat-value">{{ $nonDepreciableActiveCount }}</div>
                                <div class="stat-labe-l">Active</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card text-center p-4">
                                <div class="stat-value">{{ $nonDepreciableMaintenanceCount }}</div>
                                <div class="stat-label">Maintenance</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card text-center p-4">
                                <div class="stat-value">{{ $nonDepreciableInactiveCount }}</div>
                                <div class="stat-label">Inactive</div>
                            </div>
                        </div>
                    </div>

                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.btn-success:hover {
    background-color: #157347 !important;
}
.btn-outline-success:hover {
    background-color: #198754 !important;
    color: #fff !important;
}
.border-success {
    border-color: #198754 !important;
}
</style>
@endpush
@endsection
