@extends('layouts.app')

@section('content')
<div class="container-fluid py-5" style="background-color: #f8f9fa;">
    <div class="row justify-content-center">
        <div class="col-md-10">

            {{-- Success / Error Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-lg border-0" style="border-radius: 20px;">
                <div class="card-header text-white" style="background-color: #198754; border-radius: 20px 20px 0 0;">
                    <h5 class="mb-0">Add New Asset</h5>
                </div>
                <div class="card-body bg-white p-4">
                    <form action="{{ route('assets.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="card mb-4 shadow-sm border-0 section-card">
                                    <div class="card-header bg-light fw-semibold section-header">Basic Information</div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="year" class="form-label fw-semibold">Year <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control shadow-sm @error('year') is-invalid @enderror"
                                                id="year" name="year" value="{{ old('year', date('Y')) }}" min="2011" max="{{ date('Y') + 1 }}" required>
                                            @error('year')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="name" class="form-label fw-semibold">Asset Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control shadow-sm @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="type_make" class="form-label fw-semibold">Type/Make</label>
                                            <input type="text" class="form-control shadow-sm @error('type_make') is-invalid @enderror"
                                                id="type_make" name="type_make" value="{{ old('type_make') }}" placeholder="e.g., HP w2072a, Toyota Corolla">
                                            @error('type_make')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label fw-semibold">Description</label>
                                            <textarea class="form-control shadow-sm @error('description') is-invalid @enderror"
                                                id="description" name="description" rows="3"
                                                placeholder="Enter asset description">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Information -->
                            <div class="col-md-6">
                                <div class="card mb-4 shadow-sm border-0 section-card">
                                    <div class="card-header bg-light fw-semibold section-header">Financial Information</div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="cost" class="form-label fw-semibold">Cost (ZMK) <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" class="form-control shadow-sm @error('cost') is-invalid @enderror"
                                                id="cost" name="cost" value="{{ old('cost') }}" min="0" required placeholder="Enter cost in Zambian Kwacha">
                                            @error('cost')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="date_of_purchase" class="form-label fw-semibold">Date of Purchase <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control shadow-sm @error('date_of_purchase') is-invalid @enderror"
                                                id="date_of_purchase" name="date_of_purchase" value="{{ old('date_of_purchase') }}" required>
                                            @error('date_of_purchase')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="depreciation_rate" class="form-label fw-semibold">Depreciation Rate (%) <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" class="form-control shadow-sm @error('depreciation_rate') is-invalid @enderror"
                                                id="depreciation_rate" name="depreciation_rate" value="{{ old('depreciation_rate', 33) }}"
                                                min="0" max="100" required>
                                            @error('depreciation_rate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="depreciation_method" class="form-label fw-semibold">Depreciation Method <span class="text-danger">*</span></label>
                                            <select class="form-select shadow-sm @error('depreciation_method') is-invalid @enderror"
                                                id="depreciation_method" name="depreciation_method" required>
                                                <option value="straight_line" {{ old('depreciation_method') == 'straight_line' ? 'selected' : '' }}>Straight Line</option>
                                                <option value="reducing_balance" {{ old('depreciation_method') == 'reducing_balance' ? 'selected' : '' }}>Reducing Balance</option>
                                            </select>
                                            @error('depreciation_method')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Identification Details -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4 shadow-sm border-0 section-card">
                                    <div class="card-header bg-light fw-semibold section-header">Identification Details</div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="serial_chasis_no" class="form-label fw-semibold">Serial/Chasis No <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control shadow-sm @error('serial_chasis_no') is-invalid @enderror"
                                                id="serial_chasis_no" name="serial_chasis_no" value="{{ old('serial_chasis_no') }}"
                                                required placeholder="Enter serial number or chasis number">
                                            @error('serial_chasis_no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Enter either the serial number or chasis number for this asset.</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="ctpd_asset_code" class="form-label fw-semibold">CTPD Asset Code</label>
                                            <input type="text" class="form-control shadow-sm @error('ctpd_asset_code') is-invalid @enderror"
                                                id="ctpd_asset_code" name="ctpd_asset_code" value="{{ old('ctpd_asset_code') }}"
                                                placeholder="Enter CTPD asset code">
                                            @error('ctpd_asset_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card mb-4 shadow-sm border-0 section-card">
                                    <div class="card-header bg-light fw-semibold section-header">Status & Location</div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                            <select class="form-select shadow-sm @error('status') is-invalid @enderror"
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

                                        <div class="mb-3">
                                            <label for="location" class="form-label fw-semibold">Location</label>
                                            <input type="text" class="form-control shadow-sm @error('location') is-invalid @enderror"
                                                id="location" name="location" value="{{ old('location') }}"
                                                placeholder="Enter asset location">
                                            @error('location')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="remark" class="form-label fw-semibold">Remark</label>
                                            <textarea class="form-control shadow-sm @error('remark') is-invalid @enderror"
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

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('assets.index') }}" class="btn btn-outline-success fw-semibold custom-btn">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-success fw-semibold custom-btn">
                                Create Asset
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Styles --}}
<style>
    body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
    .card { border-radius: 20px; }
    .section-card { border-radius: 15px; transition: all 0.3s ease; }
    .section-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.08); }
    .section-header { background-color: #f1f3f4 !important; border-radius: 15px 15px 0 0; }
    .form-control:focus, .form-select:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.2rem rgba(25,135,84,0.25);
    }
    .custom-btn {
        background-color: white;
        color: #198754;
        border: 2px solid #198754;
        border-radius: 10px;
        padding: 8px 20px;
        transition: all 0.3s ease;
    }
    .custom-btn:hover {
        background-color: #198754;
        color: white;
        transform: translateY(-1px);
    }
    .alert-success {
        background-color: #e6f9ee;
        border-left: 5px solid #198754;
        color: #155724;
        border-radius: 10px;
    }
    .alert-danger {
        background-color: #fcebea;
        border-left: 5px solid #dc3545;
        color: #721c24;
        border-radius: 10px;
    }
</style>
@endsection
