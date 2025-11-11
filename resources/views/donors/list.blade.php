@extends('layouts.app')

@section('title', 'All Donors')

@section('content')
<style>
.donor-table {
    font-size: 0.875rem;
    border: 1px solid #dee2e6;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.donor-table thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #198754;
    font-weight: 600;
    color: #198754;
    padding: 0.5rem;
    position: sticky;
    top: 0;
    background-color: #f8f9fa;
}
.donor-table tbody tr {
    border-bottom: 1px solid #e9ecef;
}
.donor-table tbody tr:hover {
    background-color: #f8fff9;
}
.donor-table td {
    padding: 0.5rem;
    vertical-align: middle;
    border-right: 1px solid #f1f1f1;
}
.compact-badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
}
.action-cell {
    white-space: nowrap;
}
.notes-cell {
    max-width: 150px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.status-cell {
    text-align: center;
}
.amount-cell {
    text-align: right;
    font-weight: 600;
}
.search-box {
    max-width: 300px;
}
.table-container {
    max-height: 70vh;
    overflow-y: auto;
    border: 1px solid #dee2e6;
}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box py-2">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="page-title mb-1">All Donors</h4>

                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('donors.create') }}" class="btn btn-success btn-sm">
                            <i class="bi bi-plus-circle me-1"></i> Add Donor
                        </a>
                        <a href="{{ route('donors.index') }}" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-grid me-1"></i> Card View
                        </a>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Compact Search & Filters -->
            <div class="card mb-3">
                <div class="card-body py-2">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-8">
                            <form method="GET" action="{{ route('donors.list') }}" class="row g-2">
                                <div class="col-auto">
                                    <input type="text" class="form-control form-control-sm search-box" name="search"
                                           placeholder="Search donors..." value="{{ request('search') }}">
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="bi bi-search me-1"></i> Search
                                    </button>
                                </div>
                                <div class="col-auto">
                                    <a href="{{ route('donors.list') }}" class="btn btn-outline-secondary btn-sm">
                                        Clear
                                    </a>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4 text-end text-muted small">
                            Showing {{ $donors->count() }} of {{ $donors->total() }} donors
                        </div>
                    </div>
                </div>
            </div>

            <!-- Donors Table -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-container">
                        <table class="table table-hover donor-table mb-0">
                            <thead>
                                <tr>
                                    <th width="18%">Donor Name</th>
                                    <th width="12%">Contact</th>
                                    <th width="12%">Responsible</th>
                                    <th width="8%">Type</th>
                                    <th width="12%">Start Date</th>
                                    <th width="12%">End Date</th>
                                    <th width="10%">Amount</th>
                                    <th width="8%">Status</th>
                                    <th width="8%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($donors as $donor)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-2"
                                                 style="width: 24px; height: 24px;">
                                                <i class="bi bi-person-fill text-white" style="font-size: 0.8rem;"></i>
                                            </div>
                                            <div>
                                                <div class="fw-medium text-success">{{ Str::limit($donor->name, 20) }}</div>
                                                @if($donor->email)
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ Str::limit($donor->email, 18) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($donor->contact_number)
                                            <span style="font-size: 0.8rem;">{{ $donor->contact_number }}</span>
                                        @else
                                            <span class="text-muted" style="font-size: 0.8rem;">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span style="font-size: 0.8rem;">{{ $donor->responsible_person ? Str::limit($donor->responsible_person, 15) : '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge compact-badge bg-success text-capitalize">
                                            {{ Str::limit($donor->contract_type, 8) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.8rem;">{{ $donor->contract_start_date->format('d/m/Y') }}</span>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.8rem;" class="{{ $donor->contract_end_date < now() ? 'text-danger' : '' }}">
                                            {{ $donor->contract_end_date->format('d/m/Y') }}
                                        </span>
                                    </td>
                                    <td class="amount-cell">
                                        <span style="font-size: 0.8rem;">K{{ number_format($donor->total_budget, 0) }}</span>
                                    </td>
                                    <td class="status-cell">
                                        @if(!$donor->is_active)
                                            <span class="badge compact-badge bg-danger">Inactive</span>
                                        @elseif($donor->contract_end_date < now())
                                            <span class="badge compact-badge bg-warning text-dark">Expired</span>
                                        @else
                                            <span class="badge compact-badge bg-success">Active</span>
                                        @endif
                                    </td>
                                    <td class="action-cell">
                                        <div class="btn-group">
                                            <a href="{{ route('donors.show', $donor) }}"
                                               class="btn btn-sm btn-outline-success"
                                               title="View"
                                               style="padding: 0.15rem 0.3rem;">
                                                <i class="bi bi-eye" style="font-size: 0.7rem;"></i>
                                            </a>
                                            <a href="{{ route('donors.edit', $donor) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Edit"
                                               style="padding: 0.15rem 0.3rem;">
                                                <i class="bi bi-pencil" style="font-size: 0.7rem;"></i>
                                            </a>
                                            @if($donor->document_path)
                                            <a href="{{ route('donors.download-document', $donor) }}"
                                               class="btn btn-sm btn-outline-info"
                                               title="Document"
                                               style="padding: 0.15rem 0.3rem;">
                                                <i class="bi bi-file-earmark" style="font-size: 0.7rem;"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-3">
                                        <i class="bi bi-people text-muted d-block mb-2" style="font-size: 2rem;"></i>
                                        <span class="text-muted">No donors found</span>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Compact Pagination -->
            @if($donors->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    Page {{ $donors->currentPage() }} of {{ $donors->lastPage() }}
                </div>
                <div>
                    {{ $donors->links('pagination::bootstrap-5') }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
// Make table rows clickable for quick view
document.addEventListener('DOMContentLoaded', function() {
    const tableRows = document.querySelectorAll('.donor-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('dblclick', function() {
            const viewLink = this.querySelector('a[href*="/donors/"]');
            if (viewLink) {
                window.location.href = viewLink.href;
            }
        });

        // Add pointer cursor on hover
        row.style.cursor = 'pointer';
    });
});
</script>
@endsection
