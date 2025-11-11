@extends('layouts.app')

@section('title', 'Edit Donor: ' . $donor->name)

@section('content')
<style>
.form-card {
    border-radius: 1rem;
    border: 1px solid rgba(25, 135, 84, 0.15);
    background: linear-gradient(145deg, #ffffff, #f3fef6);
    box-shadow: 0 3px 10px rgba(25, 135, 84, 0.1);
}
.form-card .card-header {
    background: linear-gradient(145deg, #198754, #157347);
    color: white;
    border-radius: 1rem 1rem 0 0 !important;
    border: none;
    padding: 1.5rem;
}
.info-card {
    border-radius: 1rem;
    border: 1px solid rgba(25, 135, 84, 0.15);
    background: linear-gradient(145deg, #ffffff, #f8f9fa);
    box-shadow: 0 3px 10px rgba(25, 135, 84, 0.1);
}
.info-card .card-header {
    background: linear-gradient(145deg, #6c757d, #5a6268);
    color: white;
    border-radius: 1rem 1rem 0 0 !important;
    border: none;
    padding: 1rem 1.5rem;
}
.form-label {
    font-weight: 600;
    color: #198754;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}
.form-control, .form-select {
    border-radius: 0.5rem;
    border: 1px solid #ced4da;
    transition: all 0.3s ease;
    font-size: 0.875rem;
}
.form-control:focus, .form-select:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}
.btn-success {
    background: linear-gradient(145deg, #198754, #157347);
    border: none;
    border-radius: 0.5rem;
    padding: 0.75rem 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
    font-size: 0.875rem;
}
.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3);
}
.btn-outline-secondary {
    border-radius: 0.5rem;
    padding: 0.75rem 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
    font-size: 0.875rem;
}
.btn-outline-secondary:hover {
    transform: translateY(-2px);
}
.required-field::after {
    content: " *";
    color: #dc3545;
}
.current-info {
    font-size: 0.875rem;
}
.current-info strong {
    color: #198754;
}
.badge {
    font-size: 0.75rem;
}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box py-2">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="page-title mb-1" style="font-size: 1.25rem;">Edit Donor: {{ $donor->name }}</h4>

                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('donors.list') }}" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-list-ul me-1"></i> List View
                        </a>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <!-- Main Form -->
                <div class="col-xl-8 col-lg-10">
                    <div class="card form-card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0 text-white">
                                <i class="bi bi-pencil-square me-2"></i>Edit Donor Information
                            </h5>
                            <p class="text-white-50 mb-0 mt-1" style="font-size: 0.875rem;">Update the donor details below</p>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="{{ route('donors.update', $donor) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label required-field">Donor Name</label>
                                            <input type="text" class="form-control" name="name"
                                                   value="{{ old('name', $donor->name) }}" required
                                                   placeholder="Enter donor organization name">
                                            @error('name')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label required-field">Contract Type</label>
                                            <select class="form-select" name="contract_type" required>
                                                <option value="">Select Contract Type</option>
                                                <option value="monthly" {{ old('contract_type', $donor->contract_type) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                                <option value="quarterly" {{ old('contract_type', $donor->contract_type) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                                <option value="yearly" {{ old('contract_type', $donor->contract_type) == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                                <option value="one-time" {{ old('contract_type', $donor->contract_type) == 'one-time' ? 'selected' : '' }}>One-time</option>
                                            </select>
                                            @error('contract_type')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Contact Number</label>
                                            <input type="text" class="form-control" name="contact_number"
                                                   value="{{ old('contact_number', $donor->contact_number) }}"
                                                   placeholder="Enter contact number">
                                            @error('contact_number')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Email Address</label>
                                            <input type="email" class="form-control" name="email"
                                                   value="{{ old('email', $donor->email) }}"
                                                   placeholder="Enter email address">
                                            @error('email')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label required-field">Contract Start Date</label>
                                            <input type="date" class="form-control" name="contract_start_date"
                                                   value="{{ old('contract_start_date', $donor->contract_start_date->format('Y-m-d')) }}" required>
                                            @error('contract_start_date')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label required-field">Donation Amount (Kwacha)</label>
                                            <input type="number" step="0.01" class="form-control" name="donation_amount"
                                                   value="{{ old('donation_amount', $donor->total_budget) }}" required
                                                   placeholder="0.00">
                                            @error('donation_amount')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required-field">Responsible Person</label>
                                    <input type="text" class="form-control" name="responsible_person"
                                           value="{{ old('responsible_person', $donor->responsible_person) }}" required
                                           placeholder="Enter the name of the responsible person">
                                    @error('responsible_person')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Contract Document</label>
                                    @if($donor->document_path)
                                        <div class="mb-2">
                                            <small class="text-muted">Current document:
                                                <a href="{{ route('donors.download-document', $donor) }}" target="_blank" class="text-success">
                                                    {{ basename($donor->document_path) }}
                                                </a>
                                            </small>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control" name="document"
                                           accept=".pdf,.doc,.docx,.jpg,.png,.jpeg">
                                    <div class="form-text">Leave empty to keep current document. Supported formats: PDF, DOC, DOCX, JPG, PNG (Max: 5MB)</div>
                                    @error('document')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Additional Notes</label>
                                    <textarea class="form-control" name="notes" rows="3"
                                              placeholder="Any additional notes about the donor">{{ old('notes', $donor->notes) }}</textarea>
                                    @error('notes')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                    <a href="{{ route('donors.show', $donor) }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-1"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle me-1"></i> Update Donor
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Current Information Sidebar -->
                <div class="col-xl-4 col-lg-6">
                    <div class="card info-card">
                        <div class="card-header">
                            <h5 class="card-title mb-0 text-white">
                                <i class="bi bi-info-circle me-2"></i>Current Information
                            </h5>
                        </div>
                        <div class="card-body p-3">
                            <div class="current-info mb-3">
                                <strong>Contract End Date:</strong><br>
                                <span class="text-dark">{{ $donor->contract_end_date->format('M d, Y') }}</span>
                            </div>
                            <div class="current-info mb-3">
                                <strong>Contract Duration:</strong><br>
                                <span class="text-dark">{{ $donor->contract_duration }}</span>
                            </div>
                            <div class="current-info mb-3">
                                <strong>Status:</strong><br>
                                <span class="badge {{ $donor->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $donor->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            @if($donor->document_path)
                            <div class="current-info mb-3">
                                <strong>Current Document:</strong><br>
                                <a href="{{ route('donors.download-document', $donor) }}" class="btn btn-outline-success btn-sm mt-1">
                                    <i class="bi bi-download me-1"></i> Download
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Add some interactive features
document.addEventListener('DOMContentLoaded', function() {
    // Format amount field on blur
    const amountField = document.querySelector('input[name="donation_amount"]');
    if (amountField) {
        amountField.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    }
});
</script>
@endsection
