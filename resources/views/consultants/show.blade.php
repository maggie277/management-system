@extends('layouts.app')

@section('title', 'Consultant Details')

@section('content')
<style>
.detail-card {
    border-radius: 1rem;
    border: 1px solid rgba(25, 135, 84, 0.15);
    box-shadow: 0 3px 10px rgba(25, 135, 84, 0.1);
}
.detail-label {
    font-weight: 600;
    color: #198754;
    min-width: 150px;
    display: inline-block;
}
.detail-value {
    color: #3a3a3a;
}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card detail-card">
                <div class="card-header" style="background: linear-gradient(135deg, #f3fef6 0%, #ffffff 100%); border-bottom: 2px solid #198754;">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4 class="card-title mb-0" style="color: #198754; font-weight: 700;">
                                <i class="bi bi-person-badge me-2"></i>Consultant Details
                            </h4>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('consultants.edit', $consultant) }}" class="btn btn-success btn-sm">
                                <i class="bi bi-pencil me-1"></i> Edit
                            </a>
                            <a href="{{ route('consultants.index') }}" class="btn btn-outline-success btn-sm">
                                <i class="bi bi-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <span class="detail-label">Name:</span>
                                <span class="detail-value">{{ $consultant->name }}</span>
                            </div>
                            <div class="mb-3">
                                <span class="detail-label">Email:</span>
                                <span class="detail-value">{{ $consultant->email }}</span>
                            </div>
                            <div class="mb-3">
                                <span class="detail-label">Phone:</span>
                                <span class="detail-value">{{ $consultant->phone ?? '-' }}</span>
                            </div>
                            <div class="mb-3">
                                <span class="detail-label">Specialization:</span>
                                <span class="detail-value">{{ $consultant->specialization ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <span class="detail-label">Status:</span>
                                <span class="badge bg-{{ $consultant->status == 'active' ? 'success' : 'danger' }}">
                                    <i class="bi bi-{{ $consultant->status == 'active' ? 'check-circle' : 'x-circle' }} me-1"></i>
                                    {{ ucfirst($consultant->status) }}
                                </span>
                            </div>
                            <div class="mb-3">
                                <span class="detail-label">Contract Start Date:</span>
                                <span class="detail-value">{{ $consultant->contract_start_date ? \Carbon\Carbon::parse($consultant->contract_start_date)->format('d/m/Y') : '-' }}</span>
                            </div>
                            <div class="mb-3">
                                <span class="detail-label">Contract End Date:</span>
                                <span class="detail-value {{ $consultant->contract_end_date && $consultant->contract_end_date < now() ? 'text-danger' : '' }}">
                                    {{ $consultant->contract_end_date ? \Carbon\Carbon::parse($consultant->contract_end_date)->format('d/m/Y') : '-' }}
                                </span>
                            </div>
                            <div class="mb-3">
                                <span class="detail-label">Address:</span>
                                <span class="detail-value">{{ $consultant->address ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mt-2">
                                <span class="detail-label">Description:</span>
                                <div class="detail-value mt-2 p-3" style="background-color: #f8fff9; border-radius: 0.5rem; border-left: 3px solid #198754;">
                                    {{ $consultant->description ?? 'No description provided.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
