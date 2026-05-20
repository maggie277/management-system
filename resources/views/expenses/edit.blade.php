@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1" style="color: #000; font-weight: 600;">Edit Expense</h4>
            <p class="text-muted small mb-0">{{ $expense->expense_number }}</p>
        </div>
        <a href="{{ route('expenses.show', $expense->id) }}" class="btn btn-outline-dark btn-sm" style="border-radius: 20px;">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm" style="border-radius: 8px; font-size: 0.85rem;">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('expenses.update', $expense->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-3" style="border-radius: 8px;">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0" style="color: #000; font-weight: 600; font-size: 0.9rem;">Expense Details</h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold" style="color: #333;">Budget *</label>
                                <select class="form-select form-select-sm @error('budget_id') is-invalid @enderror"
                                        id="budget_id" name="budget_id" required
                                        style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">
                                    <option value="">Select Budget</option>
                                    @foreach($budgets as $budget)
                                        <option value="{{ $budget->id }}" {{ old('budget_id', $expense->budget_id) == $budget->id ? 'selected' : '' }}>
                                            {{ $budget->project_code }} - {{ Str::limit($budget->project_title, 45) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('budget_id')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold" style="color: #333;">Budget Item (Optional)</label>
                                <select class="form-select form-select-sm" id="budget_item_id" name="budget_item_id"
                                        style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">
                                    <option value="">Select Budget Item (Optional)</option>
                                    @if($budgetItems->count() > 0)
                                        @foreach($budgetItems->groupBy('section') as $section => $items)
                                            <optgroup label="{{ $section }}">
                                                @foreach($items as $item)
                                                    <option value="{{ $item->id }}" {{ old('budget_item_id', $expense->budget_item_id) == $item->id ? 'selected' : '' }}>
                                                        {{ $item->description_cost_item ?? 'Item #'.$item->id }} - ZMW {{ number_format($item->total_amount_zmw, 2) }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold" style="color: #333;">Description *</label>
                                <input type="text" class="form-control form-control-sm @error('description') is-invalid @enderror"
                                       id="description" name="description" value="{{ old('description', $expense->description) }}" required
                                       style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">
                                @error('description')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold" style="color: #333;">Category *</label>
                                <input type="text" class="form-control form-control-sm @error('category') is-invalid @enderror"
                                       id="category" name="category" value="{{ old('category', $expense->category) }}"
                                       list="categoryList" required
                                       style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">
                                <datalist id="categoryList">
                                    <option value="Travel & Transportation"><option value="Conference & Meetings">
                                    <option value="Office Supplies"><option value="Equipment & Furniture">
                                    <option value="Consultancy Services"><option value="Communication & Internet">
                                    <option value="Printing & Publications"><option value="Training & Workshops">
                                    <option value="Utilities"><option value="Maintenance & Repairs">
                                    <option value="Staff Costs"><option value="Rent & Accommodation">
                                    <option value="Insurance"><option value="Bank Charges">
                                    <option value="Other">
                                </datalist>
                                @error('category')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold" style="color: #333;">Purpose</label>
                                <textarea class="form-control form-control-sm" id="purpose" name="purpose" rows="2"
                                          style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">{{ old('purpose', $expense->purpose) }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold" style="color: #333;">Amount (ZMW) *</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text" style="border-radius: 6px 0 0 6px; background: #28a745; color: #fff; border: none; font-size: 0.75rem;">ZMW</span>
                                    <input type="number" class="form-control form-control-sm @error('amount_zmw') is-invalid @enderror"
                                           id="amount_zmw" name="amount_zmw" step="0.01" min="0"
                                           value="{{ old('amount_zmw', $expense->amount_zmw) }}" required
                                           style="border-radius: 0 6px 6px 0; border: 1px solid #ddd; padding: 8px 12px;">
                                </div>
                                @error('amount_zmw')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold" style="color: #333;">Expense Date *</label>
                                <input type="date" class="form-control form-control-sm @error('expense_date') is-invalid @enderror"
                                       id="expense_date" name="expense_date"
                                       value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required
                                       style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">
                                @error('expense_date')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold" style="color: #333;">Payment Method</label>
                                <input type="text" class="form-control form-control-sm" id="payment_method" name="payment_method"
                                       value="{{ old('payment_method', $expense->payment_method) }}" list="paymentMethodList"
                                       style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">
                                <datalist id="paymentMethodList">
                                    <option value="Cash"><option value="Bank Transfer"><option value="Check">
                                    <option value="Mobile Money"><option value="Credit Card">
                                </datalist>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-3" style="border-radius: 8px;">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0" style="color: #000; font-weight: 600; font-size: 0.9rem;">Additional Info</h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="mb-2">
                            <label class="form-label small fw-bold" style="color: #333;">Vendor/Payee</label>
                            <input type="text" class="form-control form-control-sm" id="vendor_payee" name="vendor_payee"
                                   value="{{ old('vendor_payee', $expense->vendor_payee) }}"
                                   style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold" style="color: #333;">Receipt Number</label>
                            <input type="text" class="form-control form-control-sm" id="receipt_number" name="receipt_number"
                                   value="{{ old('receipt_number', $expense->receipt_number) }}"
                                   style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold" style="color: #333;">Notes</label>
                            <textarea class="form-control form-control-sm" id="notes" name="notes" rows="2"
                                      style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">{{ old('notes', $expense->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="text-center mt-3 mb-4">
            <button type="submit" class="btn btn-dark px-4 me-2" style="border-radius: 20px; font-size: 0.85rem;">
                <i class="bi bi-check-lg me-1"></i> Update Expense
            </button>
            <a href="{{ route('expenses.show', $expense->id) }}" class="btn btn-outline-dark px-4" style="border-radius: 20px; font-size: 0.85rem;">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
document.getElementById('budget_id').addEventListener('change', function() {
    var budgetId = this.value;
    var itemSelect = document.getElementById('budget_item_id');
    if (!budgetId) {
        itemSelect.innerHTML = '<option value="">Select Budget Item (Optional)</option>';
        return;
    }
    itemSelect.innerHTML = '<option value="">Loading...</option>';
    fetch('/api/budget-items/' + budgetId)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            itemSelect.innerHTML = '<option value="">Select Budget Item (Optional)</option>';
            for (var section in data) {
                if (data.hasOwnProperty(section) && Array.isArray(data[section])) {
                    var optgroup = document.createElement('optgroup');
                    optgroup.label = section;
                    data[section].forEach(function(item) {
                        var option = document.createElement('option');
                        option.value = item.id;
                        var label = item.description_cost_item || item.description_cost_category || 'Item #' + item.id;
                        option.textContent = label + ' - ZMW ' + parseFloat(item.total_amount_zmw || 0).toFixed(2);
                        optgroup.appendChild(option);
                    });
                    itemSelect.appendChild(optgroup);
                }
            }
        })
        .catch(function() {
            itemSelect.innerHTML = '<option value="">Error loading items</option>';
        });
});
</script>
@endsection
