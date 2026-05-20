@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1" style="color: #000; font-weight: 600;">New Expense</h4>
            <p class="text-muted small mb-0">Record a new expense against a budget</p>
        </div>
        <a href="{{ route('expenses.index') }}" class="btn btn-outline-dark btn-sm" style="border-radius: 20px;">
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

    <form action="{{ route('expenses.store') }}" method="POST">
        @csrf

        <div class="row g-3">
            <div class="col-md-8">
                <!-- Expense Details -->
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
                                        <option value="{{ $budget->id }}" {{ old('budget_id', request('budget_id')) == $budget->id ? 'selected' : '' }}>
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
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold" style="color: #333;">Description *</label>
                                <input type="text" class="form-control form-control-sm @error('description') is-invalid @enderror"
                                       id="description" name="description" value="{{ old('description') }}"
                                       placeholder="e.g., Office supplies for Q1" required
                                       style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">
                                @error('description')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold" style="color: #333;">Category *</label>
                                <input type="text" class="form-control form-control-sm @error('category') is-invalid @enderror"
                                       id="category" name="category" value="{{ old('category') }}"
                                       placeholder="Type or select" list="categoryList" required
                                       style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">
                                <datalist id="categoryList">
                                    <option value="Travel & Transportation"><option value="Conference & Meetings">
                                    <option value="Office Supplies"><option value="Equipment & Furniture">
                                    <option value="Consultancy Services"><option value="Communication & Internet">
                                    <option value="Printing & Publications"><option value="Training & Workshops">
                                    <option value="Utilities"><option value="Maintenance & Repairs">
                                    <option value="Staff Costs"><option value="Rent & Accommodation">
                                    <option value="Insurance"><option value="Bank Charges">
                                    <option value="Subscriptions"><option value="Marketing & Advertising">
                                    <option value="Legal Fees"><option value="IT Services">
                                    <option value="Courier & Postage"><option value="Entertainment">
                                    <option value="Medical"><option value="Vehicle Expenses">
                                    <option value="Security"><option value="Cleaning"><option value="Other">
                                </datalist>
                                @error('category')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold" style="color: #333;">Purpose</label>
                                <textarea class="form-control form-control-sm" id="purpose" name="purpose" rows="2"
                                          placeholder="Brief purpose of this expense"
                                          style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">{{ old('purpose') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold" style="color: #333;">Amount (ZMW) *</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text" style="border-radius: 6px 0 0 6px; background: #28a745; color: #fff; border: none; font-size: 0.75rem;">ZMW</span>
                                    <input type="number" class="form-control form-control-sm @error('amount_zmw') is-invalid @enderror"
                                           id="amount_zmw" name="amount_zmw" step="0.01" min="0"
                                           value="{{ old('amount_zmw') }}" placeholder="0.00" required
                                           style="border-radius: 0 6px 6px 0; border: 1px solid #ddd; padding: 8px 12px;">
                                </div>
                                @error('amount_zmw')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold" style="color: #333;">Expense Date *</label>
                                <input type="date" class="form-control form-control-sm @error('expense_date') is-invalid @enderror"
                                       id="expense_date" name="expense_date"
                                       value="{{ old('expense_date', date('Y-m-d')) }}" required
                                       style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">
                                @error('expense_date')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold" style="color: #333;">Payment Method</label>
                                <input type="text" class="form-control form-control-sm" id="payment_method" name="payment_method"
                                       value="{{ old('payment_method') }}" placeholder="e.g., Cash"
                                       list="paymentMethodList"
                                       style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">
                                <datalist id="paymentMethodList">
                                    <option value="Cash"><option value="Bank Transfer"><option value="Check">
                                    <option value="Mobile Money"><option value="Credit Card"><option value="Debit Card">
                                    <option value="Online Transfer"><option value="Direct Deposit">
                                </datalist>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Additional Info -->
                <div class="card border-0 shadow-sm mb-3" style="border-radius: 8px;">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0" style="color: #000; font-weight: 600; font-size: 0.9rem;">Additional Info</h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="mb-2">
                            <label class="form-label small fw-bold" style="color: #333;">Vendor/Payee</label>
                            <input type="text" class="form-control form-control-sm" id="vendor_payee" name="vendor_payee"
                                   value="{{ old('vendor_payee') }}" placeholder="e.g., ABC Suppliers"
                                   style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold" style="color: #333;">Receipt Number</label>
                            <input type="text" class="form-control form-control-sm" id="receipt_number" name="receipt_number"
                                   value="{{ old('receipt_number') }}" placeholder="e.g., RCT-001"
                                   style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold" style="color: #333;">Notes</label>
                            <textarea class="form-control form-control-sm" id="notes" name="notes" rows="2"
                                      placeholder="Any additional notes"
                                      style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Budget Info Card -->
                <div class="card border-0 shadow-sm" style="border-radius: 8px;">
                    <div class="card-header bg-white border-0 py-3" style="border-radius: 8px 8px 0 0;">
                        <h6 class="mb-0" style="color: #28a745; font-weight: 600; font-size: 0.85rem;">
                            <i class="bi bi-wallet2 me-1"></i> Budget Info
                        </h6>
                    </div>
                    <div class="card-body pt-0" id="budgetInfo">
                        <p class="text-muted text-center small py-3">Select a budget to see details</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="text-center mt-3 mb-4">
            <button type="submit" class="btn btn-dark px-4 me-2" style="border-radius: 20px; font-size: 0.85rem;">
                <i class="bi bi-check-lg me-1"></i> Save Expense
            </button>
            <a href="{{ route('expenses.index') }}" class="btn btn-outline-dark px-4" style="border-radius: 20px; font-size: 0.85rem;">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
document.getElementById('budget_id').addEventListener('change', function() {
    var budgetId = this.value;
    var itemSelect = document.getElementById('budget_item_id');
    var budgetInfo = document.getElementById('budgetInfo');

    itemSelect.innerHTML = '<option value="">Loading...</option>';
    budgetInfo.innerHTML = '<p class="text-muted text-center small py-3">Loading...</p>';

    if (!budgetId) {
        itemSelect.innerHTML = '<option value="">Select Budget Item (Optional)</option>';
        budgetInfo.innerHTML = '<p class="text-muted text-center small py-3">Select a budget to see details</p>';
        return;
    }

    fetch('/api/budget-items/' + budgetId)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            itemSelect.innerHTML = '<option value="">Select Budget Item (Optional)</option>';
            if (!data || Object.keys(data).length === 0) {
                itemSelect.innerHTML = '<option value="">No budget items found</option>';
                return;
            }
            for (var section in data) {
                if (data.hasOwnProperty(section) && Array.isArray(data[section]) && data[section].length > 0) {
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
        .catch(function(err) {
            itemSelect.innerHTML = '<option value="">Error loading items</option>';
        });

    fetch('/api/budget-remaining/' + budgetId)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var tb = parseFloat(data.total_budget || 0);
            var te = parseFloat(data.total_expenses || 0);
            var rm = parseFloat(data.remaining || 0);
            var er = parseFloat(data.exchange_rate || 0);
            var pu = tb > 0 ? (te / tb) * 100 : 0;

            budgetInfo.innerHTML =
                '<div class="mb-2"><small style="color:#999;font-size:0.7rem;">Total Budget</small><h6 class="mb-0" style="font-size:0.85rem;">ZMW ' + tb.toLocaleString('en-US',{minimumFractionDigits:2}) + '</h6></div>' +
                '<div class="mb-2"><small style="color:#999;font-size:0.7rem;">Expenses</small><h6 class="mb-0 text-warning" style="font-size:0.85rem;">ZMW ' + te.toLocaleString('en-US',{minimumFractionDigits:2}) + '</h6></div>' +
                '<div class="mb-2"><small style="color:#999;font-size:0.7rem;">Remaining</small><h6 class="mb-0" style="font-size:0.85rem;color:' + (rm>0?'#28a745':'#dc3545') + ';">ZMW ' + rm.toLocaleString('en-US',{minimumFractionDigits:2}) + '</h6></div>' +
                '<div class="mb-2"><small style="color:#999;font-size:0.7rem;">Rate: 1 USD = ZMW ' + er.toFixed(2) + '</small></div>' +
                '<div class="progress" style="height:6px;border-radius:3px;"><div class="progress-bar" style="width:' + Math.min(pu,100) + '%;background:' + (rm>0?'#28a745':'#dc3545') + ';border-radius:3px;"></div></div>' +
                '<small style="font-size:0.65rem;color:#999;">' + pu.toFixed(1) + '% used</small>';
        })
        .catch(function(err) {
            budgetInfo.innerHTML = '<p class="text-danger small">Error loading info</p>';
        });
});
</script>
@endsection
