@extends('layouts.cashier')

@section('title', 'Payment Details - ' . $payment->reference_number)

@section('content')

<div class="cashier-sticky-header">PAYMENT DETAILS</div>

{{-- Scrollable Payment Details Container --}}
<div class="payment-detail-scroll">
    <div class="payment-detail-card">
        {{-- Request Information --}}
        <div class="detail-section">
            <h3><i class="bi bi-file-text me-2"></i>Request Information</h3>
            <div class="detail-grid">
                <div><label>Reference Number:</label> <strong>{{ $payment->reference_number }}</strong></div>
                <div><label>Student Name:</label> <strong>{{ $payment->full_name }}</strong></div>
                <div><label>Student Number:</label> <strong>{{ $payment->student_number }}</strong></div>
                <div><label>Course/Program:</label> <strong>{{ $payment->course_program }}</strong></div>
                <div><label>Year & Section:</label> <strong>{{ $payment->year_level }} - {{ $payment->section }}</strong></div>
                <div><label>Total Amount Due:</label> <strong style="color:#1B6B3A;">₱{{ number_format($payment->total_fee, 2) }}</strong></div>
            </div>
        </div>

        {{-- Payment Information --}}
        <div class="detail-section">
            <h3><i class="bi bi-credit-card me-2"></i>Payment Information</h3>
            <div class="detail-grid">
                <div><label>Payment Method:</label> 
                    <strong>
                        @if($payment->payment_method === 'gcash')
                            <span class="method-badge method-gcash"><i class="bi bi-phone"></i> GCash</span>
                        @elseif($payment->payment_method === 'bank_transfer')
                            <span class="method-badge method-bank"><i class="bi bi-bank"></i> Bank Transfer</span>
                        @else
                            <span class="method-badge method-cash"><i class="bi bi-cash"></i> Cash</span>
                        @endif
                    </strong>
                </div>
                <div><label>Status:</label> 
                    <strong>
                        @if($payment->status === 'payment_uploaded')
                            <span class="status-badge status-pending">Pending Verification</span>
                        @elseif($payment->status === 'payment_rejected')
                            <span class="status-badge status-rejected">Rejected</span>
                        @elseif($payment->status === 'payment_verified')
                            <span class="status-badge status-verified">Verified</span>
                        @else
                            <span class="status-badge status-waiting">{{ $payment->status }}</span>
                        @endif
                    </strong>
                </div>
                @if($payment->paymentProof && $payment->paymentProof->reference_number)
                <div><label>Reference No.:</label> <strong>{{ $payment->paymentProof->reference_number }}</strong></div>
                @endif
                @if($payment->paymentProof && $payment->paymentProof->amount_declared)
                <div><label>Amount Declared:</label> 
                    <strong class="{{ $payment->paymentProof->hasAmountMismatch() ? 'text-danger' : '' }}">
                        ₱{{ number_format($payment->paymentProof->amount_declared, 2) }}
                        @if($payment->paymentProof->hasAmountMismatch())
                            <span class="mismatch-warning">(Mismatch!)</span>
                        @endif
                    </strong>
                </div>
                @endif
            </div>
        </div>

        {{-- Uploaded Proof (for GCash/Bank Transfer) --}}
        @if(in_array($payment->payment_method, ['gcash', 'bank_transfer']) && $payment->paymentProof)
        <div class="detail-section">
            <h3><i class="bi bi-image me-2"></i>Uploaded Payment Proof</h3>
            <div class="proof-container">
                @php
                    $filePath = storage_path('app/private/' . $payment->paymentProof->file_path);
                    $isImage = in_array(strtolower(pathinfo($filePath, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']);
                @endphp
                @if($isImage && file_exists($filePath))
                    <img src="{{ route('cashier.payments.proof', $payment->id) }}" alt="Payment Proof" class="proof-image" id="proofImage">
                @elseif(file_exists($filePath))
                    <div class="pdf-viewer">
                        <i class="bi bi-file-earmark-pdf" style="font-size: 4rem; color: #DC3545;"></i>
                        <a href="{{ route('cashier.payments.proof', $payment->id) }}" target="_blank" class="btn btn-sm btn-danger mt-2">View PDF</a>
                    </div>
                @else
                    <div class="proof-placeholder">
                        <i class="bi bi-file-earmark-x"></i>
                        <p>Proof file not found</p>
                    </div>
                @endif
            </div>
            @if($payment->paymentProof && $payment->paymentProof->is_resubmission)
            <div class="resubmission-badge">
                <i class="bi bi-arrow-repeat me-1"></i> RESUBMITTED
            </div>
            @endif
            @if($payment->paymentProof && $payment->paymentProof->rejection_reason)
            <div class="rejection-reason">
                <strong>Rejection Reason:</strong> {{ $payment->paymentProof->rejection_reason }}
            </div>
            @endif
        </div>
        @endif

        {{-- Action Buttons --}}
        @if($payment->payment_method === 'cash')
            @if($payment->status === 'payment_method_set')
            <div class="action-buttons">
                <button type="button" class="btn-verify" onclick="markCashPaid()">
                    <i class="bi bi-cash-stack me-1"></i> Mark as Paid (Cash)
                </button>
            </div>
            @endif
        @else
            @if($payment->status === 'payment_uploaded')
            <div class="action-buttons">
                <button type="button" class="btn-verify" onclick="verifyPayment()">
                    <i class="bi bi-check-circle me-1"></i> Verify Payment
                </button>
                <button type="button" class="btn-reject" onclick="showRejectModal()">
                    <i class="bi bi-x-circle me-1"></i> Reject Payment
                </button>
            </div>
            @elseif($payment->status === 'payment_method_set')
            <div class="action-buttons">
                <div class="info-message">
                    <i class="bi bi-info-circle me-1"></i> Waiting for student to upload payment proof.
                </div>
            </div>
            @endif
        @endif

        <div class="back-link">
            <a href="{{ route('cashier.payments.index') }}" class="btn-back">
                <i class="bi bi-arrow-left me-1"></i> Back to Payments List
            </a>
        </div>
    </div>
</div>

{{-- Rejection Modal --}}
<div id="rejectModal" class="modal-overlay" style="display:none;">
    <div class="modal-container">
        <div class="modal-header">
            <h4><i class="bi bi-exclamation-triangle me-2"></i>Reject Payment</h4>
            <button class="modal-close" onclick="closeRejectModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Please provide a reason for rejecting this payment proof:</p>
            <textarea id="rejectionReason" class="rejection-textarea" rows="4" placeholder="Enter rejection reason..."></textarea>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel-modal" onclick="closeRejectModal()">Cancel</button>
            <button class="btn-confirm-reject" onclick="confirmReject()">Confirm Rejection</button>
        </div>
    </div>
</div>

<form id="verifyForm" method="POST" action="{{ route('cashier.payments.verify', $payment->id) }}" style="display:none;">
    @csrf
    @method('PATCH')
</form>

<form id="rejectForm" method="POST" action="{{ route('cashier.payments.reject', $payment->id) }}" style="display:none;">
    @csrf
    @method('PATCH')
    <input type="hidden" name="rejection_reason" id="rejectionReasonInput">
</form>

<form id="cashPaidForm" method="POST" action="{{ route('cashier.payments.markCashPaid', $payment->id) }}" style="display:none;">
    @csrf
    @method('PATCH')
</form>

@endsection

@section('right-panel')
    <div class="rp-date-card">
        <div class="rp-date-day">{{ now()->format('d') }}</div>
        <div class="rp-date-month">{{ now()->format('F Y') }}</div>
        <div class="rp-date-time" id="live-time">--:-- --</div>
    </div>

    <div class="ccst-card mb-0">
        <div class="ccst-card-header yellow">Verification Guide</div>
        <div class="ccst-card-body p-0">
            <div class="rp-guide-step">
                <span class="rp-step-num">1</span>
                <span>Verify receipt is clear and readable</span>
            </div>
            <div class="rp-guide-step">
                <span class="rp-step-num">2</span>
                <span>Check amount matches total due</span>
            </div>
            <div class="rp-guide-step">
                <span class="rp-step-num">3</span>
                <span>Confirm reference number is correct</span>
            </div>
            <div class="rp-guide-step" style="border-bottom:none;">
                <span class="rp-step-num">4</span>
                <span>Click Verify to generate official receipt</span>
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
        margin-bottom: 24px;
    }

    /* Scrollable Payment Details Container */
    .payment-detail-scroll {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 4px;
        margin-bottom: 20px;
    }

    /* Custom scrollbar */
    .payment-detail-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .payment-detail-scroll::-webkit-scrollbar-track {
        background: #f0f0f0;
        border-radius: 3px;
    }

    .payment-detail-scroll::-webkit-scrollbar-thumb {
        background: #1B6B3A;
        border-radius: 3px;
    }

    .payment-detail-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        padding: 24px;
    }

    .info-message {
    background: #E8F4FD;
    color: #0969A2;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

    .detail-section {
        margin-bottom: 28px;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 20px;
    }

    .detail-section:last-of-type {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .detail-section h3 {
        font-size: 1rem;
        font-weight: 700;
        color: #1B6B3A;
        margin-bottom: 16px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px 24px;
    }

    .detail-grid div {
        font-size: 0.85rem;
    }

    .detail-grid label {
        color: #888;
        font-weight: 500;
        margin-right: 8px;
    }

    .proof-container {
        text-align: center;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 12px;
    }

    .proof-image {
        max-width: 100%;
        max-height: 400px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        cursor: pointer;
    }

    .proof-placeholder {
        text-align: center;
        padding: 40px;
        color: #999;
    }

    .proof-placeholder i {
        font-size: 3rem;
        margin-bottom: 10px;
    }

    .resubmission-badge {
        background: #FFF3CD;
        color: #856404;
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        margin-top: 10px;
    }

    .rejection-reason {
        background: #F8D7DA;
        color: #721C24;
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 0.8rem;
        margin-top: 12px;
    }

    .mismatch-warning {
        background: #FFF3CD;
        color: #856404;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        margin-left: 8px;
    }

    .action-buttons {
        display: flex;
        gap: 16px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #f0f0f0;
    }

    .btn-verify {
        background: #1B6B3A;
        color: white;
        border: none;
        padding: 10px 28px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-verify:hover {
        background: #0C5A2E;
    }

    .btn-reject {
        background: #DC3545;
        color: white;
        border: none;
        padding: 10px 28px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-reject:hover {
        background: #b02a37;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        background: #f0f0f0;
        color: #666;
        padding: 8px 20px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.8rem;
        margin-top: 20px;
    }

    .btn-back:hover {
        background: #e0e0e0;
        color: #1A1A1A;
    }

    .back-link {
        margin-top: 20px;
    }

    /* Method Badges */
    .method-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
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
    }

    .status-waiting { background: #F0F0F0; color: #888; }
    .status-pending { background: #FFF3CD; color: #856404; }
    .status-rejected { background: #F8D7DA; color: #721C24; }
    .status-verified { background: #D4EDDA; color: #155724; }

    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-container {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }

    .modal-header {
        background: #DC3545;
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h4 {
        margin: 0;
        font-size: 1rem;
    }

    .modal-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
    }

    .modal-body {
        padding: 20px;
    }

    .rejection-textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #D0DDD0;
        border-radius: 6px;
        font-family: 'Poppins', sans-serif;
        font-size: 0.85rem;
        resize: vertical;
    }

    .modal-footer {
        padding: 15px 20px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        border-top: 1px solid #f0f0f0;
    }

    .btn-cancel-modal {
        background: #f0f0f0;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        cursor: pointer;
    }

    .btn-confirm-reject {
        background: #DC3545;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        cursor: pointer;
    }

    /* Right Panel Styles */
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
    }

    .rp-date-card {
        background: rgba(255,255,255,0.18);
        border-radius: 10px;
        padding: 16px;
        text-align: center;
        color: white;
        backdrop-filter: blur(8px);
        margin-bottom: 18px;
    }

    .rp-date-day { font-size: 2.8rem; font-weight: 700; line-height: 1; }
    .rp-date-month { font-size: 0.85rem; opacity: 0.85; margin-top: 2px; }
    .rp-date-time { font-size: 1rem; font-weight: 600; margin-top: 6px; }

    /* Ensure no page scroll, only container scrolls */
    .main-content {
        overflow-y: hidden !important;
        display: flex;
        flex-direction: column;
        height: calc(100vh - var(--header-h) - var(--footer-h));
        padding-bottom: 0;
    }

    .main-content > .cashier-sticky-header {
        flex-shrink: 0;
    }

    .main-content > .payment-detail-scroll {
        flex: 1;
        min-height: 0;
    }
</style>
@endpush

@push('scripts')
<script>
    function verifyPayment() {
        Swal.fire({
            title: 'Verify Payment?',
            text: 'This will mark the payment as verified and generate an official receipt.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1B6B3A',
            cancelButtonColor: '#DC3545',
            confirmButtonText: 'Yes, Verify',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('verifyForm').submit();
            }
        });
    }

    function showRejectModal() {
        document.getElementById('rejectModal').style.display = 'flex';
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
        document.getElementById('rejectionReason').value = '';
    }

    function confirmReject() {
        const reason = document.getElementById('rejectionReason').value;
        if (!reason.trim()) {
            Swal.fire('Error', 'Please provide a rejection reason.', 'error');
            return;
        }
        document.getElementById('rejectionReasonInput').value = reason;
        document.getElementById('rejectForm').submit();
    }

    function markCashPaid() {
        Swal.fire({
            title: 'Mark as Paid?',
            text: 'This will mark the cash payment as paid and generate an official receipt.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1B6B3A',
            cancelButtonColor: '#DC3545',
            confirmButtonText: 'Yes, Mark as Paid',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('cashPaidForm').submit();
            }
        });
    }

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