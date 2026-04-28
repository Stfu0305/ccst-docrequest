@extends('layouts.registrar')

@section('title', 'Appointment Calendar')

@section('content')

<div class="calendar-header">
    <h1 class="calendar-title">Appointment Calendar</h1>
    <div class="calendar-actions">
        <button class="btn-print-list" onclick="window.open('{{ route('registrar.appointments.print-cashier-list') }}', '_blank')">
            <i class="bi bi-printer"></i> Print Cashier List
        </button>
    </div>
</div>

<div class="calendar-container">
    <div id="calendar"></div>
</div>

{{-- Appointment Details Modal --}}
<div id="appointmentModal" class="modal-overlay" style="display:none;">
    <div class="modal-container" style="max-width: 450px;">
        <div class="modal-header-custom">
            <h4><i class="bi bi-calendar-check me-2"></i>Appointment Details</h4>
            <button type="button" class="modal-close" onclick="closeAppointmentModal()">&times;</button>
        </div>
        <div class="modal-body-custom">
            <div class="detail-row">
                <label>Student Name:</label>
                <span id="modalStudentName"></span>
            </div>
            <div class="detail-row">
                <label>Student Number:</label>
                <span id="modalStudentNumber"></span>
            </div>
            <div class="detail-row">
                <label>Reference No.:</label>
                <span id="modalReferenceNo"></span>
            </div>
            <div class="detail-row">
                <label>Amount:</label>
                <span id="modalAmount"></span>
            </div>
            <div class="detail-row">
                <label>Time Slot:</label>
                <span id="modalTimeSlot"></span>
            </div>
            <div class="detail-row">
                <label>Status:</label>
                <span id="modalStatus" class="status-badge"></span>
            </div>
        </div>
        <div class="modal-footer-custom" id="modalActions">
            <button type="button" class="btn-complete" onclick="markCompleted()">Mark Completed</button>
            <button type="button" class="btn-missed" onclick="markMissed()">Mark Missed</button>
            <button type="button" class="btn-cancel-modal" onclick="closeAppointmentModal()">Close</button>
        </div>
    </div>
</div>

{{-- Hidden forms for actions --}}
<form id="completeForm" method="POST" style="display:none;">
    @csrf
    @method('PATCH')
</form>

<form id="missedForm" method="POST" style="display:none;">
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
        <div class="ccst-card-header blue" style="display: flex; justify-content: space-between; align-items: center;">
            <span><i class="bi bi-clock-history me-2"></i> Time Slots</span>
            <button class="btn-add-timeslot" onclick="openTimeSlotModal()">
                <i class="bi bi-plus-lg"></i>
            </button>
        </div>
        <div class="ccst-card-body p-0" id="timeSlotsList">
            <div class="rp-stat-row text-muted">Loading time slots...</div>
        </div>
    </div>

    <div class="ccst-card mb-0">
        <div class="ccst-card-header yellow">Legend</div>
        <div class="ccst-card-body p-0">
            <div class="legend-item">
                <span class="legend-color" style="background: #F5C518;"></span>
                <span>Scheduled</span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background: #1B6B3A;"></span>
                <span>Completed</span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background: #DC3545;"></span>
                <span>Missed</span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background: #AAAAAA;"></span>
                <span>Cancelled</span>
            </div>
        </div>
    </div>

    <div class="ccst-card mb-0">
        <div class="ccst-card-header yellow">Quick Tips</div>
        <div class="ccst-card-body p-0">
            <div class="rp-guide-step">
                <span class="rp-step-num">1</span>
                <span>Click on appointment to view details</span>
            </div>
            <div class="rp-guide-step">
                <span class="rp-step-num">2</span>
                <span>Drag and drop to reschedule</span>
            </div>
            <div class="rp-guide-step">
                <span class="rp-step-num">3</span>
                <span>Use the toolbar to switch views</span>
            </div>
            <div class="rp-guide-step" style="border-bottom:none;">
                <span class="rp-step-num">4</span>
                <span>Click Print to generate cashier list</span>
            </div>
        </div>
    </div>

    {{-- Time Slot Modal --}}
    <div id="timeSlotModal" class="modal-overlay" style="display:none;">
        <div class="modal-container" style="max-width: 450px;">
            <div class="modal-header-custom">
                <h4 id="timeSlotModalTitle"><i class="bi bi-plus-circle me-2"></i>Add Time Slot</h4>
                <button type="button" class="modal-close" onclick="closeTimeSlotModal()">&times;</button>
            </div>
            <form id="timeSlotForm">
                @csrf
                <input type="hidden" id="timeSlotId" name="id" value="">
                <div class="modal-body-custom">
                    <div class="form-group">
                        <label>Slot Label <span class="required">*</span></label>
                        <input type="text" name="label" id="slotLabel" class="form-input" placeholder="e.g., 8:00 AM - 9:00 AM" required>
                    </div>
                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Start Time <span class="required">*</span></label>
                            <input type="time" name="start_time" id="slotStartTime" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label>End Time <span class="required">*</span></label>
                            <input type="time" name="end_time" id="slotEndTime" class="form-input" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Max Capacity <span class="required">*</span></label>
                        <input type="number" name="max_capacity" id="slotMaxCapacity" class="form-input" value="5" min="1" max="20" required>
                        <small class="form-hint">Maximum number of appointments per time slot per day</small>
                    </div>
                </div>
                <div class="modal-footer-custom">
                    <button type="button" class="btn-cancel-modal" onclick="closeTimeSlotModal()">Cancel</button>
                    <button type="submit" class="btn-save-modal">Save Time Slot</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteSlotModal" class="modal-overlay" style="display:none;">
        <div class="modal-container" style="max-width: 400px;">
            <div class="modal-header-custom" style="background:#DC3545;">
                <h4><i class="bi bi-exclamation-triangle me-2"></i>Delete Time Slot</h4>
                <button type="button" class="modal-close" onclick="closeDeleteSlotModal()">&times;</button>
            </div>
            <div class="modal-body-custom">
                <p>Are you sure you want to delete this time slot?</p>
                <p class="text-muted" style="font-size:0.75rem;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn-cancel-modal" onclick="closeDeleteSlotModal()">Cancel</button>
                <button type="button" class="btn-delete-confirm" id="confirmDeleteSlotBtn">Delete Permanently</button>
            </div>
        </div>
    </div>

@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<style>
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .calendar-title {
        font-family: 'Volkhov', serif;
        font-size: 1.5rem;
        font-weight: 700;
        color: #1A1A1A;
        margin: 0;
    }

    .btn-print-list {
        background: #1B6B3A;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .calendar-container {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    /* FullCalendar Customization */
    .fc {
        font-family: 'Poppins', sans-serif;
    }

    .fc-toolbar-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1B6B3A;
    }

    .fc-button-primary {
        background-color: #1A9FE0 !important;
        border-color: #1A9FE0 !important;
    }

    .fc-button-primary:hover {
        background-color: #0D7FBF !important;
        border-color: #0D7FBF !important;
    }

    .fc-button-primary:focus {
        box-shadow: none !important;
    }

    .fc-day-today {
        background-color: #F0F7F0 !important;
    }

    /* Legend */
    .legend-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 14px;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }

    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 4px;
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1050;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-container {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 450px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }

    .modal-header-custom {
        background: #1B6B3A;
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-body-custom {
        padding: 20px;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 0.85rem;
    }

    .detail-row label {
        font-weight: 700;
        color: #555;
    }

    .modal-footer-custom {
        padding: 15px 20px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        border-top: 1px solid #f0f0f0;
    }

    .btn-complete {
        background: #1B6B3A;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
    }

    .btn-missed {
        background: #DC3545;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
    }

    .status-badge {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .status-scheduled { background: #FFF3CD; color: #856404; }
    .status-completed { background: #D4EDDA; color: #155724; }
    .status-missed { background: #F8D7DA; color: #721C24; }
    .status-cancelled { background: #F0F0F0; color: #888; }

    .rp-date-card {
        background: rgba(255,255,255,0.18);
        border-radius: 12px;
        padding: 16px;
        text-align: center;
        color: white;
        backdrop-filter: blur(8px);
        margin-bottom: 18px;
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

    /* Time Slot Items */
    .time-slot-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 14px;
        border-bottom: 1px solid rgba(255,255,255,0.15);
        gap: 10px;
    }

    .time-slot-info {
        flex: 1;
    }

    .time-slot-label {
        font-size: 0.75rem;
        color: white;
        margin-bottom: 2px;
    }

    .time-slot-meta {
        font-size: 0.65rem;
        color: rgba(255,255,255,0.7);
    }

    .time-slot-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .slot-status {
        font-size: 0.6rem;
        padding: 2px 8px;
        border-radius: 20px;
        font-weight: 600;
    }

    .status-active {
        background: #D4EDDA;
        color: #155724;
    }

    .status-inactive {
        background: #F8D7DA;
        color: #721C24;
    }

    .slot-action-btn {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 28px;
        height: 28px;
        border-radius: 6px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .slot-action-btn:hover {
        background: rgba(255,255,255,0.4);
    }

    .slot-action-btn.delete-slot:hover {
        background: #DC3545;
    }

    .slot-action-btn.toggle-slot:hover {
        background: #F5C518;
        color: #1A1A1A;
    }

    .btn-add-timeslot {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 28px;
        height: 28px;
        border-radius: 6px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .btn-add-timeslot:hover {
        background: rgba(255,255,255,0.4);
    }

    .btn-delete-confirm {
        background: #DC3545;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
    }

    .form-row-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 15px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        font-size: 0.7rem;
        font-weight: 700;
        color: #555;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 5px;
    }

    .form-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #D0DDD0;
        border-radius: 6px;
        font-size: 0.8rem;
        font-family: 'Poppins', sans-serif;
    }

    .required {
        color: #DC3545;
    }

    .form-hint {
        font-size: 0.65rem;
        color: #888;
        margin-top: 4px;
        display: block;
    }

    .btn-cancel-modal {
        background: #f0f0f0;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
    }

    .btn-save-modal {
        background: #1B6B3A;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
    }
    
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script>
    let calendar = null;
    let currentAppointmentId = null;

    // Load time slots
    let currentDeleteSlotId = null;
    let currentEditSlot = null;

    // Load time slots with action buttons
    function loadTimeSlots() {
        fetch('{{ route("registrar.calendar.time-slots") }}')
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('timeSlotsList');
                if (data.length === 0) {
                    container.innerHTML = '<div class="rp-stat-row text-muted">No time slots configured. Click + to add.</div>';
                    return;
                }
                
                let html = '';
                data.forEach(slot => {
                    const statusClass = slot.is_active ? 'status-active' : 'status-inactive';
                    const statusText = slot.is_active ? 'Active' : 'Inactive';
                    html += `
                        <div class="time-slot-item" data-id="${slot.id}">
                            <div class="time-slot-info">
                                <div class="time-slot-label"><strong>${escapeHtml(slot.label)}</strong></div>
                                <div class="time-slot-meta">Max: ${slot.max_capacity} students</div>
                            </div>
                            <div class="time-slot-actions">
                                <span class="slot-status ${statusClass}">${statusText}</span>
                                <button class="slot-action-btn edit-slot" onclick="editTimeSlot(${slot.id})" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="slot-action-btn toggle-slot" onclick="toggleTimeSlot(${slot.id})" title="Toggle Active">
                                    <i class="bi bi-power"></i>
                                </button>
                                <button class="slot-action-btn delete-slot" onclick="confirmDeleteSlot(${slot.id})" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = html;
            });
    }

    // Helper function to escape HTML
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // Open Add Time Slot Modal
    function openTimeSlotModal() {
        currentEditSlot = null;
        document.getElementById('timeSlotModalTitle').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Add Time Slot';
        document.getElementById('timeSlotId').value = '';
        document.getElementById('slotLabel').value = '';
        document.getElementById('slotStartTime').value = '';
        document.getElementById('slotEndTime').value = '';
        document.getElementById('slotMaxCapacity').value = '5';
        document.getElementById('timeSlotModal').style.display = 'flex';
    }

    // Edit Time Slot
    function editTimeSlot(id) {
        fetch(`/registrar/time-slots/${id}/data`)
            .then(res => res.json())
            .then(data => {
                currentEditSlot = data;
                document.getElementById('timeSlotModalTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>Edit Time Slot';
                document.getElementById('timeSlotId').value = data.id;
                document.getElementById('slotLabel').value = data.label;
                document.getElementById('slotStartTime').value = data.start_time;
                document.getElementById('slotEndTime').value = data.end_time;
                document.getElementById('slotMaxCapacity').value = data.max_capacity;
                document.getElementById('timeSlotModal').style.display = 'flex';
            });
    }

    // Close Time Slot Modal
    function closeTimeSlotModal() {
        document.getElementById('timeSlotModal').style.display = 'none';
        document.getElementById('timeSlotForm').reset();
    }

    // Save Time Slot (Create or Update)
    document.getElementById('timeSlotForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const id = document.getElementById('timeSlotId').value;
        const formData = {
            label: document.getElementById('slotLabel').value,
            start_time: document.getElementById('slotStartTime').value,
            end_time: document.getElementById('slotEndTime').value,
            max_capacity: document.getElementById('slotMaxCapacity').value,
        };
        
        const url = id ? `/registrar/time-slots/${id}` : '{{ route("registrar.timeslots.store") }}';
        const method = id ? 'PUT' : 'POST';
        
        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Success', data.message, 'success');
                closeTimeSlotModal();
                loadTimeSlots();
                // Refresh calendar events if view changed
                if (calendar) calendar.refetchEvents();
            } else {
                Swal.fire('Error', data.message || 'Failed to save time slot.', 'error');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            Swal.fire('Error', 'Failed to save time slot.', 'error');
        });
    });

    // Toggle Time Slot Active Status
    function toggleTimeSlot(id) {
        fetch(`/registrar/time-slots/${id}/toggle`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Success', data.message, 'success');
                loadTimeSlots();
                if (calendar) calendar.refetchEvents();
            } else {
                Swal.fire('Error', data.message || 'Failed to toggle time slot.', 'error');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            Swal.fire('Error', 'Failed to toggle time slot.', 'error');
        });
    }

    // Confirm Delete Time Slot
    function confirmDeleteSlot(id) {
        currentDeleteSlotId = id;
        document.getElementById('deleteSlotModal').style.display = 'flex';
    }

    function closeDeleteSlotModal() {
        document.getElementById('deleteSlotModal').style.display = 'none';
        currentDeleteSlotId = null;
    }

    // Delete Time Slot
    document.getElementById('confirmDeleteSlotBtn').addEventListener('click', function() {
        if (!currentDeleteSlotId) return;
        
        fetch(`/registrar/time-slots/${currentDeleteSlotId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Deleted!', data.message, 'success');
                closeDeleteSlotModal();
                loadTimeSlots();
                if (calendar) calendar.refetchEvents();
            } else {
                Swal.fire('Error', data.message || 'Cannot delete this time slot.', 'error');
                closeDeleteSlotModal();
            }
        })
        .catch(err => {
            console.error('Error:', err);
            Swal.fire('Error', 'Failed to delete time slot.', 'error');
            closeDeleteSlotModal();
        });
    });

    function closeAppointmentModal() {
        document.getElementById('appointmentModal').style.display = 'none';
        currentAppointmentId = null;
    }

    function markCompleted() {
        if (!currentAppointmentId) return;
        
        Swal.fire({
            title: 'Mark as Completed?',
            text: 'This appointment will be marked as completed.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1B6B3A',
            cancelButtonColor: '#DC3545',
            confirmButtonText: 'Yes, Complete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('completeForm');
                form.action = `/registrar/appointments/${currentAppointmentId}/complete`;
                form.submit();
            }
        });
    }

    function markMissed() {
        if (!currentAppointmentId) return;
        
        Swal.fire({
            title: 'Mark as Missed?',
            text: 'This appointment will be marked as missed (student no-show).',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC3545',
            cancelButtonColor: '#1B6B3A',
            confirmButtonText: 'Yes, Mark as Missed',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('missedForm');
                form.action = `/registrar/appointments/${currentAppointmentId}/missed`;
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