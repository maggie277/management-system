@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1" style="color: #000; font-weight: 600;">Edit Budget: {{ $budget->project_code }}</h4>
            <p class="text-muted small mb-0">{{ $budget->project_title }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('budgets.show', $budget->id) }}" class="btn btn-outline-dark btn-sm" style="border-radius: 20px;">
                <i class="bi bi-eye me-1"></i> View
            </a>
            <a href="{{ route('budgets.index') }}" class="btn btn-dark btn-sm" style="border-radius: 20px; background: #000; border: none;">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
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

    @php
        // Check if editing is allowed
        $canEdit = false;
        $editRestrictionMessage = '';

        $isAdmin = auth()->user()->isAdmin();
        $isManagement = auth()->user()->isManagement();

        if ($budget->status == 'approved') {
            if ($isAdmin) {
                $canEdit = true;
                $editRestrictionMessage = 'You are editing an approved budget as administrator.';
            } else {
                $editRestrictionMessage = 'This budget has been approved and cannot be edited. Only administrators can modify approved budgets.';
            }
        } elseif ($budget->status == 'draft') {
            $canEdit = true;
        } elseif ($budget->status == 'pending') {
            if ($isAdmin || $isManagement) {
                $canEdit = true;
                $editRestrictionMessage = 'You are editing a pending budget with management privileges.';
            } else {
                $pendingDays = $budget->updated_at->diffInDays(now());
                if ($pendingDays < 7) {
                    $canEdit = true;
                    $editRestrictionMessage = 'You can edit this pending budget within 7 days of last update. Remaining: ' . (7 - $pendingDays) . ' days.';
                } else {
                    $editRestrictionMessage = 'This pending budget is over 7 days old and can no longer be edited. Please contact management.';
                }
            }
        }

        $currenciesUsed = $budget->items->pluck('currency')->unique()->filter(function($curr) { return $curr != 'ZMW'; })->values();
        $mainCurrency = $currenciesUsed->first() ?? 'USD';
    @endphp

    @if(!$canEdit)
    <div class="alert alert-warning border-0 shadow-sm mb-4" style="border-radius: 8px;">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-3" style="font-size: 1.5rem;"></i>
            <div>
                <strong>Editing Restricted</strong><br>
                {{ $editRestrictionMessage }}
                @if($budget->status == 'approved')
                    <a href="{{ route('budgets.show', $budget->id) }}" class="alert-link">View budget details</a>
                @endif
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('budgets.update', $budget->id) }}" method="POST" id="budgetForm" @if(!$canEdit) onsubmit="return false;" @endif>
        @csrf
        @method('PUT')

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
                               name="project_title" value="{{ old('project_title', $budget->project_title) }}"
                               style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;"
                               @if(!$canEdit) readonly disabled @endif>
                        @error('project_title')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold" style="color: #333;">Project Code *</label>
                        <input type="text" class="form-control form-control-sm @error('project_code') is-invalid @enderror"
                               name="project_code" value="{{ old('project_code', $budget->project_code) }}"
                               style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;"
                               @if(!$canEdit) readonly disabled @endif>
                        @error('project_code')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold" style="color: #333;">Project Goal/Objective *</label>
                        <textarea class="form-control form-control-sm @error('project_goal') is-invalid @enderror"
                                  name="project_goal" rows="2"
                                  style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;"
                                  @if(!$canEdit) readonly disabled @endif>{{ old('project_goal', $budget->project_goal) }}</textarea>
                        @error('project_goal')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold" style="color: #333;">Duration *</label>
                        <input type="text" class="form-control form-control-sm @error('duration') is-invalid @enderror"
                               name="duration" value="{{ old('duration', $budget->duration) }}"
                               style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;"
                               @if(!$canEdit) readonly disabled @endif>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold" style="color: #333;">Exchange Rate (to ZMW) *</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text" style="border-radius: 6px 0 0 6px; background: #f8f8f8; border: 1px solid #ddd; font-size: 0.75rem;">1 {{ $mainCurrency }}/EUR/GBP =</span>
                            <input type="number" class="form-control form-control-sm @error('exchange_rate') is-invalid @enderror"
                                   name="exchange_rate" id="exchange_rate" step="0.01" value="{{ old('exchange_rate', $budget->exchange_rate) }}"
                                   style="border-radius: 0 6px 6px 0; border: 1px solid #ddd; padding: 8px 12px;"
                                   @if(!$canEdit) readonly disabled @endif>
                            <span class="input-group-text" style="border-radius: 0 6px 6px 0; background: #f8f8f8;">ZMW</span>
                        </div>
                        <small class="text-muted">Exchange rate for USD, EUR, GBP to ZMW</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold" style="color: #333;">Status *</label>
                        <select class="form-select form-select-sm @error('status') is-invalid @enderror" name="status"
                                style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;"
                                @if(!$canEdit) disabled @endif>
                            <option value="draft" {{ old('status', $budget->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="pending" {{ old('status', $budget->status) == 'pending' ? 'selected' : '' }}>Pending Review</option>
                            <option value="approved" {{ old('status', $budget->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold" style="color: #333;">Total Budget (ZMW)</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text" style="border-radius: 6px 0 0 6px; background: #28a745; color: #fff; border: none; font-size: 0.75rem;">ZMW</span>
                            <input type="text" class="form-control form-control-sm" id="total_budget_zmw_display" readonly
                                   value="{{ number_format($budget->total_budget_zmw, 2) }}"
                                   style="border-radius: 0 6px 6px 0; border: 1px solid #ddd; background: #f8f8f8; font-weight: 600;">
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
                               name="contact_person" value="{{ old('contact_person', $budget->contact_person) }}"
                               style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;"
                               @if(!$canEdit) readonly disabled @endif>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold" style="color: #333;">Email *</label>
                        <input type="email" class="form-control form-control-sm @error('contact_email') is-invalid @enderror"
                               name="contact_email" value="{{ old('contact_email', $budget->contact_email) }}"
                               style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;"
                               @if(!$canEdit) readonly disabled @endif>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold" style="color: #333;">Phone *</label>
                        <input type="text" class="form-control form-control-sm @error('contact_phone') is-invalid @enderror"
                               name="contact_phone" value="{{ old('contact_phone', $budget->contact_phone) }}"
                               style="border-radius: 6px; border: 1px solid #ddd; padding: 8px 12px;"
                               @if(!$canEdit) readonly disabled @endif>
                    </div>
                </div>
            </div>
        </div>

        <!-- Budget Breakdown -->
        <div class="card border-0 shadow-sm mb-3" style="border-radius: 8px;">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0" style="color: #000; font-weight: 600; font-size: 0.9rem;">Budget Breakdown</h6>
                @if($canEdit)
                <button type="button" class="btn btn-dark btn-sm" onclick="addSection()" style="border-radius: 20px; padding: 5px 14px; font-size: 0.75rem;">
                    <i class="bi bi-plus-lg me-1"></i> Add Section
                </button>
                @endif
            </div>
            <div class="card-body pt-0 p-2">
                <div id="sections-container"></div>
            </div>
        </div>

        <!-- Summary -->
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

        <input type="hidden" name="total_budget_zmw" id="total_budget_zmw_hidden" value="{{ $budget->total_budget_zmw }}">
        <input type="hidden" name="total_budget_usd" id="total_budget_usd_hidden" value="{{ $budget->total_budget_usd }}">

        @if($canEdit)
        <div class="text-center mb-4">
            <button type="submit" class="btn btn-dark px-4 me-2" id="submitBtn" style="border-radius: 20px; font-size: 0.85rem;">
                <i class="bi bi-check-lg me-1"></i> Update Budget
            </button>
            <a href="{{ route('budgets.show', $budget->id) }}" class="btn btn-outline-dark px-4 me-2" style="border-radius: 20px; font-size: 0.85rem;">
                Cancel
            </a>
            @if($budget->status == 'draft' || $isAdmin)
            <button type="button" class="btn btn-outline-danger px-4" onclick="confirmDelete()" style="border-radius: 20px; font-size: 0.85rem;">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
            @endif
        </div>
        @else
        <div class="text-center mb-4">
            <a href="{{ route('budgets.show', $budget->id) }}" class="btn btn-outline-dark px-4" style="border-radius: 20px; font-size: 0.85rem;">
                Back to Budget
            </a>
        </div>
        @endif
    </form>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 10px; border: none;">
                <div class="modal-header border-0" style="background: #000; color: #fff; border-radius: 10px 10px 0 0;">
                    <h6 class="modal-title">Delete Budget</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <p>Delete this budget?</p>
                    <p class="text-danger small">All budget items will also be deleted.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal" style="border-radius: 20px;">Cancel</button>
                    <form action="{{ route('budgets.destroy', $budget->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-dark btn-sm" style="border-radius: 20px; background: #dc3545; border: none;">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
                               placeholder="Section name" style="font-weight: 600; font-size: 0.8rem; border-radius: 0 4px 4px 0;" @if(!$canEdit) readonly disabled @endif>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    @if($canEdit)
                    <button type="button" class="btn btn-outline-dark btn-sm" onclick="addObjective(this)" style="border-radius: 20px; font-size: 0.7rem; padding: 3px 10px;">
                        <i class="bi bi-plus"></i> Objective
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeSection(this)" style="border-radius: 20px; font-size: 0.7rem; padding: 3px 8px;">
                        <i class="bi bi-trash"></i>
                    </button>
                    @endif
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
                       placeholder="Objective description..." style="font-size: 0.75rem; border: 1px solid #ddd;" @if(!$canEdit) readonly disabled @endif>
            </div>
            <div class="d-flex gap-1">
                @if($canEdit)
                <button type="button" class="btn btn-outline-dark btn-sm" onclick="addActivity(this)" style="border-radius: 20px; font-size: 0.65rem; padding: 2px 8px;">
                    <i class="bi bi-plus"></i> Activity
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeObjective(this)" style="border-radius: 20px; font-size: 0.65rem; padding: 2px 6px;">
                    <i class="bi bi-trash"></i>
                </button>
                @endif
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
                       placeholder="Activity description..." style="font-size: 0.75rem; border: 1px solid #ddd;" @if(!$canEdit) readonly disabled @endif>
            </div>
            <div class="d-flex gap-1">
                @if($canEdit)
                <button type="button" class="btn btn-outline-dark btn-sm" onclick="addBudgetLine(this)" style="border-radius: 20px; font-size: 0.65rem; padding: 2px 8px;">
                    <i class="bi bi-plus"></i> Add Item
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeActivity(this)" style="border-radius: 20px; font-size: 0.65rem; padding: 2px 6px;">
                    <i class="bi bi-trash"></i>
                </button>
                @endif
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
        <td><input type="text" class="form-control form-control-sm" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][items][{lineId}][description]" placeholder="Description" style="font-size:0.7rem; min-width:120px;" @if(!$canEdit) readonly disabled @endif></td>
        <td><input type="number" class="form-control form-control-sm number-input" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][items][{lineId}][number]" value="1" min="0" step="1" onchange="calculateLineTotal(this)" style="font-size:0.7rem; width:60px;" @if(!$canEdit) readonly disabled @endif></td>
        <td><input type="number" class="form-control form-control-sm frequency-input" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][items][{lineId}][frequency]" value="1" min="0" step="1" onchange="calculateLineTotal(this)" style="font-size:0.7rem; width:60px;" @if(!$canEdit) readonly disabled @endif></td>
        <td><input type="number" class="form-control form-control-sm unit-input" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][items][{lineId}][unit]" value="1" min="0" step="1" onchange="calculateLineTotal(this)" style="font-size:0.7rem; width:60px;" @if(!$canEdit) readonly disabled @endif></td>
        <td><input type="number" class="form-control form-control-sm unit-cost-input" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][items][{lineId}][unit_cost]" value="0.00" step="0.01" onchange="calculateLineTotal(this)" style="font-size:0.7rem; width:80px;" @if(!$canEdit) readonly disabled @endif></td>
        <td>
            <select class="form-control form-control-sm currency-select" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][items][{lineId}][currency]" onchange="calculateLineTotal(this)" style="font-size:0.7rem; width:70px;" @if(!$canEdit) disabled @endif>
                <option value="ZMW">ZMW</option>
                <option value="USD">USD</option>
                <option value="EUR">EUR</option>
                <option value="GBP">GBP</option>
            </select>
        </td>
        <td><input type="number" class="form-control form-control-sm total-display" readonly value="0.00" style="font-size:0.7rem; width:90px; background:#f8f8f8;" @if(!$canEdit) readonly disabled @endif></td>
        <td><input type="number" class="form-control form-control-sm year-input year-1" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][items][{lineId}][year_1]" value="0.00" step="0.01" onchange="updateActivityTotals(this.closest('.activity-block'))" style="font-size:0.7rem; width:70px;" @if(!$canEdit) readonly disabled @endif></td>
        <td><input type="number" class="form-control form-control-sm year-input year-2" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][items][{lineId}][year_2]" value="0.00" step="0.01" onchange="updateActivityTotals(this.closest('.activity-block'))" style="font-size:0.7rem; width:70px;" @if(!$canEdit) readonly disabled @endif></td>
        <td><input type="number" class="form-control form-control-sm year-input year-3" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][items][{lineId}][year_3]" value="0.00" step="0.01" onchange="updateActivityTotals(this.closest('.activity-block'))" style="font-size:0.7rem; width:70px;" @if(!$canEdit) readonly disabled @endif></td>
        <td><input type="text" class="form-control form-control-sm" name="sections[{sectionId}][objectives][{objectiveId}][activities][{activityId}][items][{lineId}][note]" placeholder="Note" style="font-size:0.7rem; min-width:100px;" @if(!$canEdit) readonly disabled @endif></td>
        <td>@if($canEdit)<button type="button" class="btn btn-outline-danger btn-sm" onclick="removeBudgetLine(this)" style="border-radius:50%; padding:1px 5px; font-size:0.6rem;"><i class="bi bi-x"></i></button>@endif</td>
    </tr>
</template>

<script>
const canEdit = {{ json_encode($canEdit) }};
const existingBudget = @json($budget);
const existingItems = @json($budget->items);
let sectionCounter = 1, objectiveCounter = 0, activityCounter = 0, lineCounter = 0;

function buildStructuredData(items) {
    const sections = {};
    items.forEach(item => {
        const sn = item.section || 'A - CORE PROGRAM EXPENDITURE';
        const od = item.objective || '';
        const ad = item.activity || '';
        if (!sections[sn]) sections[sn] = { name: sn, objectives: {} };
        if (!sections[sn].objectives[od]) sections[sn].objectives[od] = { description: od, activities: {} };
        if (!sections[sn].objectives[od].activities[ad]) sections[sn].objectives[od].activities[ad] = { description: ad, items: [] };
        sections[sn].objectives[od].activities[ad].items.push({
            description: item.description || '',
            number: item.number || 1,
            frequency: item.frequency || 1,
            unit: item.unit || 1,
            unit_cost: item.unit_cost || 0,
            currency: item.currency || 'ZMW',
            total_zmw: item.total_amount_zmw || 0,
            year_1: item.year_1 || 0,
            year_2: item.year_2 || 0,
            year_3: item.year_3 || 0,
            note: item.note || ''
        });
    });
    return sections;
}

function renderExistingData() {
    const container = document.getElementById('sections-container');
    if (!container) return;
    container.innerHTML = '';
    const data = buildStructuredData(existingItems);

    Object.entries(data).forEach(([sn, sd]) => {
        const sid = sectionCounter++;
        let sectionHtml = document.getElementById('section-template').innerHTML;
        sectionHtml = sectionHtml.replace(/{sectionId}/g, sid);
        container.insertAdjacentHTML('beforeend', sectionHtml);
        const sectionDiv = container.lastElementChild;
        const sectionNameInput = sectionDiv.querySelector('.section-name');
        if (sectionNameInput) sectionNameInput.value = sn;

        const oc = sectionDiv.querySelector('.objectives-container');
        Object.entries(sd.objectives).forEach(([od, obj]) => {
            const oid = sid + '_' + objectiveCounter++;
            let objHtml = document.getElementById('objective-template').innerHTML;
            objHtml = objHtml.replace(/{sectionId}/g, sid).replace(/{objectiveId}/g, oid);
            oc.insertAdjacentHTML('beforeend', objHtml);
            const objDiv = oc.lastElementChild;
            const objTextInput = objDiv.querySelector('.objective-text');
            if (objTextInput) objTextInput.value = od;

            const ac = objDiv.querySelector('.activities-container');
            Object.entries(obj.activities).forEach(([ad, act]) => {
                const aid = oid + '_' + activityCounter++;
                let actHtml = document.getElementById('activity-template').innerHTML;
                actHtml = actHtml.replace(/{sectionId}/g, sid).replace(/{objectiveId}/g, oid).replace(/{activityId}/g, aid);
                ac.insertAdjacentHTML('beforeend', actHtml);
                const actDiv = ac.lastElementChild;
                const actTextInput = actDiv.querySelector('.activity-text');
                if (actTextInput) actTextInput.value = ad;

                const tb = actDiv.querySelector('.budget-lines-body');
                act.items.forEach(item => {
                    const lid = lineCounter++;
                    let lineHtml = document.getElementById('budget-line-template').innerHTML;
                    lineHtml = lineHtml.replace(/{sectionId}/g, sid).replace(/{objectiveId}/g, oid).replace(/{activityId}/g, aid).replace(/{lineId}/g, lid);
                    tb.insertAdjacentHTML('beforeend', lineHtml);
                    const row = tb.lastElementChild;

                    // Populate values
                    row.querySelector('input[name*="[description]"]').value = item.description || '';
                    row.querySelector('.number-input').value = item.number;
                    row.querySelector('.frequency-input').value = item.frequency;
                    row.querySelector('.unit-input').value = item.unit;
                    row.querySelector('.unit-cost-input').value = item.unit_cost;
                    row.querySelector('.currency-select').value = item.currency;
                    row.querySelector('.total-display').value = item.total_zmw;
                    row.querySelector('.year-1').value = item.year_1;
                    row.querySelector('.year-2').value = item.year_2;
                    row.querySelector('.year-3').value = item.year_3;
                    row.querySelector('input[name*="[note]"]').value = item.note || '';
                });
                updateActivityTotals(actDiv);
            });
        });
    });

    if (Object.keys(data).length === 0 && canEdit) {
        addSection();
    }
    updateAllTotals();
}

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
    updateAllTotals();
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

    const calculatedTotalOriginal = number * frequency * unit * unitCost;
    let totalInZMW = calculatedTotalOriginal;
    if (currency !== 'ZMW') {
        totalInZMW = calculatedTotalOriginal * exchangeRate;
    }

    const totalDisplay = row.querySelector('.total-display');
    totalDisplay.value = totalInZMW.toFixed(2);

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

    const totalCell = activity.querySelector('.activity-total');
    if (totalCell) totalCell.textContent = total.toFixed(2);
    const y1Cell = activity.querySelector('.activity-total-y1');
    if (y1Cell) y1Cell.textContent = y1.toFixed(2);
    const y2Cell = activity.querySelector('.activity-total-y2');
    if (y2Cell) y2Cell.textContent = y2.toFixed(2);
    const y3Cell = activity.querySelector('.activity-total-y3');
    if (y3Cell) y3Cell.textContent = y3.toFixed(2);

    updateAllTotals();
}

function updateAllTotals() {
    const exchangeRate = parseFloat(document.getElementById('exchange_rate').value) || 25;

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

    document.getElementById('summary-zmw').innerHTML = 'ZMW ' + totalZMW.toFixed(2);
    document.getElementById('summary-y1').innerHTML = totalY1.toFixed(2);
    document.getElementById('summary-y2').innerHTML = totalY2.toFixed(2);
    document.getElementById('summary-y3').innerHTML = totalY3.toFixed(2);
    document.getElementById('total_budget_zmw_display').value = totalZMW.toFixed(2);
    document.getElementById('total_budget_zmw_hidden').value = totalZMW.toFixed(2);

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

function confirmDelete() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

document.getElementById('budgetForm').addEventListener('submit', function() {
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Updating...';
    }
    updateAllTotals();
});

document.addEventListener('DOMContentLoaded', function() {
    renderExistingData();
});

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
