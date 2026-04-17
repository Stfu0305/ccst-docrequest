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
            <div class="announce-card-header">
                <i class="bi bi-megaphone-fill"></i> Announcement Board
            </div>
            <div class="announce-card-body">
                @if($announcement && $announcement->is_published)
                    <div class="announce-content">{!! nl2br(e($announcement->content)) !!}</div>
                @else
                    <div class="announce-content text-muted fst-italic">No announcement currently published.</div>
                @endif
            </div>
            <div class="announce-card-footer">
                <button type="button" class="btn-edit" onclick="openEditModal({{ $announcement?->id }}, `{{ addslashes($announcement?->content ?? '') }}`)">
                    <i class="bi bi-pencil"></i> Edit
                </button>
                <form method="POST" action="{{ route('registrar.announcements.publish', $announcement?->id) }}" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-publish">
                        <i class="bi {{ $announcement?->is_published ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                        {{ $announcement?->is_published ? 'Unpublish' : 'Publish' }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Transaction Days --}}
        <div class="announce-card">
            <div class="announce-card-header">
                <i class="bi bi-calendar-week-fill"></i> Transaction Days
            </div>
            <div class="announce-card-body">
                @if($transactionDay && $transactionDay->is_published)
                    <div class="announce-content">{!! nl2br(e($transactionDay->content)) !!}</div>
                @else
                    <div class="announce-content text-muted fst-italic">No transaction day changes at this time.</div>
                @endif
            </div>
            <div class="announce-card-footer">
                <button type="button" class="btn-edit" onclick="openEditModal({{ $transactionDay?->id }}, `{{ addslashes($transactionDay?->content ?? '') }}`)">
                    <i class="bi bi-pencil"></i> Edit
                </button>
                <form method="POST" action="{{ route('registrar.announcements.publish', $transactionDay?->id) }}" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-publish">
                        <i class="bi {{ $transactionDay?->is_published ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                        {{ $transactionDay?->is_published ? 'Unpublish' : 'Publish' }}
                    </button>
                </form>
            </div>
        </div>

    </div>

@endsection

{{-- ── RIGHT PANEL ── --}}
@section('right-panel')

    <div class="rp-date-card">
        <div class="rp-date-day">{{ now()->format('d') }}</div>
        <div class="rp-date-month">{{ now()->format('F Y') }}</div>
        <div class="rp-date-time" id="live-time">--:-- --</div>
    </div>

    <div class="ccst-card mb-0">
        <div class="ccst-card-header blue">
            <i class="bi bi-graph-up me-2"></i> Request Overview
        </div>
        <div class="ccst-card-body p-0">
            <div class="rp-stat-row">
                <span><i class="bi bi-folder2 me-2"></i> Total Requests</span>
                <strong>{{ $totalRequests }}</strong>
            </div>
            <div class="rp-stat-row">
                <span><i class="bi bi-gear me-2"></i> Processing</span>
                <strong>{{ $processing }}</strong>
            </div>
            <div class="rp-stat-row">
                <span><i class="bi bi-box-seam me-2"></i> Ready for Pickup</span>
                <strong>{{ $readyForPickup }}</strong>
            </div>
            <div class="rp-stat-row">
                <span><i class="bi bi-check2-all me-2"></i> Received Today</span>
                <strong>{{ $receivedToday }}</strong>
            </div>
            <div class="rp-stat-row" style="border-bottom:none;">
                <span><i class="bi bi-x-circle me-2"></i> Cancelled</span>
                <strong>{{ $cancelled }}</strong>
            </div>
        </div>
    </div>

    <div class="ccst-card mb-0">
        <div class="ccst-card-header yellow">
            <i class="bi bi-lightbulb me-2"></i> Quick Reminders
        </div>
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

{{-- ── EDIT MODAL (Placed outside content but before scripts) ── --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:#1B6B3A; color:white; border:none;">
                <h6 class="modal-title fw-700 mb-0" id="editModalLabel">
                    <i class="bi bi-pencil-square me-2"></i> Edit Announcement
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body" style="padding:20px;">
                    <div class="form-group">
                        <label class="form-label fw-600 mb-2">Announcement Content</label>
                        <textarea name="content" id="editContent" class="form-control" rows="6" 
                                  style="width:100%; border:1px solid #D0DDD0; border-radius:8px; padding:12px; font-family:'Poppins',sans-serif; font-size:0.85rem;"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e0e0e0; padding:15px 20px;">
                    <button type="button" class="btn-cancel-modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-save-modal">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Hero Section */
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
        transition: background 0.2s;
        margin-bottom: 15px;
    }

    .btn-view-requests:hover {
        background: #0D7FBF;
        color: white;
    }

    /* Announcement Cards */
    .announce-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .announce-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        display: flex;
        flex-direction: column;
    }

    .announce-card-header {
        background: #F5C518;
        color: #1A1A1A;
        font-size: 0.9rem;
        font-weight: 800;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .announce-card-body {
        padding: 20px;
        flex: 1;
        min-height: 100px;
    }

    .announce-content {
        font-size: 0.88rem;
        line-height: 1.65;
        color: #333;
    }

    .announce-card-footer {
        padding: 12px 20px;
        border-top: 1px solid #f0f0f0;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background: #fafafa;
    }

    .btn-edit {
        background: #F5C518;
        color: #1A1A1A;
        border: none;
        border-radius: 6px;
        padding: 6px 18px;
        font-size: 0.75rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-edit:hover {
        opacity: 0.85;
        transform: translateY(-1px);
    }

    .btn-publish {
        background: #1A9FE0;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 6px 18px;
        font-size: 0.75rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-publish:hover {
        background: #0D7FBF;
        transform: translateY(-1px);
    }

    /* Modal Styles - Ensures modal appears on top */
    .modal {
        z-index: 1060;
    }
    
    .modal-backdrop {
        z-index: 1050;
    }
    
    .modal-dialog-centered {
        display: flex;
        align-items: center;
        min-height: calc(100% - 3.5rem);
    }
    
    .modal-content {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        color: #555;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 8px;
    }

    .form-control {
        width: 100%;
        border: 1px solid #D0DDD0;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 0.85rem;
        font-family: 'Poppins', sans-serif;
        transition: border-color 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: #1B6B3A;
        box-shadow: 0 0 0 3px rgba(27,107,58,0.1);
    }

    .btn-cancel-modal {
        background: #f0f0f0;
        color: #666;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-cancel-modal:hover {
        background: #e0e0e0;
    }

    .btn-save-modal {
        background: #1B6B3A;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-save-modal:hover {
        background: #0C5A2E;
    }

    /* Right Panel */
    .rp-date-card {
        background: rgba(255,255,255,0.15);
        border-radius: 12px;
        padding: 16px;
        text-align: center;
        color: white;
        backdrop-filter: blur(8px);
        margin-bottom: 20px;
    }

    .rp-date-day {
        font-size: 3rem;
        font-weight: 700;
        line-height: 1;
        margin-top: 10px;
    }

    .rp-date-month {
        font-size: 1rem;
        opacity: 0.85;
        margin-top: 5px;
    }

    .rp-date-time {
        font-size: 1.25rem;
        font-weight: 600;
        margin-top: 8px;
        letter-spacing: 1px;
    }

    .rp-stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 16px;
        border-bottom: 1px solid rgba(255,255,255,0.15);
        font-size: 0.85rem;
        color: white;
    }

    .rp-stat-row:last-child {
        border-bottom: none;
    }

    .rp-guide-step {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 10px 16px;
        border-bottom: 1px solid rgba(255,255,255,0.15);
        font-size: 0.8rem;
        color: rgba(255,255,255,0.92);
    }

    .rp-guide-step:last-child {
        border-bottom: none;
    }

    .rp-step-num {
        width: 22px;
        height: 22px;
        min-width: 22px;
        border-radius: 50%;
        background: #F5C518;
        color: #1A1A1A;
        font-size: 0.7rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .announce-grid {
            grid-template-columns: 1fr;
        }
        
        .dash-heading {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Live clock
    function updateTime() {
        const now = new Date();
        let h = now.getHours();
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        const el = document.getElementById('live-time');
        if (el) el.textContent = `${h}:${m}:${s} ${ampm}`;
    }
    updateTime();
    setInterval(updateTime, 1000);

    // Edit announcement modal - opens centered on screen
    function openEditModal(id, content) {
        // Decode HTML entities in content
        const textarea = document.getElementById('editContent');
        if (textarea) {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = content;
            textarea.value = tempDiv.textContent || tempDiv.innerText || '';
        }
        
        // Set form action
        const form = document.getElementById('editForm');
        if (form) {
            form.action = '/registrar/announcements/' + id;
        }
        
        // Show modal (Bootstrap handles centering automatically with modal-dialog-centered)
        const modal = new bootstrap.Modal(document.getElementById('editModal'));
        modal.show();
    }
</script>
@endpush