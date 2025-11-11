<div>
    <!-- Budget Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Budget</h6>
                    <h3>${{ number_format($donor->total_budget, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Remaining Budget</h6>
                    <h3>${{ number_format($donor->remaining_budget, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Expenses</h6>
                    <h3>${{ number_format($donor->total_expenses, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">Budget Threshold</h6>
                    <h3>${{ number_format($donor->budget_threshold, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    @if($donor->is_budget_low)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Budget Alert!</strong> Remaining budget is below the threshold.
        </div>
    @endif

    <!-- Add Transaction Button -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>Budget Transactions</h5>
        <button class="btn btn-primary btn-sm" wire:click="$set('showTransactionModal', true)">
            <i class="bi bi-plus-circle me-1"></i> Add Transaction
        </button>
    </div>

    <!-- Transactions Table -->
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Added By</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->transaction_date->format('M d, Y') }}</td>
                        <td>{{ $transaction->description }}</td>
                        <td>
                            <span class="badge bg-{{ $transaction->type === 'income' ? 'success' : 'danger' }}">
                                {{ ucfirst($transaction->type) }}
                            </span>
                        </td>
                        <td class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                            ${{ number_format($transaction->amount, 2) }}
                        </td>
                        <td>{{ $transaction->creator->name }}</td>
                        <td>{{ $transaction->notes ?: 'N/A' }}</td>
                        <td>
                            <button wire:click="deleteTransaction({{ $transaction->id }})"
                                    class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this transaction?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Add Transaction Modal -->
    @if($showTransactionModal)
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Budget Transaction</h5>
                        <button type="button" class="btn-close" wire:click="$set('showTransactionModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="addTransaction">
                            <div class="mb-3">
                                <label class="form-label">Description *</label>
                                <input type="text" class="form-control" wire:model="form.description">
                                @error('form.description') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Amount *</label>
                                        <input type="number" step="0.01" class="form-control" wire:model="form.amount">
                                        @error('form.amount') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Type *</label>
                                        <select class="form-select" wire:model="form.type">
                                            <option value="expense">Expense</option>
                                            <option value="income">Income</option>
                                        </select>
                                        @error('form.type') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Transaction Date *</label>
                                <input type="date" class="form-control" wire:model="form.transaction_date">
                                @error('form.transaction_date') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" rows="3" wire:model="form.notes"></textarea>
                                @error('form.notes') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary me-2" wire:click="$set('showTransactionModal', false)">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add Transaction</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
