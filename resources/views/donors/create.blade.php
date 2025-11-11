@extends('layouts.app')

@section('title', 'Add New Donor')

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
.form-label {
    font-weight: 600;
    color: #198754;
    margin-bottom: 0.5rem;
}
.form-control, .form-select {
    border-radius: 0.5rem;
    border: 1px solid #ced4da;
    transition: all 0.3s ease;
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
}
.btn-outline-secondary:hover {
    transform: translateY(-2px);
}
.required-field::after {
    content: " *";
    color: #dc3545;
}
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="page-title-box">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="page-title mb-1">Add New Donor</h4>

                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('donors.list') }}" class="btn btn-outline-success">
                            <i class="bi bi-list-ul me-1"></i> List View
                        </a>
                    </div>
                </div>
            </div>

            <!-- Centered Form -->
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-10">
                    <div class="card form-card">
                        <div class="card-header">
                            <h4 class="card-title mb-0 text-white">
                                <i class="bi bi-person-plus me-2"></i>Donor Information
                            </h4>
                            <p class="text-white-50 mb-0 mt-1">Fill in the details below to add a new donor</p>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="{{ route('donors.store') }}" enctype="multipart/form-data">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label required-field">Donor Name</label>
                                            <input type="text" class="form-control" name="name"
                                                   value="{{ old('name') }}" required
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
                                                <option value="monthly" {{ old('contract_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                                <option value="quarterly" {{ old('contract_type') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                                <option value="yearly" {{ old('contract_type') == 'yearly' ? 'selected' : '' }}>Yearly</option>

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
                                                   value="{{ old('contact_number') }}"
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
                                                   value="{{ old('email') }}"
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
                                                   value="{{ old('contract_start_date') }}" required>
                                            @error('contract_start_date')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label required-field">Donation Amount (Kwacha)</label>
                                            <input type="number" step="0.01" class="form-control" name="donation_amount"
                                                   value="{{ old('donation_amount') }}" required
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
                                           value="{{ old('responsible_person') }}" required
                                           placeholder="Enter the name of the responsible person">
                                    @error('responsible_person')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Contract Document</label>
                                    <input type="file" class="form-control" name="document"
                                           accept=".pdf,.doc,.docx,.jpg,.png,.jpeg">
                                    <div class="form-text">Supported formats: PDF, DOC, DOCX, JPG, PNG (Max: 5MB)</div>
                                    @error('document')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Additional Notes</label>
                                    <textarea class="form-control" name="notes" rows="3"
                                              placeholder="Any additional notes about the donor">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                    <a href="{{ route('donors.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-1"></i> Back to Donors
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-person-plus me-1"></i> Create Donor
                                    </button>
                                </div>
                            </form>
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
    // Add focus styles
    const inputs = document.querySelectorAll('.form-control, .form-select');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focus');
        });
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focus');
        });
    });

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
