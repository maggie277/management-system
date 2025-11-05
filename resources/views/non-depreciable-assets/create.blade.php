@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold text-success">
                        <i class="bi bi-plus-circle me-2"></i>Add New Non-Depreciable Asset
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('non-depreciable-assets.index') }}" class="btn btn-outline-success hover-brighten">
                            <i class="bi bi-arrow-left me-2"></i>Back to Assets
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('non-depreciable-assets.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-bold">
                                            <i class="bi bi-info-circle me-2"></i>Basic Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Year <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control @error('year') is-invalid @enderror"
                                                    id="year" name="year" value="{{ old('year', date('Y')) }}" min="2011" max="{{ date('Y') + 1 }}" required>
                                                @error('year')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Asset Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                    id="name" name="name" value="{{ old('name') }}" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 mb-3">
                                                <label class="form-label fw-bold">Type/Make</label>
                                                <input type="text" class="form-control @error('type_make') is-invalid @enderror"
                                                    id="type_make" name="type_make" value="{{ old('type_make') }}" placeholder="e.g., Land, Artwork, Collectible">
                                                @error('type_make')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 mb-3">
                                                <label class="form-label fw-bold">Description</label>
                                                <textarea class="form-control @error('description') is-invalid @enderror"
                                                    id="description" name="description" rows="3"
                                                    placeholder="Enter asset description">{{ old('description') }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Information -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-bold">
                                            <i class="bi bi-currency-dollar me-2"></i>Financial Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label class="form-label fw-bold">Cost (ZMK) <span class="text-danger">*</span></label>
                                                <input type="number" step="0.01" class="form-control @error('cost') is-invalid @enderror"
                                                    id="cost" name="cost" value="{{ old('cost') }}" min="0" required placeholder="Enter cost in Zambian Kwacha">
                                                @error('cost')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text text-success">This value will remain constant as the net book value.</div>
                                            </div>

                                            <div class="col-12 mb-3">
                                                <label class="form-label fw-bold">Date of Purchase <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control @error('date_of_purchase') is-invalid @enderror"
                                                    id="date_of_purchase" name="date_of_purchase" value="{{ old('date_of_purchase') }}" required>
                                                @error('date_of_purchase')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Identification Details -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-bold">
                                            <i class="bi bi-qr-code me-2"></i>Identification Details
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label class="form-label fw-bold">Serial/Identification No <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('serial_chasis_no') is-invalid @enderror"
                                                    id="serial_chasis_no" name="serial_chasis_no" value="{{ old('serial_chasis_no') }}"
                                                    required placeholder="Enter serial number or identification number">
                                                @error('serial_chasis_no')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 mb-3">
                                                <label class="form-label fw-bold">CTPD Asset Code</label>
                                                <input type="text" class="form-control @error('ctpd_asset_code') is-invalid @enderror"
                                                    id="ctpd_asset_code" name="ctpd_asset_code" value="{{ old('ctpd_asset_code') }}"
                                                    placeholder="Enter CTPD asset code">
                                                @error('ctpd_asset_code')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-bold">
                                            <i class="bi bi-geo-alt me-2"></i>Status & Location
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                                <select class="form-select @error('status') is-invalid @enderror"
                                                    id="status" name="status" required>
                                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                                    <option value="retired" {{ old('status') == 'retired' ? 'selected' : '' }}>Retired</option>
                                                    <option value="lost" {{ old('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                                                    <option value="disposed" {{ old('status') == 'disposed' ? 'selected' : '' }}>Disposed</option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Location</label>
                                                <input type="text" class="form-control @error('location') is-invalid @enderror"
                                                    id="location" name="location" value="{{ old('location') }}"
                                                    placeholder="Enter asset location">
                                                @error('location')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 mb-3">
                                                <label class="form-label fw-bold">Remark</label>
                                                <textarea class="form-control @error('remark') is-invalid @enderror"
                                                    id="remark" name="remark" rows="3"
                                                    placeholder="Enter any remarks">{{ old('remark') }}</textarea>
                                                @error('remark')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="{{ route('non-depreciable-assets.index') }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-x-circle me-2"></i>Cancel
                                        </a>
                                    </div>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle me-2"></i>Create Non-Depreciable Asset
                                    </button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .hover-brighten {
        transition: all 0.3s ease;
    }

    .hover-brighten:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 6px rgba(0, 128, 0, 0.3);
    }

    .btn-outline-success:hover {
        background-color: #198754 !important;
        color: #fff !important;
    }

    .btn-success:hover {
        background-color: #157347 !important;
    }

    .form-control:focus, .form-select:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.2rem rgba(25,135,84,0.25);
    }

    .card {
        border-radius: 10px;
        border: 1px solid #e9ecef;
    }

    .card-header {
        border-bottom: 1px solid #e9ecef;
        background-color: #f8f9fa !important;
    }

    .alert-info {
        background-color: #e6f9ee;
        border-color: #198754;
        color: #155724;
    }
</style>
@endpush
@endsection
