@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1" style="font-weight:700; color:#000;">{{ $document->title }}</h4>
            <ol class="breadcrumb m-0" style="font-size:0.8rem;">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Documents</a></li>
                @if($document->documentCategory)
                    <li class="breadcrumb-item">
                        <a href="{{ route('documents.category', $document->documentCategory) }}">
                            {{ $document->documentCategory->name }}
                        </a>
                    </li>
                @endif
                <li class="breadcrumb-item active">{{ Str::limit($document->title, 40) }}</li>
            </ol>
        </div>
        <div class="d-flex gap-2">
            @if($document->documentCategory)
                <a href="{{ route('documents.category', $document->documentCategory) }}"
                   class="btn btn-outline-secondary btn-sm" style="border-radius:20px;">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            @else
                <a href="{{ route('documents.list') }}"
                   class="btn btn-outline-secondary btn-sm" style="border-radius:20px;">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            @endif

            @if($document->has_file)
                <a href="{{ route('documents.download', $document) }}"
                   class="btn btn-success btn-sm" style="border-radius:20px;">
                    <i class="bi bi-download me-1"></i> Download
                </a>
            @elseif($document->document_type === 'budget' && $document->reference_number)
                {{-- Link back to the Budget module --}}
                @php
                    $budgetRecord = \App\Models\Budget::where('project_code', $document->reference_number)->first();
                @endphp
                @if($budgetRecord)
                    <a href="{{ route('budgets.show', $budgetRecord->id) }}"
                       class="btn btn-dark btn-sm" style="border-radius:20px;">
                        <i class="bi bi-box-arrow-up-right me-1"></i> View Full Budget
                    </a>
                @endif
            @endif

            {{-- EDIT BUTTON REMOVED --}}
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- Left column: main info --}}
        <div class="col-md-8">

            {{-- Budget reference notice --}}
            @if($document->document_type === 'budget')
            <div class="alert alert-info border-0 mb-3" style="border-radius:8px; font-size:0.85rem;">
                <i class="bi bi-info-circle me-2"></i>
                This is a <strong>Budget reference document</strong>. The full budget details live in the
                Budget module.
                @php $budgetRecord = \App\Models\Budget::where('project_code', $document->reference_number)->first(); @endphp
                @if($budgetRecord)
                    <a href="{{ route('budgets.show', $budgetRecord->id) }}" class="alert-link ms-1">
                        Open full budget &rarr;
                    </a>
                @endif
            </div>
            @endif

            {{-- Core document details --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:8px;">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0" style="font-weight:700; font-size:0.9rem;">
                        <i class="bi bi-file-earmark-text me-2 text-success"></i>Document Details
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <table class="table table-borderless mb-0" style="font-size:0.85rem;">
                        <tr>
                            <th class="text-muted ps-0" style="width:35%;">Title</th>
                            <td><strong>{{ $document->title }}</strong></td>
                        </tr>
                        <tr>
                            <th class="text-muted ps-0">Document Type</th>
                            <td>
                                <span class="badge bg-dark" style="font-size:0.75rem;">
                                    {{ $document->document_type_formatted }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted ps-0">Category</th>
                            <td>
                                @if($document->documentCategory)
                                    <span class="badge bg-success">{{ $document->documentCategory->name }}</span>
                                @else
                                    <span class="badge bg-secondary">Uncategorized</span>
                                @endif
                            </td>
                        </tr>
                        @if($document->folder)
                        <tr>
                            <th class="text-muted ps-0">Folder</th>
                            <td>
                                <i class="bi bi-folder-fill text-warning me-1"></i>
                                {{ $document->folder->name }}
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <th class="text-muted ps-0">Department</th>
                            <td>{{ $document->department_formatted }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted ps-0">Status</th>
                            <td>
                                @php
                                    $statusColors = [
                                        'active'         => 'success',
                                        'approved'       => 'success',
                                        'draft'          => 'secondary',
                                        'pending'        => 'warning',
                                        'expired'        => 'danger',
                                        'archived'       => 'dark',
                                        'expiring_soon'  => 'warning',
                                    ];
                                    $color = $statusColors[$document->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}">
                                    {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted ps-0">Version</th>
                            <td>v{{ $document->version }}</td>
                        </tr>
                        @if($document->reference_number)
                        <tr>
                            <th class="text-muted ps-0">Reference #</th>
                            <td><code>{{ $document->reference_number }}</code></td>
                        </tr>
                        @endif
                        @if($document->effective_date)
                        <tr>
                            <th class="text-muted ps-0">Effective Date</th>
                            <td>{{ $document->effective_date->format('d M Y') }}</td>
                        </tr>
                        @endif
                        @if($document->expiry_date)
                        <tr>
                            <th class="text-muted ps-0">Expiry Date</th>
                            <td>{{ $document->expiry_date->format('d M Y') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            {{-- Description --}}
            @if($document->description)
            <div class="card border-0 shadow-sm mb-4" style="border-radius:8px;">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0" style="font-weight:700; font-size:0.9rem;">
                        <i class="bi bi-text-left me-2 text-success"></i>Description
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <p class="mb-0" style="font-size:0.85rem; line-height:1.7; color:#333;">
                        {{ $document->description }}
                    </p>
                </div>
            </div>
            @endif

            {{-- Budget-specific fields --}}
            @if($document->document_type === 'budget')
            <div class="card border-0 shadow-sm mb-4" style="border-radius:8px;">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0" style="font-weight:700; font-size:0.9rem;">
                        <i class="bi bi-cash-stack me-2 text-success"></i>Budget Information
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <table class="table table-borderless mb-0" style="font-size:0.85rem;">
                        @if($document->fiscal_year)
                        <tr>
                            <th class="text-muted ps-0" style="width:35%;">Fiscal Year</th>
                            <td>{{ $document->fiscal_year }}</td>
                        </tr>
                        @endif
                        @if($document->budget_type)
                        <tr>
                            <th class="text-muted ps-0">Budget Type</th>
                            <td>{{ $document->budget_type_formatted }}</td>
                        </tr>
                        @endif
                        @if($document->total_amount)
                        <tr>
                            <th class="text-muted ps-0">Total Amount</th>
                            <td>
                                <strong class="text-success" style="font-size:1rem;">
                                    {{ $document->currency ?? 'ZMW' }}
                                    {{ number_format($document->total_amount, 2) }}
                                </strong>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            @endif

            {{-- Donor contract fields --}}
            @if($document->document_type === 'donor_contract')
            <div class="card border-0 shadow-sm mb-4" style="border-radius:8px;">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0" style="font-weight:700; font-size:0.9rem;">
                        <i class="bi bi-handshake me-2 text-success"></i>Donor Contract Information
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <table class="table table-borderless mb-0" style="font-size:0.85rem;">
                        @if($document->donor_name)
                        <tr>
                            <th class="text-muted ps-0" style="width:35%;">Donor Name</th>
                            <td>{{ $document->donor_name }}</td>
                        </tr>
                        @endif
                        @if($document->contract_value)
                        <tr>
                            <th class="text-muted ps-0">Contract Value</th>
                            <td><strong class="text-success">{{ $document->contract_value_formatted }}</strong></td>
                        </tr>
                        @endif
                        @if($document->project_name)
                        <tr>
                            <th class="text-muted ps-0">Project Name</th>
                            <td>{{ $document->project_name }}</td>
                        </tr>
                        @endif
                        @if($document->reporting_requirements)
                        <tr>
                            <th class="text-muted ps-0">Reporting Requirements</th>
                            <td>{{ $document->reporting_requirements }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            @endif

            {{-- Staff contract fields --}}
            @if($document->document_type === 'staff_contract')
            <div class="card border-0 shadow-sm mb-4" style="border-radius:8px;">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0" style="font-weight:700; font-size:0.9rem;">
                        <i class="bi bi-person-badge me-2 text-success"></i>Staff Contract Information
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <table class="table table-borderless mb-0" style="font-size:0.85rem;">
                        @if($document->employee)
                        <tr>
                            <th class="text-muted ps-0" style="width:35%;">Employee</th>
                            <td>{{ $document->employee->name }}</td>
                        </tr>
                        @endif
                        @if($document->contract_type)
                        <tr>
                            <th class="text-muted ps-0">Contract Type</th>
                            <td>{{ $document->contract_type_formatted }}</td>
                        </tr>
                        @endif
                        @if($document->position)
                        <tr>
                            <th class="text-muted ps-0">Position</th>
                            <td>{{ $document->position }}</td>
                        </tr>
                        @endif
                        @if($document->salary_scale)
                        <tr>
                            <th class="text-muted ps-0">Salary Scale</th>
                            <td>{{ $document->salary_scale }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            @endif

        </div>

        {{-- Right column: file info + meta --}}
        <div class="col-md-4">

            {{-- File / Reference card --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:8px;">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0" style="font-weight:700; font-size:0.9rem;">
                        <i class="bi bi-paperclip me-2 text-success"></i>
                        {{ $document->has_file ? 'File Details' : 'Reference Details' }}
                    </h6>
                </div>
                <div class="card-body pt-0 text-center">
                    @if($document->has_file)
                        {{-- File icon --}}
                        @php
                            $ext = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
                            $iconClass = match($ext) {
                                'pdf'           => 'bi-file-earmark-pdf text-danger',
                                'doc', 'docx'   => 'bi-file-earmark-word text-primary',
                                'xls', 'xlsx'   => 'bi-file-earmark-excel text-success',
                                'jpg','jpeg','png','gif' => 'bi-file-earmark-image text-warning',
                                default         => 'bi-file-earmark text-secondary',
                            };
                        @endphp
                        <i class="bi {{ $iconClass }}" style="font-size:3rem;"></i>
                        <p class="mt-2 mb-1" style="font-size:0.8rem; font-weight:600; word-break:break-all;">
                            {{ $document->file_name }}
                        </p>
                        <p class="text-muted mb-3" style="font-size:0.75rem;">
                            {{ strtoupper($ext) }} &bull; {{ $document->file_size_formatted }}
                        </p>
                        <a href="{{ route('documents.download', $document) }}"
                           class="btn btn-success w-100" style="border-radius:20px; font-size:0.85rem;">
                            <i class="bi bi-download me-1"></i> Download File
                        </a>
                    @else
                        {{-- Budget / reference doc --}}
                        <i class="bi bi-file-earmark-text text-dark" style="font-size:3rem;"></i>
                        <p class="mt-2 mb-1" style="font-size:0.8rem; font-weight:600;">
                            {{ $document->file_extension }} Reference
                        </p>
                        <p class="text-muted mb-3" style="font-size:0.75rem;">
                            No physical file — data lives in the system
                        </p>
                        @if(isset($budgetRecord) && $budgetRecord)
                            <a href="{{ route('budgets.show', $budgetRecord->id) }}"
                               class="btn btn-dark w-100" style="border-radius:20px; font-size:0.85rem;">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Open in Budget Module
                            </a>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Meta info --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:8px;">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0" style="font-weight:700; font-size:0.9rem;">
                        <i class="bi bi-clock-history me-2 text-success"></i>Activity
                    </h6>
                </div>
                <div class="card-body pt-0" style="font-size:0.82rem;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3"
                             style="width:34px; height:34px; font-size:0.75rem; font-weight:700; flex-shrink:0;">
                            {{ strtoupper(substr($document->user->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight:600;">{{ $document->user->name ?? 'Unknown' }}</div>
                            <div class="text-muted" style="font-size:0.75rem;">
                                Uploaded {{ $document->created_at->format('d M Y, g:i A') }}
                            </div>
                        </div>
                    </div>
                    <div class="text-muted border-top pt-3" style="font-size:0.78rem;">
                        <i class="bi bi-pencil me-1"></i>
                        Last updated {{ $document->updated_at->diffForHumans() }}
                        ({{ $document->updated_at->format('d M Y') }})
                    </div>
                </div>
            </div>

            {{-- Delete --}}
            <div class="card border-0 shadow-sm border border-danger" style="border-radius:8px;">
                <div class="card-body py-3 text-center">
                    <p class="text-muted mb-2" style="font-size:0.78rem;">
                        Permanently remove this document record.
                    </p>
                    <form action="{{ route('documents.destroy', $document) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this document? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                style="border-radius:20px; font-size:0.82rem;">
                            <i class="bi bi-trash me-1"></i> Delete Document
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
