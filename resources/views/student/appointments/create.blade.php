@extends('layouts.student')

@section('title', 'Book Appointment')

@section('content')

<div class="appointment-sticky-header">BOOK APPOINTMENT</div>

<div class="appointment-container">
    <div class="appointment-card">
        <div class="appointment-card-body">
            <h3>Request Details</h3>
            <div class="request-summary">
                <p><strong>Reference Number:</strong> {{ $documentRequest->reference_number }}</p>
                <p><strong>Total Amount:</strong> ₱{{ number_format($documentRequest->total_fee, 2) }}</p>
                <p><strong>Documents:</strong></p>
                <ul>
                    @foreach($documentRequest->items as $item)
                        <li>{{ $item->documentType->name }} × {{ $item->copies }}</li>
                    @endforeach
                </ul>
            </div>

            <form method="POST" action="{{ route('student.appointments.store') }}">
                @csrf
                <input type="hidden" name="document_request_id" value="{{ $documentRequest->id }}">

                <div class="form-group">
                    <label>Pickup Date</label>
                    <input type="text" name="appointment_date" id="appointmentDate" class="form-input" readonly required>
                </div>

                <div class="form-group">
                    <label>Time Slot</label>
                    <select name="time_slot_id" id="timeSlot" class="form-input" required>
                        <option value="">Select a time slot</option>
                        @foreach($timeSlots as $slot)
                            <option value="{{ $slot->id }}">{{ $slot->label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-actions">
                    <a href="{{ route('student.dashboard') }}" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit">Confirm Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .appointment-sticky-header {
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

    .appointment-container {
        max-width: 600px;
        margin: 0 auto;
    }

    .appointment-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .appointment-card-body {
        padding: 24px;
    }

    .appointment-card-body h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1B6B3A;
        margin-bottom: 16px;
    }

    .request-summary {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 24px;
    }

    .request-summary p {
        margin-bottom: 8px;
        font-size: 0.85rem;
    }

    .request-summary ul {
        margin-left: 20px;
        font-size: 0.85rem;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        color: #555;
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .form-input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #D0DDD0;
        border-radius: 6px;
        font-size: 0.85rem;
        font-family: 'Poppins', sans-serif;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
    }

    .btn-cancel {
        background: #f0f0f0;
        color: #666;
        padding: 10px 24px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .btn-submit {
        background: #1B6B3A;
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
    }
</style>
@endpush

@push('scripts')
<script>
    flatpickr("#appointmentDate", {
        minDate: "today",
        dateFormat: "Y-m-d",
        disable: [
            function(date) {
                return date.getDay() === 0 || date.getDay() === 6;
            }
        ],
        locale: {
            firstDayOfWeek: 1
        }
    });
</script>
@endpush