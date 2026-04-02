@extends('layouts.registrar')

@section('title', 'Registrar Dashboard')

@section('content')

    {{-- ── Hero Section ── --}}
    <div class="dash-hero">
        <h1 class="dash-heading">
            PROCESS DOCUMENTS WITH EASE
        </h1>
        <p class="dash-subtext">
            View Document Requests submitted by the students.
        </p>
        <a href="{{ route('registrar.requests.index') }}" class="btn-view-requests">
            VIEW REQUESTS &rsaquo;
        </a>
    </div>

    {{-- ── Announcement + Transaction Days ── --}}
    <div class="announce-grid">

        {{-- Announcement Board --}}
        <div class="announce-card">
            <div class="announce-card-header">Announcement Board</div>
            <div class="announce-card-body">
                @if($announcement && $announcement->is_published)
                    <p class="announce-published-label">Currently Published:</p>
                    <div class="announce-content">{!! nl2br(e($announcement->content)) !!}</div>
                @else
                    <p class="announce-published-label">Currently Published:</p>
                    <div class="announce-content text-muted fst-italic">No announcement currently published.</div>
                @endif
            </div>
            <div class="announce-card-footer">
                <button class="btn-edit" onclick="openEditModal('announcement', {{ $announcement?->id }}, `{{ addslashes($announcement?->content ?? '') }}`)">
                    Edit
                </button>
                <form method="POST" action="{{ route('registrar.announcements.publish', $announcement?->id) }}" style="display:inline;">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-publish">
                        {{ $announcement?->is_published ? 'Unpublish' : 'Publish' }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Transaction Days --}}
        <div class="announce-card">
            <div class="announce-card-header">Transaction Days</div>
            <div class="announce-card-body">
                @if($transactionDay && $transactionDay->is_published)
                    <p class="announce-published-label">Currently Published:</p>
                    <div class="announce-content">{!! nl2br(e($transactionDay->content)) !!}</div>
                @else
                    <p class="announce-published-label">Currently Published:</p>
                    <div class="announce-content text-muted fst-italic">No transaction day changes at this time.</div>
                @endif
            </div>
            <div class="announce-card-footer">
                <button class="btn-edit" onclick="openEditModal('transaction_days', {{ $transactionDay?->id }}, `{{ addslashes($transactionDay?->content ?? '') }}`)">
                    Edit
                </button>
                <form method="POST" action="{{ route('registrar.announcements.publish', $transactionDay?->id) }}" style="display:inline;">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-publish">
                        {{ $transactionDay?->is_published ? 'Unpublish' : 'Publish' }}
                    </button>
                </form>
            </div>
        </div>

    </div>

    {{-- ── Edit Announcement Modal ── --}}
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background:var(--green-dark);color:white;">
                    <h6 class="modal-title fw-700 mb-0">Edit Announcement</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editForm" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-body">
                        <textarea name="content" id="editContent" class="form-control" rows="6"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

{{-- ── RIGHT PANEL ── --}}
@section('right-panel')

    {{-- Date card --}}
    <div class="rp-date-card">
        <div class="rp-date-day">{{ now()->format('d') }}</div>
        <div class="rp-date-month">{{ now()->format('F Y') }}</div>
        <div class="rp-date-time" id="live-time">--:-- --</div>
    </div>

    {{-- Request Stats --}}
    <div class="ccst-card mb-0">
        <div class="ccst-card-header blue">Request Overview</div>
        <div class="ccst-card-body p-0">
            <div class="rp-stat-row">
                <span><span class="rp-icon-circle"><i class="bi bi-folder2"></i></span> Total Requests</span>
                <strong>{{ $totalRequests }}</strong>
            </div>
            <div class="rp-stat-row">
                <span><span class="rp-icon-circle"><i class="bi bi-gear"></i></span> Processing</span>
                <strong>{{ $processing }}</strong>
            </div>
            <div class="rp-stat-row">
                <span><span class="rp-icon-circle"><i class="bi bi-box-seam"></i></span> Ready for Pickup</span>
                <strong>{{ $readyForPickup }}</strong>
            </div>
            <div class="rp-stat-row">
                <span><span class="rp-icon-circle"><i class="bi bi-check2-all"></i></span> Received Today</span>
                <strong>{{ $receivedToday }}</strong>
            </div>
            <div class="rp-stat-row" style="border-bottom:none;">
                <span><span class="rp-icon-circle"><i class="bi bi-x-circle"></i></span> Cancelled</span>
                <strong>{{ $cancelled }}</strong>
            </div>
        </div>
    </div>

    {{-- Quick Tips --}}
    <div class="ccst-card mb-0">
        <div class="ccst-card-header yellow">Quick Reminders</div>
        <div class="ccst-card-body p-0">
            <div class="rp-guide-step">
                <span class="rp-step-num">1</span>
                <span>Check payment-verified requests daily</span>
            </div>
            <div class="rp-guide-step">
                <span class="rp-step-num">2</span>
                <span>Update status to Processing promptly</span>
            </div>
            <div class="rp-guide-step">
                <span class="rp-step-num">3</span>
                <span>Generate claiming number when ready</span>
            </div>
            <div class="rp-guide-step" style="border-bottom:none;">
                <span class="rp-step-num">4</span>
                <span>Verify claiming number at pickup</span>
            </div>
        </div>
    </div>

@endsection

@push('styles')
<style>
    /* ── Hero ── */
    .dash-hero {
        margin-bottom: 28px;
    }

    .dash-heading {
        font-family: 'Volkhov', serif;
        font-weight: 700;
        font-size: 2.2rem;
        color: #1A1A1A;
        line-height: 1.2;
        text-transform: uppercase;
        margin-top: 35px;
        margin-bottom: 12px;
    }

    .dash-subtext {
        font-size: 0.95rem;
        color: #444;
        margin-bottom: 35px;
        
    }

    .btn-view-requests {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #1A9FE0;
        color: white;
        font-weight: 700;
        font-size: 0.9rem;
        padding: 15px 24px;
        border-radius: 6px;
        text-decoration: none;
        letter-spacing: 0.3px;
        transition: background 0.2s;
        margin-bottom: 15px;     /* space below button — increase to add gap before cards */
    }

    .btn-view-requests:hover { background: #0D7FBF; color: white; }

    /* ── Announcement cards ── */
    .announce-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .announce-card {
        background: white;
        border: 1px solid #D0DDD0;
        border-radius: 8px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .announce-card-header {
        background: #F5C518;
        color: #1A1A1A;
        font-size: 0.86rem;
        font-weight: 700;
        padding: 10px 16px;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .announce-card-body {
        padding: 14px 16px;
        flex: 1;
        font-size: 0.83rem;
        line-height: 1.6;
        color: #333;
    }

    .announce-published-label {
        font-weight: 700;
        color: #1A9FE0;
        margin-bottom: 8px;
        font-size: 0.82rem;
    }

    .announce-content {
        font-size: 0.82rem;
        line-height: 1.65;
        color: #444;
    }

    .announce-card-footer {
        padding: 10px 16px;
        border-top: 1px solid #D0DDD0;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    .btn-edit {
        background: #F5C518;
        color: #1A1A1A;
        border: none;
        border-radius: 6px;
        padding: 6px 18px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.15s;
    }

    .btn-edit:hover { opacity: 0.85; }

    .btn-publish {
        background: #1A9FE0;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 6px 18px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s;
    }

    .btn-publish:hover { background: #0D7FBF; }

    /* ── Right panel ── */
    .rp-date-card {
        background: rgba(255,255,255,0.18);
        border-radius: 10px;
        padding: 16px;
        text-align: center;
        color: white;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.3);
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

    // Edit modal
    function openEditModal(type, id, content) {
        document.getElementById('editContent').value = content;
        document.getElementById('editForm').action = `/registrar/announcements/${id}`;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }
</script>
@endpush