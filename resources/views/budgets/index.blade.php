@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1" style="font-weight:600; color:#000;">Budgets</h4>
            <p class="text-muted small mb-0">Manage and track all project budgets</p>
        </div>
        <a href="{{ route('budgets.create') }}" style="background:#000; color:#fff; border:none; border-radius:4px; padding:8px 16px; text-decoration:none; font-size:0.85rem;">
            + New Budget
        </a>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div style="border:1px solid #ddd; border-radius:4px; padding:14px;">
                <div style="font-size:0.7rem; color:#999; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">Total Budgets</div>
                <div style="font-size:1.6rem; font-weight:700; color:#000;">{{ $stats['total_budgets'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div style="border:1px solid #ddd; border-radius:4px; padding:14px;">
                <div style="font-size:0.7rem; color:#999; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">Total Budget (ZMW)</div>
                <div style="font-size:1.1rem; font-weight:700; color:#28a745;">ZMW {{ number_format($stats['total_amount_zmw'], 0) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div style="border:1px solid #ddd; border-radius:4px; padding:14px;">
                <div style="font-size:0.7rem; color:#999; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">Total Expenses (ZMW)</div>
                <div style="font-size:1.1rem; font-weight:700; color:#000;">ZMW {{ number_format($totalExpensesAll ?? 0, 0) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div style="border:1px solid #ddd; border-radius:4px; padding:14px;">
                <div style="font-size:0.7rem; color:#999; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">Approved</div>
                <div style="font-size:1.6rem; font-weight:700; color:#28a745;">{{ $stats['approved_count'] }}</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" id="searchInput" class="form-control"
                   placeholder="Search by title, code, or contact..."
                   style="font-size:0.85rem; border:1px solid #ddd; border-radius:4px;">
        </div>
        <div class="col-md-3">
            <select id="statusFilter" class="form-select" style="font-size:0.85rem; border:1px solid #ddd; border-radius:4px;">
                <option value="all">All Status</option>
                <option value="approved">Approved</option>
                <option value="pending">Pending</option>
                <option value="draft">Draft</option>
            </select>
        </div>
        <div class="col-md-3">
            <select id="sortBy" class="form-select" style="font-size:0.85rem; border:1px solid #ddd; border-radius:4px;">
                <option value="newest">Newest First</option>
                <option value="oldest">Oldest First</option>
                <option value="amount_high">Highest Amount</option>
                <option value="amount_low">Lowest Amount</option>
                <option value="title_asc">Title A-Z</option>
                <option value="title_desc">Title Z-A</option>
            </select>
        </div>
        <div class="col-md-2">
            <button onclick="resetFilters()" style="width:100%; padding:6px 12px; border:1px solid #ddd; background:#fff; border-radius:4px; font-size:0.85rem; cursor:pointer;">
                Reset
            </button>
        </div>
    </div>

    <!-- Table -->
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.82rem;">
            <thead>
                <tr style="background:#f2f2f2;">
                    <th style="border:1px solid #bbb; padding:10px 12px; text-align:left; font-weight:600; white-space:nowrap;">Project Code</th>
                    <th style="border:1px solid #bbb; padding:10px 12px; text-align:left; font-weight:600;">Project Title</th>
                    <th style="border:1px solid #bbb; padding:10px 12px; text-align:right; font-weight:600; white-space:nowrap;">Budget (ZMW)</th>
                    <th style="border:1px solid #bbb; padding:10px 12px; text-align:right; font-weight:600; white-space:nowrap;">Expenses (ZMW)</th>
                    <th style="border:1px solid #bbb; padding:10px 12px; text-align:center; font-weight:600;">Utilization</th>
                    <th style="border:1px solid #bbb; padding:10px 12px; text-align:left; font-weight:600;">Contact</th>
                    <th style="border:1px solid #bbb; padding:10px 12px; text-align:center; font-weight:600;">Status</th>
                    <th style="border:1px solid #bbb; padding:10px 12px; text-align:center; font-weight:600;">Actions</th>
                </tr>
            </thead>
            <tbody id="budgetsTableBody">
                @forelse($budgets as $budget)
                @php
                    $totalExpenses = $budget->expenses()->whereIn('status', ['approved', 'paid'])->sum('amount_zmw');
                    $percentUsed = $budget->total_budget_zmw > 0 ? ($totalExpenses / $budget->total_budget_zmw) * 100 : 0;
                    $statusColor = $budget->status == 'approved' ? '#28a745' : ($budget->status == 'pending' ? '#e6a817' : '#6c757d');
                    $barColor = $percentUsed > 90 ? '#dc3545' : ($percentUsed > 70 ? '#ffc107' : '#28a745');
                @endphp
                <tr class="budget-row"
                    data-status="{{ $budget->status }}"
                    data-title="{{ strtolower($budget->project_title) }}"
                    data-code="{{ strtolower($budget->project_code) }}"
                    data-contact="{{ strtolower($budget->contact_person) }}"
                    data-amount="{{ $budget->total_budget_zmw }}"
                    data-created="{{ $budget->created_at }}"
                    style="background:#fff;">
                    <td style="border:1px solid #ddd; padding:10px 12px; font-weight:600; white-space:nowrap;">
                        <a href="{{ route('budgets.show', $budget->id) }}" style="color:#000; text-decoration:none;">
                            {{ $budget->project_code }}
                        </a>
                    </td>
                    <td style="border:1px solid #ddd; padding:10px 12px;">
                        <div style="font-weight:500;">{{ Str::limit($budget->project_title, 55) }}</div>
                        <div style="font-size:0.72rem; color:#888; margin-top:2px;">{{ $budget->duration }}</div>
                    </td>
                    <td style="border:1px solid #ddd; padding:10px 12px; text-align:right; color:#28a745; font-weight:600; white-space:nowrap;">
                        {{ number_format($budget->total_budget_zmw, 2) }}
                    </td>
                    <td style="border:1px solid #ddd; padding:10px 12px; text-align:right; white-space:nowrap;">
                        {{ number_format($totalExpenses, 2) }}
                    </td>
                    <td style="border:1px solid #ddd; padding:10px 12px; text-align:center; min-width:110px;">
                        <div style="background:#e9e9e9; height:5px; border-radius:2px; margin-bottom:4px;">
                            <div style="width:{{ min($percentUsed, 100) }}%; height:5px; background:{{ $barColor }}; border-radius:2px;"></div>
                        </div>
                        <span style="font-size:0.72rem; color:#555;">{{ number_format($percentUsed, 1) }}% used</span>
                    </td>
                    <td style="border:1px solid #ddd; padding:10px 12px;">
                        <div>{{ $budget->contact_person }}</div>
                        <div style="font-size:0.72rem; color:#888; margin-top:2px;">{{ $budget->contact_email }}</div>
                    </td>
                    <td style="border:1px solid #ddd; padding:10px 12px; text-align:center;">
                        <span style="color:{{ $statusColor }}; font-weight:600; font-size:0.8rem;">
                            {{ ucfirst($budget->status) }}
                        </span>
                    </td>
                    <td style="border:1px solid #ddd; padding:10px 12px; text-align:center; white-space:nowrap;">
                        <a href="{{ route('budgets.show', $budget->id) }}" style="color:#000; text-decoration:none; margin-right:10px; font-size:0.8rem;">View</a>
                        <a href="{{ route('budgets.edit', $budget->id) }}" style="color:#28a745; text-decoration:none; margin-right:10px; font-size:0.8rem;">Edit</a>
                        <button onclick="deleteBudget({{ $budget->id }}, '{{ $budget->project_code }}')"
                                style="color:#dc3545; background:none; border:none; cursor:pointer; font-size:0.8rem; padding:0;">
                            Delete
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="border:1px solid #ddd; padding:40px; text-align:center; color:#888;">
                        No budgets found.
                        <a href="{{ route('budgets.create') }}" style="color:#000; font-weight:600; margin-left:8px;">+ New Budget</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($budgets instanceof \Illuminate\Pagination\LengthAwarePaginator && $budgets->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $budgets->links() }}
    </div>
    @endif

</div>

<!-- Delete Form -->
<form id="deleteForm" action="" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<style>
.budget-row:hover { background:#f7f7f7 !important; }
.form-control:focus, .form-select:focus { border-color:#000; box-shadow:none; outline:none; }
</style>

<script>
const searchInput  = document.getElementById('searchInput');
const statusFilter = document.getElementById('statusFilter');
const sortBy       = document.getElementById('sortBy');
const tableBody    = document.getElementById('budgetsTableBody');
let allRows = Array.from(document.querySelectorAll('.budget-row'));

function filterBudgets() {
    const search = searchInput.value.toLowerCase();
    const status = statusFilter.value;
    const sort   = sortBy.value;

    let rows = allRows.filter(row => {
        const matchSearch = !search ||
            (row.dataset.title   || '').includes(search) ||
            (row.dataset.code    || '').includes(search) ||
            (row.dataset.contact || '').includes(search);
        const matchStatus = status === 'all' || row.dataset.status === status;
        return matchSearch && matchStatus;
    });

    rows.sort((a, b) => {
        if (sort === 'newest')      return new Date(b.dataset.created) - new Date(a.dataset.created);
        if (sort === 'oldest')      return new Date(a.dataset.created) - new Date(b.dataset.created);
        if (sort === 'amount_high') return parseFloat(b.dataset.amount) - parseFloat(a.dataset.amount);
        if (sort === 'amount_low')  return parseFloat(a.dataset.amount) - parseFloat(b.dataset.amount);
        if (sort === 'title_asc')   return (a.dataset.title || '').localeCompare(b.dataset.title || '');
        if (sort === 'title_desc')  return (b.dataset.title || '').localeCompare(a.dataset.title || '');
        return 0;
    });

    tableBody.innerHTML = '';
    if (rows.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="8" style="border:1px solid #ddd; padding:40px; text-align:center; color:#888;">No matching budgets found.</td></tr>';
    } else {
        rows.forEach(r => tableBody.appendChild(r));
    }
}

function resetFilters() {
    searchInput.value  = '';
    statusFilter.value = 'all';
    sortBy.value       = 'newest';
    filterBudgets();
}

searchInput.addEventListener('input', filterBudgets);
statusFilter.addEventListener('change', filterBudgets);
sortBy.addEventListener('change', filterBudgets);

function deleteBudget(id, code) {
    if (confirm(`Delete budget ${code}? This cannot be undone.`)) {
        const form = document.getElementById('deleteForm');
        form.action = `/budgets/${id}`;
        form.submit();
    }
}

filterBudgets();
</script>
@endsection
