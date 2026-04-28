@extends('layouts.registrar')

@section('title', 'Reports & Analytics')

@section('content')

<div class="reports-sticky-header">REPORTS & ANALYTICS</div>

{{-- Analytics Dashboard --}}
<div class="analytics-section">
    <div class="section-header">
        <i class="bi bi-graph-up me-2"></i> Analytics Dashboard
    </div>
    
    <div class="analytics-grid">
        {{-- Monthly Requests Chart --}}
        <div class="analytics-card">
            <div class="analytics-card-header">Monthly Document Requests</div>
            <div class="analytics-card-body">
                <canvas id="requestsChart" height="200"></canvas>
            </div>
        </div>
        
        {{-- Monthly Appointments Chart --}}
        <div class="analytics-card">
            <div class="analytics-card-header">Monthly Appointments</div>
            <div class="analytics-card-body">
                <canvas id="appointmentsChart" height="200"></canvas>
            </div>
        </div>
        
        {{-- Top Requested Documents --}}
        <div class="analytics-card">
            <div class="analytics-card-header">Most Requested Documents</div>
            <div class="analytics-card-body">
                <canvas id="topDocumentsChart" height="200"></canvas>
            </div>
        </div>
        
        {{-- Status Distribution --}}
        <div class="analytics-card">
            <div class="analytics-card-header">Request Status Distribution</div>
            <div class="analytics-card-body">
                <canvas id="statusChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Report Generation Section --}}
<div class="reports-section">
    <div class="section-header">
        <i class="bi bi-file-text me-2"></i> Generate Reports
    </div>
    
    <div class="reports-grid">
        {{-- Document Requests Report --}}
        <div class="report-card">
            <div class="report-card-header">Document Requests Report</div>
            <div class="report-card-body">
                <form method="POST" action="{{ route('registrar.reports.export') }}" target="_blank">
                    @csrf
                    <input type="hidden" name="report_type" value="requests">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date From</label>
                            <input type="date" name="date_from" class="form-input" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label>Date To</label>
                            <input type="date" name="date_to" class="form-input" value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="include_summary" value="1" checked>
                            <span>Include Summary (Totals by Status)</span>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn-generate">
                        <i class="bi bi-file-pdf"></i> Generate PDF
                    </button>
                </form>
            </div>
        </div>
        
        {{-- Payments Report --}}
        <div class="report-card">
            <div class="report-card-header">Payments Report</div>
            <div class="report-card-body">
                <form method="POST" action="{{ route('registrar.reports.export') }}" target="_blank">
                    @csrf
                    <input type="hidden" name="report_type" value="payments">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date From</label>
                            <input type="date" name="date_from" class="form-input" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label>Date To</label>
                            <input type="date" name="date_to" class="form-input" value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="include_summary" value="1" checked>
                            <span>Include Summary (Total Collected by Method)</span>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn-generate">
                        <i class="bi bi-file-pdf"></i> Generate PDF
                    </button>
                </form>
            </div>
        </div>
        
        {{-- Appointments Report --}}
        <div class="report-card">
            <div class="report-card-header">Appointments Report</div>
            <div class="report-card-body">
                <form method="POST" action="{{ route('registrar.reports.export') }}" target="_blank">
                    @csrf
                    <input type="hidden" name="report_type" value="appointments">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date From</label>
                            <input type="date" name="date_from" class="form-input" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label>Date To</label>
                            <input type="date" name="date_to" class="form-input" value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="include_summary" value="1" checked>
                            <span>Include Summary (Attendance Statistics)</span>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn-generate">
                        <i class="bi bi-file-pdf"></i> Generate PDF
                    </button>
                </form>
            </div>
        </div>
        
        {{-- Students Report --}}
        <div class="report-card">
            <div class="report-card-header">Students Report</div>
            <div class="report-card-body">
                <form method="POST" action="{{ route('registrar.reports.export') }}" target="_blank">
                    @csrf
                    <input type="hidden" name="report_type" value="students">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date From</label>
                            <input type="date" name="date_from" class="form-input" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label>Date To</label>
                            <input type="date" name="date_to" class="form-input" value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="include_summary" value="1" checked>
                            <span>Include Summary (Student Count by Strand/Grade)</span>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn-generate">
                        <i class="bi bi-file-pdf"></i> Generate PDF
                    </button>
                </form>
            </div>
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
                <span><i class="bi bi-files me-2"></i> Total Requests</span>
                <strong>{{ \App\Models\DocumentRequest::count() }}</strong>
            </div>
            <div class="rp-stat-row">
                <span><i class="bi bi-check-circle me-2"></i> Completed</span>
                <strong>{{ \App\Models\DocumentRequest::where('status', 'completed')->count() }}</strong>
            </div>
            <div class="rp-stat-row">
                <span><i class="bi bi-people me-2"></i> Total Students</span>
                <strong>{{ \App\Models\User::where('role', 'student')->count() }}</strong>
            </div>
            <div class="rp-stat-row" style="border-bottom:none;">
                <span><i class="bi bi-calendar-check me-2"></i> Total Appointments</span>
                <strong>{{ \App\Models\Appointment::count() }}</strong>
            </div>
        </div>
    </div>

    <div class="ccst-card mb-0">
        <div class="ccst-card-header yellow">Report Tips</div>
        <div class="ccst-card-body p-0">
            <div class="rp-guide-step">
                <span class="rp-step-num">1</span>
                <span>Select date range for accurate data</span>
            </div>
            <div class="rp-guide-step">
                <span class="rp-step-num">2</span>
                <span>Check "Include Summary" for totals</span>
            </div>
            <div class="rp-guide-step" style="border-bottom:none;">
                <span class="rp-step-num">3</span>
                <span>PDF opens in new tab for printing</span>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .reports-sticky-header {
        background: #1B6B3A;
        color: white;
        font-size: 0.9rem;
        font-weight: 700;
        text-align: center;
        padding: 10px 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 20px;
    }

    .section-header {
        background: #F5C518;
        color: #1A1A1A;
        font-size: 1rem;
        font-weight: 700;
        padding: 12px 20px;
        border-radius: 12px 12px 0 0;
    }

    .analytics-section, .reports-section {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 24px;
    }

    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0;
    }

    .analytics-card {
        padding: 20px;
        border-right: 1px solid #f0f0f0;
        border-bottom: 1px solid #f0f0f0;
    }

    .analytics-card:nth-child(2n) {
        border-right: none;
    }

    .analytics-card:nth-last-child(-n+2) {
        border-bottom: none;
    }

    .analytics-card-header {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1B6B3A;
        margin-bottom: 15px;
        text-transform: uppercase;
    }

    .reports-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        padding: 20px;
    }

    .report-card {
        background: #f8f9fa;
        border-radius: 12px;
        overflow: hidden;
    }

    .report-card-header {
        background: #1B6B3A;
        color: white;
        font-size: 0.85rem;
        font-weight: 700;
        padding: 12px 16px;
    }

    .report-card-body {
        padding: 16px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 12px;
    }

    .form-group {
        margin-bottom: 12px;
    }

    .form-group label {
        display: block;
        font-size: 0.7rem;
        font-weight: 700;
        color: #555;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .form-input {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid #D0DDD0;
        border-radius: 6px;
        font-size: 0.8rem;
        font-family: 'Poppins', sans-serif;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .checkbox-label span {
        font-size: 0.75rem;
        font-weight: normal;
        text-transform: none;
    }

    .btn-generate {
        width: 100%;
        background: #1A9FE0;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: background 0.2s;
    }

    .btn-generate:hover {
        background: #0D7FBF;
    }

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

    @media (max-width: 1000px) {
        .analytics-grid, .reports-grid {
            grid-template-columns: 1fr;
        }
        .analytics-card {
            border-right: none;
        }
        .analytics-card:nth-last-child(-n+2) {
            border-bottom: 1px solid #f0f0f0;
        }
        .analytics-card:last-child {
            border-bottom: none;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly Requests Chart
    const requestsCtx = document.getElementById('requestsChart').getContext('2d');
    new Chart(requestsCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyRequests['labels']) !!},
            datasets: [{
                label: 'Document Requests',
                data: {!! json_encode($monthlyRequests['data']) !!},
                borderColor: '#1A9FE0',
                backgroundColor: 'rgba(26, 159, 224, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' }
            }
        }
    });

    // Monthly Appointments Chart
    const appointmentsCtx = document.getElementById('appointmentsChart').getContext('2d');
    new Chart(appointmentsCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyAppointments['labels']) !!},
            datasets: [{
                label: 'Appointments',
                data: {!! json_encode($monthlyAppointments['data']) !!},
                borderColor: '#1B6B3A',
                backgroundColor: 'rgba(27, 107, 58, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' }
            }
        }
    });

    // Top Documents Chart (Bar)
    const topDocsCtx = document.getElementById('topDocumentsChart').getContext('2d');
    new Chart(topDocsCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($topDocuments->pluck('name')) !!},
            datasets: [{
                label: 'Number of Requests',
                data: {!! json_encode($topDocuments->pluck('count')) !!},
                backgroundColor: '#F5C518',
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

    // Status Distribution Pie Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: ['Pending', 'Ready for Pickup', 'Completed', 'Cancelled'],
            datasets: [{
                data: [
                    {{ $statusDistribution['pending'] }},
                    {{ $statusDistribution['ready_for_pickup'] }},
                    {{ $statusDistribution['completed'] }},
                    {{ $statusDistribution['cancelled'] }}
                ],
                backgroundColor: ['#FFF3CD', '#E8F4FD', '#D4EDDA', '#F0F0F0'],
                borderColor: ['#856404', '#0969A2', '#155724', '#888'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom' }
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
</script>
@endpush