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

{{-- Stats Cards --}}
<div class="stats-grid">
    <!-- Your stat cards here -->
</div>

{{-- Quick Actions + Announcement Row --}}
<div class="two-column-row">
    <!-- Your quick actions and announcement here -->
</div>

{{-- MOVED RIGHT PANEL CONTENT - Weekly Overview & Most Requested Documents --}}
<div class="dashboard-bottom-row">
    <div class="dashboard-card">
        <div class="dashboard-card-header blue">
            <i class="bi bi-graph-up me-2"></i> Weekly Overview
        </div>
        <div class="dashboard-card-body">
            <canvas id="weeklyChart" style="height: 250px; width: 100%;"></canvas>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="dashboard-card-header yellow">
            <i class="bi bi-bar-chart-steps me-2"></i> Most Requested Documents
        </div>
        <div class="dashboard-card-body">
            @foreach($topDocuments as $doc)
            <div class="stat-row-item">
                <span>{{ $doc->name }}</span>
                <span class="stat-count">{{ $doc->count }} requests</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@section('right-panel')
    {{-- Only the image remains in right panel --}}
    {{-- Image is loaded from layout --}}
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


    .dashboard-bottom-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-top: 28px;
    }

    .dashboard-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .dashboard-card-header {
        font-size: 0.9rem;
        font-weight: 700;
        padding: 14px 20px;
    }

    .dashboard-card-header.blue {
        background: #1A9FE0;
        color: white;
    }

    .dashboard-card-header.yellow {
        background: #F5C518;
        color: #1A1A1A;
    }

    .dashboard-card-body {
        padding: 20px;
    }

    .stat-row-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .stat-row-item:last-child {
        border-bottom: none;
    }

    .stat-count {
        font-weight: 700;
        color: #1B6B3A;
        background: #F0F7F0;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
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