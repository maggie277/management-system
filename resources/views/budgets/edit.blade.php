@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Budget: {{ $budget->project_code }}</h1>
        <div class="btn-group">
            <a href="{{ route('budgets.show', $budget->id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to View
            </a>
            <a href="{{ route('budgets.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-grid me-1"></i> Dashboard
            </a>
        </div>
    </div>

    <form action="{{ route('budgets.update', $budget->id) }}" method="POST" id="budgetForm">
        @csrf
        @method('PUT')

        <!-- Basic Information Card -->
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="m-0 font-weight-bold">Basic Budget Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="project_title" class="form-label">Project Title *</label>
                        <input type="text" class="form-control" id="project_title" name="project_title"
                               value="{{ old('project_title', $budget->project_title) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="project_code" class="form-label">Project Code *</label>
                        <input type="text" class="form-control" id="project_code" name="project_code"
                               value="{{ old('project_code', $budget->project_code) }}" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label for="project_goal" class="form-label">Project Goal/Objective *</label>
                        <textarea class="form-control" id="project_goal" name="project_goal" rows="3" required>{{ old('project_goal', $budget->project_goal) }}</textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="duration" class="form-label">Duration *</label>
                        <input type="text" class="form-control" id="duration" name="duration"
                               value="{{ old('duration', $budget->duration) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="exchange_rate" class="form-label">Exchange Rate (1 USD = ZMW) *</label>
                        <input type="number" class="form-control" id="exchange_rate" name="exchange_rate"
                               step="0.01" value="{{ old('exchange_rate', $budget->exchange_rate) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="draft" {{ $budget->status == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="pending" {{ $budget->status == 'pending' ? 'selected' : '' }}>Pending Review</option>
                            <option value="approved" {{ $budget->status == 'approved' ? 'selected' : '' }}>Approved</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="total_budget_zmw" class="form-label">Total Budget (ZMW) *</label>
                        <input type="number" class="form-control" id="total_budget_zmw" name="total_budget_zmw"
                               step="0.01" value="{{ old('total_budget_zmw', $budget->total_budget_zmw) }}" required readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="total_budget_usd" class="form-label">Total Budget (USD) *</label>
                        <input type="number" class="form-control" id="total_budget_usd" name="total_budget_usd"
                               step="0.01" value="{{ old('total_budget_usd', $budget->total_budget_usd) }}" required readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information Card -->
        <div class="card shadow mb-4">
            <div class="card-header bg-info text-white py-3">
                <h6 class="m-0 font-weight-bold">Contact Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="contact_person" class="form-label">Contact Person *</label>
                        <input type="text" class="form-control" id="contact_person" name="contact_person"
                               value="{{ old('contact_person', $budget->contact_person) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="contact_email" class="form-label">Contact Email *</label>
                        <input type="email" class="form-control" id="contact_email" name="contact_email"
                               value="{{ old('contact_email', $budget->contact_email) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="contact_phone" class="form-label">Contact Phone *</label>
                        <input type="text" class="form-control" id="contact_phone" name="contact_phone"
                               value="{{ old('contact_phone', $budget->contact_phone) }}" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Budget Items Card -->
        <div class="card shadow mb-4">
            <div class="card-header bg-success text-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Detailed Budget Breakdown</h6>
                <div>
                    <button type="button" class="btn btn-light btn-sm" onclick="addSection('A - CORE PROGRAM EXPENDITURE')">
                        <i class="bi bi-plus-circle me-1"></i> Add Core Program
                    </button>
                    <button type="button" class="btn btn-light btn-sm" onclick="addSection('B - INSTITUTIONAL SUPPORT EXPENDITURE')">
                        <i class="bi bi-plus-circle me-1"></i> Add Institutional Support
                    </button>
                    <button type="button" class="btn btn-light btn-sm" onclick="addSection('C - CONTINGENCY')">
                        <i class="bi bi-plus-circle me-1"></i> Add Contingency
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="budgetItemsContainer">
                    <!-- Template for budget item -->
                    <div class="budget-item-template d-none">
                        <div class="card mb-3 budget-item-card">
                            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <select class="form-select form-select-sm section-select" style="width: auto; display: inline-block;">
                                        <option value="A - CORE PROGRAM EXPENDITURE">A - CORE PROGRAM EXPENDITURE</option>
                                        <option value="B - INSTITUTIONAL SUPPORT EXPENDITURE">B - INSTITUTIONAL SUPPORT EXPENDITURE</option>
                                        <option value="C - CONTINGENCY">C - CONTINGENCY</option>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-danger btn-sm remove-item">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label class="form-label">Objective</label>
                                        <input type="text" class="form-control objective"
                                               placeholder="e.g., OBJECTIVE 1: To strengthen technical and institutional capacity...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Activity</label>
                                        <input type="text" class="form-control activity"
                                               placeholder="e.g., ACTIVITY 1.1: Conduct the Quarterly review monitoring and evaluation process">
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-md-3">
                                        <label class="form-label">Component</label>
                                        <input type="text" class="form-control component"
                                               placeholder="e.g., Civil Society Institutional Support">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Description Cost Category</label>
                                        <input type="text" class="form-control description_cost_category"
                                               placeholder="e.g., Civil Society Institutional Support">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Description Cost Item</label>
                                        <input type="text" class="form-control description_cost_item"
                                               placeholder="e.g., Venue and Conference">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Comments</label>
                                        <input type="text" class="form-control comments"
                                               placeholder="e.g., Venue and conference at K450 per person for 22 persons...">
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-md-2">
                                        <label class="form-label">Number</label>
                                        <input type="number" class="form-control number" value="1" min="1">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Frequency</label>
                                        <input type="text" class="form-control frequency" placeholder="e.g., 8">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Unit</label>
                                        <input type="text" class="form-control unit" placeholder="e.g., Quarter">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Unit Cost (ZMW)</label>
                                        <input type="number" class="form-control unit_cost" step="0.01" value="0">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Total ZMW</label>
                                        <input type="number" class="form-control total_amount_zmw" step="0.01" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Total USD</label>
                                        <input type="number" class="form-control total_amount_usd" step="0.01" readonly>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">Revised Year 1</label>
                                        <input type="number" class="form-control revised_year_1" step="0.01">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Revised Year 2</label>
                                        <input type="number" class="form-control revised_year_2" step="0.01">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Revised Year 3</label>
                                        <input type="number" class="form-control revised_year_3" step="0.01">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Existing items will be loaded here -->
                    @foreach($groupedItems as $section => $items)
                        @foreach($items as $index => $item)
                            <div class="card mb-3 budget-item-card">
                                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                    <div>
                                        <select class="form-select form-select-sm section-select" style="width: auto; display: inline-block;">
                                            <option value="A - CORE PROGRAM EXPENDITURE" {{ $item->section == 'A - CORE PROGRAM EXPENDITURE' ? 'selected' : '' }}>A - CORE PROGRAM EXPENDITURE</option>
                                            <option value="B - INSTITUTIONAL SUPPORT EXPENDITURE" {{ $item->section == 'B - INSTITUTIONAL SUPPORT EXPENDITURE' ? 'selected' : '' }}>B - INSTITUTIONAL SUPPORT EXPENDITURE</option>
                                            <option value="C - CONTINGENCY" {{ $item->section == 'C - CONTINGENCY' ? 'selected' : '' }}>C - CONTINGENCY</option>
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm remove-item">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label">Objective</label>
                                            <input type="text" class="form-control objective"
                                                   value="{{ $item->objective }}"
                                                   placeholder="e.g., OBJECTIVE 1: To strengthen technical and institutional capacity...">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Activity</label>
                                            <input type="text" class="form-control activity"
                                                   value="{{ $item->activity }}"
                                                   placeholder="e.g., ACTIVITY 1.1: Conduct the Quarterly review monitoring and evaluation process">
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-md-3">
                                            <label class="form-label">Component</label>
                                            <input type="text" class="form-control component"
                                                   value="{{ $item->component }}"
                                                   placeholder="e.g., Civil Society Institutional Support">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Description Cost Category</label>
                                            <input type="text" class="form-control description_cost_category"
                                                   value="{{ $item->description_cost_category }}"
                                                   placeholder="e.g., Civil Society Institutional Support">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Description Cost Item</label>
                                            <input type="text" class="form-control description_cost_item"
                                                   value="{{ $item->description_cost_item }}"
                                                   placeholder="e.g., Venue and Conference">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Comments</label>
                                            <input type="text" class="form-control comments"
                                                   value="{{ $item->comments }}"
                                                   placeholder="e.g., Venue and conference at K450 per person for 22 persons...">
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-md-2">
                                            <label class="form-label">Number</label>
                                            <input type="number" class="form-control number"
                                                   value="{{ $item->number }}" min="1">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Frequency</label>
                                            <input type="text" class="form-control frequency"
                                                   value="{{ $item->frequency }}" placeholder="e.g., 8">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Unit</label>
                                            <input type="text" class="form-control unit"
                                                   value="{{ $item->unit }}" placeholder="e.g., Quarter">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Unit Cost (ZMW)</label>
                                            <input type="number" class="form-control unit_cost"
                                                   value="{{ $item->unit_cost }}" step="0.01">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Total ZMW</label>
                                            <input type="number" class="form-control total_amount_zmw"
                                                   value="{{ $item->total_amount_zmw }}" step="0.01" readonly>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Total USD</label>
                                            <input type="number" class="form-control total_amount_usd"
                                                   value="{{ $item->total_amount_usd }}" step="0.01" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label">Revised Year 1</label>
                                            <input type="number" class="form-control revised_year_1"
                                                   value="{{ $item->revised_year_1 }}" step="0.01">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Revised Year 2</label>
                                            <input type="number" class="form-control revised_year_2"
                                                   value="{{ $item->revised_year_2 }}" step="0.01">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Revised Year 3</label>
                                            <input type="number" class="form-control revised_year_3"
                                                   value="{{ $item->revised_year_3 }}" step="0.01">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>

                @if($budget->items->isEmpty())
                <!-- Empty state -->
                <div id="emptyState" class="text-center py-4">
                    <i class="bi bi-table display-1 text-muted mb-3"></i>
                    <h5 class="text-muted">No budget items added yet</h5>
                    <p class="text-muted">Click the buttons above to add budget sections</p>
                </div>
                @else
                <div id="emptyState" class="text-center py-4 d-none">
                    <i class="bi bi-table display-1 text-muted mb-3"></i>
                    <h5 class="text-muted">No budget items added yet</h5>
                    <p class="text-muted">Click the buttons above to add budget sections</p>
                </div>
                @endif

                <!-- Summary Section -->
                <div class="card mt-4">
                    <div class="card-header bg-warning">
                        <h6 class="m-0 font-weight-bold">Budget Summary</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h6>Core Program Expenditure</h6>
                                <div class="h4 text-success" id="coreProgramTotal">
                                    ZMW {{ number_format($budget->items->where('section', 'A - CORE PROGRAM EXPENDITURE')->sum('total_amount_zmw'), 2) }}
                                </div>
                                <small class="text-muted" id="coreProgramTotalUsd">
                                    ${{ number_format($budget->items->where('section', 'A - CORE PROGRAM EXPENDITURE')->sum('total_amount_usd'), 2) }}
                                </small>
                            </div>
                            <div class="col-md-4">
                                <h6>Institutional Support</h6>
                                <div class="h4 text-primary" id="institutionalSupportTotal">
                                    ZMW {{ number_format($budget->items->where('section', 'B - INSTITUTIONAL SUPPORT EXPENDITURE')->sum('total_amount_zmw'), 2) }}
                                </div>
                                <small class="text-muted" id="institutionalSupportTotalUsd">
                                    ${{ number_format($budget->items->where('section', 'B - INSTITUTIONAL SUPPORT EXPENDITURE')->sum('total_amount_usd'), 2) }}
                                </small>
                            </div>
                            <div class="col-md-4">
                                <h6>Contingency</h6>
                                <div class="h4 text-info" id="contingencyTotal">
                                    ZMW {{ number_format($budget->items->where('section', 'C - CONTINGENCY')->sum('total_amount_zmw'), 2) }}
                                </div>
                                <small class="text-muted" id="contingencyTotalUsd">
                                    ${{ number_format($budget->items->where('section', 'C - CONTINGENCY')->sum('total_amount_usd'), 2) }}
                                </small>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h4 class="text-center">GRAND TOTAL:
                                    <span id="grandTotalZMW">ZMW {{ number_format($budget->total_budget_zmw, 2) }}</span> /
                                    <span id="grandTotalUSD">${{ number_format($budget->total_budget_usd, 2) }}</span>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden fields for storing budget items -->
        <div id="hiddenFieldsContainer"></div>

        <!-- Action Buttons -->
        <div class="card shadow">
            <div class="card-body text-center">
                <button type="button" class="btn btn-primary btn-lg me-3" onclick="saveBudget()">
                    <i class="bi bi-save me-2"></i> Update Budget
                </button>
                <a href="{{ route('budgets.show', $budget->id) }}" class="btn btn-secondary btn-lg me-3">
                    <i class="bi bi-x-circle me-2"></i> Cancel
                </a>
                <button type="button" class="btn btn-danger btn-lg" onclick="confirmDelete()">
                    <i class="bi bi-trash me-2"></i> Delete Budget
                </button>
            </div>
        </div>
    </form>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this budget?</p>
                    <p class="text-danger"><strong>Warning:</strong> This action cannot be undone. All budget items will also be deleted.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('budgets.destroy', $budget->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Budget</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.budget-item-card {
    border-left: 4px solid #28a745;
}
.section-a { border-left-color: #28a745; }
.section-b { border-left-color: #007bff; }
.section-c { border-left-color: #6c757d; }
</style>
@endpush

@push('scripts')
<script>
let itemCounter = {{ $budget->items->count() }};

// Function to add a new budget item section
function addSection(sectionType) {
    const template = document.querySelector('.budget-item-template');
    const clone = template.cloneNode(true);
    clone.classList.remove('d-none', 'budget-item-template');

    // Set section
    const sectionSelect = clone.querySelector('.section-select');
    sectionSelect.value = sectionType;

    // Update visual indicator based on section
    updateSectionColor(clone, sectionType);

    // Add event listeners
    const removeBtn = clone.querySelector('.remove-item');
    removeBtn.addEventListener('click', function() {
        clone.remove();
        updateTotals();
        checkEmptyState();
    });

    // Add calculation listeners
    const numberInput = clone.querySelector('.number');
    const frequencyInput = clone.querySelector('.frequency');
    const unitCostInput = clone.querySelector('.unit_cost');
    const totalZMWInput = clone.querySelector('.total_amount_zmw');
    const totalUSDInput = clone.querySelector('.total_amount_usd');

    const calculateTotal = () => {
        const number = parseFloat(numberInput.value) || 0;
        const frequency = parseFloat(frequencyInput.value) || 0;
        const unitCost = parseFloat(unitCostInput.value) || 0;
        const exchangeRate = parseFloat(document.getElementById('exchange_rate').value) || 25;

        const totalZMW = number * frequency * unitCost;
        const totalUSD = totalZMW / exchangeRate;

        totalZMWInput.value = totalZMW.toFixed(2);
        totalUSDInput.value = totalUSD.toFixed(2);

        updateTotals();
    };

    numberInput.addEventListener('input', calculateTotal);
    frequencyInput.addEventListener('input', calculateTotal);
    unitCostInput.addEventListener('input', calculateTotal);

    // Section change listener
    sectionSelect.addEventListener('change', function() {
        updateSectionColor(clone, this.value);
        updateTotals();
    });

    // Add to container
    document.getElementById('budgetItemsContainer').appendChild(clone);
    document.getElementById('emptyState').classList.add('d-none');

    itemCounter++;
    updateTotals();
}

// Function to update section color
function updateSectionColor(element, section) {
    element.classList.remove('section-a', 'section-b', 'section-c');
    if (section === 'A - CORE PROGRAM EXPENDITURE') {
        element.classList.add('section-a');
    } else if (section === 'B - INSTITUTIONAL SUPPORT EXPENDITURE') {
        element.classList.add('section-b');
    } else {
        element.classList.add('section-c');
    }
}

// Update all totals
function updateTotals() {
    const items = document.querySelectorAll('#budgetItemsContainer .budget-item-card');
    const exchangeRate = parseFloat(document.getElementById('exchange_rate').value) || 25;

    let coreProgramTotalZMW = 0;
    let institutionalSupportTotalZMW = 0;
    let contingencyTotalZMW = 0;

    items.forEach(item => {
        const section = item.querySelector('.section-select').value;
        const totalZMW = parseFloat(item.querySelector('.total_amount_zmw').value) || 0;

        if (section === 'A - CORE PROGRAM EXPENDITURE') {
            coreProgramTotalZMW += totalZMW;
        } else if (section === 'B - INSTITUTIONAL SUPPORT EXPENDITURE') {
            institutionalSupportTotalZMW += totalZMW;
        } else if (section === 'C - CONTINGENCY') {
            contingencyTotalZMW += totalZMW;
        }
    });

    const grandTotalZMW = coreProgramTotalZMW + institutionalSupportTotalZMW + contingencyTotalZMW;
    const grandTotalUSD = grandTotalZMW / exchangeRate;

    // Update display
    document.getElementById('coreProgramTotal').textContent = `ZMW ${coreProgramTotalZMW.toFixed(2)}`;
    document.getElementById('coreProgramTotalUsd').textContent = `$${(coreProgramTotalZMW / exchangeRate).toFixed(2)}`;

    document.getElementById('institutionalSupportTotal').textContent = `ZMW ${institutionalSupportTotalZMW.toFixed(2)}`;
    document.getElementById('institutionalSupportTotalUsd').textContent = `$${(institutionalSupportTotalZMW / exchangeRate).toFixed(2)}`;

    document.getElementById('contingencyTotal').textContent = `ZMW ${contingencyTotalZMW.toFixed(2)}`;
    document.getElementById('contingencyTotalUsd').textContent = `$${(contingencyTotalZMW / exchangeRate).toFixed(2)}`;

    document.getElementById('grandTotalZMW').textContent = `ZMW ${grandTotalZMW.toFixed(2)}`;
    document.getElementById('grandTotalUSD').textContent = `$${grandTotalUSD.toFixed(2)}`;

    // Update form totals
    document.getElementById('total_budget_zmw').value = grandTotalZMW.toFixed(2);
    document.getElementById('total_budget_usd').value = grandTotalUSD.toFixed(2);
}

// Check if no items exist
function checkEmptyState() {
    const items = document.querySelectorAll('#budgetItemsContainer .budget-item-card');
    const emptyState = document.getElementById('emptyState');

    if (items.length === 0) {
        emptyState.classList.remove('d-none');
    } else {
        emptyState.classList.add('d-none');
    }
}

// Prepare data for submission
function prepareBudgetItems() {
    const items = [];
    const itemElements = document.querySelectorAll('#budgetItemsContainer .budget-item-card');

    itemElements.forEach((item, index) => {
        const itemData = {
            section: item.querySelector('.section-select').value,
            objective: item.querySelector('.objective').value,
            activity: item.querySelector('.activity').value,
            component: item.querySelector('.component').value,
            description_cost_category: item.querySelector('.description_cost_category').value,
            description_cost_item: item.querySelector('.description_cost_item').value,
            number: item.querySelector('.number').value,
            frequency: item.querySelector('.frequency').value,
            unit: item.querySelector('.unit').value,
            unit_cost: item.querySelector('.unit_cost').value,
            total_amount_zmw: item.querySelector('.total_amount_zmw').value,
            total_amount_usd: item.querySelector('.total_amount_usd').value,
            revised_year_1: item.querySelector('.revised_year_1').value,
            revised_year_2: item.querySelector('.revised_year_2').value,
            revised_year_3: item.querySelector('.revised_year_3').value,
            comments: item.querySelector('.comments').value,
            sort_order: index + 1
        };
        items.push(itemData);
    });

    return items;
}

// Save budget
function saveBudget() {
    const items = prepareBudgetItems();

    // Clear previous hidden fields
    document.getElementById('hiddenFieldsContainer').innerHTML = '';

    // Add hidden fields for each item
    items.forEach((item, index) => {
        for (const [key, value] of Object.entries(item)) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `budget_items[${index}][${key}]`;
            input.value = value;
            document.getElementById('hiddenFieldsContainer').appendChild(input);
        }
    });

    // Submit form
    document.getElementById('budgetForm').submit();
}

// Confirm delete
function confirmDelete() {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Initialize existing items
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to existing items
    const existingItems = document.querySelectorAll('#budgetItemsContainer .budget-item-card');

    existingItems.forEach(item => {
        // Set section color
        const section = item.querySelector('.section-select').value;
        updateSectionColor(item, section);

        // Add remove button listener
        const removeBtn = item.querySelector('.remove-item');
        removeBtn.addEventListener('click', function() {
            item.remove();
            updateTotals();
            checkEmptyState();
        });

        // Add calculation listeners to existing items
        const numberInput = item.querySelector('.number');
        const frequencyInput = item.querySelector('.frequency');
        const unitCostInput = item.querySelector('.unit_cost');
        const sectionSelect = item.querySelector('.section-select');

        const calculateTotal = () => {
            const number = parseFloat(numberInput.value) || 0;
            const frequency = parseFloat(frequencyInput.value) || 0;
            const unitCost = parseFloat(unitCostInput.value) || 0;
            const exchangeRate = parseFloat(document.getElementById('exchange_rate').value) || 25;

            const totalZMW = number * frequency * unitCost;
            const totalUSD = totalZMW / exchangeRate;

            const totalZMWInput = item.querySelector('.total_amount_zmw');
            const totalUSDInput = item.querySelector('.total_amount_usd');

            totalZMWInput.value = totalZMW.toFixed(2);
            totalUSDInput.value = totalUSD.toFixed(2);

            updateTotals();
        };

        numberInput.addEventListener('input', calculateTotal);
        frequencyInput.addEventListener('input', calculateTotal);
        unitCostInput.addEventListener('input', calculateTotal);
        sectionSelect.addEventListener('change', function() {
            updateSectionColor(item, this.value);
            updateTotals();
        });
    });

    // Exchange rate change listener
    document.getElementById('exchange_rate').addEventListener('input', function() {
        // Recalculate all item totals
        const items = document.querySelectorAll('#budgetItemsContainer .budget-item-card');
        const exchangeRate = parseFloat(this.value) || 25;

        items.forEach(item => {
            const number = parseFloat(item.querySelector('.number').value) || 0;
            const frequency = parseFloat(item.querySelector('.frequency').value) || 0;
            const unitCost = parseFloat(item.querySelector('.unit_cost').value) || 0;
            const totalZMW = number * frequency * unitCost;
            const totalUSD = totalZMW / exchangeRate;

            const totalZMWInput = item.querySelector('.total_amount_zmw');
            const totalUSDInput = item.querySelector('.total_amount_usd');

            totalZMWInput.value = totalZMW.toFixed(2);
            totalUSDInput.value = totalUSD.toFixed(2);
        });

        updateTotals();
    });

    // Add sample data button for testing
    const actionButtons = document.querySelector('.card-body.text-center');
    if (actionButtons) {
        const testBtn = document.createElement('button');
        testBtn.type = 'button';
        testBtn.className = 'btn btn-info btn-lg me-3';
        testBtn.innerHTML = '<i class="bi bi-plus-circle me-2"></i> Add Sample Item';
        testBtn.onclick = function() {
            addSection('A - CORE PROGRAM EXPENDITURE');
            const lastItem = document.querySelector('#budgetItemsContainer .budget-item-card:last-child');
            if (lastItem) {
                lastItem.querySelector('.objective').value = 'OBJECTIVE 1: To strengthen technical and institutional capacity of CTPD for effective delivery of its tobacco control mandate in Zambia.';
                lastItem.querySelector('.activity').value = 'ACTIVITY 1.1: Conduct the Quarterly review monitoring and evaluation process';
                lastItem.querySelector('.description_cost_category').value = 'Civil Society Institutional Support';
                lastItem.querySelector('.description_cost_item').value = 'Venue and Conference';
                lastItem.querySelector('.number').value = '1';
                lastItem.querySelector('.frequency').value = '8';
                lastItem.querySelector('.unit').value = 'Quarter';
                lastItem.querySelector('.unit_cost').value = '12522';
                lastItem.querySelector('.comments').value = 'Venue and conference at K450 per person for 22 persons, for 2 days, per quarter';

                // Trigger calculation
                const event = new Event('input');
                lastItem.querySelector('.unit_cost').dispatchEvent(event);
            }
        };
        actionButtons.prepend(testBtn);
    }
});
</script>
@endpush
