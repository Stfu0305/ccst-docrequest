@extends('layouts.student')

@section('title', 'Upload Payment Proof — ' . $docRequest->reference_number)

@section('content')

@php
    $flashText = session('success') ?? session('error') ?? null;
    $flashType = $flashText && session()->has('error') ? 'error' : 'success';
@endphp
@if($flashText)
<div id="flash-data"
     data-text="{{ e($flashText) }}"
     data-type="{{ $flashType }}"
     style="display:none;" aria-hidden="true"></div>
@endif

@php
    $isReupload      = $docRequest->status === 'payment_rejected';
    $rejectionReason = $docRequest->paymentProof?->rejection_reason;
    $uploadRoute     = $isReupload
        ? route('student.payments.reupload', $docRequest->id)
        : route('student.payments.store', $docRequest->id);
    $methodLabel     = $docRequest->payment_method === 'gcash' ? 'GCash' : 'Bank Transfer';
@endphp

{{-- ══════════════════════════════════════════════════════════════════
     STICKY HEADER
══════════════════════════════════════════════════════════════════ --}}
<div class="req-sticky-header">
    {{ $isReupload ? 'RE-UPLOAD PAYMENT PROOF' : 'UPLOAD PAYMENT PROOF' }}
</div>

{{-- ══════════════════════════════════════════════════════════════════
     SCROLLABLE CONTAINER
══════════════════════════════════════════════════════════════════ --}}
<div class="req-scroll">

    {{-- ── MAIN CARD ── --}}
    <div class="req-card">
        <div class="req-card-body">

            {{-- ─────────────────────────────────────────────────────
                 REQUEST INFO ROW
            ───────────────────────────────────────────────────── --}}
            <div class="section-heading-row">
                <span class="section-heading">Payment Proof Upload</span>
                <div style="font-size:0.78rem; color:#666; line-height:1.6; text-align:right;">
                    <strong>Request No.</strong> {{ $docRequest->reference_number }}<br>
                    <strong>Method:</strong>
                    @if($docRequest->payment_method === 'gcash')
                        <i class="bi bi-phone-fill me-1" style="color:#00A2E8;"></i>GCash
                    @else
                        <i class="bi bi-bank2 me-1"></i>Bank Transfer
                    @endif
                </div>
            </div>

            {{-- Amount reminder --}}
            <div class="method-detail-box mb-3" style="padding:10px 14px;">
                <div class="form-row-2" style="margin-bottom:0;">
                    <div class="form-field">
                        <label>Total Amount Due</label>
                        <div class="field-readonly fw-bold"
                             style="color:#1B6B3A; font-size:1.05rem;">
                            ₱{{ number_format($docRequest->total_fee, 2) }}
                        </div>
                    </div>
                    <div class="form-field">
                        <label>Date Submitted</label>
                        <div class="field-readonly">
                            {{ $docRequest->created_at->format('m/d/Y') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─────────────────────────────────────────────────────
                 REJECTION REASON BANNER (re-upload only)
            ───────────────────────────────────────────────────── --}}
            @if($isReupload && $rejectionReason)
            <div style="background:#F8D7DA; border:1px solid #f5c2c7; border-radius:8px;
                        padding:12px 14px; margin-bottom:14px;">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-x-circle-fill"
                       style="color:#DC3545; font-size:1rem; flex-shrink:0; margin-top:2px;"></i>
                    <div>
                        <div class="fw-bold" style="color:#721C24; font-size:0.88rem;">
                            Your previous proof was rejected
                        </div>
                        <div style="font-size:0.82rem; color:#721C24; margin-top:3px; line-height:1.5;">
                            <strong>Reason:</strong> {{ $rejectionReason }}
                        </div>
                        <div style="font-size:0.78rem; color:#9C2121; margin-top:5px;">
                            <i class="bi bi-lightbulb-fill me-1"></i>
                            Upload a clearer screenshot showing the correct amount, recipient, and reference number.
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- ─────────────────────────────────────────────────────
                 SERVER-SIDE VALIDATION ERRORS
            ───────────────────────────────────────────────────── --}}
            @if($errors->any())
            <div style="background:#FFF0F0; border:1px solid #F5C6CB; border-left:4px solid #DC3545;
                        border-radius:6px; padding:12px 14px; margin-bottom:14px;">
                <div class="fw-bold" style="color:#721C24; font-size:0.85rem; margin-bottom:5px;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Please fix the following:
                </div>
                <ul class="mb-0 ps-3" style="font-size:0.82rem; color:#721C24;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="section-divider"></div>

            {{-- ─────────────────────────────────────────────────────
                 UPLOAD FORM
            ───────────────────────────────────────────────────── --}}
            <form action="{{ $uploadRoute }}"
                  method="POST"
                  enctype="multipart/form-data"
                  id="upload-form">
                @csrf

                {{-- ── FILE DROP ZONE ── --}}
                <div class="form-field mb-3">
                    <label>
                        Screenshot / Receipt File
                        <span style="color:#DC3545;">*</span>
                    </label>

                    <div id="drop-zone">

                        {{-- Empty state --}}
                        <div id="dz-empty">
                            <div style="width:48px; height:48px; background:#E8F5E9;
                                        border-radius:50%; display:flex; align-items:center;
                                        justify-content:center; margin:0 auto 10px;">
                                <i class="bi bi-cloud-upload"
                                   style="font-size:1.3rem; color:#1B6B3A;"></i>
                            </div>
                            <div class="fw-semibold mb-1"
                                 style="font-size:0.88rem; color:#1A1A1A;">
                                Click to browse or drag your file here
                            </div>
                            <div style="font-size:0.76rem; color:#666;">
                                Accepted: JPG, PNG, PDF &nbsp;·&nbsp; Maximum: 5 MB
                            </div>
                        </div>

                        {{-- Preview state --}}
                        <div id="dz-preview" style="display:none;">
                            <div id="prev-img-wrap" style="display:none; margin-bottom:10px;">
                                <img id="prev-img" src="" alt="Preview"
                                     style="max-height:150px; max-width:100%;
                                            border-radius:6px;
                                            box-shadow:0 2px 8px rgba(0,0,0,0.10);">
                            </div>
                            <div id="prev-icon-wrap" style="display:none; margin-bottom:10px;">
                                <div style="width:48px; height:48px; background:#FFF3E0;
                                            border-radius:8px; display:flex; align-items:center;
                                            justify-content:center; margin:0 auto;">
                                    <i class="bi bi-file-earmark-pdf-fill"
                                       style="font-size:1.4rem; color:#E65100;"></i>
                                </div>
                            </div>
                            <div id="prev-name" class="fw-semibold"
                                 style="font-size:0.85rem; color:#1B6B3A;"></div>
                            <div id="prev-size"
                                 style="font-size:0.76rem; color:#666; margin-top:2px;"></div>
                            <button type="button" id="remove-file-btn"
                                    class="btn btn-sm mt-2"
                                    style="background:#FFF0F0; color:#DC3545;
                                           border:1px solid #F5C6CB; border-radius:20px;
                                           font-size:0.74rem; font-weight:600;
                                           padding:3px 12px;">
                                <i class="bi bi-x-circle me-1"></i>Remove
                            </button>
                        </div>

                        {{-- Hidden file input covering the entire drop zone --}}
                        <input type="file"
                               name="proof_file"
                               id="proof_file"
                               accept=".jpg,.jpeg,.png,.pdf"
                               style="position:absolute; inset:0; opacity:0;
                                      cursor:pointer; width:100%; height:100%;">
                    </div>

                    <div id="file-error"
                         style="font-size:0.78rem; color:#DC3545; margin-top:5px; display:none;"></div>
                    @error('proof_file')
                        <div style="font-size:0.78rem; color:#DC3545; margin-top:5px;">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- ── OPTIONAL FIELDS ── --}}
                <div class="form-row-2 mb-3">

                    <div class="form-field">
                        <label>
                            Reference Number
                            <span style="font-weight:400; color:#888; text-transform:none;
                                         font-size:0.7rem;">(optional)</span>
                        </label>
                        <input type="text"
                               name="reference_number"
                               id="reference_number"
                               class="field-input @error('reference_number') input-error @enderror"
                               placeholder="e.g. 1234567890"
                               value="{{ old('reference_number') }}">
                        <div style="font-size:0.72rem; color:#888; margin-top:3px;">
                            <i class="bi bi-info-circle me-1"></i>
                            Transaction number printed on your receipt.
                        </div>
                        @error('reference_number')
                            <div style="font-size:0.76rem; color:#DC3545; margin-top:3px;">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label>
                            Amount You Paid
                            <span style="font-weight:400; color:#888; text-transform:none;
                                         font-size:0.7rem;">(optional)</span>
                        </label>
                        <div style="display:flex; align-items:center;">
                            <span style="background:#F0F7F0; border:1px solid #D0DDD0;
                                         border-right:none; border-radius:4px 0 0 4px;
                                         padding:6px 10px; font-size:0.82rem;
                                         font-weight:700; color:#1B6B3A;">₱</span>
                            <input type="number"
                                   name="amount_declared"
                                   id="amount_declared"
                                   class="field-input @error('amount_declared') input-error @enderror"
                                   style="border-radius:0 4px 4px 0;"
                                   value="{{ old('amount_declared', number_format($docRequest->total_fee, 2, '.', '')) }}"
                                   step="0.01"
                                   min="0">
                        </div>
                        <div style="font-size:0.72rem; color:#888; margin-top:3px;">
                            <i class="bi bi-info-circle me-1"></i>
                            Pre-filled with your total. Change only if your screenshot differs.
                        </div>
                        @error('amount_declared')
                            <div style="font-size:0.76rem; color:#DC3545; margin-top:3px;">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>

                {{-- ── AMOUNT MISMATCH WARNING (JS-controlled) ── --}}
                <div id="mismatch-warning" class="method-warning"
                     style="display:none; margin-bottom:14px;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    <strong>Amount mismatch:</strong> What you entered differs from the total fee of
                    <strong>₱{{ number_format($docRequest->total_fee, 2) }}</strong>.
                    Make sure you sent the exact amount or the cashier may reject your proof.
                </div>

                <div class="section-divider"></div>

                {{-- ── SUBMIT / CANCEL ── --}}
                <div class="submit-row" style="padding:10px 0 0;">
                    <a href="{{ route('student.requests.show', $docRequest->id) }}"
                       class="btn-cancel">
                        Back to Summary
                    </a>
                    <button type="submit"
                            id="submit-btn"
                            class="btn-submit">
                        <span id="submit-text">
                            <i class="bi bi-cloud-upload-fill me-1"></i>
                            {{ $isReupload ? 'Re-Upload Proof' : 'Submit Proof' }}
                        </span>
                        <span id="submit-loading" style="display:none; align-items:center;">
                            <span class="spinner-border spinner-border-sm me-2"
                                  role="status"></span>Uploading...
                        </span>
                    </button>
                </div>

            </form>

        </div>{{-- end req-card-body --}}
    </div>{{-- end req-card --}}

    {{-- ── TIPS ── --}}
    <div style="max-width:900px; padding: 14px 0 20px;">
        <div style="font-size:0.78rem; color:#666; display:flex; gap:20px; flex-wrap:wrap;">
            <span>
                <i class="bi bi-check-circle-fill me-1" style="color:#1B6B3A;"></i>
                Make sure the full receipt is visible — not cropped.
            </span>
            <span>
                <i class="bi bi-check-circle-fill me-1" style="color:#1B6B3A;"></i>
                Amount, date, and recipient must be clearly readable.
            </span>
            <span>
                <i class="bi bi-check-circle-fill me-1" style="color:#1B6B3A;"></i>
                Send exactly ₱{{ number_format($docRequest->total_fee, 2) }} — not more, not less.
            </span>
        </div>
    </div>

</div>{{-- end req-scroll --}}

@endsection


@push('styles')
<style>
    .layout-alert-hide { display: none !important; }

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

    .req-scroll {
        height: calc(100vh - var(--header-h) - var(--footer-h) - 120px);
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-width: none;
    }
    .req-scroll::-webkit-scrollbar { display: none; }

    .req-card {
        background: #ffffff;
        border: 1px solid #D0DDD0;
        border-radius: 0 0 12px 12px;
        width: 100%;
        max-width: 900px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.10);
    }
    .req-card-body { padding: 20px 24px; }

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

    .form-row-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 10px;
    }
    .form-field { display: flex; flex-direction: column; }
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
    .field-input {
        padding: 6px 10px;
        border: 1px solid #D0DDD0;
        border-radius: 4px;
        background: white;
        font-size: 0.82rem;
        color: #1A1A1A;
        font-family: 'Poppins', sans-serif;
        min-height: 32px;
        width: 100%;
        outline: none;
    }
    .field-input:focus { border-color: #1B6B3A; box-shadow: 0 0 0 2px rgba(27,107,58,0.12); }
    .field-input.input-error { border-color: #DC3545; }

    #drop-zone {
        border: 2px dashed #D0DDD0;
        border-radius: 8px;
        padding: 32px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.15s ease;
        background: #FAFAFA;
        position: relative;
    }
    #drop-zone:hover, #drop-zone.dz-dragover { border-color: #1B6B3A; background: #F0F7F0; }
    #drop-zone.dz-error  { border-color: #DC3545; background: #FFF5F5; }
    #drop-zone.dz-success { border-color: #1B6B3A; background: #F0F7F0; }

    .method-detail-box {
        background: #F0F7F0;
        border: 1px solid #C3DEC9;
        border-radius: 8px;
        padding: 14px 16px;
        margin-bottom: 8px;
    }
    .method-warning {
        background: #FFF3CD;
        border: 1px solid #ffd700;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 0.8rem;
        color: #664d03;
    }

    .btn-submit {
        display: inline-flex;
        align-items: center;
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
    .btn-submit:disabled { opacity: 0.7; cursor: not-allowed; }
    .btn-cancel {
        background: #f0f0f0;
        color: #1A1A1A;
        font-weight: 700;
        font-size: 0.85rem;
        padding: 10px 28px;
        border-radius: 6px;
        text-decoration: none;
        transition: opacity 0.2s;
        font-family: 'Poppins', sans-serif;
    }
    .btn-cancel:hover { opacity: 0.8; color: #1A1A1A; }
    .submit-row {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
        padding: 14px 0 0;
        max-width: 900px;
    }

    #bell-btn-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    #bell-badge {
        position: absolute; top: -5px; right: -5px;
        background: #DC3545; color: white;
        font-size: 0.58rem; font-weight: 700;
        min-width: 16px; height: 16px; border-radius: 8px;
        padding: 0 3px; display: none;
        align-items: center; justify-content: center;
        pointer-events: none; line-height: 1;
    }
    #bell-dropdown {
        position: absolute; top: calc(100% + 10px); right: 0;
        width: 310px; background: white;
        border: 1px solid #D0DDD0; border-radius: 10px;
        box-shadow: 0 8px 28px rgba(0,0,0,0.14);
        z-index: 9999; overflow: hidden;
        opacity: 0; visibility: hidden; transform: translateY(-6px);
        transition: opacity 0.18s ease, transform 0.18s ease, visibility 0s linear 0.18s;
    }
    #bell-dropdown.open {
        opacity: 1; visibility: visible; transform: translateY(0);
        transition: opacity 0.18s ease, transform 0.18s ease, visibility 0s linear 0s;
    }
    #bell-dropdown-header {
        background: #1B6B3A; color: white;
        font-size: 0.78rem; font-weight: 700;
        padding: 9px 14px; text-transform: uppercase;
        letter-spacing: 0.4px; display: flex; align-items: center; gap: 6px;
    }
    #bell-dropdown-body { max-height: 260px; overflow-y: auto; }
    .bell-notif-empty { padding: 18px 14px; font-size: 0.82rem; color: #999; text-align: center; }
    .bell-notif-item {
        display: flex; align-items: flex-start; gap: 9px;
        padding: 10px 14px; border-bottom: 1px solid #f0f0f0;
        font-size: 0.8rem; color: #333; line-height: 1.4;
    }
    .bell-notif-item:last-child { border-bottom: none; }
    .bell-notif-item.unread { background: #f5fdf7; }
    .bell-notif-icon { flex-shrink: 0; margin-top: 2px; font-size: 0.9rem; }
    .bell-notif-icon.success { color: #2E8B57; }
    .bell-notif-icon.error   { color: #DC3545; }
    .bell-notif-time { font-size: 0.7rem; color: #bbb; margin-top: 3px; }
</style>
@endpush


@push('scripts')
<script>
(function suppressLayoutAlerts() {
    function hideAlerts() {
        document.querySelectorAll('.alert:not(#bell-dropdown *)').forEach(function (el) {
            el.style.display = 'none';
        });
    }
    hideAlerts();
    const observer = new MutationObserver(hideAlerts);
    observer.observe(document.documentElement, { childList: true, subtree: true });
})();

(function injectFlashToBell() {
    const el = document.getElementById('flash-data');
    if (!el) return;
    const text = el.dataset.text;
    const type = el.dataset.type || 'success';
    if (!text) return;
    const KEY = 'ccst_notifications';
    function getN() { try { return JSON.parse(sessionStorage.getItem(KEY)) || []; } catch { return []; } }
    function saveN(l) { sessionStorage.setItem(KEY, JSON.stringify(l)); }
    const list = getN();
    list.unshift({
        id: Date.now(), text, type,
        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        read: false
    });
    saveN(list);
    if (typeof refreshBadge === 'function') refreshBadge();
})();

const KEY = 'ccst_notifications';
function getNotifications() { try { return JSON.parse(sessionStorage.getItem(KEY)) || []; } catch { return []; } }
function saveNotifications(l) { sessionStorage.setItem(KEY, JSON.stringify(l)); }

function refreshBadge() {
    const badge = document.getElementById('bell-badge');
    if (!badge) return;
    const unread = getNotifications().filter(function (n) { return !n.read; }).length;
    badge.textContent = unread > 9 ? '9+' : unread;
    badge.style.display = unread > 0 ? 'inline-flex' : 'none';
}

function renderDropdown() {
    const body = document.getElementById('bell-dropdown-body');
    if (!body) return;
    const list = getNotifications();
    if (list.length === 0) {
        body.innerHTML = '<div class="bell-notif-empty"><i class="bi bi-bell-slash me-1"></i>No notifications yet.</div>';
        return;
    }
    body.innerHTML = list.map(function (n) {
        return '<div class="bell-notif-item' + (n.read ? '' : ' unread') + '">'
             + '<i class="bell-notif-icon bi '
             + (n.type === 'error' ? 'bi-exclamation-circle-fill error' : 'bi-check-circle-fill success')
             + '"></i><div><div>' + escapeHtml(n.text) + '</div>'
             + '<div class="bell-notif-time">' + escapeHtml(n.time) + '</div></div></div>';
    }).join('');
}

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function toggleBell(e) {
    if (e) e.stopPropagation();
    const dropdown = document.getElementById('bell-dropdown');
    if (!dropdown) return;
    if (dropdown.classList.contains('open')) {
        dropdown.classList.remove('open');
    } else {
        renderDropdown();
        dropdown.classList.add('open');
        saveNotifications(getNotifications().map(function (n) {
            return Object.assign({}, n, { read: true });
        }));
        refreshBadge();
    }
}

document.addEventListener('click', function (e) {
    const wrapper = document.getElementById('bell-btn-wrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        const dd = document.getElementById('bell-dropdown');
        if (dd) dd.classList.remove('open');
    }
});

(function injectBell() {
    const bellIcon = document.querySelector('.bi-bell, .bi-bell-fill');
    if (!bellIcon) return;
    const orig = bellIcon.closest('a, button') || bellIcon.parentElement;
    const wrapper = document.createElement('div');
    wrapper.id = 'bell-btn-wrapper';
    orig.parentNode.insertBefore(wrapper, orig);
    wrapper.appendChild(orig);
    orig.style.pointerEvents = 'none';
    if (orig.tagName === 'A') orig.setAttribute('href', 'javascript:void(0)');
    wrapper.addEventListener('click', toggleBell);
    const badge = document.createElement('span');
    badge.id = 'bell-badge';
    wrapper.appendChild(badge);
    const dd = document.createElement('div');
    dd.id = 'bell-dropdown';
    dd.innerHTML = '<div id="bell-dropdown-header"><i class="bi bi-bell-fill"></i> Notifications</div>'
                 + '<div id="bell-dropdown-body"></div>';
    wrapper.appendChild(dd);
    dd.addEventListener('click', function (e) { e.stopPropagation(); });
    refreshBadge();
})();

document.addEventListener('DOMContentLoaded', function () {

    const dropZone    = document.getElementById('drop-zone');
    const fileInput   = document.getElementById('proof_file');
    const dzEmpty     = document.getElementById('dz-empty');
    const dzPreview   = document.getElementById('dz-preview');
    const prevImgWrap = document.getElementById('prev-img-wrap');
    const prevIconWrap= document.getElementById('prev-icon-wrap');
    const prevImg     = document.getElementById('prev-img');
    const prevName    = document.getElementById('prev-name');
    const prevSize    = document.getElementById('prev-size');
    const removeBtn   = document.getElementById('remove-file-btn');
    const fileError   = document.getElementById('file-error');
    const form        = document.getElementById('upload-form');
    const submitBtn   = document.getElementById('submit-btn');
    const submitText  = document.getElementById('submit-text');
    const submitLoad  = document.getElementById('submit-loading');
    const amountInput = document.getElementById('amount_declared');
    const mismatchWarn= document.getElementById('mismatch-warning');
    const totalFee    = {{ $docRequest->total_fee }};

    fileInput.addEventListener('change', function () { handleFile(this.files[0]); });

    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropZone.classList.add('dz-dragover');
    });
    dropZone.addEventListener('dragleave', function () {
        dropZone.classList.remove('dz-dragover');
    });
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.classList.remove('dz-dragover');
        const file = e.dataTransfer.files[0];
        if (file) {
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            handleFile(file);
        }
    });

    removeBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        fileInput.value = '';
        resetDropZone();
    });

    if (amountInput) {
        amountInput.addEventListener('input', function () {
            const val = parseFloat(this.value);
            mismatchWarn.style.display =
                (!isNaN(val) && Math.abs(val - totalFee) > 1) ? 'block' : 'none';
        });
    }

    form.addEventListener('submit', function (e) {
        if (!fileInput.files || !fileInput.files[0]) {
            e.preventDefault();
            fileError.textContent   = 'Please select a file to upload.';
            fileError.style.display = 'block';
            dropZone.classList.add('dz-error');
            return;
        }
        submitText.style.display = 'none';
        submitLoad.style.display = 'inline-flex';
        submitBtn.disabled = true;
    });

    function handleFile(file) {
        if (!file) return;
        fileError.style.display = 'none';
        dropZone.classList.remove('dz-error');

        const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
        if (!allowed.includes(file.type)) {
            fileError.textContent   = 'Invalid file type. Please upload a JPG, PNG, or PDF.';
            fileError.style.display = 'block';
            dropZone.classList.add('dz-error');
            fileInput.value = '';
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            fileError.textContent   = 'File too large. Maximum allowed size is 5 MB.';
            fileError.style.display = 'block';
            dropZone.classList.add('dz-error');
            fileInput.value = '';
            return;
        }

        prevName.textContent = file.name;
        prevSize.textContent = formatBytes(file.size);

        if (file.type === 'application/pdf') {
            prevImgWrap.style.display  = 'none';
            prevIconWrap.style.display = 'block';
        } else {
            prevIconWrap.style.display = 'none';
            const reader = new FileReader();
            reader.onload = function (e) {
                prevImg.src               = e.target.result;
                prevImgWrap.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }

        dzEmpty.style.display   = 'none';
        dzPreview.style.display = 'block';
        dropZone.classList.add('dz-success');
    }

    function resetDropZone() {
        dzPreview.style.display    = 'none';
        prevImgWrap.style.display  = 'none';
        prevIconWrap.style.display = 'none';
        prevImg.src                = '';
        dzEmpty.style.display      = 'block';
        dropZone.classList.remove('dz-success', 'dz-error');
        fileError.style.display    = 'none';
    }

    function formatBytes(bytes) {
        if (bytes < 1024)    return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(2) + ' MB';
    }
});
</script>
@endpush
