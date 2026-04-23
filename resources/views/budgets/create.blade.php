@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create New Detailed Budget</h1>
        <a href="{{ route('budgets.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    <form action="{{ route('budgets.store') }}" method="POST" id="budgetForm">
        @csrf

        <!-- Basic Budget Information Card -->
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="m-0 font-weight-bold">Basic Budget Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="project_title" class="form-label">Project Title *</label>
                        <input type="text" class="form-control" id="project_title" name="project_title"
                               value="{{ old('project_title') }}" placeholder="Enter project title" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="project_code" class="form-label">Project Code *</label>
                        <input type="text" class="form-control" id="project_code" name="project_code"
                               value="{{ old('project_code') }}" placeholder="e.g., TC-ZM-2024-001" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="project_goal" class="form-label">Project Goal/Objective *</label>
                        <textarea class="form-control" id="project_goal" name="project_goal" rows="3"
                                  placeholder="Enter project goal/objective" required>{{ old('project_goal') }}</textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="duration" class="form-label">Duration *</label>
                        <input type="text" class="form-control" id="duration" name="duration"
                               value="{{ old('duration') }}" placeholder="e.g., 18 Months (2024-2025)" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="exchange_rate" class="form-label">Exchange Rate (1 USD = ZMW) *</label>
                        <input type="number" class="form-control" id="exchange_rate" name="exchange_rate"
                               step="0.01" value="{{ old('exchange_rate', 25.00) }}" required onchange="updateAllUSDAmounts()">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="draft">Draft</option>
                            <option value="pending">Pending Review</option>
                            <option value="approved">Approved</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="total_budget_zmw_display" class="form-label">Total Budget (ZMW)</label>
                        <input type="text" class="form-control" id="total_budget_zmw_display" readonly value="0.00">
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
                               value="{{ old('contact_person') }}" placeholder="Enter contact person" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="contact_email" class="form-label">Contact Email *</label>
                        <input type="email" class="form-control" id="contact_email" name="contact_email"
                               value="{{ old('contact_email') }}" placeholder="email@example.com" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="contact_phone" class="form-label">Contact Phone *</label>
                        <input type="text" class="form-control" id="contact_phone" name="contact_phone"
                               value="{{ old('contact_phone') }}" placeholder="+260XXXXXXXXX" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Budget Breakdown -->
        <div class="card shadow mb-4">
            <div class="card-header bg-success text-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Detailed Budget Breakdown</h6>
                <button type="button" class="btn btn-light btn-sm" onclick="addSection()">
                    <i class="bi bi-plus-circle"></i> Add New Section
                </button>
            </div>
            <div class="card-body p-0">
                <div id="sections-container">
                    <!-- Default Section A -->
                    <div class="section-block mb-4" data-section-id="0">
                        <div class="section-header bg-light p-3 border">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <span class="input-group-text">Section:</span>
                                        <input type="text" class="form-control section-name" name="sections[0][name]"
                                               value="A - CORE PROGRAM EXPENDITURE" placeholder="Section Name" style="font-weight: bold;">
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="addObjective(this)">
                                        <i class="bi bi-plus-circle"></i> Add Objective
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeSection(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="objectives-container p-3 border border-top-0">
                            <!-- Objectives will be added here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Budget Summary -->
        <div class="card shadow mb-4">
            <div class="card-header bg-dark text-white py-3">
                <h6 class="m-0 font-weight-bold">Budget Summary</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Section</th>
                                <th class="text-end">Total ZMW</th>
                                <th class="text-end">Total USD</th>
                            </tr>
                        </thead>
                        <tbody id="section-summaries">
                            <!-- Section summaries will be populated here -->
                        </tbody>
                        <tfoot>
                            <tr class="table-primary">
                                <th>GRAND TOTAL</th>
                                <th class="text-end"><span id="grand-total-zmw">0.00</span></th>
                                <th class="text-end"><span id="grand-total-usd">0.00</span></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Hidden fields -->
        <input type="hidden" name="total_budget_zmw" id="total_budget_zmw_hidden" value="0">
        <input type="hidden" name="total_budget_usd" id="total_budget_usd_hidden" value="0">

        <!-- Submit Buttons -->
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-save me-2"></i> Save Budget
                </button>
                <a href="{{ route('budgets.index') }}" class="btn btn-secondary btn-lg px-5">
                    <i class="bi bi-x-circle me-2"></i> Cancel
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Template for new section -->
<template id="section-template">
    <div class="section-block mb-4" data-section-id="{sectionId}">
        <div class="section-header bg-light p-3 border">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text">Section:</span>
                        <input type="text" class="form-control section-name" name="sections[{sectionId}][name]"
                               placeholder="e.g., B - INSTITUTIONAL SUPPORT EXPENDITURE" style="font-weight: bold;">
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <button type="button" class="btn btn-primary btn-sm" onclick="addObjective(this)">
                        <i class="bi bi-plus-circle"></i> Add Objective
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeSection(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="objectives-container p-3 border border-top-0">
            <!-- Objectives will be added here -->
        </div>
    </div>
</template>

<!-- Template for new objective -->
<template id="objective-template">
    <div class="objective-block mb-3 p-3 bg-white border rounded">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="flex-grow-1 me-3">
                <label class="form-label fw-bold">Objective:</label>
                <input type="text" class="form-control objective-text" name="sections[{sectionId}][objectives][{objectiveId}][text]"
                       placeholder="e.g., OBJECTIVE 1: To strengthen technical and institutional capacity...">
            </div>
            <div>
                <button type="button" class="btn btn-success btn-sm" onclick="addActivity(this)">
                    <i class="bi bi-plus-circle"></i> Add Activity
                </button>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeObjective(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
        <div class="activities-container ps-3">
            <!-- Activities will be added here -->
        </div>
    </div>
</template>

<!-- Template for new activity -->
<template id="activity-template">
    <div class="activity-block mb-3 p-3 bg-light border rounded">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="flex-grow-1 me-3">
                <label class="form-label fw-bold">Activity:</label>
                <input type="text" class="form-control activity-text" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][text]"
                       placeholder="e.g., ACTIVITY 1.1: Conduct the Quarterly review monitoring and evaluation process">
            </div>
            <div>
                <button type="button" class="btn btn-info btn-sm" onclick="addBudgetLine(this)">
                    <i class="bi bi-plus-circle"></i> Add Budget Line
                </button>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeActivity(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>

        <!-- Budget Lines Table -->
        <div class="table-responsive">
            <table class="table table-sm table-bordered budget-lines-table">
                <thead class="table-secondary">
                    <tr>
                        <th>Component</th>
                        <th>Cost Category</th>
                        <th>Cost Item</th>
                        <th>No.</th>
                        <th>Freq.</th>
                        <th>Unit</th>
                        <th>Unit Cost (ZMW)</th>
                        <th>Total (ZMW)</th>
                        <th>Total (USD)</th>
                        <th>Revised Y1</th>
                        <th>Revised Y2</th>
                        <th>Revised Y3</th>
                        <th>Comment</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="budget-lines-body">
                    <!-- Budget lines will be added here -->
                </tbody>
                <tfoot>
                    <tr class="table-active fw-bold">
                        <td colspan="7" class="text-end">Activity Total:</td>
                        <td class="activity-total-zmw">0.00</td>
                        <td class="activity-total-usd">0.00</td>
                        <td class="activity-total-y1">0.00</td>
                        <td class="activity-total-y2">0.00</td>
                        <td class="activity-total-y3">0.00</td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</template>

<!-- Template for budget line -->
<template id="budget-line-template">
    <tr class="budget-line-row">
        <td><input type="text" class="form-control form-control-sm" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][lines][{lineId}][component]" placeholder="Component"></td>
        <td><input type="text" class="form-control form-control-sm" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][lines][{lineId}][cost_category]" placeholder="Cost Category"></td>
        <td><input type="text" class="form-control form-control-sm" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][lines][{lineId}][cost_item]" placeholder="Cost Item"></td>
        <td><input type="number" class="form-control form-control-sm number-input" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][lines][{lineId}][number]" value="1" min="0" step="1" onchange="calculateLineTotal(this)"></td>
        <td><input type="number" class="form-control form-control-sm frequency-input" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][lines][{lineId}][frequency]" value="1" min="0" step="1" onchange="calculateLineTotal(this)"></td>
        <td><input type="text" class="form-control form-control-sm" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][lines][{lineId}][unit]" placeholder="Unit"></td>
        <td><input type="number" class="form-control form-control-sm unit-cost-input" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][lines][{lineId}][unit_cost]" value="0.00" step="0.01" min="0" onchange="calculateLineTotal(this)"></td>
        <td><input type="number" class="form-control form-control-sm total-zmw-input" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][lines][{lineId}][total_zmw]" value="0.00" step="0.01" readonly></td>
        <td><input type="number" class="form-control form-control-sm total-usd-input" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][lines][{lineId}][total_usd]" value="0.00" step="0.01" readonly></td>
        <td><input type="number" class="form-control form-control-sm revised-y1" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][lines][{lineId}][revised_y1]" value="0.00" step="0.01" min="0" onchange="updateActivityTotals(this.closest('.activity-block'))"></td>
        <td><input type="number" class="form-control form-control-sm revised-y2" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][lines][{lineId}][revised_y2]" value="0.00" step="0.01" min="0" onchange="updateActivityTotals(this.closest('.activity-block'))"></td>
        <td><input type="number" class="form-control form-control-sm revised-y3" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][lines][{lineId}][revised_y3]" value="0.00" step="0.01" min="0" onchange="updateActivityTotals(this.closest('.activity-block'))"></td>
        <td><input type="text" class="form-control form-control-sm" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][lines][{lineId}][comment]" placeholder="Comment"></td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="removeBudgetLine(this)"><i class="bi bi-trash"></i></button></td>
    </tr>
</template>

<style>
.section-block {
    border-radius: 5px;
}
.objective-block {
    border-left: 3px solid #4e73df !important;
}
.activity-block {
    border-left: 3px solid #1cc88a !important;
}
.budget-lines-table {
    font-size: 0.8rem;
}
.budget-lines-table td, .budget-lines-table th {
    padding: 0.3rem;
    vertical-align: middle;
}
.budget-lines-table input {
    min-width: 80px;
}
</style>

<script>
let sectionCounter = 1;
let objectiveCounter = 0;
let activityCounter = 0;
let lineCounter = 0;

// Add new section
function addSection() {
    const container = document.getElementById('sections-container');
    const template = document.getElementById('section-template');
    const sectionId = sectionCounter++;

    let content = template.innerHTML.replace(/{sectionId}/g, sectionId);
    container.insertAdjacentHTML('beforeend', content);
    updateAllTotals();
}

// Remove section
function removeSection(button) {
    const section = button.closest('.section-block');
    const container = document.getElementById('sections-container');
    if (container.children.length > 1) {
        section.remove();
        updateAllTotals();
    } else {
        alert('At least one section is required.');
    }
}

// Add objective
function addObjective(button) {
    const section = button.closest('.section-block');
    const objectivesContainer = section.querySelector('.objectives-container');
    const sectionId = section.dataset.sectionId;
    const objectiveId = `${sectionId}_${objectiveCounter++}`;

    const template = document.getElementById('objective-template');
    let content = template.innerHTML
        .replace(/{sectionId}/g, sectionId)
        .replace(/{objectiveId}/g, objectiveId);

    objectivesContainer.insertAdjacentHTML('beforeend', content);
    updateAllTotals();
}

// Remove objective
function removeObjective(button) {
    const objective = button.closest('.objective-block');
    objective.remove();
    updateAllTotals();
}

// Add activity
function addActivity(button) {
    const objective = button.closest('.objective-block');
    const activitiesContainer = objective.querySelector('.activities-container');
    const section = objective.closest('.section-block');
    const sectionId = section.dataset.sectionId;
    const objectiveInput = objective.querySelector('.objective-text');
    const objectiveName = objectiveInput.name;
    const matches = objectiveName.match(/sections\[(\d+)\]\[objectives\]\[([^\]]+)\]/);
    const objectiveId = matches ? matches[2] : `${sectionId}_${activityCounter}`;
    const activityId = `${objectiveId}_${activityCounter++}`;

    const template = document.getElementById('activity-template');
    let content = template.innerHTML
        .replace(/{sectionId}/g, sectionId)
        .replace(/{objectiveId}/g, objectiveId)
        .replace(/{activityId}/g, activityId);

    activitiesContainer.insertAdjacentHTML('beforeend', content);
}

// Remove activity
function removeActivity(button) {
    const activity = button.closest('.activity-block');
    activity.remove();
    updateAllTotals();
}

// Add budget line
function addBudgetLine(button) {
    const activity = button.closest('.activity-block');
    const tbody = activity.querySelector('.budget-lines-body');
    const section = activity.closest('.section-block');
    const objective = activity.closest('.objective-block');

    const sectionId = section.dataset.sectionId;
    const objectiveInput = objective.querySelector('.objective-text');
    const objectiveName = objectiveInput.name;
    const activityInput = activity.querySelector('.activity-text');
    const activityName = activityInput.name;

    const objMatches = objectiveName.match(/sections\[(\d+)\]\[objectives\]\[([^\]]+)\]/);
    const actMatches = activityName.match(/sections\[\d+\]\[objectives\]\[[^\]]+\]\[activities\]\[([^\]]+)\]/);

    const objectiveId = objMatches ? objMatches[2] : `${sectionId}_obj`;
    const activityId = actMatches ? actMatches[1] : `${objectiveId}_act`;
    const lineId = `${activityId}_${lineCounter++}`;

    const template = document.getElementById('budget-line-template');
    let content = template.innerHTML
        .replace(/{sectionId}/g, sectionId)
        .replace(/{objectiveId}/g, objectiveId)
        .replace(/{activityId}/g, activityId)
        .replace(/{lineId}/g, lineId);

    tbody.insertAdjacentHTML('beforeend', content);
    updateActivityTotals(activity);
}

// Remove budget line
function removeBudgetLine(button) {
    const row = button.closest('tr');
    const activity = row.closest('.activity-block');
    row.remove();
    updateActivityTotals(activity);
}

// Calculate line total
function calculateLineTotal(element) {
    const row = element.closest('tr');
    const number = parseFloat(row.querySelector('.number-input').value) || 0;
    const frequency = parseFloat(row.querySelector('.frequency-input').value) || 0;
    const unitCost = parseFloat(row.querySelector('.unit-cost-input').value) || 0;
    const exchangeRate = parseFloat(document.getElementById('exchange_rate').value) || 25.00;

    const totalZMW = number * frequency * unitCost;
    const totalUSD = totalZMW / exchangeRate;

    row.querySelector('.total-zmw-input').value = totalZMW.toFixed(2);
    row.querySelector('.total-usd-input').value = totalUSD.toFixed(2);

    const activity = row.closest('.activity-block');
    updateActivityTotals(activity);
}

// Update activity totals
function updateActivityTotals(activity) {
    const rows = activity.querySelectorAll('.budget-line-row');
    let totalZMW = 0, totalUSD = 0, totalY1 = 0, totalY2 = 0, totalY3 = 0;

    rows.forEach(row => {
        totalZMW += parseFloat(row.querySelector('.total-zmw-input').value) || 0;
        totalUSD += parseFloat(row.querySelector('.total-usd-input').value) || 0;
        totalY1 += parseFloat(row.querySelector('.revised-y1')?.value) || 0;
        totalY2 += parseFloat(row.querySelector('.revised-y2')?.value) || 0;
        totalY3 += parseFloat(row.querySelector('.revised-y3')?.value) || 0;
    });

    activity.querySelector('.activity-total-zmw').textContent = totalZMW.toFixed(2);
    activity.querySelector('.activity-total-usd').textContent = totalUSD.toFixed(2);
    activity.querySelector('.activity-total-y1').textContent = totalY1.toFixed(2);
    activity.querySelector('.activity-total-y2').textContent = totalY2.toFixed(2);
    activity.querySelector('.activity-total-y3').textContent = totalY3.toFixed(2);

    updateAllTotals();
}

// Update all totals and summaries
function updateAllTotals() {
    const sections = document.querySelectorAll('.section-block');
    const summaryBody = document.getElementById('section-summaries');
    let grandTotalZMW = 0;
    let grandTotalUSD = 0;

    // Clear summary
    summaryBody.innerHTML = '';

    sections.forEach((section, index) => {
        const sectionName = section.querySelector('.section-name').value || `Section ${index + 1}`;
        const activities = section.querySelectorAll('.activity-block');
        let sectionTotalZMW = 0;
        let sectionTotalUSD = 0;

        activities.forEach(activity => {
            const totalZMW = parseFloat(activity.querySelector('.activity-total-zmw').textContent) || 0;
            const totalUSD = parseFloat(activity.querySelector('.activity-total-usd').textContent) || 0;
            sectionTotalZMW += totalZMW;
            sectionTotalUSD += totalUSD;
        });

        grandTotalZMW += sectionTotalZMW;
        grandTotalUSD += sectionTotalUSD;

        // Add to summary table
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${sectionName}</td>
            <td class="text-end">${sectionTotalZMW.toFixed(2)}</td>
            <td class="text-end">${sectionTotalUSD.toFixed(2)}</td>
        `;
        summaryBody.appendChild(row);
    });

    // Update grand totals
    document.getElementById('grand-total-zmw').textContent = grandTotalZMW.toFixed(2);
    document.getElementById('grand-total-usd').textContent = grandTotalUSD.toFixed(2);
    document.getElementById('total_budget_zmw_display').value = grandTotalZMW.toFixed(2);
    document.getElementById('total_budget_zmw_hidden').value = grandTotalZMW.toFixed(2);
    document.getElementById('total_budget_usd_hidden').value = grandTotalUSD.toFixed(2);
}

// Update all USD amounts when exchange rate changes
function updateAllUSDAmounts() {
    const rows = document.querySelectorAll('.budget-line-row');
    const exchangeRate = parseFloat(document.getElementById('exchange_rate').value) || 25.00;

    rows.forEach(row => {
        const totalZMW = parseFloat(row.querySelector('.total-zmw-input').value) || 0;
        const totalUSD = totalZMW / exchangeRate;
        row.querySelector('.total-usd-input').value = totalUSD.toFixed(2);
    });

    // Update all activity totals
    document.querySelectorAll('.activity-block').forEach(activity => {
        updateActivityTotals(activity);
    });
}

// Initialize with one empty section
document.addEventListener('DOMContentLoaded', function() {
    // Add first objective to default section
    const defaultSection = document.querySelector('.section-block');
    if (defaultSection) {
        addObjective(defaultSection.querySelector('.btn-primary'));
    }
});
</script>
@endsection
