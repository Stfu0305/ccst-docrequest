@extends('layouts.cashier')

@section('title', 'Payment Verification')

@section('content')

<div class="cashier-sticky-header">PAYMENT VERIFICATION</div>

{{-- Filter Tabs - Full width row with uniform buttons --}}
<div class="filter-tabs-wrapper">
    <div class="filter-tabs">
        <button class="filter-tab active" data-filter="all">All Payments</button>
        <button class="filter-tab" data-filter="gcash">GCash</button>
        <button class="filter-tab" data-filter="bank_transfer">Bank Transfer</button>
        <button class="filter-tab" data-filter="cash">Cash</button>
    </div>
</div>

{{-- Payments Table with Scrollable Body --}}
<div class="payments-card">
    <div class="table-scroll-wrapper">
        <div class="table-scroll-body">
            <table class="payments-table" id="paymentsTable">
                <thead>
                    <tr>
                        <th style="width: 15%">Reference No.</th>
                        <th style="width: 20%">Student Name</th>
                        <th style="width: 12%">Request Date</th>
                        <th style="width: 10%">Amount</th>
                        <th style="width: 13%">Payment Method</th>
                        <th style="width: 15%">Status</th>
                        <th style="width: 15%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr data-method="{{ $payment->payment_method }}">
                        <td style="width: 15%">
                            <strong>{{ $payment->reference_number }}</strong>
                        </td>
                        <td style="width: 20%">{{ $payment->full_name }}</td>
                        <td style="width: 12%">{{ $payment->created_at->format('M d, Y') }}</td>
                        <td style="width: 10%">₱{{ number_format($payment->total_fee, 2) }}</td>
                        <td style="width: 13%">
                            @if($payment->payment_method === 'gcash')
                                <span class="method-badge method-gcash"><i class="bi bi-phone"></i> GCash</span>
                            @elseif($payment->payment_method === 'bank_transfer')
                                <span class="method-badge method-bank"><i class="bi bi-bank"></i> Bank Transfer</span>
                            @else
                                <span class="method-badge method-cash"><i class="bi bi-cash"></i> Cash</span>
                            @endif
                        </td>
                        <td style="width: 15%">
                            @php
                                $statusBadge = match($payment->status) {
                                    'payment_method_set' => '<span class="status-badge status-waiting">Waiting for Upload</span>',
                                    'payment_uploaded' => '<span class="status-badge status-pending">Pending Verification</span>',
                                    'payment_rejected' => '<span class="status-badge status-rejected">Rejected</span>',
                                    'payment_verified' => '<span class="status-badge status-verified">Verified</span>',
                                    default => '<span class="status-badge status-waiting">' . $payment->status . '</span>',
                                };
                            @endphp
                            {!! $statusBadge !!}
                        </td>
                        <td style="width: 15%">
                            <a href="{{ route('cashier.payments.show', $payment->id) }}" class="action-btn">
                                <i class="bi bi-eye"></i> Review
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('right-panel')
    <div class="rp-date-card">
        <div class="rp-date-day">{{ now()->format('d') }}</div>
        <div class="rp-date-month">{{ now()->format('F Y') }}</div>
        <div class="rp-date-time" id="live-time">--:-- --</div>
    </div>

    <div class="ccst-card mb-0">
        <div class="ccst-card-header blue">Quick Stats</div>
        <div class="ccst-card-body p-0">
            <div class="rp-stat-row">
                <span><i class="bi bi-hourglass-split me-2"></i> Pending Verification</span>
                <strong>{{ $pendingCount }}</strong>
            </div>
            <div class="rp-stat-row" style="border-bottom:none;">
                <span><i class="bi bi-check-circle me-2"></i> Verified Today</span>
                <strong>{{ $verifiedToday }}</strong>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .cashier-sticky-header {
        background: #1B6B3A;
        color: white;
        font-size: 0.9rem;
        font-weight: 700;
        text-align: center;
        padding: 10px 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: sticky;
        top: 0;
        z-index: 10;
        margin-bottom: 15px;
    }

    /* Filter Tabs - Full width wrapper */
    .filter-tabs-wrapper {
        margin-bottom: 15px;
    }

    .filter-tabs {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        background: white;
        padding: 8px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .filter-tab {
        flex: 1;
        min-width: 0;
        background: #f0f0f0;
        border: none;
        padding: 10px 16px;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #666;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
        white-space: nowrap;
    }

    .filter-tab:hover {
        background: #e0e0e0;
    }

    .filter-tab.active {
        background: #1B6B3A;
        color: white;
    }

    /* Payments Card */
    .payments-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
    }

    /* Scrollable Table */
    .table-scroll-wrapper {
        flex: 1;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }

    .table-scroll-body {
        flex: 1;
        overflow-y: auto;
        max-height: calc(100vh - 280px);
    }

    .payments-table {
        width: 100%;
        border-collapse: collapse;
    }

    .payments-table thead {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .payments-table th {
        background: #F0F7F0;
        padding: 12px 16px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #1B6B3A;
        text-align: center;
        border-bottom: 2px solid #D0DDD0;
    }

    .payments-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.8rem;
        vertical-align: middle;
        text-align: center;
    }

    .payments-table tr:hover {
        background: #f8fafb;
    }

    /* Make first column text left-aligned for better readability */
    .payments-table td:first-child,
    .payments-table th:first-child {
        text-align: left;
    }

    /* Method Badges */
    .method-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .method-gcash { background: #E8F4FD; color: #0969A2; }
    .method-bank { background: #FFF3CD; color: #856404; }
    .method-cash { background: #D4EDDA; color: #155724; }

    /* Status Badges */
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-waiting { background: #F0F0F0; color: #888; }
    .status-pending { background: #FFF3CD; color: #856404; }
    .status-rejected { background: #F8D7DA; color: #721C24; }
    .status-verified { background: #D4EDDA; color: #155724; }

    /* Action Button */
    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        background: #1A9FE0;
        color: white;
        padding: 4px 16px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
    }

    .action-btn:hover {
        background: #0D7FBF;
        color: white;
    }

    /* Right Panel Styles */
    .rp-stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 9px 14px;
        border-bottom: 1px solid rgba(255,255,255,0.2);
        font-size: 0.82rem;
        color: white;
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

    /* Ensure no page scroll, only table scrolls */
    .main-content {
        overflow-y: hidden !important;
        display: flex;
        flex-direction: column;
        height: calc(100vh - var(--header-h) - var(--footer-h));
        padding-bottom: 20px;
    }

    .main-content > .cashier-sticky-header {
        flex-shrink: 0;
    }

    .main-content > .filter-tabs-wrapper {
        flex-shrink: 0;
    }

    .main-content > .payments-card {
        flex: 1;
        min-height: 0;
        margin-bottom: 0;
    }
</style>
@endpush

@push('scripts')
<script>
    // Filter tabs functionality
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            const rows = document.querySelectorAll('#paymentsTable tbody tr');
            
            rows.forEach(row => {
                if (filter === 'all') {
                    row.style.display = '';
                } else {
                    const method = row.dataset.method;
                    row.style.display = method === filter ? '' : 'none';
                }
            });
        });
    });

    // Live clock
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