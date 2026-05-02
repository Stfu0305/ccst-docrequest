@extends('layouts.student')

@section('title', 'Request Summary — ' . $docRequest->reference_number)

@section('content')

{{-- ── STICKY HEADER ── --}}
<div class="req-sticky-header">DOCUMENT REQUEST SUMMARY</div>

{{-- ── SCROLLABLE CONTAINER ── --}}
<div class="req-scroll">

    <div class="req-card">
        <div class="req-card-body">

            {{-- ════════════════════════════════════════════════════
                 SECTION 1: STUDENT INFORMATION
            ════════════════════════════════════════════════════ --}}
            <div class="section-heading-row">
                <span class="section-heading">Student Information</span>
                <div class="ref-meta">
                    <strong>Request No.</strong> {{ $docRequest->reference_number }}<br>
                    <strong>Date:</strong> {{ $docRequest->created_at->format('m/d/Y') }}
                </div>
            </div>

            <div class="form-row-3">
                <div class="form-field">
                    <label>Student Number</label>
                    <div class="field-readonly">{{ $docRequest->student_number ?? '—' }}</div>
                </div>
                <div class="form-field">
                    <label>Email</label>
                    <div class="field-readonly field-ellipsis">{{ auth()->user()->email }}</div>
                </div>
                <div class="form-field">
                    <label>Contact No.</label>
                    <div class="field-readonly">{{ $docRequest->contact_number }}</div>
                </div>
            </div>

            <div class="form-row-1">
                <div class="form-field">
                    <label>Full Name</label>
                    <div class="field-readonly">{{ $docRequest->full_name }}</div>
                </div>
            </div>

            <div class="form-row-2">
                <div class="form-field">
                    <label>Course / Program</label>
                    <div class="field-readonly">{{ $docRequest->course_program }}</div>
                </div>
                <div class="form-field">
                    <label>Year &amp; Section</label>
                    <div class="field-readonly">{{ $docRequest->year_level }} — {{ $docRequest->section }}</div>
                </div>
            </div>

            <div class="section-divider"></div>

            {{-- ════════════════════════════════════════════════════
                 SECTION 2: REQUESTED DOCUMENTS
            ════════════════════════════════════════════════════ --}}
            <div class="section-heading" style="margin-bottom:10px;">Requested Documents</div>

            <table class="docs-table">
                <thead>
                    <tr class="docs-table-header">
                        <th>Document</th>
                        <th style="width:130px;" class="text-center">Assessment Year</th>
                        <th style="width:110px;" class="text-center">Grading Period</th>
                        <th style="width:80px;"  class="text-center">Qty</th>
                        <th style="width:80px;"  class="text-end">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($docRequest->items as $index => $item)
                    <tr style="background:{{ $index % 2 === 0 ? '#f8fafb' : 'white' }};">
                        <td class="doc-name-cell">{{ $item->documentType->name }}</td>
                        <td class="text-center doc-meta">{{ $item->assessment_year ?? 'n/a' }}</td>
                        <td class="text-center doc-meta">{{ $item->semester ?? 'n/a' }}</td>
                        <td class="text-center doc-meta">{{ $item->copies }}</td>
                        <td class="text-end doc-price">₱{{ number_format($item->fee * $item->copies, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="total-row">
                <span class="total-label">Total:</span>
                <div class="total-display">₱{{ number_format($docRequest->total_fee, 2) }}</div>
            </div>

            <div class="section-divider"></div>

            {{-- ════════════════════════════════════════════════════
                 SECTION 3: PAYMENT DETAILS
                 State A → pending            — choose method
                 State B → payment_method_set — confirmed, next step
                 State C → anything beyond    — locked, read-only
            ════════════════════════════════════════════════════ --}}
            @php
                $status      = $docRequest->status;
                $method      = $docRequest->payment_method;
                $isSelecting = $status === 'pending';
                $isConfirmed = $status === 'payment_method_set';
                $isLocked    = !$isSelecting && !$isConfirmed;
            @endphp

            <div class="section-heading" style="margin-bottom:12px;">Payment Details</div>

            {{-- ── STATE A: SELECT METHOD ── --}}
            @if($isSelecting)

                <p class="payment-prompt">
                    Choose how you will pay
                    <strong style="color:#1B6B3A;">₱{{ number_format($docRequest->total_fee, 2) }}</strong>
                    for your document request.
                </p>

                <div class="d-flex flex-wrap gap-2 mb-3" id="method-pills">
                    @if($paymentSettings->has('gcash'))
                        <button type="button" class="method-pill" data-method="gcash" onclick="selectMethod('gcash')">
                            <i class="bi bi-phone me-1"></i> GCash
                        </button>
                    @endif
                    @if($paymentSettings->has('bdo') || $paymentSettings->has('bpi'))
                        <button type="button" class="method-pill" data-method="bank_transfer" onclick="selectMethod('bank_transfer')">
                            <i class="bi bi-bank me-1"></i> Bank Transfer
                        </button>
                    @endif
                    @if($paymentSettings->has('cash'))
                        <button type="button" class="method-pill" data-method="cash" onclick="selectMethod('cash')">
                            <i class="bi bi-cash-coin me-1"></i> Over-the-Counter Cash
                        </button>
                    @endif
                </div>

                {{-- GCash detail block --}}
                @if($paymentSettings->has('gcash'))
                    @php $gcash = $paymentSettings->get('gcash'); @endphp
                    <div id="block-gcash" class="method-block">
                        <div class="method-detail-box">
                            <div class="method-detail-title">GCash Payment Details</div>
                            <div class="form-row-2 mb-2">
                                <div class="form-field">
                                    <label>Send to Number</label>
                                    <div class="field-readonly fw-bold" style="color:#1B6B3A;font-size:1rem;letter-spacing:1px;">
                                        {{ $gcash->account_number }}
                                    </div>
                                </div>
                                <div class="form-field">
                                    <label>Account Name</label>
                                    <div class="field-readonly">{{ $gcash->account_name }}</div>
                                </div>
                            </div>
                            <div class="form-field mb-2">
                                <label>Amount to Send</label>
                                <div class="field-readonly fw-bold" style="color:#2E8B57;font-size:1.15rem;">
                                    ₱{{ number_format($docRequest->total_fee, 2) }}
                                </div>
                            </div>
                            <div class="method-warning">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                Send <strong>exactly ₱{{ number_format($docRequest->total_fee, 2) }}</strong>, then
                                <strong>screenshot your GCash receipt</strong>. You will upload it in the next step.
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Bank Transfer detail block --}}
                @if($paymentSettings->has('bdo') || $paymentSettings->has('bpi'))
                    <div id="block-bank_transfer" class="method-block">
                        <div class="method-detail-box">
                            <div class="method-detail-title">Bank Transfer Payment Details</div>

                            @if($paymentSettings->has('bdo'))
                                @php $bdo = $paymentSettings->get('bdo'); @endphp
                                <div class="bank-row">
                                    <div class="fw-semibold mb-1" style="font-size:0.82rem;">
                                        <i class="bi bi-building me-1"></i> BDO Unibank
                                    </div>
                                    <div class="form-row-3" style="font-size:0.8rem;">
                                        <div><span class="text-muted">Account Name:</span><br><strong>{{ $bdo->account_name }}</strong></div>
                                        <div><span class="text-muted">Account No.:</span><br><strong>{{ $bdo->account_number }}</strong></div>
                                        @if($bdo->branch)
                                            <div><span class="text-muted">Branch:</span><br><strong>{{ $bdo->branch }}</strong></div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($paymentSettings->has('bpi'))
                                @php $bpi = $paymentSettings->get('bpi'); @endphp
                                <div class="bank-row">
                                    <div class="fw-semibold mb-1" style="font-size:0.82rem;">
                                        <i class="bi bi-building me-1"></i> Bank of the Philippine Islands (BPI)
                                    </div>
                                    <div class="form-row-3" style="font-size:0.8rem;">
                                        <div><span class="text-muted">Account Name:</span><br><strong>{{ $bpi->account_name }}</strong></div>
                                        <div><span class="text-muted">Account No.:</span><br><strong>{{ $bpi->account_number }}</strong></div>
                                        @if($bpi->branch)
                                            <div><span class="text-muted">Branch:</span><br><strong>{{ $bpi->branch }}</strong></div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="form-field mt-2 mb-2">
                                <label>Amount to Transfer</label>
                                <div class="field-readonly fw-bold" style="color:#2E8B57;font-size:1.15rem;">
                                    ₱{{ number_format($docRequest->total_fee, 2) }}
                                </div>
                            </div>
                            <div class="method-warning">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                Transfer <strong>exactly ₱{{ number_format($docRequest->total_fee, 2) }}</strong>, then
                                <strong>screenshot the bank confirmation</strong>. You will upload it in the next step.
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Cash detail block --}}
                @if($paymentSettings->has('cash'))
                    @php $cash = $paymentSettings->get('cash'); @endphp
                    <div id="block-cash" class="method-block">
                        <div class="method-detail-box">
                            <div class="method-detail-title">Over-the-Counter Cash Payment</div>
                            @if($cash->extra_info)
                                <div class="form-field mb-2">
                                    <label>Office Location &amp; Hours</label>
                                    <div class="field-readonly">{{ $cash->extra_info }}</div>
                                </div>
                            @endif
                            <div class="form-field mb-2">
                                <label>Amount to Pay</label>
                                <div class="field-readonly fw-bold" style="color:#2E8B57;font-size:1.15rem;">
                                    ₱{{ number_format($docRequest->total_fee, 2) }}
                                </div>
                            </div>
                            <div class="method-info">
                                <i class="bi bi-info-circle-fill me-1"></i>
                                Visit the cashier office and show your request number:
                                <strong>{{ $docRequest->reference_number }}</strong>.
                                The cashier will mark your payment in the system.
                                <strong>No upload needed</strong> for cash.
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Confirm form --}}
                <form id="confirm-method-form"
                      method="POST"
                      action="{{ route('student.payments.setMethod', $docRequest->id) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="payment_method" id="selected-method-input" value="">
                    <div id="confirm-btn-wrap" style="display:none; margin-top:14px;">
                        <button type="submit" class="btn-submit">
                            <i class="bi bi-check-circle me-1"></i> CONFIRM PAYMENT METHOD
                        </button>
                        <span id="selected-method-label" class="ms-3 text-muted" style="font-size:0.82rem;"></span>
                    </div>
                </form>

            {{-- ── STATE B: METHOD CONFIRMED ── --}}
            @elseif($isConfirmed)

                <div class="status-confirmed-box">
                    <i class="bi bi-check-circle-fill" style="color:#155724; font-size:1.1rem; flex-shrink:0;"></i>
                    <div>
                        <div class="fw-bold" style="color:#155724; font-size:0.88rem;">Payment Method Confirmed</div>
                        <div style="color:#155724; font-size:0.82rem;">
                            You selected: <strong>
                                @if($method === 'gcash') GCash
                                @elseif($method === 'bank_transfer') Bank Transfer
                                @elseif($method === 'cash') Over-the-Counter Cash
                                @endif
                            </strong>
                        </div>
                    </div>
                </div>

                @if(in_array($method, ['gcash', 'bank_transfer']))
                    <p class="payment-prompt">
                        Please proceed to upload a screenshot of your
                        {{ $method === 'gcash' ? 'GCash receipt' : 'bank transfer confirmation' }}.
                    </p>
                    <a href="{{ route('student.payments.showUpload', $docRequest->id) }}" class="btn-submit">
                        <i class="bi bi-upload me-1"></i> PROCEED TO UPLOAD PROOF
                    </a>
                    <div style="margin-top:10px;">
                        <a href="#"
                           onclick="toggleReselect(); return false;"
                           class="reselect-link">
                            Changed your mind? Choose a different method
                        </a>
                        <div id="reselect-wrap" style="display:none; margin-top:8px;">
                            <form method="POST" action="{{ route('student.payments.setMethod', $docRequest->id) }}">
                                @csrf
                                @method('PATCH')
                                <div class="d-flex flex-wrap gap-2">
                                    @if($paymentSettings->has('gcash'))
                                        <button type="submit" name="payment_method" value="gcash" class="method-pill method-pill--sm">
                                            <i class="bi bi-phone me-1"></i> GCash
                                        </button>
                                    @endif
                                    @if($paymentSettings->has('bdo') || $paymentSettings->has('bpi'))
                                        <button type="submit" name="payment_method" value="bank_transfer" class="method-pill method-pill--sm">
                                            <i class="bi bi-bank me-1"></i> Bank Transfer
                                        </button>
                                    @endif
                                    @if($paymentSettings->has('cash'))
                                        <button type="submit" name="payment_method" value="cash" class="method-pill method-pill--sm">
                                            <i class="bi bi-cash-coin me-1"></i> Cash
                                        </button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>

                @elseif($method === 'cash')
                    <div class="method-info" style="margin:12px 0;">
                        <i class="bi bi-building me-1"></i>
                        Visit the cashier office and pay
                        <strong>₱{{ number_format($docRequest->total_fee, 2) }}</strong> in person.
                        Tell them your request number: <strong>{{ $docRequest->reference_number }}</strong>.
                        @if($paymentSettings->has('cash') && $paymentSettings->get('cash')->extra_info)
                            <br><span style="color:#0c5460;">{{ $paymentSettings->get('cash')->extra_info }}</span>
                        @endif
                    </div>
                    <a href="{{ route('student.requests.history') }}" class="btn-submit">
                        <i class="bi bi-clock-history me-1"></i> DONE — VIEW MY REQUEST HISTORY
                    </a>
                @endif

            {{-- ── STATE C: LOCKED ── --}}
            @elseif($isLocked)

                <div class="locked-row">
                    <strong>Payment Method:</strong>
                    <span class="ms-2">
                        @if($method === 'gcash') GCash
                        @elseif($method === 'bank_transfer') Bank Transfer
                        @elseif($method === 'cash') Over-the-Counter Cash
                        @else —
                        @endif
                    </span>
                </div>

                @if($docRequest->paymentProof?->reference_number)
                    <div class="locked-row">
                        <strong>Reference Number:</strong>
                        <span class="ms-2">{{ $docRequest->paymentProof->reference_number }}</span>
                    </div>
                @endif

                @if($status === 'payment_uploaded')
                    <div class="method-warning">
                        <i class="bi bi-hourglass-split me-1"></i>
                        Proof uploaded. Waiting for cashier verification.
                    </div>
                @elseif($status === 'payment_rejected')
                    <div class="box-rejected">
                        <i class="bi bi-x-circle-fill me-1"></i>
                        <strong>Proof rejected.</strong>
                        @if($docRequest->paymentProof?->rejection_reason)
                            Reason: {{ $docRequest->paymentProof->rejection_reason }}
                        @endif
                        Go to <a href="{{ route('student.requests.history') }}" class="rejected-link">Request History</a> to re-upload.
                    </div>
                @else
                    <div class="status-confirmed-box">
                        <i class="bi bi-check-circle-fill" style="color:#155724; flex-shrink:0;"></i>
                        <span style="color:#155724; font-size:0.85rem;">Payment verified. Your documents are being processed.</span>
                    </div>
                @endif

            @endif

            <div class="section-divider"></div>

            <p class="note-text">
                <strong>Note:</strong> Updates on your request status can be viewed in the
                <a href="{{ route('student.requests.history') }}" class="note-link">Request History</a> section.
            </p>

        </div>{{-- end req-card-body --}}
    </div>{{-- end req-card --}}



    {{-- ── BOTTOM ACTION BUTTONS ── --}}
    <div class="submit-row">
        <a href="{{ route('student.dashboard') }}" class="btn-cancel">Back to Home</a>

        @if($docRequest->status === 'pending')
            <button type="button" class="btn-danger" id="cancel-request-btn">
                <i class="bi bi-x-circle me-1"></i> Cancel Request
            </button>
            <form id="cancel-request-form"
                  method="POST"
                  action="{{ route('student.requests.cancel', $docRequest->id) }}"
                  style="display:none;">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>

</div>{{-- end req-scroll --}}

{{-- ── APPOINTMENT MODAL ── --}}
@if($showAppointmentModal && $timeSlots)
<div id="appointment-modal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Book Your Pickup Appointment</h3>
            <button type="button" class="modal-close" onclick="closeAppointmentModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="appointment-summary">
                <p><strong>Request:</strong> {{ $docRequest->reference_number }}</p>
                <p><strong>Total Amount:</strong> ₱{{ number_format($docRequest->total_fee, 2) }}</p>
            </div>

            <form id="appointment-form" method="POST" action="{{ route('student.appointments.store') }}">
                @csrf
                <input type="hidden" name="document_request_id" value="{{ $docRequest->id }}">

                <div class="form-group">
                    <label>Pickup Date</label>
                    <input type="text" name="appointment_date" id="modal-appointment-date" class="form-input" readonly required>
                </div>

                <div class="form-group">
                    <label>Available Time Slots</label>
                    <div id="time-slots-container">
                        <div style="padding: 15px; text-align: center; color: #666; background: #f8f9fa; border-radius: 8px; border: 1px dashed #ccc;">
                            Please select a pickup date first to view available time slots.
                        </div>
                    </div>
                    <input type="hidden" name="time_slot_id" id="selected-time-slot" value="">
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeAppointmentModal()">Skip for Now</button>
                    <button type="submit" class="btn-submit" id="book-appointment-btn" disabled>Book Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection


{{-- ════════════════════════════════════════════════════════════════════
     PAGE STYLES
     Bell styles are in the layout — nothing bell-related lives here.
════════════════════════════════════════════════════════════════════ --}}
@push('styles')
<style>

    /* ── Sticky header ── */
    .req-sticky-header {
        background: #1B6B3A;
        color: white;
        font-size: 0.9rem;
        font-weight: 700;
        text-align: center;
        padding: 10px 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        max-width: 900px;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    /* ── Scrollable container ── */
    .req-scroll {
        height: calc(100vh - var(--header-h) - var(--footer-h) - 120px);
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-width: none;
    }
    .req-scroll::-webkit-scrollbar { display: none; }

    /* ── Main card ── */
    .req-card {
        background: #ffffff;
        border: 1px solid #D0DDD0;
        border-radius: 0 0 12px 12px;
        width: 100%;
        max-width: 900px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.10);
    }
    .req-card-body { padding: 20px 24px; }

    /* ── Section helpers ── */
    .section-heading {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1A1A1A;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .section-heading-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }
    .section-divider { border-top: 1px solid #D0DDD0; margin: 16px 0; }

    /* ── Reference meta (top-right of student info section) ── */
    .ref-meta { font-size: 0.78rem; color: #666; line-height: 1.6; text-align: right; }

    /* ── Form grid rows ── */
    .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 10px; }
    .form-row-2 { display: grid; grid-template-columns: 1fr 1fr;     gap: 12px; margin-bottom: 10px; }
    .form-row-1 { display: grid; grid-template-columns: 1fr;         gap: 12px; margin-bottom: 10px; }
    .form-field  { display: flex; flex-direction: column; }
    .form-field label {
        font-size: 0.73rem;
        font-weight: 700;
        color: #555;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 3px;
    }
    .field-readonly {
        padding: 6px 10px;
        border: 1px solid #D0DDD0;
        border-radius: 4px;
        background: #f8f9fa;
        font-size: 0.82rem;
        color: #1A1A1A;
        font-family: 'Poppins', sans-serif;
        min-height: 32px;
    }
    .field-ellipsis { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    /* ── Documents table ── */
    .docs-table { width: 100%; border-collapse: collapse; font-size: 0.8rem; margin-bottom: 4px; }
    .docs-table-header { background: #1B6B3A; }
    .docs-table-header th { padding: 8px; font-size: 0.75rem; font-weight: 600; color: white; text-align: left; }
    .docs-table td { padding: 7px 8px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
    .doc-name-cell { font-weight: 600; color: #1A1A1A; font-size: 0.82rem; }
    .doc-meta      { color: #555; font-size: 0.8rem; }
    .doc-price     { font-weight: 700; color: #1B6B3A; font-size: 0.82rem; }

    /* ── Total row ── */
    .total-row    { display: flex; align-items: center; justify-content: flex-end; gap: 10px; padding-top: 8px; }
    .total-label  { font-size: 0.82rem; font-weight: 700; color: #1A1A1A; }
    .total-display {
        padding: 5px 16px;
        border: 2px solid #1B6B3A;
        border-radius: 4px;
        font-size: 0.9rem;
        font-weight: 700;
        color: #1B6B3A;
        background: white;
        min-width: 100px;
        text-align: right;
        font-family: 'Poppins', sans-serif;
    }

    /* ── Payment method pills ── */
    .method-pill {
        border: 2px solid #1B6B3A;
        color: #1B6B3A;
        background: white;
        font-weight: 600;
        font-size: 0.82rem;
        padding: 7px 18px;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.15s;
        font-family: 'Poppins', sans-serif;
    }
    .method-pill:hover,
    .method-pill.active { background: #1B6B3A; color: white; }
    .method-pill--sm    { padding: 4px 14px; font-size: 0.78rem; }

    /* ── Method detail blocks (hidden until pill selected) ── */
    .method-block       { display: none; }
    .method-detail-box  { background: #F0F7F0; border: 1px solid #C3DEC9; border-radius: 8px; padding: 14px 16px; margin-bottom: 8px; }
    .method-detail-title { font-size: 0.88rem; font-weight: 700; color: #1B6B3A; margin-bottom: 12px; }
    .bank-row           { background: white; border: 1px solid #D0DDD0; border-radius: 6px; padding: 10px 12px; margin-bottom: 8px; }
    .method-warning     { background: #FFF3CD; border: 1px solid #ffd700; border-radius: 6px; padding: 8px 12px; font-size: 0.8rem; color: #664d03; }
    .method-info        { background: #D1ECF1; border: 1px solid #bee5eb; border-radius: 6px; padding: 8px 12px; font-size: 0.8rem; color: #0c5460; }

    /* ── Status / confirmation boxes ── */
    .status-confirmed-box {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        background: #D4EDDA;
        border: 1px solid #C3E6CB;
        border-radius: 8px;
        padding: 12px 14px;
        margin-bottom: 12px;
    }
    .box-rejected {
        background: #F8D7DA;
        border: 1px solid #f5c2c7;
        border-radius: 6px;
        padding: 10px 12px;
        font-size: 0.83rem;
        color: #721C24;
    }
    .rejected-link { color: #721C24; font-weight: 700; }

    /* ── Locked state rows ── */
    .locked-row { font-size: 0.85rem; color: #444; margin-bottom: 8px; }

    /* ── Payment prompt text ── */
    .payment-prompt { font-size: 0.85rem; color: #555; margin-bottom: 14px; }

    /* ── "Changed your mind" reselect link ── */
    .reselect-link { font-size: 0.78rem; color: #888; text-decoration: underline; }

    /* ── Note at bottom of card ── */
    .note-text { font-size: 0.78rem; color: #888; margin: 0; }
    .note-link  { color: #1A9FE0; font-weight: 600; }

    /* ── Action buttons ── */
    .btn-submit {
        display: inline-block;
        background: #1A9FE0;
        color: white;
        font-weight: 700;
        font-size: 0.85rem;
        padding: 10px 28px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        letter-spacing: 0.3px;
        transition: background 0.2s;
        font-family: 'Poppins', sans-serif;
    }
    .btn-submit:hover { background: #0D7FBF; color: white; }

    .btn-cancel {
        background: #1A9FE0;
        color: white;
        font-weight: 700;
        font-size: 0.85rem;
        padding: 10px 28px;
        border-radius: 6px;
        text-decoration: none;
        transition: opacity 0.2s;
        font-family: 'Poppins', sans-serif;
    }
    .btn-cancel:hover { opacity: 0.85; color: white; }

    .btn-danger {
        background: #DC3545;
        color: white;
        font-weight: 700;
        font-size: 0.85rem;
        padding: 10px 24px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-family: 'Poppins', sans-serif;
        transition: background 0.2s;
    }
    .btn-danger:hover { background: #b02a37; }

    .submit-row {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
        padding: 14px 0 20px;
        max-width: 900px;
    }

    /* ── Appointment Modal ── */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 99999;
    }
    .modal-content {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid #D0DDD0;
        background: #1B6B3A;
        color: white;
        border-radius: 12px 12px 0 0;
    }
    .modal-header h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 700;
    }
    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: white;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .modal-body {
        padding: 24px;
    }
    .appointment-summary {
        background: #F0F7F0;
        border: 1px solid #C3DEC9;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 20px;
    }
    .appointment-summary p {
        margin: 8px 0;
        font-size: 0.9rem;
        color: #1A1A1A;
    }
    .time-slot-option {
        border: 2px solid #D0DDD0;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 8px;
        cursor: pointer;
        transition: all 0.2s;
        background: white;
    }
    .time-slot-option:hover {
        border-color: #1B6B3A;
        background: #F0F7F0;
    }
    .time-slot-option.selected {
        border-color: #1B6B3A;
        background: #1B6B3A;
        color: white;
    }
    .time-slot-label {
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 4px;
    }
    .time-slot-capacity {
        font-size: 0.8rem;
        color: #666;
    }
    .time-slot-option.selected .time-slot-capacity {
        color: #E8F5E8;
    }
    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
    }

</style>
@endpush


{{-- ════════════════════════════════════════════════════════════════════
     PAGE SCRIPTS
     Bell system is entirely in the layout — nothing bell-related here.
════════════════════════════════════════════════════════════════════ --}}
@push('scripts')
<script>

/* ── Payment method selection ────────────────────────────────────────
   Called when a method pill is clicked.
   1. Marks the clicked pill active, clears others.
   2. Shows the matching detail block, hides all others.
   3. Sets the hidden input value so the form knows what was picked.
   4. Reveals the Confirm button with a readable label.
─────────────────────────────────────────────────────────────────── */
function selectMethod(method) {
    // Pill active state
    document.querySelectorAll('.method-pill').forEach(function (btn) {
        btn.classList.toggle('active', btn.dataset.method === method);
    });

    // Show only the matching detail block
    document.querySelectorAll('.method-block').forEach(function (block) {
        block.style.display = 'none';
    });
    var block = document.getElementById('block-' + method);
    if (block) block.style.display = 'block';

    // Update hidden input
    var input = document.getElementById('selected-method-input');
    if (input) input.value = method;

    // Show confirm button with readable label
    var btnWrap = document.getElementById('confirm-btn-wrap');
    if (btnWrap) btnWrap.style.display = 'block';

    var labels  = { gcash: 'GCash', bank_transfer: 'Bank Transfer', cash: 'Over-the-Counter Cash' };
    var labelEl = document.getElementById('selected-method-label');
    if (labelEl) labelEl.textContent = 'Selected: ' + (labels[method] || method);
}

/* ── Guard: block form submit if no method was chosen ───────────────
   Uses CcstAlert.warning() from the layout's shared alert system.
─────────────────────────────────────────────────────────────────── */
var confirmForm = document.getElementById('confirm-method-form');
if (confirmForm) {
    confirmForm.addEventListener('submit', function (e) {
        if (!document.getElementById('selected-method-input').value) {
            e.preventDefault();
            CcstAlert.incomplete('Please choose a payment method before confirming.');
        }
    });
}

/* ── Toggle reselect panel (State B — change mind link) ─────────────
   Shows/hides the re-selection pills under the confirmed state box.
─────────────────────────────────────────────────────────────────── */
function toggleReselect() {
    var wrap = document.getElementById('reselect-wrap');
    if (!wrap) return;
    wrap.style.display = wrap.style.display === 'none' ? 'block' : 'none';
}

/* ── Cancel request ──────────────────────────────────────────────────
   Uses CcstAlert.cancel() from the layout's shared alert system.
   Submits the hidden DELETE form only after user confirms.
─────────────────────────────────────────────────────────────────── */
var cancelBtn = document.getElementById('cancel-request-btn');
if (cancelBtn) {
    cancelBtn.addEventListener('click', function () {
        CcstAlert.cancel({
            refNumber: '{{ $docRequest->reference_number }}',
            onConfirm: function () {
                document.getElementById('cancel-request-form').submit();
            }
        });
    });
}

/* ── Appointment Modal Functions ────────────────────────────────────
   Handle appointment booking modal functionality.
─────────────────────────────────────────────────────────────────── */
@if($showAppointmentModal && $timeSlots)
function selectTimeSlot(slotId, label) {
    // Remove selected class from all options
    document.querySelectorAll('.time-slot-option').forEach(function(option) {
        option.classList.remove('selected');
    });

    // Add selected class to clicked option
    var selectedOption = document.querySelector('[data-slot-id="' + slotId + '"]');
    if (selectedOption) {
        selectedOption.classList.add('selected');
    }

    // Set hidden input value
    document.getElementById('selected-time-slot').value = slotId;

    // Enable book button
    document.getElementById('book-appointment-btn').disabled = false;
}

function closeAppointmentModal() {
    document.getElementById('appointment-modal').style.display = 'none';
}

// Form validation before submission
document.addEventListener('DOMContentLoaded', function() {
    var appointmentForm = document.getElementById('appointment-form');
    if (appointmentForm) {
        appointmentForm.addEventListener('submit', function(e) {
            var dateInput = document.getElementById('modal-appointment-date');
            var timeSlotInput = document.getElementById('selected-time-slot');

            if (!dateInput.value) {
                e.preventDefault();
                CcstAlert.warning('Please select a pickup date.');
                return;
            }

            if (!timeSlotInput.value) {
                e.preventDefault();
                CcstAlert.warning('Please select a time slot.');
                return;
            }
        });
    }

    // Initialize date picker for appointment modal
    var dateInput = document.getElementById('modal-appointment-date');
    if (dateInput) {
        // Initialize flatpickr for date selection
        flatpickr(dateInput, {
            minDate: 'today',
            dateFormat: 'Y-m-d',
            disable: [
                function(date) {
                    // Disable weekends (Saturday = 6, Sunday = 0)
                    return (date.getDay() === 0 || date.getDay() === 6);
                }
            ],
            onChange: function(selectedDates, dateStr, instance) {
                const container = document.getElementById('time-slots-container');
                container.innerHTML = '<div style="padding: 15px; text-align: center; color: #666;">Loading available time slots...</div>';
                
                // Clear selected slot
                document.getElementById('selected-time-slot').value = '';
                document.getElementById('book-appointment-btn').disabled = true;

                // Fetch available time slots for the selected date
                fetch('{{ route('student.appointments.available-slots') }}?date=' + dateStr)
                    .then(response => response.json())
                    .then(data => {
                        container.innerHTML = ''; // clear current

                        if (data.length === 0) {
                            container.innerHTML = '<div style="padding: 15px; text-align: center; color: #666; background: #f8f9fa; border-radius: 8px;">No time slots available for this date.</div>';
                            return;
                        }

                        data.forEach(slot => {
                            const isFull = slot.is_full;
                            const option = document.createElement('div');
                            option.className = 'time-slot-option';
                            option.dataset.slotId = slot.id;
                            
                            if (isFull) {
                                option.style.opacity = '0.5';
                                option.style.cursor = 'not-allowed';
                                option.style.background = '#f8f9fa';
                                option.style.borderStyle = 'dashed';
                            } else {
                                option.onclick = function() { selectTimeSlot(slot.id, slot.label); };
                            }

                            const capacityText = isFull ? 'Fully Booked' : 'Available: ' + slot.available + ' / ' + slot.max_capacity;
                            const capacityColor = isFull ? 'color: #DC3545;' : 'color: #198754;';

                            option.innerHTML = `
                                <div class="time-slot-label">${slot.label}</div>
                                <div class="time-slot-capacity" style="${capacityColor}">${capacityText}</div>
                            `;
                            container.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching time slots:', error);
                        container.innerHTML = '<div style="padding: 15px; text-align: center; color: #DC3545;">Failed to load time slots. Please try again.</div>';
                    });
            }
        });
    }

    // Show modal if flag is set
    @if(session('show_appointment_modal'))
        document.getElementById('appointment-modal').style.display = 'flex';
    @endif
});
@endif

</script>
@endpush