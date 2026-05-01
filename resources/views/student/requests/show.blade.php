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

            <div class="section-heading" style="margin-bottom:12px;">Payment Instruction</div>

            <div class="payment-info-box" style="background: #F0F7F0; border: 1px solid #C3DEC9; border-radius: 8px; padding: 14px 16px; margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                <i class="bi bi-cash-stack" style="color: #1B6B3A; font-size: 1.2rem;"></i>
                <div>
                    <div style="font-weight: 700; color: #1B6B3A; font-size: 0.88rem;">Payment Method: Over-the-Counter Cash</div>
                    <div style="color: #1B6B3A; font-size: 0.82rem;">Please pay at the cashier office on your appointment day.</div>
                </div>
            </div>

            <div class="payment-instructions" style="background: #D1ECF1; border: 1px solid #bee5eb; border-radius: 8px; padding: 12px 14px; font-size: 0.8rem; color: #0c5460; margin-bottom: 16px;">
                <i class="bi bi-info-circle-fill me-2"></i>
                Bring your school ID and reference number (<strong>{{ $docRequest->reference_number }}</strong>) to the cashier office.
            </div>

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
        font-siz�────────────────────
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

</script>
@endpush