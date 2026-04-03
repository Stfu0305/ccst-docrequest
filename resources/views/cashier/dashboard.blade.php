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

    {{-- Only 3 Stat Cards (removed individual method cards) --}}
    <div class="stat-grid">
        <div class="stat-card stat-blue">
            <div class="stat-icon"><i class="bi bi-credit-card-2-front"></i></div>
            <div class="stat-info">
                <div class="stat-value">{{ $totalPending }}</div>
                <div class="stat-label">Total Pending</div>
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

    {{-- Recent Pending Payments Table --}}
    <div class="ccst-card">
        <div class="ccst-card-header">
            <i class="bi bi-clock-history me-1"></i> Recent Pending Payments
        </div>
        <div class="ccst-card-body p-0">
            <div class="table-responsive">
                <div class="table-scroll-body">
                    <table class="pending-table">
                        <thead style="text-align: center;">
                            <tr >
                                <th style="width: 18% ; text-align: center;">Reference No.</th>
                                <th style="width: 25%; text-align: center;">Student</th>
                                <th style="width: 12%; text-align: center;">Method</th>
                                <th style="width: 12%; text-align: center;">Amount</th>
                                <th style="width: 15%; text-align: center;">Submitted</th>
                                <th style="width: 15%; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody style="text-align: center;">
                            @forelse($recentPending as $request)
                            <tr>
                                <td style="width: 18%">
                                    <span class="ref-number">{{ $request->reference_number }}</span>
                                </td>
                                <td style="width: 25%">{{ $request->full_name }}</td>
                                <td style="width: 15%; text-align: center;">
                                    @php
                                        $methodLabels = [
                                            'gcash'         => ['GCash', 'method-gcash'],
                                            'bank_transfer' => ['Bank Transfer', 'method-bank'],
                                            'cash'          => ['Cash', 'method-cash'],
                                        ];
                                        [$label, $methodClass] = $methodLabels[$request->payment_method] ?? ['—', ''];
                                    @endphp
                                    <span class="method-badge {{ $methodClass }}">{{ $label }}</span>
                                </td>
                                <td style="width: 12%">₱{{ number_format($request->total_fee, 2) }}</td>
                                <td style="width: 15%" class="text-muted">{{ $request->updated_at->format('M d, Y') }}</td>
                                <td style="width: 15%">
                                    <a href="{{ route('cashier.payments.show', $request->id) }}" class="action-btn">
                                        <i class="bi bi-eye"></i> Review
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
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
    </div>

@endsection

{{-- RIGHT PANEL --}}
@section('right-panel')

    {{-- Date + Time card --}}
    <div class="rp-date-card">
        <div class="rp-date-day">{{ now()->format('d') }}</div>
        <div class="rp-date-month">{{ now()->format('F Y') }}</div>
        <div class="rp-date-time" id="live-time">--:-- --</div>
    </div>

    {{-- Today's Summary (removed Verified row) --}}
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
            <div class="rp-stat-row" style="border-bottom:none;">
                <span><span class="rp-icon-circle"><i class="bi bi-cash"></i></span> Cash</span>
                <strong>{{ $cashPending }}</strong>
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

    /* ── Stat cards grid - Only 3 cards now ── */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
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
    .stat-success { background: linear-gradient(135deg, #28A745, #1A7430); }
    .stat-red     { background: linear-gradient(135deg, #DC3545, #A71D2A); }

    /* ── Recent Pending Payments Table ── */
    .ccst-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 0;
    }

    .ccst-card-header {
        background: #1B6B3A;
        color: white;
        font-size: 0.86rem;
        font-weight: 700;
        padding: 12px 20px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .table-scroll-body {
        max-height: 380px;
        overflow-y: auto;
    }

    .pending-table {
        width: 100%;
        border-collapse: collapse;
    }

    .pending-table thead {
        position: sticky;
        top: 0;
        background: #F0F7F0;
        z-index: 10;
    }

    .pending-table th {
        background: #F0F7F0;
        padding: 12px 16px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #1B6B3A;
        text-align: left;
        border-bottom: 1px solid #D0DDD0;
    }

    .pending-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.8rem;
        vertical-align: middle;
    }

    .pending-table tr:hover {
        background: #f8fafb;
    }

    .ref-number {
        font-weight: 600;
        color: #1B6B3A;
    }

    .method-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .method-gcash { background: #E8F4FD; color: #0969A2; }
    .method-bank { background: #FFF3CD; color: #856404; }
    .method-cash { background: #D4EDDA; color: #155724; }

    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #1A9FE0;
        color: white;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 600;
        text-decoration: none;
    }

    .action-btn:hover {
        background: #0D7FBF;
        color: white;
    }

    /* ── Right panel styles ── */
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
        background: #F5C518;
        color: #1A1A1A;
        font-size: 0.68rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 1px;
    }

    .rp-date-card {
        /* background: rgba(255,255,255,0.18); */
        border-radius: 10px;
        padding: 16px;
        text-align: center;
        color: white;
        backdrop-filter: blur(8px);
        /* -webkit-backdrop-filter: blur(8px); */
        /* border: 1px solid rgba(255,255,255,0.3); */
        margin-bottom: 10px;
    }

    .rp-date-day {
        font-size: 3.25rem;
        font-weight: 700;
        line-height: 1;
        text-shadow: 0 2px 8px rgba(0,0,0,0.3);
        margin-top: 20px;
    }

    .rp-date-month {
        font-size: 1.25rem;
        opacity: 0.85;
        margin-top: 2px;
    }

    .rp-date-time {
        font-size: 1.50rem;
        font-weight: 600;
        margin-top: 6px;
        opacity: 0.9;
        letter-spacing: 1px;
    }

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

    /* Ensure no page scroll, only table scrolls */
    .main-content {
        overflow-y: hidden !important;
        display: flex;
        flex-direction: column;
    }

    .main-content > .dash-heading-row {
        flex-shrink: 0;
    }

    .main-content > .stat-grid {
        flex-shrink: 0;
    }

    .main-content > .ccst-card {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        margin-bottom: 0;
    }

    .main-content > .ccst-card .ccst-card-body {
        flex: 1;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .main-content > .ccst-card .table-scroll-body {
        flex: 1;
        overflow-y: auto;
    }

    /* Bottom margin for table to match top margin */
    .main-content {
        padding-bottom: 28px;
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