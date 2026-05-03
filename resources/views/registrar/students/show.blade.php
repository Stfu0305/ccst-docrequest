@extends('layouts.registrar')

@section('title', 'Student Profile - ' . $student->name)

@section('content')

<div class="registrar-sticky-header">STUDENT PROFILE</div>

<div class="mb-3">
    <a href="{{ route('registrar.students.index') }}" class="btn btn-sm btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Directory
    </a>
</div>

<div class="pending-card">
    <div class="row">
        <div class="col-md-4 text-center">
            @if($student->profile_photo)
                <img src="{{ Storage::url($student->profile_photo) }}" class="rounded-circle img-thumbnail mb-3" style="width: 150px; height: 150px; object-fit: cover;">
            @else
                <div class="rounded-circle img-thumbnail mb-3 d-inline-flex justify-content-center align-items-center" style="width: 150px; height: 150px; background-color: #f0f0f0; font-size: 3rem; color: #ccc;">
                    <i class="bi bi-person"></i>
                </div>
            @endif
            <h4>{{ $student->full_name ?? $student->name }}</h4>
            <p class="text-muted">{{ $student->student_number }}</p>
            
            <div class="mt-3">
                @if($student->is_active)
                    <span class="badge bg-success px-3 py-2">Account Active</span>
                @else
                    <span class="badge bg-danger px-3 py-2">Account Deactivated</span>
                @endif
            </div>
        </div>
        <div class="col-md-8">
            <h5 class="border-bottom pb-2 mb-3">Personal Information</h5>
            <div class="row mb-3">
                <div class="col-sm-4 fw-bold">Email:</div>
                <div class="col-sm-8">{{ $student->email }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-4 fw-bold">Contact Number:</div>
                <div class="col-sm-8">{{ $student->contact_number ?? 'N/A' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-4 fw-bold">Strand:</div>
                <div class="col-sm-8">{{ $student->strand }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-4 fw-bold">Grade Level:</div>
                <div class="col-sm-8">{{ $student->grade_level }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-4 fw-bold">Section:</div>
                <div class="col-sm-8">{{ $student->section }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-4 fw-bold">Registered On:</div>
                <div class="col-sm-8">{{ $student->created_at->format('F d, Y h:i A') }}</div>
            </div>
            
            <h5 class="border-bottom pb-2 mb-3 mt-4">ID Photo</h5>
            @if($student->student_id_photo)
                <a href="{{ Storage::url($student->student_id_photo) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-card-image"></i> View ID Photo
                </a>
            @else
                <span class="text-muted">No ID photo uploaded.</span>
            @endif
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
        padding: 30px;
    }
</style>
@endpush
