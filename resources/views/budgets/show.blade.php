@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="mb-1" style="color:#000; font-weight:700; font-size:1.6rem; line-height:1.2;">
                {{ $budget->project_title }}
            </h2>
            <div style="display:flex; align-items:center; gap:10px; margin-top:4px;">
                <span style="font-size:0.8rem; color:#000; font-weight:500;">Project Code:</span>
                <span style="font-size:0.85rem; font-weight:700; color:#fff; background:#000; padding:2px 10px; border-radius:4px; letter-spacing:0.5px;">
                    {{ $budget->project_code }}
                </span>
            </div>
        </div>
        <div class="d-flex gap-2 no-print">
            <button onclick="window.print()" class="btn btn-outline-dark btn-sm" style="border:1px solid #000; background:#fff; border-radius:4px; padding:6px 14px; font-size:0.8rem;">
                Print Budget
            </button>
            <a href="{{ route('budgets.edit', $budget->id) }}" class="btn btn-outline-dark btn-sm" style="border:1px solid #000; background:#fff; color:#000; border-radius:4px; padding:6px 14px; font-size:0.8rem; text-decoration:none;">
                Edit
            </a>
            <a href="{{ route('budgets.index') }}" class="btn btn-dark btn-sm" style="background:#000; color:#fff; border-radius:4px; padding:6px 14px; font-size:0.8rem; text-decoration:none;">
                Back
            </a>
        </div>
    </div>

    <!-- Stats (visible on screen only) -->
    <div class="row g-3 mb-4 no-print">
        <div class="col-md-3">
            <div style="border:1px solid #ddd; border-radius:4px; padding:14px; border-left:3px solid #000;">
                <div style="font-size:0.65rem; color:#000; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px; font-weight:600;">Status</div>
                @php
                    $statusBg  = $budget->status == 'approved' ? '#d4edda' : ($budget->status == 'pending' ? '#fff3cd' : '#e2e3e5');
                    $statusTxt = $budget->status == 'approved' ? '#155724' : ($budget->status == 'pending' ? '#856404' : '#383d41');
                @endphp
                <span style="background:{{ $statusBg }}; color:{{ $statusTxt }}; padding:3px 10px; border-radius:4px; font-size:0.78rem; font-weight:600;">
                    {{ ucfirst($budget->status) }}
                </span>
            </div>
        </div>
        <div class="col-md-3">
            <div style="border:1px solid #ddd; border-radius:4px; padding:14px; border-left:3px solid #28a745;">
                <div style="font-size:0.65rem; color:#000; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px; font-weight:600;">Total Budget (ZMW)</div>
                <div style="font-size:1.1rem; font-weight:700; color:#28a745;">ZMW {{ number_format($budget->total_budget_zmw, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div style="border:1px solid #ddd; border-radius:4px; padding:14px; border-left:3px solid #ffc107;">
                <div style="font-size:0.65rem; color:#000; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px; font-weight:600;">Total Expenses</div>
                <div style="font-size:1.1rem; font-weight:700; color:#e6a817;">ZMW {{ number_format($budget->total_expenses_zmw, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div style="border:1px solid #ddd; border-radius:4px; padding:14px; border-left:3px solid #17a2b8;">
                <div style="font-size:0.65rem; color:#000; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px; font-weight:600;">Remaining Budget</div>
                <div style="font-size:1.1rem; font-weight:700; color:#17a2b8;">ZMW {{ number_format($budget->remaining_budget_zmw, 2) }}</div>
                <div style="font-size:0.7rem; color:#000; margin-top:2px;">{{ number_format($budget->percentage_used, 1) }}% used</div>
            </div>
        </div>
    </div>

    <!-- Utilization Bar (visible on screen only) -->
    <div class="no-print" style="border:1px solid #ddd; border-radius:4px; padding:16px; margin-bottom:20px;">
        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
            <span style="font-size:0.82rem; font-weight:600; color:#000;">Budget Utilization</span>
            <span style="font-size:0.82rem; font-weight:600; color:#000;">{{ number_format($budget->percentage_used, 1) }}%</span>
        </div>
        <div style="background:#e9e9e9; height:10px; border-radius:5px;">
            <div style="width:{{ min($budget->percentage_used, 100) }}%; height:10px; border-radius:5px; background:{{ $budget->remaining_budget_zmw > 0 ? '#28a745' : '#dc3545' }};"></div>
        </div>
        <div class="row mt-3 text-center">
            <div class="col-4">
                <div style="font-size:0.7rem; color:#000; margin-bottom:2px; font-weight:600;">Budgeted</div>
                <div style="font-weight:700; color:#28a745; font-size:0.85rem;">ZMW {{ number_format($budget->total_budget_zmw, 2) }}</div>
            </div>
            <div class="col-4">
                <div style="font-size:0.7rem; color:#000; margin-bottom:2px; font-weight:600;">Expensed</div>
                <div style="font-weight:700; color:#e6a817; font-size:0.85rem;">ZMW {{ number_format($budget->total_expenses_zmw, 2) }}</div>
            </div>
            <div class="col-4">
                <div style="font-size:0.7rem; color:#000; margin-bottom:2px; font-weight:600;">Remaining</div>
                <div style="font-weight:700; color:#17a2b8; font-size:0.85rem;">ZMW {{ number_format($budget->remaining_budget_zmw, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Currency Summary (visible on screen only) -->
    @php
        $currencyTotals = [];
        foreach($budget->items as $item) {
            if (!isset($currencyTotals[$item->currency])) $currencyTotals[$item->currency] = 0;
            $originalAmount = $item->currency != 'ZMW' ? $item->total_amount_zmw / $budget->exchange_rate : $item->total_amount_zmw;
            $currencyTotals[$item->currency] += $originalAmount;
        }
        $currenciesUsed = $budget->items->pluck('currency')->unique()->filter(fn($c) => $c != 'ZMW')->values();
        $mainCurrency = $currenciesUsed->first() ?? 'USD';
    @endphp
    <div class="row g-3 mb-4 no-print">
        @if(isset($currencyTotals['USD']) && $currencyTotals['USD'] > 0)
        <div class="col-md-3">
            <div style="border:1px solid #ddd; border-radius:4px; padding:14px;">
                <div style="font-size:0.65rem; color:#000; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px; font-weight:600;">Total (USD)</div>
                <div style="font-size:1.1rem; font-weight:700; color:#000;">${{ number_format($currencyTotals['USD'], 2) }}</div>
            </div>
        </div>
        @endif
        @if(isset($currencyTotals['EUR']) && $currencyTotals['EUR'] > 0)
        <div class="col-md-3">
            <div style="border:1px solid #ddd; border-radius:4px; padding:14px;">
                <div style="font-size:0.65rem; color:#000; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px; font-weight:600;">Total (EUR)</div>
                <div style="font-size:1.1rem; font-weight:700; color:#000;">€{{ number_format($currencyTotals['EUR'], 2) }}</div>
            </div>
        </div>
        @endif
        @if(isset($currencyTotals['GBP']) && $currencyTotals['GBP'] > 0)
        <div class="col-md-3">
            <div style="border:1px solid #ddd; border-radius:4px; padding:14px;">
                <div style="font-size:0.65rem; color:#000; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px; font-weight:600;">Total (GBP)</div>
                <div style="font-size:1.1rem; font-weight:700; color:#000;">£{{ number_format($currencyTotals['GBP'], 2) }}</div>
            </div>
        </div>
        @endif
        <div class="col-md-3">
            <div style="border:1px solid #ddd; border-radius:4px; padding:14px;">
                <div style="font-size:0.65rem; color:#000; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px; font-weight:600;">Exchange Rate</div>
                <div style="font-size:0.95rem; font-weight:700; color:#000;">1 {{ $mainCurrency }} = ZMW {{ number_format($budget->exchange_rate, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Project Details (visible on screen only) -->
    <div class="no-print" style="border:1px solid #ddd; border-radius:4px; margin-bottom:20px;">
        <div style="padding:12px 16px; border-bottom:1px solid #ddd; font-weight:600; font-size:0.88rem; color:#000;">Project Details</div>
        <div class="row g-0">
            <div class="col-md-6" style="padding:14px 16px; border-right:1px solid #ddd;">
                <div style="margin-bottom:12px;">
                    <div style="font-size:0.68rem; color:#000; text-transform:uppercase; margin-bottom:3px; font-weight:600;">Goal / Objective</div>
                    <div style="font-size:0.85rem; color:#000;">{{ $budget->project_goal }}</div>
                </div>
                <div>
                    <div style="font-size:0.68rem; color:#000; text-transform:uppercase; margin-bottom:3px; font-weight:600;">Duration</div>
                    <div style="font-size:0.85rem; color:#000;">{{ $budget->duration }}</div>
                </div>
            </div>
            <div class="col-md-6" style="padding:14px 16px;">
                <div style="margin-bottom:10px;">
                    <div style="font-size:0.68rem; color:#000; text-transform:uppercase; margin-bottom:3px; font-weight:600;">Contact Person</div>
                    <div style="font-size:0.85rem; color:#000;">{{ $budget->contact_person }}</div>
                </div>
                <div style="margin-bottom:10px;">
                    <div style="font-size:0.68rem; color:#000; text-transform:uppercase; margin-bottom:3px; font-weight:600;">Email</div>
                    <div style="font-size:0.85rem; color:#000;">{{ $budget->contact_email }}</div>
                </div>
                <div>
                    <div style="font-size:0.68rem; color:#000; text-transform:uppercase; margin-bottom:3px; font-weight:600;">Phone</div>
                    <div style="font-size:0.85rem; color:#000;">{{ $budget->contact_phone }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Budget Items Section (PRINTS THIS) -->
    <div style="margin-bottom:20px;">
        <h5 style="font-weight:700; margin-bottom:12px; border-bottom:2px solid #000; padding-bottom:6px;">Budget Items</h5>

        @foreach($groupedItems as $section => $items)
        <div style="border:1px solid #ddd; border-radius:4px; margin-bottom:16px; page-break-inside:avoid;">
            <div style="padding:10px 14px; border-bottom:2px solid #000; display:flex; justify-content:space-between; align-items:center; background:#fff;">
                <span style="font-weight:700; font-size:0.85rem; color:#000;">{{ $section }}</span>
                <span style="font-size:0.8rem; font-weight:600; color:#28a745;">ZMW {{ number_format($sectionTotals[$section]['zmw'] ?? 0, 2) }}</span>
            </div>
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:0.75rem;">
                    <thead>
                        <tr style="border-bottom:2px solid #000; background:#fff;">
                            <th style="padding:8px 10px; text-align:left;">Description</th>
                            <th style="padding:8px 10px; text-align:center;">No.</th>
                            <th style="padding:8px 10px; text-align:center;">Freq.</th>
                            <th style="padding:8px 10px; text-align:center;">Unit</th>
                            <th style="padding:8px 10px; text-align:right;">Cost</th>
                            <th style="padding:8px 10px; text-align:center;">Currency</th>
                            <th style="padding:8px 10px; text-align:right;">Total ZMW</th>
                            <th style="padding:8px 10px; text-align:right;">Y1</th>
                            <th style="padding:8px 10px; text-align:right;">Y2</th>
                            <th style="padding:8px 10px; text-align:right;">Y3</th>
                            <th style="padding:8px 10px; text-align:left;">Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $currentObjective = null; $currentActivity = null; @endphp
                        @foreach($items as $item)
                            @if($item->objective !== $currentObjective)
                                @php $currentObjective = $item->objective; @endphp
                                <tr style="background:#e8e8e8;">
                                    <td colspan="11" style="padding:6px 10px; font-weight:700; font-size:0.75rem;">{{ $currentObjective }}</td>
                                </tr>
                            @endif
                            @if($item->activity !== $currentActivity)
                                @php $currentActivity = $item->activity; @endphp
                                <tr style="background:#f5f5f5;">
                                    <td colspan="11" style="padding:4px 10px 4px 20px; font-size:0.7rem; font-weight:600;">{{ $currentActivity }}</td>
                                </tr>
                            @endif
                            <tr style="border-bottom:1px solid #eee;">
                                <td style="padding:7px 10px 7px 28px;">{{ $item->description }}</td>
                                <td style="padding:7px 10px; text-align:center;">{{ $item->number }}</td>
                                <td style="padding:7px 10px; text-align:center;">{{ $item->frequency }}</td>
                                <td style="padding:7px 10px; text-align:center;">{{ $item->unit }}</td>
                                <td style="padding:7px 10px; text-align:right;">{{ number_format($item->unit_cost, 2) }}</td>
                                <td style="padding:7px 10px; text-align:center;">
                                    <span style="background:#000; color:#fff; padding:1px 6px; border-radius:3px; font-size:0.7rem;">{{ $item->currency }}</span>
                                </td>
                                <td style="padding:7px 10px; text-align:right; font-weight:600; color:#28a745;">{{ number_format($item->total_amount_zmw, 2) }}</td>
                                <td style="padding:7px 10px; text-align:right;">{{ number_format($item->year_1, 2) }}</td>
                                <td style="padding:7px 10px; text-align:right;">{{ number_format($item->year_2, 2) }}</td>
                                <td style="padding:7px 10px; text-align:right;">{{ number_format($item->year_3, 2) }}</td>
                                <td style="padding:7px 10px;">{{ $item->note ?? '-' }}</td>
                            </tr>
                        @endforeach
                        <tr style="border-top:2px solid #000; background:#f0f0f0;">
                            <td colspan="6" style="padding:8px 10px; text-align:right; font-weight:700;">Section Total</td>
                            <td style="padding:8px 10px; text-align:right; font-weight:700; color:#28a745;">{{ number_format($sectionTotals[$section]['zmw'] ?? 0, 2) }}</td>
                            <td colspan="4"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Year Summary (visible on screen only) -->
    <div class="row g-3 mb-4 no-print">
        <div class="col-md-4">
            <div style="border:1px solid #ddd; border-top:3px solid #28a745; border-radius:4px; padding:14px; text-align:center;">
                <div style="font-size:0.68rem; color:#000; text-transform:uppercase; margin-bottom:6px; font-weight:600;">Year 1 Budget</div>
                <div style="font-size:1.1rem; font-weight:700; color:#28a745;">ZMW {{ number_format($budget->total_year_1, 2) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div style="border:1px solid #ddd; border-top:3px solid #17a2b8; border-radius:4px; padding:14px; text-align:center;">
                <div style="font-size:0.68rem; color:#000; text-transform:uppercase; margin-bottom:6px; font-weight:600;">Year 2 Budget</div>
                <div style="font-size:1.1rem; font-weight:700; color:#17a2b8;">ZMW {{ number_format($budget->total_year_2, 2) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div style="border:1px solid #ddd; border-top:3px solid #fd7e14; border-radius:4px; padding:14px; text-align:center;">
                <div style="font-size:0.68rem; color:#000; text-transform:uppercase; margin-bottom:6px; font-weight:600;">Year 3 Budget</div>
                <div style="font-size:1.1rem; font-weight:700; color:#fd7e14;">ZMW {{ number_format($budget->total_year_3, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Expenses Section (visible on screen only) -->
    <div class="no-print" style="margin-bottom:20px;">
        <h5 style="font-weight:700; margin-bottom:12px; border-bottom:2px solid #000; padding-bottom:6px;">Expenses</h5>

        <div style="border:1px solid #ddd; border-radius:4px;">
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:0.78rem;">
                    <thead>
                        <tr style="background:#fff; border-bottom:2px solid #000;">
                            <th style="padding:9px 12px; text-align:left;">Expense #</th>
                            <th style="padding:9px 12px; text-align:left;">Date</th>
                            <th style="padding:9px 12px; text-align:left;">Description</th>
                            <th style="padding:9px 12px; text-align:left;">Category</th>
                            <th style="padding:9px 12px; text-align:right;">Amount (ZMW)</th>
                            <th style="padding:9px 12px; text-align:center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:9px 12px; font-weight:600;">{{ $expense->expense_number }}</td>
                            <td style="padding:9px 12px;">{{ $expense->expense_date->format('d/m/Y') }}</td>
                            <td style="padding:9px 12px;">{{ Str::limit($expense->description, 50) }}</td>
                            <td style="padding:9px 12px;">{{ $expense->category }}</td>
                            <td style="padding:9px 12px; text-align:right; font-weight:600;">{{ number_format($expense->amount_zmw, 2) }}</td>
                            <td style="padding:9px 12px; text-align:center;">
                                @php
                                    $eBg  = $expense->status == 'pending'  ? '#fff3cd' : ($expense->status == 'approved' ? '#d4edda' : ($expense->status == 'paid' ? '#d1ecf1' : '#f8d7da'));
                                    $eTxt = $expense->status == 'pending'  ? '#856404' : ($expense->status == 'approved' ? '#155724' : ($expense->status == 'paid' ? '#0c5460' : '#721c24'));
                                @endphp
                                <span style="background:{{ $eBg }}; color:{{ $eTxt }}; padding:2px 8px; border-radius:3px; font-size:0.72rem; font-weight:600;">
                                    {{ ucfirst($expense->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="padding:40px; text-align:center;">No expenses recorded for this budget.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr style="border-top:2px solid #000; background:#f0f0f0;">
                            <td colspan="4" style="padding:9px 12px; text-align:right; font-weight:700;">Total Expenses:</td>
                            <td style="padding:9px 12px; text-align:right; font-weight:700; color:#e6a817;">ZMW {{ number_format($budget->total_expenses_zmw, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer (visible on screen only) -->
    <div class="no-print" style="text-align:center; color:#666; font-size:0.7rem; margin-bottom:24px; border-top:1px solid #eee; padding-top:16px;">
        Created {{ $budget->created_at->format('d M Y') }} by {{ $budget->createdBy->name ?? 'Unknown' }}
        &nbsp;|&nbsp; Last updated {{ $budget->updated_at->format('d M Y H:i') }}
    </div>

</div>

<style>
@media print {
    /* Hide everything except budget items */
    .no-print,
    .btn,
    .btn-group,
    .navbar,
    .sidebar,
    .main-sidebar,
    .main-header,
    nav,
    header,
    footer,
    .d-flex.gap-2,
    .row.g-3.mb-4,
    .col-md-3,
    .col-md-4 {
        display: none !important;
    }

    /* Show only budget items */
    .container-fluid > div:not(.no-print) {
        display: block !important;
    }

    /* Page setup */
    @page {
        size: A4 landscape;
        margin: 1cm 1.2cm;
    }

    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
    }

    body {
        background: #fff;
        font-size: 8pt;
        color: #000;
    }

    .container-fluid {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    /* Tables */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
        font-size: 7pt !important;
    }

    th, td {
        padding: 5px 8px !important;
        border: 1px solid #ddd !important;
    }

    /* Card borders */
    div[style*="border:1px solid #ddd"] {
        border: 1px solid #ddd !important;
        margin-bottom: 10px !important;
    }

    /* Headers */
    h2 { font-size: 14pt !important; margin-bottom: 8px !important; }
    h5 { font-size: 11pt !important; }
}
</style>
@endsection
