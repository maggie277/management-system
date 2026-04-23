@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Budget Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detailed Budget: {{ $budget->project_code }}</h1>
        <div class="btn-group">
            <a href="{{ route('budgets.edit', $budget->id) }}" class="btn btn-secondary">
                <i class="bi bi-pencil me-1"></i> Edit Budget
            </a>
            <a href="{{ route('budgets.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Budget Summary Card -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="m-0 font-weight-bold">Project Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="30%">Project Title:</th>
                                    <td>{{ $budget->project_title }}</td>
                                </tr>
                                <tr>
                                    <th>Project Goal:</th>
                                    <td>{{ $budget->project_goal }}</td>
                                </tr>
                                <tr>
                                    <th>Duration:</th>
                                    <td>{{ $budget->duration }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($budget->status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($budget->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-secondary">Draft</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="30%">Contact Person:</th>
                                    <td>{{ $budget->contact_person }}</td>
                                </tr>
                                <tr>
                                    <th>Contact Email:</th>
                                    <td>{{ $budget->contact_email }}</td>
                                </tr>
                                <tr>
                                    <th>Contact Phone:</th>
                                    <td>{{ $budget->contact_phone }}</td>
                                </tr>
                                <tr>
                                    <th>Exchange Rate:</th>
                                    <td>1 USD = ZMW {{ number_format($budget->exchange_rate, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Budget Totals -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Total Budget (ZMW)</div>
                    <div class="h2 mb-0 font-weight-bold text-gray-800">
                        ZMW {{ number_format($budget->total_budget_zmw, 2) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        Total Budget (USD)</div>
                    <div class="h2 mb-0 font-weight-bold text-gray-800">
                        ${{ number_format($budget->total_budget_usd, 2) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                        Exchange Rate</div>
                    <div class="h2 mb-0 font-weight-bold text-gray-800">
                        1 USD = ZMW {{ number_format($budget->exchange_rate, 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Budget Sections -->
    @foreach($groupedItems as $section => $items)
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">{{ $section }}</h6>
            <div class="text-end">
                <strong>Total (ZMW):</strong>
                <span class="text-success">ZMW {{ number_format($sectionTotals[$section]['zmw'] ?? 0, 2) }}</span> |
                <strong>Total (USD):</strong>
                <span class="text-info">${{ number_format($sectionTotals[$section]['usd'] ?? 0, 2) }}</span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>Component</th>
                            <th>Cost Category</th>
                            <th>Cost Item</th>
                            <th>Number</th>
                            <th>Frequency</th>
                            <th>Unit</th>
                            <th>Unit Cost</th>
                            <th>Total ZMW</th>
                            <th>Total USD</th>
                            <th>Revised Y1</th>
                            <th>Revised Y2</th>
                            <th>Revised Y3</th>
                            <th>Comments</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currentObjective = null;
                            $currentActivity = null;
                        @endphp

                        @foreach($items as $item)
                            @if($item->objective !== $currentObjective)
                                @php $currentObjective = $item->objective; @endphp
                                <tr class="table-warning">
                                    <td colspan="13" class="fw-bold">
                                        <i class="bi bi-target me-1"></i> {{ $currentObjective }}
                                    </td>
                                </tr>
                            @endif

                            @if($item->activity !== $currentActivity)
                                @php $currentActivity = $item->activity; @endphp
                                <tr class="table-info">
                                    <td colspan="13" class="fw-bold ps-4">
                                        <i class="bi bi-list-task me-1"></i> {{ $currentActivity }}
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <td>{{ $item->component }}</td>
                                <td>{{ $item->description_cost_category }}</td>
                                <td>{{ $item->description_cost_item }}</td>
                                <td class="text-center">{{ $item->number }}</td>
                                <td class="text-center">{{ $item->frequency }}</td>
                                <td class="text-center">{{ $item->unit }}</td>
                                <td class="text-end">ZMW {{ number_format($item->unit_cost, 2) }}</td>
                                <td class="text-end fw-bold">ZMW {{ number_format($item->total_amount_zmw, 2) }}</td>
                                <td class="text-end">${{ number_format($item->total_amount_usd, 2) }}</td>
                                <td class="text-end">
                                    @if($item->revised_year_1)
                                        ZMW {{ number_format($item->revised_year_1, 2) }}
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($item->revised_year_2)
                                        ZMW {{ number_format($item->revised_year_2, 2) }}
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($item->revised_year_3)
                                        ZMW {{ number_format($item->revised_year_3, 2) }}
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ $item->comments }}</small></td>
                            </tr>
                        @endforeach

                        <!-- Section Total Row -->
                        <tr class="table-light fw-bold">
                            <td colspan="7" class="text-end">SECTION TOTAL:</td>
                            <td class="text-end text-success">
                                ZMW {{ number_format($sectionTotals[$section]['zmw'] ?? 0, 2) }}
                            </td>
                            <td class="text-end text-info">
                                ${{ number_format($sectionTotals[$section]['usd'] ?? 0, 2) }}
                            </td>
                            <td colspan="4"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Grand Total -->
    <div class="card shadow">
        <div class="card-header bg-success text-white py-3">
            <h6 class="m-0 font-weight-bold">GRAND TOTAL</h6>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-6">
                    <h2 class="text-success">ZMW {{ number_format($budget->total_budget_zmw, 2) }}</h2>
                    <p class="text-muted mb-0">Total in Zambian Kwacha</p>
                </div>
                <div class="col-md-6">
                    <h2 class="text-primary">${{ number_format($budget->total_budget_usd, 2) }}</h2>
                    <p class="text-muted mb-0">Total in US Dollars</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Budget Notes -->
    <div class="card shadow mt-4">
        <div class="card-header bg-light py-3">
            <h6 class="m-0 font-weight-bold text-primary">Budget Notes</h6>
        </div>
        <div class="card-body">
            <p class="mb-2"><strong>Created:</strong> {{ $budget->created_at->format('F d, Y H:i') }} by {{ $budget->createdBy->name ?? 'Unknown' }}</p>
            <p class="mb-0"><strong>Last Updated:</strong> {{ $budget->updated_at->format('F d, Y H:i') }} by {{ $budget->updatedBy->name ?? 'Unknown' }}</p>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-4 text-center">
        <a href="{{ route('budgets.index') }}" class="btn btn-outline-secondary me-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </a>
        <a href="{{ route('budgets.edit', $budget->id) }}" class="btn btn-primary me-2">
            <i class="bi bi-pencil me-1"></i> Edit Budget
        </a>
        <button class="btn btn-success" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print Budget
        </button>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .btn, .no-print {
        display: none !important;
    }

    .card {
        border: 1px solid #ddd !important;
    }

    .table th, .table td {
        border: 1px solid #ddd !important;
    }
}
</style>
@endpush
