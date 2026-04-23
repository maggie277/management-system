@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Budget Management Dashboard</h1>
        <a href="{{ route('budgets.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Create New Budget
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <!-- Total Budgets Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Budgets</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_budgets'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-wallet2 fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Amount (ZMW) Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Amount (ZMW)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ZMW {{ number_format($stats['total_amount_zmw'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-exchange fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Amount (USD) Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Amount (USD)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($stats['total_amount_usd'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approved Budgets Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Approved Budgets</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['approved_count'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Budgets</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search by title, code, or contact...">
                </div>
                <div class="col-md-3 mb-3">
                    <select class="form-control" id="statusFilter">
                        <option value="all">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <select class="form-control" id="yearFilter">
                        <option value="all">All Years</option>
                        @foreach(range(date('Y'), date('Y')-3) as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <button class="btn btn-primary btn-block" onclick="filterBudgets()">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Budgets Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Budgets</h6>
            <span class="badge bg-secondary">{{ $budgets->count() }} total</span>
        </div>
        <div class="card-body">
            <div class="table-responsive" id="budgetsTableContainer">
                @include('budgets.partials.budget_table', ['budgets' => $budgets])
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterBudgets() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    const year = document.getElementById('yearFilter').value;

    fetch(`{{ route('budgets.search') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ search, status, year })
    })
    .then(response => response.text())
    .then(html => {
        document.getElementById('budgetsTableContainer').innerHTML = html;
    })
    .catch(error => console.error('Error:', error));
}

// Auto-search on input (with debounce)
let searchTimeout;
document.getElementById('searchInput').addEventListener('keyup', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(filterBudgets, 500);
});

document.getElementById('statusFilter').addEventListener('change', filterBudgets);
document.getElementById('yearFilter').addEventListener('change', filterBudgets);
</script>
@endpush

@push('styles')
<style>
.border-left-primary { border-left: 4px solid #4e73df; }
.border-left-success { border-left: 4px solid #1cc88a; }
.border-left-info { border-left: 4px solid #36b9cc; }
.border-left-warning { border-left: 4px solid #f6c23e; }

.fa-2x { font-size: 2rem; }
</style>
@endpush
