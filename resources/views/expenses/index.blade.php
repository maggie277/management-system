@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1" style="color: #000; font-weight: 600;">Expenses</h4>
            <p class="text-muted small mb-0">Manage all expenses</p>
        </div>
        <a href="{{ route('expenses.create') }}" class="btn btn-dark btn-sm" style="border-radius: 20px;">
            <i class="bi bi-plus-lg me-1"></i> New Expense
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 8px; border-left: 3px solid #28a745;">
                <div class="card-body p-3">
                    <p class="text-muted small mb-1 text-uppercase" style="font-size: 0.65rem;">Total Expenses</p>
                    <h5 class="mb-0 fw-bold" style="font-size: 1.1rem; color: #28a745;">ZMW {{ number_format($stats['total_expenses'], 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 8px; border-left: 3px solid #ffc107;">
                <div class="card-body p-3">
                    <p class="text-muted small mb-1 text-uppercase" style="font-size: 0.65rem;">Pending Approval</p>
                    <h5 class="mb-0 fw-bold" style="font-size: 1.1rem;">{{ $stats['pending_count'] }}</h5>
                    <small class="text-muted">ZMW {{ number_format($stats['pending_amount'], 2) }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 8px; border-left: 3px solid #17a2b8;">
                <div class="card-body p-3">
                    <p class="text-muted small mb-1 text-uppercase" style="font-size: 0.65rem;">This Month</p>
                    <h5 class="mb-0 fw-bold" style="font-size: 1.1rem;">ZMW {{ number_format($stats['paid_this_month'], 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 8px; border-left: 3px solid #6c757d;">
                <div class="card-body p-3">
                    <p class="text-muted small mb-1 text-uppercase" style="font-size: 0.65rem;">Total Transactions</p>
                    <h5 class="mb-0 fw-bold" style="font-size: 1.1rem;">{{ $stats['total_count'] }}</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="card border-0 shadow-sm" style="border-radius: 8px;">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="mb-0" style="color: #000; font-weight: 600; font-size: 0.9rem;">Expense List</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size: 0.8rem;">
                    <thead style="background: #f8f8f8;">
                        <tr>
                            <th class="border-0 py-3 px-3">Expense #</th>
                            <th class="border-0 py-3">Date</th>
                            <th class="border-0 py-3">Description</th>
                            <th class="border-0 py-3">Budget</th>
                            <th class="border-0 py-3">Category</th>
                            <th class="border-0 py-3 text-end">Amount</th>
                            <th class="border-0 py-3 text-center">Status</th>
                            <th class="border-0 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                        <tr>
                            <td class="px-3 fw-bold">
                                <a href="{{ route('expenses.show', $expense) }}" class="text-decoration-none">
                                    {{ $expense->expense_number }}
                                </a>
                            </td>
                            <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
                            <td>{{ Str::limit($expense->description, 40) }}</td>
                            <td>
                                <span class="small">{{ $expense->budget->project_code ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $expense->category }}</td>
                            <td class="text-end fw-bold">ZMW {{ number_format($expense->amount_zmw, 2) }}</td>
                            <td class="text-center">
                                <span class="badge" style="font-size: 0.65rem;
                                    @if($expense->status == 'pending') background: #fff3cd; color: #856404;
                                    @elseif($expense->status == 'approved') background: #d4edda; color: #155724;
                                    @elseif($expense->status == 'paid') background: #d1ecf1; color: #0c5460;
                                    @else background: #f8d7da; color: #721c24; @endif">
                                    {{ ucfirst($expense->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-outline-dark" style="border-radius: 20px; padding: 2px 10px; font-size: 0.7rem;">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(in_array($expense->status, ['pending', 'draft']))
                                    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-outline-dark ms-1" style="border-radius: 20px; padding: 2px 10px; font-size: 0.7rem;">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                    <p class="mb-0">No expenses found</p>
                                    <a href="{{ route('expenses.create') }}" class="btn btn-sm btn-dark mt-2">Create First Expense</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($expenses->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $expenses->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
