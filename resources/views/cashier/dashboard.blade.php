@extends('layouts.cashier')

@section('title', 'Cashier Dashboard')

@section('content')

    {{-- Page heading --}}
    <div class="dash-heading-row">
        <div>
            <h1 class="dash-heading">PAYMENT DASHBOARD</h1>
            <p class="dash-subtext">Manage and verify student payment submissions.</p>
        </div>
        <a href="{{ route('cashier.payments.index') }}" class="btn-view-all">
            View All Payments <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    {{-- ── 6 Stat Cards ── --}}
    <div class="stat-grid">

        <div class="stat-card stat-blue">
            <div class="stat-icon"><i class="bi bi-credit-card-2-front"></i></div>
            <div class="stat-info">
                <div class="stat-value">{{ $totalPending }}</div>
                <div class="stat-label">Total Pending</div>
            </div>
        </div>

        <div class="stat-card stat-green">
            <div class="stat-icon"><i class="bi bi-phone"></i></div>
            <div class="stat-info">
                <div class="stat-value">{{ $gcashPending }}</div>
                <div class="stat-label">GCash Pending</div>
            </div>
        </div>

        <div class="stat-card stat-teal">
            <div class="stat-icon"><i class="bi bi-bank"></i></div>
            <div class="stat-info">
                <div class="stat-value">{{ $bankPending }}</div>
                <div class="stat-label">Bank Transfer Pending</div>
            </div>
        </div>

        <div class="stat-card stat-yellow">
            <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-info">
                <div class="stat-value">{{ $cashPending }}</div>
                <div class="stat-label">Cash Pending</div>
            </div>
        </div>

        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
            <div class="stat-info">
                <div class="stat-value">{{ $verifiedToday }}</div>
                <div class="stat-label">Verified Today</div>
            </div>
        </div>

        <div class="stat-card stat-red">
            <div class="stat-icon"><i class="bi bi-x-circle"></i></div>
            <div class="stat-info">
                <div class="stat-value">{{ $rejectedToday }}</div>
                <div class="stat-label">Rejected Today</div>
            </div>
        </div>

    </div>

    {{-- ── Recent Pending Payments Table ── --}}
    <div class="ccst-card">
        <div class="ccst-card-header">
            <i class="bi bi-clock-history me-1"></i> Recent Pending Payments
        </div>
        <div class="ccst-card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="pendingTable">
                    <thead style="background:#f8f9fa; font-size:0.8rem;">
                        <tr>
                            <th class="px-3 py-2">Reference No.</th>
                            <th class="px-3 py-2">Student</th>
                            <th class="px-3 py-2">Method</th>
                            <th class="px-3 py-2">Amount</th>
                            <th class="px-3 py-2">Submitted</th>
                            <th class="px-3 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:0.83rem;">
                        @forelse($recentPending as $request)
                        <tr>
                            <td class="px-3 py-2">
                                <span style="font-weight:600; color:#1B6B3A;">
                                    {{ $request->reference_number }}
                                </span>
                            </td>
                            <td class="px-3 py-2">{{ $request->full_name }}</td>
                            <td class="px-3 py-2">
                                @php
                                    $methodLabels = [
                                        'gcash'         => ['GCash',         'text-success'],
                                        'bank_transfer' => ['Bank Transfer',  'text-primary'],
                                        'cash'          => ['Cash',           'text-warning'],
                                    ];
                                    [$label, $cls] = $methodLabels[$request->payment_method] ?? ['—', ''];
                                @endphp
                                <span class="fw-600 {{ $cls }}">{{ $label }}</span>
                            </td>
                            <td class="px-3 py-2">₱{{ number_format($request->total_fee, 2) }}</td>
                            <td class="px-3 py-2 text-muted">
                                {{ $request->updated_at->format('M d, Y') }}
                            </td>
                            <td class="px-3 py-2">
                                <a href="{{ route('cashier.payments.show', $request->id) }}"
                                   class="btn btn-sm btn-primary py-0 px-2"
                                   style="font-size:0.75rem;">
                                    Review
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4" style="font-size:0.85rem;">
                                <i class="bi bi-check-circle me-2 text-success"></i>
                                No pending payments at this time.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

{{-- RIGHT PANEL --}}
@section('right-panel')

    {{-- Date + Time card — glassmorphism, no green background --}}
    <div class="rp-date-card">
        <div class="rp-date-day">{{ now()->format('d') }}</div>
        <div class="rp-date-month">{{ now()->format('F Y') }}</div>
        <div class="rp-date-time" id="live-time">--:-- --</div>
    </div>

    {{-- Today's Summary --}}
    <div class="ccst-card mb-0">
        <div class="ccst-card-header blue">Today's Summary</div>
        <div class="ccst-card-body p-0">
            <div class="rp-stat-row">
                <span><span class="rp-icon-circle"><i class="bi bi-phone"></i></span> GCash</span>
                <strong>{{ $gcashPending }}</strong>
            </div>
            <div class="rp-stat-row">
                <span><span class="rp-icon-circle"><i class="bi bi-bank"></i></span> Bank</span>
                <strong>{{ $bankPending }}</strong>
            </div>
            <div class="rp-stat-row">
                <span><span class="rp-icon-circle"><i class="bi bi-cash"></i></span> Cash</span>
                <strong>{{ $cashPending }}</strong>
            </div>
            <div class="rp-stat-row" style="border-bottom:none;">
                <span><span class="rp-icon-circle"><i class="bi bi-check-circle"></i></span> Verified</span>
                <strong style="color:white;">{{ $verifiedToday }}</strong>
            </div>
        </div>
    </div>

    {{-- How to Verify --}}
    <div class="ccst-card mb-0">
        <div class="ccst-card-header yellow">How to Verify</div>
        <div class="ccst-card-body p-0">
            <div class="rp-guide-step">
                <span class="rp-step-num">1</span>
                <span>Open the payment under Payments list</span>
            </div>
            <div class="rp-guide-step">
                <span class="rp-step-num">2</span>
                <span>Check the uploaded receipt image carefully</span>
            </div>
            <div class="rp-guide-step">
                <span class="rp-step-num">3</span>
                <span>Confirm amount matches total due</span>
            </div>
            <div class="rp-guide-step" style="border-bottom:none;">
                <span class="rp-step-num">4</span>
                <span>Click Verify or Reject with a reason</span>
            </div>
        </div>
    </div>

@endsection

@push('styles')
<style>
    /* Heading row */
    .dash-heading-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 24px;
    }

    .dash-heading {
        font-family: 'Volkhov', serif;
        font-size: 2.2rem;
        font-weight: 700;
        color: #1A1A1A;
        margin-bottom: 4px;
    }

    .dash-subtext {
        font-size: 1.04rem;
        color: #666;
    }

    .btn-view-all {
        display: inline-flex;
        align-items: center;
        background: #1A9FE0;
        color: white;
        font-size: 0.9rem;
        font-weight: 700;
        padding: 12px 24px;
        border-radius: 6px;
        text-decoration: none;
        white-space: nowrap;
        transition: background 0.2s;
        margin-top: 4px;
    }

    .btn-view-all:hover { background: #0D7FBF; color: white; }

    /* ── Stat cards grid ── */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-bottom: 24px;
    }

    .stat-card {
        border-radius: 10px;
        padding: 16px 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        color: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .stat-icon {
        font-size: 1.8rem;
        opacity: 0.9;
        flex-shrink: 0;
    }

    .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 2px;
    }

    .stat-label {
        font-size: 0.75rem;
        font-weight: 500;
        opacity: 0.9;
    }

    .stat-blue    { background: linear-gradient(135deg, #1A9FE0, #0D7FBF); }
    .stat-green   { background: linear-gradient(135deg, #2E8B57, #1B6B3A); }
    .stat-teal    { background: linear-gradient(135deg, #20B2AA, #148F8A); }
    .stat-yellow  { background: linear-gradient(135deg, #F5A623, #E08A00); }
    .stat-success { background: linear-gradient(135deg, #28A745, #1A7430); }
    .stat-red     { background: linear-gradient(135deg, #DC3545, #A71D2A); }

    /* ── Right panel ── */
    .rp-stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 9px 14px;
        border-bottom: 1px solid rgba(255,255,255,0.2);
        font-size: 0.82rem;
        color: white;
    }

    .rp-guide-step {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 9px 14px;
        border-bottom: 1px solid rgba(255,255,255,0.2);
        font-size: 0.78rem;
        color: rgba(255,255,255,0.92);
    }

    .rp-step-num {
        width: 20px;
        height: 20px;
        min-width: 20px;
        border-radius: 50%;
        background: #F5C518; /* yellow — matches HOW TO VERIFY header */
        color: #1A1A1A;
        font-size: 0.68rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 1px;
    }

    /* ── Date card (right panel) ── */
    .rp-date-card {
        background: rgba(255,255,255,0.18);
        border-radius: 10px;
        padding: 16px;
        text-align: center;
        color: white;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.3);
        margin-bottom: 0;
    }

    .rp-date-day {
        font-size: 2.8rem;
        font-weight: 700;
        line-height: 1;
        text-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }

    .rp-date-month {
        font-size: 0.85rem;
        opacity: 0.85;
        margin-top: 2px;
    }

    .rp-date-time {
        font-size: 1rem;
        font-weight: 600;
        margin-top: 6px;
        opacity: 0.9;
        letter-spacing: 1px;
    }

    /* Blue circle icon for Today's Summary */
    .rp-icon-circle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        min-width: 22px;
        border-radius: 50%;
        background: #1A9FE0;
        color: white;
        font-size: 0.65rem;
        margin-right: 6px;
        vertical-align: middle;
        flex-shrink: 0;
    }
</style>

@endpush

@push('scripts')
<script>
    // Live clock in right panel
    function updateTime() {
        const now = new Date();
        let h = now.getHours();
        const m = String(now.getMinutes()).padStart(2,'0');
        const s = String(now.getSeconds()).padStart(2,'0');
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        const el = document.getElementById('live-time');
        if (el) el.textContent = `${h}:${m}:${s} ${ampm}`;
    }
    updateTime();
    setInterval(updateTime, 1000);
</script>
@endpush