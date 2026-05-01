@extends('layouts.registrar')

@section('title', 'Request Details - ' . $request->reference_number)

@section('content')

<div class="registrar-sticky-header">REQUEST DETAILS</div>

<div class="request-detail-scroll">
    <div class="request-detail-card">
        {{-- Request Information --}}
        <div class="detail-section">
            <h3><i class="bi bi-person-badge me-2"></i>Student Information</h3>
            <div class="detail-grid">
                <div><label>Reference Number:</label> <strong>{{ $request->reference_number }}</strong></div>
                <div><label>Student Name:</label> <strong>{{ $request->full_name }}</strong></div>
                <div><label>Student Number:</label> <strong>{{ $request->student_number }}</strong></div>
                <div><label>Contact Number:</label> <strong>{{ $request->contact_number }}</strong></div>
                <div><label>Course/Program:</label> <strong>{{ $request->course_program }}</strong></div>
                <div><label>Year & Section:</label> <strong>{{ $request->year_level }} - {{ $request->section }}</strong></div>
                <div><label>Status:</label> 
                    <strong>
                        @php
                            $statusConfig = [
                                'pending' => ['label' => 'Pending', 'class' => 'status-pending'],
                                'payment_method_set' => ['label' => 'Payment Method Set', 'class' => 'status-pending'],
                                'payment_uploaded' => ['label' => 'Payment Uploaded', 'class' => 'status-pending'],
                                'payment_rejected' => ['label' => 'Payment Rejected', 'class' => 'status-rejected'],
                                'payment_verified' => ['label' => 'Payment Verified', 'class' => 'status-verified'],
                                'processing' => ['label' => 'Processing', 'class' => 'status-processing'],
                                'ready_for_pickup' => ['label' => 'Ready for Pickup', 'class' => 'status-ready'],
                                'received' => ['label' => 'Received', 'class' => 'status-received'],
                                'cancelled' => ['label' => 'Cancelled', 'class' => 'status-cancelled'],
                            ];
                            $reqStatus = $statusConfig[$request->status] ?? ['label' => ucfirst($request->status), 'class' => ''];
                        @endphp
                        <span class="status-badge {{ $reqStatus['class'] }}">{{ $reqStatus['label'] }}</span>
                    </strong>
                </div>
                <div><label>Total Fee:</label> <strong style="color:#1B6B3A;">₱{{ number_format($request->total_fee, 2) }}</strong></div>
            </div>
        </div>

        {{-- Requested Documents --}}
        <div class="detail-section">
            <h3><i class="bi bi-file-earmark-text me-2"></i>Requested Documents</h3>
            <table class="docs-table">
                <thead>
                    <tr>
                        <th>Document</th>
                        <th class="text-center">Year/Sem</th>
                        <th class="text-center">Copies</th>
                        <th class="text-end">Fee</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($request->items as $item)
                    <tr>
                        <td><strong>{{ $item->documentType->name }}</strong></td>
                        <td class="text-center">{{ $item->assessment_year ?? 'N/A' }} / {{ $item->semester ?? 'N/A' }}</td>
                        <td class="text-center">{{ $item->copies }}</td>
                        <td class="text-end">₱{{ number_format($item->fee * $item->copies, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Action Buttons --}}
        <div class="action-buttons">
            @if(in_array($request->status, ['pending', 'payment_method_set', 'payment_uploaded', 'payment_rejected', 'payment_verified']))
                <button onclick="updateStatus({{ $request->id }}, 'processing')" class="btn-action btn-processing">
                    <i class="bi bi-gear"></i> Start Processing
                </button>
            @endif
            
            @if($request->status === 'processing')
                <button onclick="updateStatus({{ $request->id }}, 'ready_for_pickup')" class="btn-action btn-ready">
                    <i class="bi bi-box-seam"></i> Ready for Pickup
                </button>
            @endif
            
            @if($request->status === 'ready_for_pickup')
                <button onclick="markReceived({{ $request->id }})" class="btn-action btn-received">
                    <i class="bi bi-check2-all"></i> Mark as Received
                </button>
            @endif
            
            @if(!in_array($request->status, ['received', 'cancelled']))
                <button onclick="updateStatus({{ $request->id }}, 'cancelled')" class="btn-action btn-cancelled">
                    <i class="bi bi-x-circle"></i> Cancel Request
                </button>
            @endif
        </div>

        <div class="back-link">
            <a href="{{ route('registrar.requests.index') }}" class="btn-back">
                <i class="bi bi-arrow-left me-1"></i> Back to Requests List
            </a>
        </div>
    </div>
</div>

{{-- Hidden forms for status updates --}}
<form id="statusForm" method="POST" style="display:none;">
    @csrf
    @method('PATCH')
    <input type="hidden" name="status" id="statusValue">
</form>

<form id="receivedForm" method="POST" style="display:none;">
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
        <div class="ccst-card-header blue">Status Guide</div>
        <div class="ccst-card-body p-0">
            <div class="rp-guide-step">
                <span class="rp-step-num">1</span>
                <span>Start processing when requirements are met</span>
            </div>
            <div class="rp-guide-step">
                <span class="rp-step-num">2</span>
                <span>Mark ready when documents are printed</span>
            </div>
            <div class="rp-guide-step" style="border-bottom:none;">
                <span class="rp-step-num">3</span>
                <span>Mark received once claimed by student</span>
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

    .request-detail-scroll {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 4px;
        margin-bottom: 20px;
    }

    .request-detail-scroll::-webkit-scrollbar { width: 6px; }
    .request-detail-scroll::-webkit-scrollbar-track { background: #f0f0f0; border-radius: 3px; }
    .request-detail-scroll::-webkit-scrollbar-thumb { background: #1B6B3A; border-radius: 3px; }

    .request-detail-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        padding: 24px;
    }

    .detail-section {
        margin-bottom: 28px;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 20px;
    }
    .detail-section:last-of-type { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }

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

    .detail-grid div { font-size: 0.85rem; }
    .detail-grid label { color: #888; font-weight: 500; margin-right: 8px; }

    .docs-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }
    .docs-table th {
        background: #F0F7F0;
        padding: 10px;
        color: #1B6B3A;
        font-weight: 600;
        text-align: left;
    }
    .docs-table td {
        padding: 10px;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
    }
    .text-center { text-align: center; }
    .text-end { text-align: right; }

    .action-buttons {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #f0f0f0;
    }

    .btn-action {
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    .btn-action:hover { opacity: 0.85; }

    .btn-processing { background: #1A9FE0; }
    .btn-ready { background: #F5C518; color: #1A1A1A; }
    .btn-received { background: #1B6B3A; }
    .btn-cancelled { background: #DC3545; }

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
    .btn-back:hover { background: #e0e0e0; color: #1A1A1A; }
    .back-link { margin-top: 20px; }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    .status-pending { background: #FFF3CD; color: #856404; }
    .status-rejected { background: #F8D7DA; color: #721C24; }
    .status-verified { background: #D4EDDA; color: #155724; }
    .status-processing { background: #E8F4FD; color: #0969A2; }
    .status-ready { background: #FFF3CD; color: #856404; }
    .status-received { background: #D4EDDA; color: #155724; }
    .status-cancelled { background: #F8D7DA; color: #721C24; }

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

    .main-content {
        overflow-y: hidden !important;
        display: flex;
        flex-direction: column;
        height: calc(100vh - var(--header-h) - var(--footer-h));
        padding-bottom: 0;
    }
    .main-content > .registrar-sticky-header { flex-shrink: 0; }
    .main-content > .request-detail-scroll { flex: 1; min-height: 0; }
</style>
@endpush

@push('scripts')
<script>
    function updateStatus(id, status) {
        let title = '';
        let text = '';
        let confirmColor = '#1B6B3A';
        
        switch(status) {
            case 'processing':
                title = 'Start Processing?';
                text = 'This will notify the student that their documents are being processed.';
                confirmColor = '#1A9FE0';
                break;
            case 'ready_for_pickup':
                title = 'Ready for Pickup?';
                text = 'This will notify the student to claim their documents.';
                confirmColor = '#F5C518';
                break;
            case 'cancelled':
                title = 'Cancel Request?';
                text = 'This will cancel the document request permanently.';
                confirmColor = '#DC3545';
                break;
        }

        Swal.fire({
            title: title,
            text: text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, proceed',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('statusForm');
                form.action = `/registrar/requests/${id}/status`;
                document.getElementById('statusValue').value = status;
                form.submit();
            }
        });
    }

    function markReceived(id) {
        Swal.fire({
            title: 'Mark as Received?',
            text: 'This confirms the student has claimed their documents. This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1B6B3A',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Mark as Received',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('receivedForm');
                form.action = `/registrar/requests/${id}/received`;
                form.submit();
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
