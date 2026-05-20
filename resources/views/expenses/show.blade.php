@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1" style="color: #000; font-weight: 600;">{{ $expense->expense_number }}</h4>
            <p class="text-muted small mb-0">{{ $expense->description }}</p>
        </div>
        <div class="d-flex gap-2">
            @if($expense->isEditable())
                <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-outline-dark btn-sm" style="border-radius: 20px;">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
            @endif
            @if($expense->isApprovable())
                <form action="{{ route('expenses.approve', $expense->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-dark btn-sm" style="background: #28a745; border: none; border-radius: 20px;">
                        <i class="bi bi-check-lg me-1"></i> Approve
                    </button>
                </form>
                <button type="button" class="btn btn-outline-danger btn-sm" style="border-radius: 20px;" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="bi bi-x-lg me-1"></i> Reject
                </button>
            @endif
            @if($expense->isPayable())
                <form action="{{ route('expenses.mark-paid', $expense->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-dark btn-sm" style="background: #17a2b8; border: none; border-radius: 20px;">
                        <i class="bi bi-cash-stack me-1"></i> Mark Paid
                    </button>
                </form>
            @endif
            <a href="{{ route('expenses.index') }}" class="btn btn-outline-dark btn-sm" style="border-radius: 20px;">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-8">
            <!-- Expense Details -->
            <div class="card border-0 shadow-sm mb-3" style="border-radius: 8px;">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0" style="font-weight: 600; font-size: 0.9rem;">Expense Details</h6>
                    {!! $expense->status_badge !!}
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3 mb-2">
                        <div class="col-md-4">
                            <small class="text-muted" style="font-size:0.7rem;">Amount (ZMW)</small>
                            <h4 style="color:#28a745;font-weight:700;">{{ $expense->formatted_amount_ZMW }}</h4>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted" style="font-size:0.7rem;">Amount (USD)</small>
                            <h5>{{ $expense->formatted_amount_USD }}</h5>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted" style="font-size:0.7rem;">Date</small>
                            <h5>{{ $expense->expense_date->format('d F, Y') }}</h5>
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <small class="text-muted" style="font-size:0.7rem;">Category</small>
                            <p class="mb-0 fw-bold" style="font-size:0.85rem;">{{ $expense->category }}</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted" style="font-size:0.7rem;">Payment Method</small>
                            <p class="mb-0 fw-bold" style="font-size:0.85rem;">{{ $expense->payment_method ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @if($expense->purpose)
                    <div class="mb-2">
                        <small class="text-muted" style="font-size:0.7rem;">Purpose</small>
                        <p class="mb-0" style="font-size:0.85rem;">{{ $expense->purpose }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Approval History -->
            <div class="card border-0 shadow-sm mb-3" style="border-radius: 8px;">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0" style="font-weight: 600; font-size: 0.85rem;">Approval History</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="timeline">
                        <div class="d-flex mb-3">
                            <div class="me-3">
                                <div class="rounded-circle bg-success p-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-plus-circle text-white" style="font-size: 0.8rem;"></i>
                                </div>
                            </div>
                            <div>
                                <p class="mb-0 small fw-bold">Created</p>
                                <p class="mb-0 small text-muted">{{ $expense->created_at->format('d M Y H:i') }} by {{ $expense->createdBy->name ?? 'Unknown' }}</p>
                            </div>
                        </div>
                        @if($expense->approved_at)
                        <div class="d-flex mb-3">
                            <div class="me-3">
                                <div class="rounded-circle bg-info p-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-check-lg text-white" style="font-size: 0.8rem;"></i>
                                </div>
                            </div>
                            <div>
                                <p class="mb-0 small fw-bold">Approved</p>
                                <p class="mb-0 small text-muted">{{ \Carbon\Carbon::parse($expense->approved_at)->format('d M Y H:i') }} by {{ $expense->approvedBy->name ?? 'Unknown' }}</p>
                            </div>
                        </div>
                        @endif
                        @if($expense->paid_at)
                        <div class="d-flex mb-3">
                            <div class="me-3">
                                <div class="rounded-circle bg-success p-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-cash-stack text-white" style="font-size: 0.8rem;"></i>
                                </div>
                            </div>
                            <div>
                                <p class="mb-0 small fw-bold">Paid</p>
                                <p class="mb-0 small text-muted">{{ \Carbon\Carbon::parse($expense->paid_at)->format('d M Y H:i') }} by {{ $expense->paidBy->name ?? 'Unknown' }}</p>
                            </div>
                        </div>
                        @endif
                        @if($expense->rejected_at)
                        <div class="d-flex mb-3">
                            <div class="me-3">
                                <div class="rounded-circle bg-danger p-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-x-lg text-white" style="font-size: 0.8rem;"></i>
                                </div>
                            </div>
                            <div>
                                <p class="mb-0 small fw-bold">Rejected</p>
                                <p class="mb-0 small text-muted">{{ \Carbon\Carbon::parse($expense->rejected_at)->format('d M Y H:i') }} by {{ $expense->rejectedBy->name ?? 'Unknown' }}</p>
                                @if($expense->rejection_reason)
                                <p class="mb-0 small text-danger mt-1">Reason: {{ $expense->rejection_reason }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Budget Information -->
            @if($expense->budget)
            <div class="card border-0 shadow-sm mb-3" style="border-radius: 8px;">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0" style="font-weight: 600; font-size: 0.85rem; color: #28a745;">
                        <i class="bi bi-wallet2 me-1"></i> Budget Information
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <p class="fw-bold mb-1" style="font-size:0.85rem;">{{ $expense->budget->project_code }}</p>
                    <p class="small text-muted mb-3">{{ Str::limit($expense->budget->project_title, 80) }}</p>

                    @if($utilization)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Budget Utilization</span>
                            <span class="fw-bold">{{ number_format($utilization['percentage_used'], 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 8px; border-radius: 4px;">
                            <div class="progress-bar" style="width: {{ min($utilization['percentage_used'], 100) }}%; background: {{ $utilization['remaining'] > 0 ? '#28a745' : '#dc3545' }};"></div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted" style="font-size:0.65rem;">Total Budget</small>
                            <p class="mb-0 fw-bold" style="font-size:0.8rem;">{{ $expense->budget->formatted_total_ZMW }}</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted" style="font-size:0.65rem;">Total Expenses</small>
                            <p class="mb-0 fw-bold text-warning" style="font-size:0.8rem;">{{ $expense->budget->formatted_expenses_ZMW }}</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted" style="font-size:0.65rem;">Remaining</small>
                            <p class="mb-0 fw-bold" style="font-size:0.8rem; color: {{ $utilization['remaining'] > 0 ? '#28a745' : '#dc3545' }};">
                                {{ $expense->budget->formatted_remaining_ZMW }}
                            </p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted" style="font-size:0.65rem;">Exchange Rate</small>
                            <p class="mb-0 fw-bold" style="font-size:0.8rem;">1 USD = ZMW {{ number_format($expense->budget->exchange_rate, 2) }}</p>
                        </div>
                    </div>
                    @endif

                    <a href="{{ route('budgets.show', $expense->budget->id) }}" class="btn btn-outline-dark btn-sm w-100" style="border-radius:20px;font-size:0.75rem;">
                        <i class="bi bi-eye me-1"></i> View Full Budget
                    </a>
                </div>
            </div>
            @endif

            <!-- Additional Info -->
            <div class="card border-0 shadow-sm mb-3" style="border-radius: 8px;">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0" style="font-weight: 600; font-size: 0.85rem;">Additional Information</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="mb-2">
                        <small class="text-muted" style="font-size:0.65rem;">Vendor/Payee</small>
                        <p class="mb-0 small">{{ $expense->vendor_payee ?? '-' }}</p>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted" style="font-size:0.65rem;">Receipt Number</small>
                        <p class="mb-0 small">{{ $expense->receipt_number ?? '-' }}</p>
                    </div>
                    @if($expense->notes)
                    <div class="mb-2">
                        <small class="text-muted" style="font-size:0.65rem;">Notes</small>
                        <p class="mb-0 small">{{ $expense->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="text-muted mt-3" style="font-size:0.65rem;">
                Created {{ $expense->created_at->format('d M Y H:i') }} by {{ $expense->createdBy->name ?? 'Unknown' }}
                @if($expense->updated_at != $expense->created_at)
                    <br>Last updated {{ $expense->updated_at->format('d M Y H:i') }}
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 10px; border: none;">
            <div class="modal-header border-0" style="background: #dc3545; color: #fff; border-radius: 10px 10px 0 0;">
                <h6 class="modal-title">Reject Expense</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('expenses.reject', $expense->id) }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <p>Please provide a reason for rejecting this expense:</p>
                    <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="Enter rejection reason..."></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal" style="border-radius: 20px;">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm" style="border-radius: 20px;">Reject Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
