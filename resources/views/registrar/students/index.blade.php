@extends('layouts.registrar')

@section('title', 'All Students Directory')

@section('content')

<div class="registrar-sticky-header">ALL STUDENTS DIRECTORY</div>

<div class="pending-card">
    <div class="d-flex justify-content-between mb-3">
        <div>
            <form action="{{ route('registrar.students.index') }}" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Search students..." value="{{ request('search') }}" style="width: 250px;">
                <button type="submit" class="btn btn-sm btn-primary" style="background-color: #1B6B3A; border-color: #1B6B3A;"><i class="bi bi-search"></i></button>
            </form>
        </div>
    </div>

    <div class="table-scroll-body">
        <table class="pending-table">
            <thead>
                <tr>
                    <th style="width: 20%">Student Name</th>
                    <th style="width: 15%">Student Number</th>
                    <th style="width: 20%">Email</th>
                    <th style="width: 10%">Strand</th>
                    <th style="width: 10%">Grade</th>
                    <th style="width: 10%">Status</th>
                    <th style="width: 15%">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                    <td>{{ $student->full_name ?? $student->name }}</td>
                    <td>{{ $student->student_number }}</td>
                    <td>{{ $student->email }}</td>
                    <td>{{ $student->strand }}</td>
                    <td>{{ $student->grade_level }}</td>
                    <td>
                        @if($student->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Deactivated</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button type="button" class="btn-view-id" onclick="viewStudent({{ $student->id }})" title="View Details">
                                <i class="bi bi-eye"></i>
                            </button>
                            <form action="{{ route('registrar.students.toggle-active', $student->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn-reject" style="background: {{ $student->is_active ? '#DC3545' : '#1B6B3A' }};" title="{{ $student->is_active ? 'Deactivate' : 'Activate' }}">
                                    <i class="bi bi-power"></i>
                                </button>
                            </form>
                            <form action="{{ route('registrar.students.send-reset', $student->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn-verify" style="background: #0d6efd;" title="Send Password Reset" onclick="return confirm('Send password reset link to {{ $student->email }}?')">
                                    <i class="bi bi-key"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No students found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-3">
        {{ $students->links() }}
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
        <div class="ccst-card-header blue">Directory Guide</div>
        <div class="ccst-card-body p-0">
            <div class="rp-guide-step">
                <span class="rp-step-num">1</span>
                <span>Search by name, student number, or email.</span>
            </div>
            <div class="rp-guide-step">
                <span class="rp-step-num">2</span>
                <span>Click the eye icon to view full student details.</span>
            </div>
            <div class="rp-guide-step">
                <span class="rp-step-num">3</span>
                <span>Click the power button to deactivate/activate a student.</span>
            </div>
            <div class="rp-guide-step" style="border-bottom:none;">
                <span class="rp-step-num">4</span>
                <span>Click the key icon to send a password reset link.</span>
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
        margin-bottom: 20px;
    }

    .pending-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        padding: 20px;
    }

    .pending-table {
        width: 100%;
        border-collapse: collapse;
    }

    .pending-table th {
        background: #F0F7F0;
        padding: 12px;
        font-size: 0.7rem;
        font-weight: 700;
        color: #1B6B3A;
        text-align: left;
    }

    .pending-table td {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.8rem;
        vertical-align: middle;
    }

    .action-buttons {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }

    .btn-view-id {
        background: #F5C518;
        color: #1A1A1A;
        border: none;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        cursor: pointer;
    }

    .btn-verify {
        background: #1B6B3A;
        color: white;
        border: none;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        cursor: pointer;
    }

    .btn-reject {
        background: #DC3545;
        color: white;
        border: none;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        cursor: pointer;
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

    .rp-date-day { font-size: 2.8rem; font-weight: 700; }
    .rp-date-month { font-size: 0.85rem; opacity: 0.85; }
    .rp-date-time { font-size: 1rem; font-weight: 600; margin-top: 6px; }

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

    .text-center { text-align: center; }
    .py-4 { padding-top: 24px; padding-bottom: 24px; }
    .text-muted { color: #888; }
</style>
@endpush

@push('scripts')
<script>
    function viewStudent(id) {
        window.location.href = `/registrar/students/${id}`;
    }

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
</script>
@endpush
