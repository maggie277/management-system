@extends('layouts.app')

@section('title', 'Donor Details: ' . $donor->name)

@section('content')
<style>
.detail-card {
    border-radius: 1rem;
    border: 1px solid rgba(25, 135, 84, 0.15);
    background: linear-gradient(145deg, #ffffff, #f3fef6);
    box-shadow: 0 3px 10px rgba(25, 135, 84, 0.1);
    transition: all 0.3s ease;
}
.detail-card .card-header {
    background: linear-gradient(145deg, #198754, #157347);
    color: white;
    border-radius: 1rem 1rem 0 0 !important;
    border: none;
    padding: 1rem 1.5rem;
}
.detail-card .card-header h5 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
}
.info-table {
    font-size: 0.875rem;
}
.info-table th {
    color: #198754;
    font-weight: 600;
    width: 40%;
    padding: 0.75rem 0.5rem;
    border-bottom: 1px solid rgba(25, 135, 84, 0.1);
}
.info-table td {
    padding: 0.75rem 0.5rem;
    border-bottom: 1px solid rgba(25, 135, 84, 0.1);
    font-weight: 500;
}
.badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
}
.person-avatar {
    width: 36px;
    height: 36px;
    background: linear-gradient(145deg, #198754, #157347);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.person-avatar i {
    font-size: 0.9rem;
    color: white;
}
.notes-content {
    font-size: 0.875rem;
    line-height: 1.5;
    color: #495057;
}
.btn-sm {
    padding: 0.4rem 0.8rem;
    font-size: 0.8rem;
    border-radius: 0.5rem;
}
.hover-brighten {
    transition: all 0.3s ease;
}
.hover-brighten:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3);
}
.amount-display {
    font-size: 1.1rem;
    font-weight: 700;
    color: #198754;
}
.btn-outline-primary {
    border-color: #198754;
    color: #198754;
}
.btn-outline-primary:hover {
    background-color: #198754;
    border-color: #198754;
    color: white;
}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box py-2">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="page-title mb-1" style="font-size: 1.25rem;">Donor Details: {{ $donor->name }}</h4>

                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('donors.edit', $donor) }}" class="btn btn-outline-primary btn-sm hover-brighten">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>
                        <a href="{{ route('donors.list') }}" class="btn btn-outline-success btn-sm hover-brighten">
                            <i class="bi bi-list-ul me-1"></i> List View
                        </a>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2" role="alert" style="font-size: 0.875rem;">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row justify-content-center">
                <!-- Left Column -->
                <div class="col-xl-5 col-lg-6">
                    <!-- Donor Information -->
                    <div class="card detail-card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-person-badge me-2"></i>Donor Information
                            </h5>
                        </div>
                        <div class="card-body p-3">
                            <table class="table table-borderless info-table mb-0">
                                <tr>
                                    <th>Donor Name</th>
                                    <td>{{ $donor->name }}</td>
                                </tr>
                                <tr>
                                    <th>Contact Number</th>
                                    <td>{{ $donor->contact_number ?? 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <th>Email Address</th>
                                    <td>{{ $donor->email ?? 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <th>Contract Type</th>
                                    <td>
                                        <span class="badge bg-success text-capitalize">
                                            {{ $donor->contract_type }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Contract Period</th>
                                    <td>
                                        <div class="small">
                                            <div><strong>Start:</strong> {{ $donor->contract_start_date->format('M d, Y') }}</div>
                                            <div><strong>End:</strong> {{ $donor->contract_end_date->format('M d, Y') }}</div>
                                            @if(!$donor->is_contract_active)
                                                <span class="badge bg-danger mt-1">Expired</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge {{ $donor->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $donor->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                @if($donor->document_path)
                                <tr>
                                    <th>Contract Document</th>
                                    <td>
                                        <a href="{{ route('donors.download-document', $donor) }}"
                                           class="btn btn-outline-success btn-sm hover-brighten">
                                            <i class="bi bi-download me-1"></i> Download
                                        </a>
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Budget Information -->
                    <div class="card detail-card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-currency-dollar me-2"></i>Budget Information
                            </h5>
                        </div>
                        <div class="card-body p-3">
                            <table class="table table-borderless info-table mb-0">
                                <tr>
                                    <th>Total Donation</th>
                                    <td class="amount-display">K{{ number_format($donor->total_budget, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-xl-5 col-lg-6">
                    <!-- Responsible Person -->
                    <div class="card detail-card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-person-check me-2"></i>Responsible Person
                            </h5>
                        </div>
                        <div class="card-body p-3">
                            @if($donor->responsible_person)
                                <div class="d-flex align-items-center">
                                    <div class="person-avatar me-3">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1" style="font-size: 0.95rem; color: #198754;">{{ $donor->responsible_person }}</h6>
                                        <small class="text-muted" style="font-size: 0.8rem;">Assigned Responsible Person</small>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <i class="bi bi-person-x display-6 text-muted mb-2"></i>
                                    <p class="text-muted mb-0" style="font-size: 0.875rem;">No responsible person assigned</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="card detail-card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-journal-text me-2"></i>Additional Notes
                            </h5>
                        </div>
                        <div class="card-body p-3">
                            @if($donor->notes)
                                <div class="notes-content">
                                    {{ $donor->notes }}
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <i class="bi bi-journal display-6 text-muted mb-2"></i>
                                    <p class="text-muted mb-0" style="font-size: 0.875rem;">No notes available</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
