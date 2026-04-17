@extends('layouts.registrar')

@section('title', 'Reports')

@section('content')

<div class="registrar-sticky-header">REPORTS</div>

<div class="reports-container">
    {{-- Report Type Selection --}}
    <div class="report-card">
        <div class="report-card-header">
            <i class="bi bi-file-text me-2"></i> Select Report Type
        </div>
        <div class="report-card-body">
            <div class="report-type-buttons">
                <button class="report-type-btn active" data-report="requests">
                    <i class="bi bi-files"></i> Document Requests Report
                </button>
                <button class="report-type-btn" data-report="payments">
                    <i class="bi bi-credit-card"></i> Payments Report
                </button>
                <button class="report-type-btn" data-report="appointments">
                    <i class="bi bi-calendar-check"></i> Appointments Report
                </button>
                <button class="report-type-btn" data-report="students">
                    <i class="bi bi-people"></i> Students Report
                </button>
            </div>
        </div>
    </div>

    {{-- Document Requests Report Form --}}
    <div id="report-requests" class="report-form active">
        <div class="report-card">
            <div class="report-card-header">
                <i class="bi bi-files me-2"></i> Document Requests Report
            </div>
            <div class="report-card-body">
                <form method="POST" action="{{ route('registrar.reports.export') }}" target="_blank">
                    @csrf
                    <input type="hidden" name="report_type" value="requests">
                    
                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Date From</label>
                            <input type="date" name="date_from" id="requests_date_from" class="form-input" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label>Date To</label>
                            <input type="date" name="date_to" id="requests_date_to" class="form-input" value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Status Filter</label>
                        <select name="status" class="form-input">
                            <option value="all">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="payment_verified">Payment Verified</option>
                            <option value="processing">Processing</option>
                            <option value="ready_for_pickup">Ready for Pickup</option>
                            <option value="received">Received</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="include_summary" value="1" checked>
                            <span>Include Summary (Totals by Status)</span>
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-generate">
                            <i class="bi bi-file-pdf me-2"></i> Generate PDF Report
                        </button>
                        <button type="button" class="btn-preview" onclick="previewReport('requests')">
                            <i class="bi bi-eye me-2"></i> Preview
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Payments Report Form --}}
    <div id="report-payments" class="report-form">
        <div class="report-card">
            <div class="report-card-header">
                <i class="bi bi-credit-card me-2"></i> Payments Report
            </div>
            <div class="report-card-body">
                <form method="POST" action="{{ route('registrar.reports.export') }}" target="_blank">
                    @csrf
                    <input type="hidden" name="report_type" value="payments">
                    
                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Date From</label>
                            <input type="date" name="date_from" id="payments_date_from" class="form-input" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label>Date To</label>
                            <input type="date" name="date_to" id="payments_date_to" class="form-input" value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select name="payment_method" class="form-input">
                            <option value="all">All Methods</option>
                            <option value="gcash">GCash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">Cash</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Payment Status</label>
                        <select name="status" class="form-input">
                            <option value="all">All Statuses</option>
                            <option value="payment_verified">Verified</option>
                            <option value="payment_rejected">Rejected</option>
                            <option value="payment_uploaded">Pending Verification</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="include_summary" value="1" checked>
                            <span>Include Summary (Total Collected by Method)</span>
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-generate">
                            <i class="bi bi-file-pdf me-2"></i> Generate PDF Report
                        </button>
                        <button type="button" class="btn-preview" onclick="previewReport('payments')">
                            <i class="bi bi-eye me-2"></i> Preview
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Appointments Report Form --}}
    <div id="report-appointments" class="report-form">
        <div class="report-card">
            <div class="report-card-header">
                <i class="bi bi-calendar-check me-2"></i> Appointments Report
            </div>
            <div class="report-card-body">
                <form method="POST" action="{{ route('registrar.reports.export') }}" target="_blank">
                    @csrf
                    <input type="hidden" name="report_type" value="appointments">
                    
                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Date From</label>
                            <input type="date" name="date_from" id="appointments_date_from" class="form-input" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label>Date To</label>
                            <input type="date" name="date_to" id="appointments_date_to" class="form-input" value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Appointment Status</label>
                        <select name="status" class="form-input">
                            <option value="all">All Statuses</option>
                            <option value="scheduled">Scheduled</option>
                            <option value="completed">Completed</option>
                            <option value="missed">Missed</option>
                            <option value="canceled">Canceled</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="include_summary" value="1" checked>
                            <span>Include Summary (Attendance Statistics)</span>
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-generate">
                            <i class="bi bi-file-pdf me-2"></i> Generate PDF Report
                        </button>
                        <button type="button" class="btn-preview" onclick="previewReport('appointments')">
                            <i class="bi bi-eye me-2"></i> Preview
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Students Report Form --}}
    <div id="report-students" class="report-form">
        <div class="report-card">
            <div class="report-card-header">
                <i class="bi bi-people me-2"></i> Students Report
            </div>
            <div class="report-card-body">
                <form method="POST" action="{{ route('registrar.reports.export') }}" target="_blank">
                    @csrf
                    <input type="hidden" name="report_type" value="students">
                    
                    <div class="form-group">
                        <label>Grade Level</label>
                        <select name="grade_level" class="form-input">
                            <option value="all">All Grade Levels</option>
                            <option value="Grade 11">Grade 11</option>
                            <option value="Grade 12">Grade 12</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Strand</label>
                        <select name="strand" class="form-input">
                            <option value="all">All Strands</option>
                            <option value="ABM">ABM</option>
                            <option value="ICT">ICT</option>
                            <option value="HUMSS">HUMSS</option>
                            <option value="STEM">STEM</option>
                            <option value="GAS">GAS</option>
                            <option value="HE">HE</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="include_summary" value="1" checked>
                            <span>Include Summary (Student Count by Strand/Grade)</span>
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-generate">
                            <i class="bi bi-file-pdf me-2"></i> Generate PDF Report
                        </button>
                        <button type="button" class="btn-preview" onclick="previewReport('students')">
                            <i class="bi bi-eye me-2"></i> Preview
                        </button>
                    </div>
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
        <div class="ccst-card-header blue">Report Information</div>
        <div class="ccst-card-body p-0">
            <div class="rp-stat-row">
                <span><i class="bi bi-info-circle me-2"></i> PDF Format</span>
            </div>
            <div class="rp-stat-row">
                <span><i class="bi bi-download me-2"></i> Auto-download</span>
            </div>
            <div class="rp-stat-row" style="border-bottom:none;">
                <span><i class="bi bi-printer me-2"></i> Print Ready</span>
            </div>
        </div>
    </div>

    <div class="ccst-card mb-0">
        <div class="ccst-card-header yellow">Tips</div>
        <div class="ccst-card-body p-0">
            <div class="rp-guide-step">
                <span class="rp-step-num">1</span>
                <span>Select date range for accurate data</span>
            </div>
            <div class="rp-guide-step">
                <span class="rp-step-num">2</span>
                <span>Use filters to narrow down results</span>
            </div>
            <div class="rp-guide-step" style="border-bottom:none;">
                <span class="rp-step-num">3</span>
                <span>Generate PDF for record keeping</span>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .registrar-sticky-header {
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

    .reports-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .report-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 24px;
    }

    .report-card-header {
        background: #1B6B3A;
        color: white;
        font-size: 0.9rem;
        font-weight: 700;
        padding: 12px 20px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .report-card-body {
        padding: 20px;
    }

    .report-type-buttons {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .report-type-btn {
        background: #f0f0f0;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #666;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .report-type-btn i {
        font-size: 1.1rem;
    }

    .report-type-btn:hover {
        background: #e0e0e0;
    }

    .report-type-btn.active {
        background: #1B6B3A;
        color: white;
    }

    .report-form {
        display: none;
    }

    .report-form.active {
        display: block;
    }

    .form-row-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        font-size: 0.7rem;
        font-weight: 700;
        color: #555;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 6px;
    }

    .form-input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #D0DDD0;
        border-radius: 8px;
        font-size: 0.85rem;
        font-family: 'Poppins', sans-serif;
        background: white;
    }

    .form-input:focus {
        outline: none;
        border-color: #1B6B3A;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
    }

    .checkbox-label input {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .checkbox-label span {
        font-size: 0.8rem;
        font-weight: 500;
        color: #444;
        text-transform: none;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px solid #f0f0f0;
    }

    .btn-generate {
        background: #1B6B3A;
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-generate:hover {
        background: #0C5A2E;
    }

    .btn-preview {
        background: #1A9FE0;
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-preview:hover {
        background: #0D7FBF;
    }

    /* Right Panel Styles */
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
</style>
@endpush

@push('scripts')
<script>
    // Report type switching
    document.querySelectorAll('.report-type-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.report-type-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const reportType = this.dataset.report;
            document.querySelectorAll('.report-form').forEach(form => {
                form.classList.remove('active');
            });
            document.getElementById(`report-${reportType}`).classList.add('active');
        });
    });

    // Preview report (opens in new tab)
    function previewReport(type) {
        let form;
        let data;
        
        switch(type) {
            case 'requests':
                form = document.querySelector('#report-requests form');
                break;
            case 'payments':
                form = document.querySelector('#report-payments form');
                break;
            case 'appointments':
                form = document.querySelector('#report-appointments form');
                break;
            case 'students':
                form = document.querySelector('#report-students form');
                break;
        }
        
        if (form) {
            // Add preview flag to form
            const previewInput = document.createElement('input');
            previewInput.type = 'hidden';
            previewInput.name = 'preview';
            previewInput.value = '1';
            form.appendChild(previewInput);
            form.submit();
            previewInput.remove();
        }
    }

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
</script>
@endpush