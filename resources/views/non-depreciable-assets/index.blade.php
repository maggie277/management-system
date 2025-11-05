@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold text-success">
                        NON-DEPRECIABLE ASSETS REGISTER
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('assets.index') }}" class="btn btn-outline-success hover-brighten">
                            <i class="bi bi-arrow-left me-2"></i>View Depreciable Assets
                        </a>
                        <a href="{{ route('non-depreciable-assets.create') }}" class="btn btn-success hover-brighten">
                            <i class="bi bi-plus-circle me-2"></i>Add Non-Depreciable Asset
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive" style="font-size: 0.7rem;">
                        <table class="table table-bordered table-sm mb-0" id="assetTable">
                            <thead class="table-success text-white" style="position: sticky; top: 0; z-index: 10;">
                                <tr>
                                    <th class="text-center align-middle fixed-column">Asset Name</th>
                                    <th class="text-center align-middle fixed-column">Type/Make</th>
                                    <th class="text-center align-middle fixed-column">Cost (ZMK)</th>
                                    <th class="text-center align-middle fixed-column">Date of Purchase</th>
                                    <th class="text-center align-middle fixed-column">Serial/Identification No</th>
                                    <th class="text-center align-middle fixed-column">CTPD Asset Code</th>
                                    <th class="text-center align-middle fixed-column">Status</th>
                                    <th class="text-center align-middle fixed-column">Net Book Value</th>
                                    <th class="text-center align-middle fixed-column">Location</th>
                                    <th class="text-center align-middle fixed-column">Remark</th>
                                    <th class="text-center align-middle fixed-column">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assets as $asset)
                                    <tr>
                                        <td class="fw-bold fixed-column text-success"><strong>{{ $asset->name }}</strong></td>
                                        <td class="fixed-column">{{ $asset->type_make ?? 'N/A' }}</td>
                                        <td class="text-end fw-bold fixed-column">{{ number_format($asset->cost, 2) }}</td>
                                        <td class="text-center fixed-column">{{ $asset->date_of_purchase ? $asset->date_of_purchase->format('d/m/Y') : 'N/A' }}</td>
                                        <td class="fixed-column">{{ $asset->serial_chasis_no ?? 'N/A' }}</td>
                                        <td class="fixed-column">{{ $asset->ctpd_asset_code ?? 'N/A' }}</td>
                                        <td class="text-center fixed-column">
                                            @php
                                                $statusColors = [
                                                    'active' => 'success',
                                                    'maintenance' => 'warning',
                                                    'retired' => 'secondary',
                                                    'lost' => 'danger',
                                                    'disposed' => 'dark'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$asset->status] ?? 'secondary' }}">
                                                {{ ucfirst($asset->status) }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold fixed-column text-success">
                                            <strong>{{ number_format($asset->net_book_value, 2) }}</strong>
                                        </td>
                                        <td class="fixed-column">{{ $asset->location ?? 'N/A' }}</td>
                                        <td class="fixed-column">{{ $asset->remark ?? 'N/A' }}</td>

                                        <td class="text-center fixed-column">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('non-depreciable-assets.show', $asset) }}"
                                                   class="btn btn-outline-success btn-sm hover-brighten"
                                                   data-bs-toggle="tooltip"
                                                   title="View & Edit Asset Details">
                                                    <i class="bi bi-eye"></i> View
                                                </a>

                                                <form action="{{ route('non-depreciable-assets.destroy', $asset) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this non-depreciable asset?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-outline-danger btn-sm hover-brighten"
                                                            data-bs-toggle="tooltip"
                                                            title="Delete Asset">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                            No non-depreciable assets found. <a href="{{ route('non-depreciable-assets.create') }}">Create the first non-depreciable asset</a>.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .fixed-column {
        background-color: #ffffff !important;
        position: sticky;
        left: 0;
        z-index: 5;
    }

    .table-responsive {
        max-height: 70vh;
        overflow: auto;
    }

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

    .btn-outline-danger:hover {
        background-color: #dc3545 !important;
        color: #fff !important;
    }

    .btn-success:hover {
        background-color: #157347 !important;
    }

    .table-success {
        background-color: #198754 !important;
    }

    .text-success {
        color: #198754 !important;
    }

    .table-bordered {
        border: 1px solid #dee2e6;
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid #dee2e6;
        padding: 0.5rem;
    }

    .badge {
        font-size: 0.65rem;
        padding: 0.25rem 0.5rem;
    }
</style>
@endpush
