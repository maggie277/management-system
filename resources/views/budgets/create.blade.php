@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1" style="color: #000; font-weight: 600;">New Budget</h4>
            <p class="text-muted small mb-0">Create a detailed project budget</p>
        </div>
        <a href="{{ route('budgets.index') }}" class="btn btn-outline-dark btn-sm" style="border-radius: 20px;">
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

    <form action="{{ route('budgets.store') }}" method="POST" id="budgetForm">
        @csrf

        <!-- Basic Information -->
        <div class="card border-0 shadow-sm mb-3" style="border-radius: 8px;">
            <div class="card-header bg-white border-0 py-3" style="border-radius: 8px 8px 0 0;">
                <h6 class="mb-0" style="color: #000; font-weight: 600; font-size: 0.9rem;">Basic Information</h6>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold" style="color: #333;">Project Title *</label>
                        <input type="text" class="form-control form-control-sm @error('project_title') is-invalid @enderror"
                               name="project_title" value="{{ old('project_title') }}" placeholder="Enter project title"
                               style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">
                        @error('project_title')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold" style="color: #333;">Project Code *</label>
                        <input type="text" class="form-control form-control-sm @error('project_code') is-invalid @enderror"
                               name="project_code" value="{{ old('project_code') }}" placeholder="e.g., TC-ZM-2024-001"
                               style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">
                        @error('project_code')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold" style="color: #333;">Project Goal/Objective *</label>
                        <textarea class="form-control form-control-sm @error('project_goal') is-invalid @enderror"
                                  name="project_goal" rows="2" placeholder="Enter project goal/objective"
                                  style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">{{ old('project_goal') }}</textarea>
                        @error('project_goal')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold" style="color: #333;">Duration *</label>
                        <input type="text" class="form-control form-control-sm @error('duration') is-invalid @enderror"
                               name="duration" value="{{ old('duration') }}" placeholder="e.g., 18 Months"
                               style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold" style="color: #333;">Exchange Rate (to ZMW) *</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text" style="border-radius: 6px 0 0 6px; background: #f8f8f8; border: 1px solid #ddd; font-size: 0.75rem;">1 USD/EUR/GBP =</span>
                            <input type="number" class="form-control form-control-sm @error('exchange_rate') is-invalid @enderror"
                                   name="exchange_rate" id="exchange_rate" step="0.01" value="{{ old('exchange_rate', 25.00) }}"
                                   style="border-radius: 0 6px 6px 0; border: 1px solid #ddd; padding: 8px 12px;">
                            <span class="input-group-text" style="border-radius: 0 6px 6px 0; background: #f8f8f8;">ZMW</span>
                        </div>
                        <small class="text-muted">Exchange rate for USD, EUR, GBP to ZMW</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold" style="color: #333;">Status *</label>
                        <select class="form-select form-select-sm @error('status') is-invalid @enderror" name="status"
                                style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">
                            <option value="draft">Draft</option>
                            <option value="pending">Pending Review</option>
                            <option value="approved">Approved</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold" style="color: #333;">Total Budget (ZMW)</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text" style="border-radius: 6px 0 0 6px; background: #28a745; color: #fff; border: none; font-size: 0.75rem;">ZMW</span>
                            <input type="text" class="form-control form-control-sm" id="total_budget_zmw_display" readonly
                                   value="0.00" style="border-radius: 0 6px 6px 0; border: 1px solid #ddd; background: #f8f8f8; font-weight: 600;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="card border-0 shadow-sm mb-3" style="border-radius: 8px;">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0" style="color: #000; font-weight: 600; font-size: 0.9rem;">Contact Information</h6>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold" style="color: #333;">Contact Person *</label>
                        <input type="text" class="form-control form-control-sm @error('contact_person') is-invalid @enderror"
                               name="contact_person" value="{{ old('contact_person') }}" placeholder="Full name"
                               style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold" style="color: #333;">Email *</label>
                        <input type="email" class="form-control form-control-sm @error('contact_email') is-invalid @enderror"
                               name="contact_email" value="{{ old('contact_email') }}" placeholder="email@example.com"
                               style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold" style="color: #333;">Phone *</label>
                        <input type="text" class="form-control form-control-sm @error('contact_phone') is-invalid @enderror"
                               name="contact_phone" value="{{ old('contact_phone') }}" placeholder="+260XXXXXXXXX"
                               style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Budget Breakdown -->
        <div class="card border-0 shadow-sm mb-3" style="border-radius: 8px;">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0" style="color: #000; font-weight: 600; font-size: 0.9rem;">Budget Breakdown</h6>
                <button type="button" class="btn btn-dark btn-sm" onclick="addSection()" style="border-radius: 20px; padding: 5px 14px; font-size: 0.75rem;">
                    <i class="bi bi-plus-lg me-1"></i> Add Section
                </button>
            </div>
            <div class="card-body pt-0 p-2">
                <div id="sections-container">
                    <div class="section-block mb-3" data-section-id="0">
                        <div style="background: #f8f8f8; padding: 10px 14px; border-radius: 6px 6px 0 0; border: 1px solid #eee;">
                            <div class="row align-items-center g-2">
                                <div class="col-md-8">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text" style="background: #000; color: #fff; border: none; font-size: 0.7rem; border-radius: 4px 0 0 4px;">Section</span>
                                        <input type="text" class="form-control section-name" name="sections[0][name]"
                                               value="A - CORE PROGRAM EXPENDITURE" style="font-weight: 600; font-size: 0.8rem; border-radius: 0 4px 4px 0;">
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <button type="button" class="btn btn-outline-dark btn-sm" onclick="addObjective(this)" style="border-radius: 20px; font-size: 0.7rem; padding: 3px 10px;">
                                        <i class="bi bi-plus"></i> Objective
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeSection(this)" style="border-radius: 20px; font-size: 0.7rem; padding: 3px 8px;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="objectives-container p-2" style="border: 1px solid #eee; border-top: 0; border-radius: 0 0 6px 6px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary - Simplified -->
        <div class="card border-0 shadow-sm mb-3" style="border-radius: 8px;">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0" style="color: #000; font-weight: 600; font-size: 0.9rem;">Budget Summary</h6>
            </div>
            <div class="card-body pt-0">
                <div class="row" id="summary-container">
                    <div class="col-md-3 mb-2">
                        <div class="card bg-light border-0" style="border-radius: 8px;">
                            <div class="card-body p-3 text-center">
                                <small class="text-muted">Total Budget</small>
                                <h5 class="mb-0 text-success" id="summary-zmw">ZMW 0.00</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2" id="summary-usd-container" style="display: none;">
                        <div class="card bg-light border-0" style="border-radius: 8px;">
                            <div class="card-body p-3 text-center">
                                <small class="text-muted">Total (USD)</small>
                                <h5 class="mb-0" id="summary-usd">$0.00</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2" id="summary-eur-container" style="display: none;">
                        <div class="card bg-light border-0" style="border-radius: 8px;">
                            <div class="card-body p-3 text-center">
                                <small class="text-muted">Total (EUR)</small>
                                <h5 class="mb-0" id="summary-eur">€0.00</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2" id="summary-gbp-container" style="display: none;">
                        <div class="card bg-light border-0" style="border-radius: 8px;">
                            <div class="card-body p-3 text-center">
                                <small class="text-muted">Total (GBP)</small>
                                <h5 class="mb-0" id="summary-gbp">£0.00</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="card bg-light border-0" style="border-radius: 8px;">
                            <div class="card-body p-3 text-center">
                                <small class="text-muted">Year 1 Total</small>
                                <h5 class="mb-0" id="summary-y1">0.00</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="card bg-light border-0" style="border-radius: 8px;">
                            <div class="card-body p-3 text-center">
                                <small class="text-muted">Year 2 Total</small>
                                <h5 class="mb-0" id="summary-y2">0.00</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="card bg-light border-0" style="border-radius: 8px;">
                            <div class="card-body p-3 text-center">
                                <small class="text-muted">Year 3 Total</small>
                                <h5 class="mb-0" id="summary-y3">0.00</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="total_budget_zmw" id="total_budget_zmw_hidden" value="0">
        <input type="hidden" name="total_budget_usd" id="total_budget_usd_hidden" value="0">

        <div class="text-center mb-4">
            <button type="submit" class="btn btn-dark px-4 me-2" id="submitBtn" style="border-radius: 20px; font-size: 0.85rem;">
                <i class="bi bi-check-lg me-1"></i> Save Budget
            </button>
            <a href="{{ route('budgets.index') }}" class="btn btn-outline-dark px-4" style="border-radius: 20px; font-size: 0.85rem;">
                Cancel
            </a>
        </div>
    </form>
</div>

<!-- TEMPLATES -->
<template id="section-template">
    <div class="section-block mb-3" data-section-id="{sectionId}">
        <div style="background: #f8f8f8; padding: 10px 14px; border-radius: 6px 6px 0 0; border: 1px solid #eee;">
            <div class="row align-items-center g-2">
                <div class="col-md-8">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text" style="background: #000; color: #fff; border: none; font-size: 0.7rem; border-radius: 4px 0 0 4px;">Section</span>
                        <input type="text" class="form-control section-name" name="sections[{sectionId}][name]"
                               placeholder="Section name" style="font-weight: 600; font-size: 0.8rem; border-radius: 0 4px 4px 0;">
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <button type="button" class="btn btn-outline-dark btn-sm" onclick="addObjective(this)" style="border-radius: 20px; font-size: 0.7rem; padding: 3px 10px;">
                        <i class="bi bi-plus"></i> Objective
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeSection(this)" style="border-radius: 20px; font-size: 0.7rem; padding: 3px 8px;">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="objectives-container p-2" style="border: 1px solid #eee; border-top: 0; border-radius: 0 0 6px 6px;"></div>
    </div>
</template>

<template id="objective-template">
    <div class="objective-block mb-2 p-2" style="border-left: 3px solid #000; background: #fff; border-radius: 0 4px 4px 0;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="flex-grow-1 me-2">
                <input type="text" class="form-control form-control-sm objective-text"
                       name="sections[{sectionId}][objectives][{objectiveId}][description]"
                       placeholder="Objective description..." style="font-size: 0.75rem; border: 1px solid #ddd;">
            </div>
            <div class="d-flex gap-1">
                <button type="button" class="btn btn-outline-dark btn-sm" onclick="addActivity(this)" style="border-radius: 20px; font-size: 0.65rem; padding: 2px 8px;">
                    <i class="bi bi-plus"></i> Activity
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeObjective(this)" style="border-radius: 20px; font-size: 0.65rem; padding: 2px 6px;">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
        <div class="activities-container"></div>
    </div>
</template>

<template id="activity-template">
    <div class="activity-block mb-2 p-2" style="border-left: 3px solid #28a745; background: #f9faf9; border-radius: 0 4px 4px 0;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="flex-grow-1 me-2">
                <input type="text" class="form-control form-control-sm activity-text"
                       name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][description]"
                       placeholder="Activity description..." style="font-size: 0.75rem; border: 1px solid #ddd;">
            </div>
            <div class="d-flex gap-1">
                <button type="button" class="btn btn-outline-dark btn-sm" onclick="addBudgetLine(this)" style="border-radius: 20px; font-size: 0.65rem; padding: 2px 8px;">
                    <i class="bi bi-plus"></i> Add Item
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeActivity(this)" style="border-radius: 20px; font-size: 0.65rem; padding: 2px 6px;">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-bordered budget-lines-table" style="font-size: 0.7rem; margin: 0;">
                <thead style="background: #f0f0f0; font-size: 0.65rem;">
                    <tr>
                        <th>Description</th>
                        <th>No.</th>
                        <th>Freq.</th>
                        <th>Unit</th>
                        <th>Cost</th>
                        <th>Currency</th>
                        <th>Total (ZMW)</th>
                        <th>Y1</th>
                        <th>Y2</th>
                        <th>Y3</th>
                        <th>Note</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="budget-lines-body"></tbody>
                <tfoot>
                    <tr style="font-weight: 600; font-size: 0.7rem; background: #f8f8f8;">
                        <td colspan="6" class="text-end">Activity Total:</td>
                        <td class="activity-total text-end">0.00</td>
                        <td class="activity-total-y1 text-end">0.00</td>
                        <td class="activity-total-y2 text-end">0.00</td>
                        <td class="activity-total-y3 text-end">0.00</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</template>

<template id="budget-line-template">
    <tr class="budget-line-row">
        <td><input type="text" class="form-control form-control-sm" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][items][{lineId}][description]" placeholder="Description" style="font-size:0.7rem; min-width:120px;"></td>
        <td><input type="number" class="form-control form-control-sm number-input" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][items][{lineId}][number]" value="1" min="0" step="1" onchange="calculateLineTotal(this)" style="font-size:0.7rem; width:60px;"></td>
        <td><input type="number" class="form-control form-control-sm frequency-input" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][items][{lineId}][frequency]" value="1" min="0" step="1" onchange="calculateLineTotal(this)" style="font-size:0.7rem; width:60px;"></td>
        <td><input type="number" class="form-control form-control-sm unit-input" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][items][{lineId}][unit]" value="1" min="0" step="1" onchange="calculateLineTotal(this)" style="font-size:0.7rem; width:60px;"></td>
        <td><input type="number" class="form-control form-control-sm unit-cost-input" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][items][{lineId}][unit_cost]" value="0.00" step="0.01" onchange="calculateLineTotal(this)" style="font-size:0.7rem; width:80px;"></td>
        <td>
            <select class="form-control form-control-sm currency-select" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][items][{lineId}][currency]" onchange="calculateLineTotal(this)" style="font-size:0.7rem; width:70px;">
                <option value="ZMW">ZMW</option>
                <option value="USD">USD</option>
                <option value="EUR">EUR</option>
                <option value="GBP">GBP</option>
            </select>
        </td>
        <td><input type="number" class="form-control form-control-sm total-display" readonly value="0.00" style="font-size:0.7rem; width:90px; background:#f8f8f8;"></td>
        <td><input type="number" class="form-control form-control-sm year-input year-1" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][items][{lineId}][year_1]" value="0.00" step="0.01" onchange="updateActivityTotals(this.closest('.activity-block'))" style="font-size:0.7rem; width:70px;"></td>
        <td><input type="number" class="form-control form-control-sm year-input year-2" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][items][{lineId}][year_2]" value="0.00" step="0.01" onchange="updateActivityTotals(this.closest('.activity-block'))" style="font-size:0.7rem; width:70px;"></td>
        <td><input type="number" class="form-control form-control-sm year-input year-3" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][items][{lineId}][year_3]" value="0.00" step="0.01" onchange="updateActivityTotals(this.closest('.activity-block'))" style="font-size:0.7rem; width:70px;"></td>
        <td><input type="text" class="form-control form-control-sm" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][items][{lineId}][note]" placeholder="Note" style="font-size:0.7rem; min-width:100px;"></td>
        <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="removeBudgetLine(this)" style="border-radius:50%; padding:1px 5px; font-size:0.6rem;"><i class="bi bi-x"></i></button></td>
    </tr>
</template>

<script>
let sectionCounter = 1, objectiveCounter = 0, activityCounter = 0, lineCounter = 0;

function addSection() {
    const container = document.getElementById('sections-container');
    const template = document.getElementById('section-template');
    container.insertAdjacentHTML('beforeend', template.innerHTML.replace(/{sectionId}/g, sectionCounter++));
    updateAllTotals();
}

function removeSection(btn) {
    const section = btn.closest('.section-block');
    if (document.querySelectorAll('.section-block').length > 1) {
        if (confirm('Delete this section?')) { section.remove(); updateAllTotals(); }
    } else { alert('At least one section required.'); }
}

function addObjective(btn) {
    const section = btn.closest('.section-block');
    const container = section.querySelector('.objectives-container');
    const sectionId = section.dataset.sectionId;
    const objectiveId = sectionId + '_' + objectiveCounter++;
    container.insertAdjacentHTML('beforeend', document.getElementById('objective-template').innerHTML.replace(/{sectionId}/g, sectionId).replace(/{objectiveId}/g, objectiveId));
    updateAllTotals();
}

function removeObjective(btn) {
    if (confirm('Delete this objective?')) { btn.closest('.objective-block').remove(); updateAllTotals(); }
}

function addActivity(btn) {
    const objective = btn.closest('.objective-block');
    const section = objective.closest('.section-block');
    const sectionId = section.dataset.sectionId;
    const objInput = objective.querySelector('.objective-text');
    const match = objInput.name.match(/sections\[(\d+)\]\[objectives\]\[([^\]]+)\]/);
    const objectiveId = match ? match[2] : sectionId + '_' + activityCounter;
    const activityId = objectiveId + '_' + activityCounter++;
    objective.querySelector('.activities-container').insertAdjacentHTML('beforeend',
        document.getElementById('activity-template').innerHTML.replace(/{sectionId}/g, sectionId).replace(/{objectiveId}/g, objectiveId).replace(/{activityId}/g, activityId));
}

function removeActivity(btn) {
    if (confirm('Delete this activity?')) { btn.closest('.activity-block').remove(); updateAllTotals(); }
}

function addBudgetLine(btn) {
    const activity = btn.closest('.activity-block');
    const section = activity.closest('.section-block');
    const objective = activity.closest('.objective-block');
    const sectionId = section.dataset.sectionId;
    const objMatch = objective.querySelector('.objective-text').name.match(/sections\[(\d+)\]\[objectives\]\[([^\]]+)\]/);
    const actMatch = activity.querySelector('.activity-text').name.match(/activities\]\[([^\]]+)\]/);
    const objectiveId = objMatch ? objMatch[2] : sectionId + '_obj';
    const activityId = actMatch ? actMatch[1] : objectiveId + '_act';
    const lineId = lineCounter++;
    activity.querySelector('.budget-lines-body').insertAdjacentHTML('beforeend',
        document.getElementById('budget-line-template').innerHTML.replace(/{sectionId}/g, sectionId).replace(/{objectiveId}/g, objectiveId).replace(/{activityId}/g, activityId).replace(/{lineId}/g, lineId));
    updateActivityTotals(activity);
}

function removeBudgetLine(btn) {
    const activity = btn.closest('.activity-block');
    btn.closest('tr').remove();
    updateActivityTotals(activity);
}

function calculateLineTotal(el) {
    const row = el.closest('tr');
    const number = parseFloat(row.querySelector('.number-input').value) || 0;
    const frequency = parseFloat(row.querySelector('.frequency-input').value) || 0;
    const unit = parseFloat(row.querySelector('.unit-input').value) || 0;
    const unitCost = parseFloat(row.querySelector('.unit-cost-input').value) || 0;
    const currency = row.querySelector('.currency-select').value;
    const exchangeRate = parseFloat(document.getElementById('exchange_rate').value) || 25;

    // Calculate total in original currency: Number × Frequency × Unit × Cost
    const calculatedTotalOriginal = number * frequency * unit * unitCost;

    // Convert to ZMW for display and storage
    let totalInZMW = calculatedTotalOriginal;
    if (currency !== 'ZMW') {
        totalInZMW = calculatedTotalOriginal * exchangeRate;
    }

    // Display total in ZMW
    const totalDisplay = row.querySelector('.total-display');
    totalDisplay.value = totalInZMW.toFixed(2);

    // Auto-distribute to years equally
    const year1Input = row.querySelector('.year-1');
    const year2Input = row.querySelector('.year-2');
    const year3Input = row.querySelector('.year-3');

    if (year1Input.value == 0 && year2Input.value == 0 && year3Input.value == 0 && totalInZMW > 0) {
        const equalShare = totalInZMW / 3;
        year1Input.value = equalShare.toFixed(2);
        year2Input.value = equalShare.toFixed(2);
        year3Input.value = equalShare.toFixed(2);
    }

    updateActivityTotals(row.closest('.activity-block'));
}

function updateActivityTotals(activity) {
    const rows = activity.querySelectorAll('.budget-line-row');
    let total = 0, y1 = 0, y2 = 0, y3 = 0;

    rows.forEach(r => {
        total += parseFloat(r.querySelector('.total-display').value) || 0;
        y1 += parseFloat(r.querySelector('.year-1').value) || 0;
        y2 += parseFloat(r.querySelector('.year-2').value) || 0;
        y3 += parseFloat(r.querySelector('.year-3').value) || 0;
    });

    activity.querySelector('.activity-total').textContent = total.toFixed(2);
    activity.querySelector('.activity-total-y1').textContent = y1.toFixed(2);
    activity.querySelector('.activity-total-y2').textContent = y2.toFixed(2);
    activity.querySelector('.activity-total-y3').textContent = y3.toFixed(2);

    updateAllTotals();
}

function updateAllTotals() {
    const exchangeRate = parseFloat(document.getElementById('exchange_rate').value) || 25;

    // Calculate all totals from all rows
    let totalZMW = 0;
    let totalUSD = 0;
    let totalEUR = 0;
    let totalGBP = 0;
    let totalY1 = 0;
    let totalY2 = 0;
    let totalY3 = 0;

    document.querySelectorAll('.budget-line-row').forEach(row => {
        const totalInZMW = parseFloat(row.querySelector('.total-display').value) || 0;
        const currency = row.querySelector('.currency-select').value;
        const year1 = parseFloat(row.querySelector('.year-1').value) || 0;
        const year2 = parseFloat(row.querySelector('.year-2').value) || 0;
        const year3 = parseFloat(row.querySelector('.year-3').value) || 0;

        totalZMW += totalInZMW;

        // Calculate original currency amount
        let originalAmount = totalInZMW;
        if (currency !== 'ZMW') {
            originalAmount = totalInZMW / exchangeRate;
        }

        if (currency === 'USD') totalUSD += originalAmount;
        if (currency === 'EUR') totalEUR += originalAmount;
        if (currency === 'GBP') totalGBP += originalAmount;

        totalY1 += year1;
        totalY2 += year2;
        totalY3 += year3;
    });

    // Update summary display
    document.getElementById('summary-zmw').innerHTML = 'ZMW ' + totalZMW.toFixed(2);
    document.getElementById('summary-y1').innerHTML = totalY1.toFixed(2);
    document.getElementById('summary-y2').innerHTML = totalY2.toFixed(2);
    document.getElementById('summary-y3').innerHTML = totalY3.toFixed(2);
    document.getElementById('total_budget_zmw_display').value = totalZMW.toFixed(2);
    document.getElementById('total_budget_zmw_hidden').value = totalZMW.toFixed(2);

    // Show/hide currency containers and update values
    if (totalUSD > 0) {
        document.getElementById('summary-usd-container').style.display = 'block';
        document.getElementById('summary-usd').innerHTML = '$' + totalUSD.toFixed(2);
    } else {
        document.getElementById('summary-usd-container').style.display = 'none';
    }

    if (totalEUR > 0) {
        document.getElementById('summary-eur-container').style.display = 'block';
        document.getElementById('summary-eur').innerHTML = '€' + totalEUR.toFixed(2);
    } else {
        document.getElementById('summary-eur-container').style.display = 'none';
    }

    if (totalGBP > 0) {
        document.getElementById('summary-gbp-container').style.display = 'block';
        document.getElementById('summary-gbp').innerHTML = '£' + totalGBP.toFixed(2);
    } else {
        document.getElementById('summary-gbp-container').style.display = 'none';
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

document.getElementById('budgetForm').addEventListener('submit', function() {
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Saving...';
    updateAllTotals();
});

document.addEventListener('DOMContentLoaded', function() {
    const s = document.querySelector('.section-block');
    if (s) {
        const objectivesContainer = s.querySelector('.objectives-container');
        if (objectivesContainer && objectivesContainer.children.length === 0) {
            const addBtn = s.querySelector('button[onclick*="addObjective"]');
            if (addBtn) addObjective(addBtn);
        }
    }
    updateAllTotals();
});

// Update all calculations when exchange rate changes
const exchangeRateInput = document.getElementById('exchange_rate');
if (exchangeRateInput) {
    exchangeRateInput.addEventListener('input', function() {
        document.querySelectorAll('.budget-line-row').forEach(row => {
            calculateLineTotal(row.querySelector('.unit-cost-input'));
        });
    });
}
</script>
@endsection
