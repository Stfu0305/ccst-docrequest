@extends('layouts.registrar')

@section('title', 'Registrar Dashboard')

@section('content')

{{-- Hero Section --}}
<div class="dash-hero">
    <h1 class="dash-heading">
        PROCESS DOCUMENTS WITH EASE
    </h1>
    <p class="dash-subtext">
        Manage document requests and student verifications
    </p>
</div>

{{-- Stats Cards - Glass Morphism with Colors --}}
<div class="stats-grid">

    <a href="{{ route('registrar.calendar') }}" class="stat-card glass-card glass-yellow">
        <div class="stat-row">
            <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
            <div class="stat-value">{{ $todayAppointments }}</div>
        </div>
        <div class="stat-label">Today's Appointments</div>
        <div class="stat-arrow"><i class="bi bi-arrow-right"></i></div>
    </a>

    <a href="{{ route('registrar.students.pending') }}" class="stat-card glass-card glass-blue">
        <div class="stat-row">
            <div class="stat-icon"><i class="bi bi-people"></i></div>
            <div class="stat-value">{{ $pendingVerifications }}</div>
        </div>
        <div class="stat-label">Pending Verifications</div>
        <div class="stat-arrow"><i class="bi bi-arrow-right"></i></div>
    </a>

    <a href="{{ route('registrar.requests.index') }}" class="stat-card glass-card glass-green">
        <div class="stat-row">
            <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-value">{{ $pendingRequests }}</div>
        </div>
        <div class="stat-label">Pending Requests</div>
        <div class="stat-arrow"><i class="bi bi-arrow-right"></i></div>
    </a>

</div>

{{-- Two Column Row: Quick Actions (Left) + Announcement Board (Right) --}}
<div class="two-column-row">
    {{-- Quick Actions - Side by Side Buttons --}}
    <div class="quick-actions-buttons">
        <a href="{{ route('registrar.appointments.print-cashier-list') }}" class="print-btn" target="_blank">
            <div class="print-icon">
                <img src="{{ asset('images/print.png') }}" alt="Print">
            </div>
            <div class="print-label">
                <span>PRINT RECEIPTS</span>
            </div>
        </a>

        <a href="{{ route('registrar.walkin.index') }}" class="walkin-btn">
            <div class="walkin-icon">
                <img src="{{ asset('images/walk-in.png') }}" alt="Walk-in">
            </div>
            <div class="walkin-label">
                <span>WALK-IN REQUEST</span>
            </div>
        </a>
    </div>

    {{-- Announcement Card --}}
    <div class="announcements-section">
        <div class="announcements-header">
            <i class="bi bi-megaphone-fill me-2"></i> Announcement Board
        </div>
        <div class="announce-card">
            <div class="announce-card-body">
                @if($announcement && $announcement->is_published)
                    <div class="announce-content">{!! nl2br(e($announcement->content)) !!}</div>
                @else
                    <div class="announce-content text-muted fst-italic">No announcement currently published.</div>
                @endif
            </div>
            <div class="announce-card-footer">
                <button class="btn-edit" onclick="openEditModal({{ $announcement?->id }}, `{{ addslashes($announcement?->content ?? '') }}`)">
                    <i class="bi bi-pencil"></i> Edit
                </button>
                <form method="POST" action="{{ route('registrar.announcements.publish', $announcement?->id) }}" class="d-inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-publish">
                        <i class="bi {{ $announcement?->is_published ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                        {{ $announcement?->is_published ? 'Unpublish' : 'Publish' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Edit Announcement Modal --}}
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
                        <textarea name="content" id="editContent" class="form-control" rows="8" 
                                  style="width:100%; border:1px solid #D0DDD0; border-radius:8px; padding:12px; font-family:'Poppins',sans-serif; font-size:0.85rem; resize:vertical;"></textarea>
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

@endsection

@section('right-panel')
    <div class="rp-date-card">
        <div class="rp-date-day">{{ now()->format('d') }}</div>
        <div class="rp-date-month">{{ now()->format('F Y') }}</div>
        <div class="rp-date-time" id="live-time">--:-- --</div>
    </div>

    <div class="ccst-card mb-0">
        <div class="ccst-card-header blue">Weekly Overview</div>
        <div class="ccst-card-body p-0">
            <div id="weeklyChart" style="height: 200px;"></div>
        </div>
    </div>

    <div class="ccst-card mb-0">
        <div class="ccst-card-header yellow">Most Requested Documents</div>
        <div class="ccst-card-body p-0">
            @foreach($topDocuments as $doc)
            <div class="rp-stat-row">
                <span>{{ $doc->name }}</span>
                <strong>{{ $doc->count }} requests</strong>
            </div>
            @endforeach
        </div>
    </div>
@endsection

@push('styles')
<style>
    .dash-hero {
        margin-bottom: 28px;
    }

    .dash-heading {
        font-family: 'Volkhov', serif;
        font-weight: 700;
        font-size: 2rem;
        color: #1A1A1A;
        line-height: 1.2;
        text-transform: uppercase;
        margin-top: 20px;
        margin-bottom: 12px;
    }

    .dash-subtext {
        font-size: 0.9rem;
        color: #444;
        margin-bottom: 25px;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        margin-bottom: 32px;
    }

    /* Glass Card Base */
    .glass-card {
        display: flex;
        flex-direction: column;
        justify-content: center;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: 
            0 8px 32px rgba(0, 0, 0, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.5),
            inset 0 -1px 0 rgba(255, 255, 255, 0.1),
            inset 0 0 22px 11px rgba(255, 255, 255, 0.2);
        position: relative;
        overflow: hidden;
        text-decoration: none;
        transition: transform 0.2s, box-shadow 0.2s;
        padding: 18px 22px;
        min-height: 130px;
    }

    .glass-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }

    .glass-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(255, 255, 255, 0.9),
            transparent
        );
    }

    .glass-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 1px;
        height: 100%;
        background: linear-gradient(
            180deg,
            rgba(255, 255, 255, 0),
            transparent,
            rgba(255, 255, 255, 0.3)
        );
    }

    /* Colored Glass Cards */
    .glass-blue {
        background: rgba(26, 158, 224, 0.125);
    }
    .glass-blue .stat-icon,
    .glass-blue .stat-value,
    .glass-blue .stat-label,
    .glass-blue .stat-arrow {
        color: #0D7FBF;
    }

    .glass-green {
        background: rgba(27, 107, 58, 0.125);
    }
    .glass-green .stat-icon,
    .glass-green .stat-value,
    .glass-green .stat-label,
    .glass-green .stat-arrow {
        color: #1B6B3A;
    }

    .glass-yellow {
        background: rgba(245, 197, 24, 0.125);
    }
    .glass-yellow .stat-icon,
    .glass-yellow .stat-value,
    .glass-yellow .stat-label,
    .glass-yellow .stat-arrow {
        color: #E08A00;
    }

    /* Card Content Layout */
    .stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        margin-bottom: 8px;
    }

    .stat-icon {
        font-size: 2.2rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 4px;
    }

    .stat-arrow {
        position: absolute;
        bottom: 16px;
        right: 20px;
        font-size: 1rem;
        opacity: 0.5;
        transition: opacity 0.2s, transform 0.2s;
    }

    .glass-card:hover .stat-arrow {
        opacity: 1;
        transform: translateX(3px);
    }

    /* Two Column Row */
    .two-column-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 28px;
    }

    /* Quick Actions - Side by Side Buttons */
    .quick-actions-buttons {
        display: flex;
        flex-direction: row;
        gap: 20px;
        align-items: stretch;
        justify-content: flex-start;
    }

    /* PRINT BUTTON */
    .print-btn {
        position: relative;
        width: 203px;
        height: 206px;
        background: linear-gradient(179.89deg, #FFFFFF -21.69%, #CDECFF 51.86%, #029CFE 146.82%);
        border-radius: 30px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s ease;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .print-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(2, 156, 254, 0.3);
    }

    .print-icon {
        width: 70px;
        height: 70px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .print-icon img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .print-label {
        width: 140px;
        height: 28px;
        background: #01025F;
        border-radius: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .print-label span {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        font-size: 11px;
        line-height: 18px;
        text-align: center;
        color: #FFFFFF;
        letter-spacing: 0.5px;
    }

    /* WALK-IN BUTTON */
    .walkin-btn {
        position: relative;
        width: 203px;
        height: 206px;
        background: linear-gradient(179.89deg, #FFFFFF -21.69%, #FFE8C2 51.86%, #F5A623 146.82%);
        border-radius: 24px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s ease;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .walkin-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(245, 166, 35, 0.3);
    }

    .walkin-icon {
        width: 70px;
        height: 70px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .walkin-icon img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .walkin-label {
        width: 150px;
        height: 28px;
        background: #FFAA00;
        border-radius: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .walkin-label span {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        font-size: 11px;
        line-height: 18px;
        text-align: center;
        color: #000000;
        letter-spacing: 0.5px;
    }

    /* Announcement Card */
    .announcements-section {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .announcements-header {
        background: #F5C518;
        color: #1A1A1A;
        font-size: 0.9rem;
        font-weight: 700;
        padding: 14px 20px;
    }

    .announce-card {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .announce-card-body {
        flex: 1;
        min-height: 100px;
    }

    .announce-content {
        font-size: 0.88rem;
        line-height: 1.6;
        color: #444;
    }

    .announce-card-footer {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #f0f0f0;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .btn-edit {
        background: #F5C518;
        color: #1A1A1A;
        border: none;
        padding: 6px 18px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-publish {
        background: #1A9FE0;
        color: white;
        border: none;
        padding: 6px 18px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
    }

    /* Right Panel */
    .rp-date-card {
        background: rgba(255,255,255,0.18);
        border-radius: 12px;
        padding: 16px;
        text-align: center;
        color: white;
        backdrop-filter: blur(8px);
        margin-bottom: 18px;
    }

    .rp-date-day { font-size: 2.8rem; font-weight: 700; line-height: 1; }
    .rp-date-month { font-size: 0.85rem; opacity: 0.85; margin-top: 2px; }
    .rp-date-time { font-size: 1rem; font-weight: 600; margin-top: 6px; }

    .rp-stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 9px 14px;
        border-bottom: 1px solid rgba(255,255,255,0.2);
        font-size: 0.82rem;
        color: white;
    }

    /* Responsive */
    @media (max-width: 1000px) {
        .stats-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        .two-column-row {
            grid-template-columns: 1fr;
        }
        .quick-actions-buttons {
            justify-content: center;
        }
    }

    @media (max-width: 550px) {
        .quick-actions-buttons {
            flex-direction: column;
            align-items: center;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Weekly chart
    const ctx = document.getElementById('weeklyChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($weeklyLabels) !!},
            datasets: [{
                label: 'Appointments',
                data: {!! json_encode($weeklyData) !!},
                backgroundColor: '#1B6B3A',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false }
            }
        }
    });

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

    function openEditModal(id, content) {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = content;
        document.getElementById('editContent').value = tempDiv.textContent || tempDiv.innerText || '';
        document.getElementById('editForm').action = `/registrar/announcements/${id}`;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }
</script>
@endpush