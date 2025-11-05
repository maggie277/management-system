@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold text-success">
                        COMPREHENSIVE ASSET REGISTER WITH DEPRECIATION
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('depreciation-years.index') }}" class="btn btn-outline-success hover-brighten">
                            <i class="bi bi-columns-gap me-2"></i>Manage Year Columns
                        </a>
                        <a href="{{ route('assets.create') }}" class="btn btn-success hover-brighten">
                            <i class="bi bi-plus-circle me-2"></i>Add Asset
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
                                    <th rowspan="2" class="text-center align-middle fixed-column">Asset</th>
                                    <th rowspan="2" class="text-center align-middle fixed-column">Type/Make</th>
                                    <th rowspan="2" class="text-center align-middle fixed-column">Cost (ZMK)</th>
                                    <th rowspan="2" class="text-center align-middle fixed-column">Date of Purchase</th>
                                    <th rowspan="2" class="text-center align-middle fixed-column">Serial/Chasis No</th>
                                    <th rowspan="2" class="text-center align-middle fixed-column">CTPD Asset Code</th>
                                    <th rowspan="2" class="text-center align-middle fixed-column">Status</th>
                                    <th rowspan="2" class="text-center align-middle fixed-column">Dep Rate</th>
                                    <th colspan="{{ $years->count() }}" class="text-center bg-success text-white">
                                        DEPRECIATION CHARGE FOR YEAR
                                    </th>
                                    <th rowspan="2" class="text-center align-middle fixed-column">Accumulated Dep</th>
                                    <th rowspan="2" class="text-center align-middle fixed-column">Net Book Value</th>
                                    <th rowspan="2" class="text-center align-middle fixed-column">Location</th>
                                    <th rowspan="2" class="text-center align-middle fixed-column">Remark</th>
                                    <th rowspan="2" class="text-center align-middle fixed-column">Cost of Disposal</th>
                                    <th rowspan="2" class="text-center align-middle fixed-column">Actions</th>
                                </tr>
                                <tr id="yearHeaders">
                                    @foreach($years as $year)
                                        <th class="text-center year-column"
                                            data-year-id="{{ $year->id }}"
                                            style="background-color: {{ $year->background_color ?? '#e8f5e9' }}; color: {{ $year->text_color ?? '#000' }}; min-width: 80px;">
                                            YEAR {{ $year->year }}
                                            @if($year->year == $currentYear)
                                                <br><small class="text-danger fw-bold">CURRENT</small>
                                            @endif
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assets as $asset)
                                    @php
                                        $accumulatedDepreciation = $asset->calculateAccumulatedDepreciation($currentYear);
                                        $netBookValue = $asset->calculateNetBookValue($currentYear);

                                        if ($asset->accumulated_depreciation != $accumulatedDepreciation || $asset->net_book_value != $netBookValue) {
                                            $asset->update([
                                                'accumulated_depreciation' => $accumulatedDepreciation,
                                                'net_book_value' => $netBookValue
                                            ]);
                                        }
                                    @endphp
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
                                        <td class="text-center fw-bold fixed-column">{{ $asset->depreciation_rate }}%</td>

                                        @foreach($years as $year)
                                            @php
                                                $depreciationForYear = $asset->getDepreciationForYear($year->year);
                                                $isActiveYear = $asset->wasActiveInYear($year->year);
                                            @endphp
                                            <td class="text-end year-cell @if($depreciationForYear > 0 && $isActiveYear) text-success fw-bold @endif"
                                                style="background-color: {{ $year->background_color ?? '#ffffff' }}; color: {{ $year->text_color ?? '#000' }};">
                                                @if($isActiveYear && $depreciationForYear > 0)
                                                    {{ number_format($depreciationForYear, 2) }}
                                                @else
                                                    0.00
                                                @endif
                                            </td>
                                        @endforeach

                                        <td class="text-end fw-bold fixed-column text-danger">
                                            {{ number_format($accumulatedDepreciation, 2) }}
                                        </td>
                                        <td class="text-end fw-bold fixed-column text-primary">
                                            <strong>{{ number_format($netBookValue, 2) }}</strong>
                                        </td>

                                        <td class="fixed-column">{{ $asset->location ?? 'N/A' }}</td>
                                        <td class="fixed-column">{{ $asset->remark ?? 'N/A' }}</td>
                                        <td class="text-end fixed-column">
                                            @if($asset->status === 'disposed' && $asset->cost_of_disposal)
                                                {{ number_format($asset->cost_of_disposal, 2) }}
                                            @else
                                                0.00
                                            @endif
                                        </td>

                                        <td class="text-center fixed-column">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('assets.show', $asset) }}"
                                                   class="btn btn-outline-success btn-sm hover-brighten"
                                                   data-bs-toggle="tooltip"
                                                   title="View & Edit Asset Details">
                                                    <i class="bi bi-eye"></i> View
                                                </a>

                                                <form action="{{ route('assets.destroy', $asset) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this asset?')">
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
                                        <td colspan="{{ $years->count() + 14 }}" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                            No assets found. <a href="{{ route('assets.create') }}">Create the first asset</a>.
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

<!-- Column Customization Modal -->
<div class="modal fade" id="columnModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Customize Column</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="columnForm">
                    <input type="hidden" id="editingYearId">
                    <div class="mb-3">
                        <label class="form-label">Background Color</label>
                        <input type="color" class="form-control form-control-color" id="backgroundColor" value="#ffffff">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Text Color</label>
                        <input type="color" class="form-control form-control-color" id="textColor" value="#000000">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="saveColumnStyle">Save</button>
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

    .modal-header.bg-success {
        background-color: #198754 !important;
    }
</style>
@endpush
