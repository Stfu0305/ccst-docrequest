@extends('layouts.registrar')

@section('title', 'Create Registrar Account')

@section('content')

<div class="registrar-sticky-header">CREATE REGISTRAR ACCOUNT</div>

<div class="pending-card" style="max-width: 500px; margin: 0 auto;">
    <form action="{{ route('registrar.manage.store') }}" method="POST">
        @csrf
        
        <div class="mb-3">
            <label class="form-label fw-bold text-muted" style="font-size:0.8rem">FULL NAME <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        
        <div class="mb-3">
            <label class="form-label fw-bold text-muted" style="font-size:0.8rem">EMAIL ADDRESS <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        
        <div class="mb-3">
            <label class="form-label fw-bold text-muted" style="font-size:0.8rem">PASSWORD <span class="text-danger">*</span></label>
            <input type="password" name="password" class="form-control" required>
            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        
        <div class="mb-4">
            <label class="form-label fw-bold text-muted" style="font-size:0.8rem">CONFIRM PASSWORD <span class="text-danger">*</span></label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        
        <div class="d-flex justify-content-end gap-2 border-top pt-3">
            <a href="{{ route('registrar.manage.index') }}" class="btn btn-secondary px-4">Cancel</a>
            <button type="submit" class="btn text-white px-4" style="background-color: #1B6B3A;">Create Account</button>
        </div>
    </form>
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
    .form-control {
        border-radius: 6px;
    }
    .form-control:focus {
        border-color: #1B6B3A;
        box-shadow: 0 0 0 0.25rem rgba(27, 107, 58, 0.25);
    }
</style>
@endpush
