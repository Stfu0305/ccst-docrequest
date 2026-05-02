@extends('layouts.registrar')

@section('title', 'Walk-In Mode')

@section('content')

<div class="registrar-sticky-header">WALK-IN MODE</div>

<div class="ccst-card">
    <div class="ccst-card-header green">
        <i class="bi bi-search me-2"></i> Search Existing Student
    </div>
    <div class="ccst-card-body">
        <form method="POST" action="{{ route('registrar.walkin.search') }}" class="mb-4">
            @csrf
            <div style="display: flex; gap: 10px;">
                <input type="text" name="query" value="{{ $query ?? '' }}" placeholder="Enter Student Number or Name..." 
                       style="flex: 1; padding: 10px 15px; border: 1px solid #D0DDD0; border-radius: 8px; font-family: 'Poppins', sans-serif;">
                <button type="submit" class="btn-action" style="background: #1B6B3A;">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>

        @if(isset($students))
            @if($students->count() > 0)
                <div style="margin-top: 20px;">
                    <h4 style="font-size: 1rem; color: #1B6B3A; margin-bottom: 12px;">Search Results</h4>
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                        <thead>
                            <tr style="background: #F0F7F0; border-bottom: 2px solid #D0DDD0; text-align: left;">
                                <th style="padding: 10px; color: #1B6B3A;">Student Number</th>
                                <th style="padding: 10px; color: #1B6B3A;">Name</th>
                                <th style="padding: 10px; color: #1B6B3A;">Course/Strand</th>
                                <th style="padding: 10px; text-align: right; color: #1B6B3A;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 10px; font-weight: 600;">{{ $student->student_number ?? 'N/A' }}</td>
                                <td style="padding: 10px;">{{ $student->full_name }}</td>
                                <td style="padding: 10px;">{{ $student->strand ?? 'N/A' }}</td>
                                <td style="padding: 10px; text-align: right;">
                                    <a href="{{ route('registrar.walkin.create', ['student_id' => $student->id]) }}" class="btn-action" style="background: #1A9FE0; text-decoration: none; display: inline-block;">
                                        <i class="bi bi-plus-circle me-1"></i> Create Request
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="padding: 20px; background: #F8D7DA; color: #721C24; border-radius: 8px; margin-top: 15px;">
                    <i class="bi bi-exclamation-circle me-2"></i> No students found matching "{{ $query }}".
                </div>
            @endif
        @endif
    </div>
</div>

<div class="ccst-card" style="margin-top: 24px;">
    <div class="ccst-card-header blue">
        <i class="bi bi-person-plus me-2"></i> Register New Walk-In Student
    </div>
    <div class="ccst-card-body">
        <p style="font-size: 0.85rem; color: #666; margin-bottom: 20px;">
            If the student is not in the system, fill out this quick form to create a profile for them and proceed to document selection.
        </p>

        @if($errors->any())
            <div style="padding: 15px; background: #F8D7DA; color: #721C24; border-radius: 8px; margin-bottom: 20px; font-size: 0.85rem;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('registrar.walkin.store') }}">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #555; margin-bottom: 5px;">First Name <span style="color:#DC3545">*</span></label>
                    <input type="text" name="first_name" required style="width: 100%; padding: 10px; border: 1px solid #D0DDD0; border-radius: 8px; font-family: 'Poppins', sans-serif;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #555; margin-bottom: 5px;">Last Name <span style="color:#DC3545">*</span></label>
                    <input type="text" name="last_name" required style="width: 100%; padding: 10px; border: 1px solid #D0DDD0; border-radius: 8px; font-family: 'Poppins', sans-serif;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #555; margin-bottom: 5px;">Student Number (Optional)</label>
                    <input type="text" name="student_number" placeholder="Leave blank to auto-generate" style="width: 100%; padding: 10px; border: 1px solid #D0DDD0; border-radius: 8px; font-family: 'Poppins', sans-serif;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #555; margin-bottom: 5px;">Contact Number <span style="color:#DC3545">*</span></label>
                    <input type="text" name="contact_number" required style="width: 100%; padding: 10px; border: 1px solid #D0DDD0; border-radius: 8px; font-family: 'Poppins', sans-serif;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #555; margin-bottom: 5px;">Course / Strand <span style="color:#DC3545">*</span></label>
                    <input type="text" name="course_program" required style="width: 100%; padding: 10px; border: 1px solid #D0DDD0; border-radius: 8px; font-family: 'Poppins', sans-serif;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #555; margin-bottom: 5px;">Year / Grade Level <span style="color:#DC3545">*</span></label>
                    <input type="text" name="year_level" required style="width: 100%; padding: 10px; border: 1px solid #D0DDD0; border-radius: 8px; font-family: 'Poppins', sans-serif;">
                </div>
            </div>

            <div style="text-align: right; margin-top: 20px; border-top: 1px solid #f0f0f0; padding-top: 20px;">
                <button type="submit" class="btn-action" style="background: #1B6B3A; padding: 12px 24px;">
                    Register & Continue to Request <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </form>
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
    
    .btn-action {
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    .btn-action:hover { opacity: 0.85; }
</style>
@endpush
