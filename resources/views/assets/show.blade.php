@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold text-success">
                        <i class="bi bi-eye me-2"></i>Asset Details: {{ $asset->name }}
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('assets.index') }}" class="btn btn-outline-success hover-brighten">
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

                    <form action="{{ route('assets.update', $asset) }}" method="POST" id="assetForm">
                        @csrf
                        @method('PUT')

                        <!-- Add this hidden field -->
                        <input type="hidden" name="is_depreciable" value="1">

                        <div class="row">
                            <!-- Asset Information Card -->
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
                                                <label class="form-label fw-bold">Asset Name *</label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                       name="name" value="{{ old('name', $asset->name) }}" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Type/Make</label>
                                                <input type="text" class="form-control @error('type_make') is-invalid @enderror"
                                                       name="type_make" value="{{ old('type_make', $asset->type_make) }}">
                                                @error('type_make')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Cost (ZMK) *</label>
                                                <input type="number" step="0.01" class="form-control @error('cost') is-invalid @enderror"
                                                       name="cost" value="{{ old('cost', $asset->cost) }}" required>
                                                @error('cost')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Purchase Date *</label>
                                                <input type="date" class="form-control @error('date_of_purchase') is-invalid @enderror"
                                                       name="date_of_purchase" value="{{ old('date_of_purchase', $asset->date_of_purchase->format('Y-m-d')) }}" required>
                                                @error('date_of_purchase')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Serial/Chasis No *</label>
                                                <input type="text" class="form-control @error('serial_chasis_no') is-invalid @enderror"
                                                       name="serial_chasis_no" value="{{ old('serial_chasis_no', $asset->serial_chasis_no) }}" required>
                                                @error('serial_chasis_no')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">CTPD Asset Code</label>
                                                <input type="text" class="form-control @error('ctpd_asset_code') is-invalid @enderror"
                                                       name="ctpd_asset_code" value="{{ old('ctpd_asset_code', $asset->ctpd_asset_code) }}">
                                                @error('ctpd_asset_code')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Status *</label>
                                                <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                                    <option value="active" {{ old('status', $asset->status) == 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="maintenance" {{ old('status', $asset->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                                    <option value="retired" {{ old('status', $asset->status) == 'retired' ? 'selected' : '' }}>Retired</option>
                                                    <option value="lost" {{ old('status', $asset->status) == 'lost' ? 'selected' : '' }}>Lost</option>
                                                    <option value="disposed" {{ old('status', $asset->status) == 'disposed' ? 'selected' : '' }}>Disposed</option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Depreciation Rate (%) *</label>
                                                <input type="number" step="0.01" class="form-control @error('depreciation_rate') is-invalid @enderror"
                                                       name="depreciation_rate" value="{{ old('depreciation_rate', $asset->depreciation_rate) }}" required>
                                                @error('depreciation_rate')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Depreciation Method</label>
                                                <input type="text" class="form-control bg-light" value="{{ ucfirst(str_replace('_', ' ', $asset->depreciation_method)) }}" readonly>
                                                <input type="hidden" name="depreciation_method" value="{{ $asset->depreciation_method }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Location</label>
                                                <input type="text" class="form-control @error('location') is-invalid @enderror"
                                                       name="location" value="{{ old('location', $asset->location) }}">
                                                @error('location')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Year</label>
                                                <input type="text" class="form-control bg-light" value="{{ $asset->year }}" readonly>
                                                <input type="hidden" name="year" value="{{ $asset->year }}">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label fw-bold">Description</label>
                                                <textarea class="form-control @error('description') is-invalid @enderror"
                                                          name="description" rows="3">{{ old('description', $asset->description) }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label fw-bold">Remark</label>
                                                <textarea class="form-control @error('remark') is-invalid @enderror"
                                                          name="remark" rows="2">{{ old('remark', $asset->remark) }}</textarea>
                                                @error('remark')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Depreciation & Financial Information -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-bold">
                                            <i class="bi bi-graph-up me-2"></i>Financial Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 mb-3">
                                                <label class="form-label fw-bold text-dark">Original Cost</label>
                                                <div class="fs-5 fw-bold text-dark">ZMK {{ number_format($asset->cost, 2) }}</div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label class="form-label fw-bold text-dark">Accumulated Depreciation</label>
                                                <div class="fs-5 fw-bold text-dark">ZMK {{ number_format($asset->accumulated_depreciation, 2) }}</div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label class="form-label fw-bold text-dark">Net Book Value</label>
                                                <div class="fs-5 fw-bold text-success">ZMK {{ number_format($asset->net_book_value, 2) }}</div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label class="form-label fw-bold text-dark">Depreciation Rate</label>
                                                <div class="fs-5 fw-bold text-dark">{{ $asset->depreciation_rate }}%</div>
                                            </div>
                                        </div>

                                        <!-- Current Year Depreciation -->
                                        <div class="alert alert-success">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <strong class="text-dark">Current Year ({{ $currentYear }}) Depreciation:</strong>
                                                <span class="fs-6 fw-bold text-dark">ZMK {{ number_format($asset->getDepreciationForYear($currentYear), 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Depreciation Schedule -->
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-bold">
                                            <i class="bi bi-calendar-range me-2"></i>Depreciation Schedule
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive" style="max-height: 300px;">
                                            <table class="table table-sm table-bordered" style="font-size: 0.75rem;">
                                                <thead class="table-success">
                                                    <tr>
                                                        <th class="text-dark">Year</th>
                                                        <th class="text-dark">Depreciation</th>
                                                        <th class="text-dark">Accumulated</th>
                                                        <th class="text-dark">Net Book Value</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $purchaseYear = $asset->date_of_purchase->year;
                                                        $accumulated = 0;
                                                    @endphp
                                                    @for($year = $purchaseYear; $year <= $currentYear; $year++)
                                                        @php
                                                            $yearDepreciation = $asset->getDepreciationForYear($year);
                                                            $accumulated += $yearDepreciation;
                                                            $nbv = max(0, $asset->cost - $accumulated);
                                                        @endphp
                                                        <tr class="{{ $year == $currentYear ? 'table-warning' : '' }}">
                                                            <td class="text-dark">{{ $year }}{{ $year == $currentYear ? ' (Current)' : '' }}</td>
                                                            <td class="text-end text-dark">ZMK {{ number_format($yearDepreciation, 2) }}</td>
                                                            <td class="text-end text-dark">ZMK {{ number_format($accumulated, 2) }}</td>
                                                            <td class="text-end fw-bold text-success">ZMK {{ number_format($nbv, 2) }}</td>
                                                        </tr>
                                                        @if($nbv <= 0) @break @endif
                                                    @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <button type="submit" class="btn btn-success hover-brighten">
                                            <i class="bi bi-check-circle me-2"></i>Update Asset
                                        </button>
                                        <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-x-circle me-2"></i>Cancel
                                        </a>
                                    </div>
                                    <button type="button" class="btn btn-outline-danger hover-brighten"
                                            onclick="confirmDelete()">
                                        <i class="bi bi-trash me-2"></i>Delete Asset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Delete Form (Hidden) -->
                    <form action="{{ route('assets.destroy', $asset) }}" method="POST" id="deleteForm" class="d-none">
                        @csrf
                        @method('DELETE')
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

    .btn-outline-danger:hover {
        background-color: #dc3545 !important;
        color: #fff !important;
    }

    .form-control:focus, .form-select:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.2rem rgba(25,135,84,0.25);
    }

    .table-success {
        background-color: #198754 !important;
    }

    .table-success th {
        color: white !important;
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }

    .text-dark {
        color: #212529 !important;
    }

    .alert-success {
        background-color: #e6f9ee;
        border-color: #198754;
        color: #155724;
    }

    .form-control[readonly] {
        background-color: #f8f9fa;
        border-color: #e9ecef;
        color: #6c757d;
    }
</style>
@endpush

<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this asset? This action cannot be undone.')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endsection
