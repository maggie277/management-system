<table class="table table-bordered table-hover" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th>Project Code</th>
            <th>Project Title</th>
            <th>Goal</th>
            <th>Duration</th>
            <th>Total (ZMW)</th>
            <th>Total (USD)</th>
            <th>Status</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($budgets as $budget)
            <tr>
                <td>
                    <strong>{{ $budget->project_code }}</strong>
                </td>
                <td>{{ Str::limit($budget->project_title, 40) }}</td>
                <td>{{ Str::limit($budget->project_goal, 50) }}</td>
                <td>{{ $budget->duration }}</td>
                <td class="text-end">ZMW {{ number_format($budget->total_budget_zmw, 2) }}</td>
                <td class="text-end">${{ number_format($budget->total_budget_usd, 2) }}</td>
                <td>
                    @if($budget->status === 'approved')
                        <span class="badge bg-success">Approved</span>
                    @elseif($budget->status === 'pending')
                        <span class="badge bg-warning text-dark">Pending</span>
                    @else
                        <span class="badge bg-secondary">Draft</span>
                    @endif
                </td>
                <td>
                    <div>{{ $budget->created_at->format('Y-m-d') }}</div>
                    <small class="text-muted">{{ $budget->createdBy->name ?? 'Unknown' }}</small>
                </td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('budgets.show', $budget->id) }}"
                           class="btn btn-outline-primary"
                           title="View Details">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('budgets.edit', $budget->id) }}"
                           class="btn btn-outline-secondary"
                           title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button"
                                class="btn btn-outline-info dropdown-toggle"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="#" onclick="duplicateBudget({{ $budget->id }})">
                                    <i class="bi bi-files me-2"></i>Duplicate
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="changeStatus({{ $budget->id }})">
                                    <i class="bi bi-arrow-repeat me-2"></i>Change Status
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('budgets.export-pdf', $budget->id) }}">
                                    <i class="bi bi-file-pdf me-2"></i>Export PDF
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('budgets.destroy', $budget->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this budget?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-trash me-2"></i>Delete
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center text-muted py-4">
                    <i class="bi bi-table display-4 d-block mb-3"></i>
                    <h5>No budgets found</h5>
                    <p>Click the "Create New Budget" button to get started.</p>
                </td>
            </tr>
        @endforelse
    </tbody>
    @if($budgets->count() > 0)
    <tfoot class="table-light">
        <tr>
            <td colspan="4" class="text-end"><strong>TOTALS:</strong></td>
            <td class="text-end">
                <strong>ZMW {{ number_format($budgets->sum('total_budget_zmw'), 2) }}</strong>
            </td>
            <td class="text-end">
                <strong>${{ number_format($budgets->sum('total_budget_usd'), 2) }}</strong>
            </td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
    @endif
</table>

<script>
function duplicateBudget(id) {
    if (confirm('Create a copy of this budget?')) {
        fetch(`/budgets/${id}/duplicate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(response => {
            if (response.redirected) {
                window.location.href = response.url;
            }
        });
    }
}

function changeStatus(id) {
    const status = prompt('Enter new status (draft, pending, approved):');
    if (status && ['draft', 'pending', 'approved'].includes(status)) {
        fetch(`/budgets/${id}/change-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status })
        }).then(() => {
            window.location.reload();
        });
    }
}
</script>
