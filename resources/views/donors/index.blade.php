@extends('layouts.app')

@section('title', 'Donors Management')

@section('content')
<style>
.stat-card {
    transition: all 0.35s ease;
    border-radius: 1rem;
    border: 1px solid rgba(25, 135, 84, 0.15);
    background: linear-gradient(145deg, #ffffff, #f3fef6);
    box-shadow: 0 3px 10px rgba(25, 135, 84, 0.1);
    min-height: 180px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.stat-card:hover {
    transform: translateY(-6px) scale(1.02);
    box-shadow: 0 10px 20px rgba(25, 135, 84, 0.2);
    background: linear-gradient(145deg, #e9fff0, #ffffff);
}
.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #198754;
    letter-spacing: 0.5px;
    line-height: 1.2;
    word-break: break-word;
}
.stat-label {
    color: #3a3a3a;
    font-weight: 500;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 1px;
    margin-top: 0.5rem;
}
.hover-brighten {
    transition: all 0.3s ease;
}
.hover-brighten:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 6px rgba(25, 135, 84, 0.3);
}
.page-title {
    color: #198754;
    font-weight: 700;
}
.amount-responsive {
    font-size: clamp(1.5rem, 2.5vw, 2rem);
    font-weight: 700;
    color: #198754;
    line-height: 1.2;
}
@media (max-width: 768px) {
    .stat-card {
        min-height: 150px;
        margin-bottom: 1rem;
    }
    .stat-value {
        font-size: 1.5rem;
    }
    .amount-responsive {
        font-size: 1.3rem;
    }
}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="page-title mb-1">Donors Management</h4>
                        <ol class="breadcrumb m-0">
                        </ol>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('donors.list') }}" class="btn btn-success hover-brighten">
                                <i class="bi bi-grid me-1"></i> View All Donors
                            </a>
                            <a href="{{ route('donors.create') }}" class="btn btn-outline-success hover-brighten">
                                <i class="bi bi-plus-circle me-1"></i> Add New Donor
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Donor Statistics -->
            <div class="row mt-5">
                <div class="col-xl-4 col-md-4 mb-4">
                    <div class="stat-card text-center p-4">
                        <div class="stat-value">{{ $totalDonors }}</div>
                        <div class="stat-label">Total Donors</div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-4 mb-4">
                    <div class="stat-card text-center p-4">
                        <div class="amount-responsive">K{{ number_format($totalBudget, 2) }}</div>
                        <div class="stat-label">Total Donations</div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-4 mb-4">
                    <div class="stat-card text-center p-4">
                        <div class="stat-value">{{ $activeContracts }}</div>
                        <div class="stat-label">Active Contracts</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
