@extends('layouts.app')

@section('title', 'Edit Consultant')

@section('content')
<style>
.form-card {
    border-radius: 1rem;
    border: 1px solid rgba(25, 135, 84, 0.15);
    box-shadow: 0 3px 10px rgba(25, 135, 84, 0.1);
}
.form-label {
    font-weight: 600;
    color: #198754;
}
.btn-success {
    background-color: #198754;
    border-color: #198754;
}
.btn-success:hover {
    background-color: #0f5c3a;
    border-color: #0f5c3a;
}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card form-card">
                <div class="card-header" style="background: linear-gradient(135deg, #f3fef6 0%, #ffffff 100%); border-bottom: 2px solid #198754;">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="card-title mb-0" style="color: #198754; font-weight: 700;">
                                <i class="bi bi-pencil-square me-2"></i>Edit Consultant
                            </h4>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('consultants.index') }}" class="btn btn-outline-success btn-sm">
                                <i class="bi bi-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('consultants.update', $consultant) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    <i class="bi bi-person me-1"></i>Name *
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $consultant->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope me-1"></i>Email *
                                </label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $consultant->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">
                                    <i class="bi bi-telephone me-1"></i>Phone
                                </label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone', $consultant->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="specialization" class="form-label">
                                    <i class="bi bi-briefcase me-1"></i>Specialization
                                </label>
                                <input type="text" class="form-control @error('specialization') is-invalid @enderror"
                                       id="specialization" name="specialization" value="{{ old('specialization', $consultant->specialization) }}">
                                @error('specialization')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">
                                    <i class="bi bi-toggle-on me-1"></i>Status *
                                </label>
                                <select class="form-control @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                    <option value="active" {{ old('status', $consultant->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $consultant->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="contract_start_date" class="form-label">
                                    <i class="bi bi-calendar-plus me-1"></i>Contract Start Date
                                </label>
                                <input type="date" class="form-control @error('contract_start_date') is-invalid @enderror"
                                       id="contract_start_date" name="contract_start_date"
                                       value="{{ old('contract_start_date', $consultant->contract_start_date ? \Carbon\Carbon::parse($consultant->contract_start_date)->format('Y-m-d') : '') }}">
                                @error('contract_start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="contract_end_date" class="form-label">
                                    <i class="bi bi-calendar-minus me-1"></i>Contract End Date
                                </label>
                                <input type="date" class="form-control @error('contract_end_date') is-invalid @enderror"
                                       id="contract_end_date" name="contract_end_date"
                                       value="{{ old('contract_end_date', $consultant->contract_end_date ? \Carbon\Carbon::parse($consultant->contract_end_date)->format('Y-m-d') : '') }}">
                                @error('contract_end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label">
                                    <i class="bi bi-geo-alt me-1"></i>Address
                                </label>
                                <textarea class="form-control @error('address') is-invalid @enderror"
                                          id="address" name="address" rows="2">{{ old('address', $consultant->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">
                                    <i class="bi bi-file-text me-1"></i>Description
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="3">{{ old('description', $consultant->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i> Update Consultant
                            </button>
                            <a href="{{ route('consultants.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
