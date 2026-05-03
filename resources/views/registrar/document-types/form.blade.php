@extends('layouts.registrar')

@section('title', isset($documentType) ? 'Edit Document Type' : 'Add Document Type')

@section('content')

<div class="registrar-sticky-header">{{ isset($documentType) ? 'EDIT DOCUMENT TYPE' : 'ADD DOCUMENT TYPE' }}</div>

<div class="pending-card" style="max-width: 700px; margin: 0 auto;">
    <form action="{{ isset($documentType) ? route('registrar.document-types.update', $documentType->id) : route('registrar.document-types.store') }}" method="POST">
        @csrf
        @if(isset($documentType))
            @method('PUT')
        @endif
        
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold text-muted" style="font-size:0.8rem">CODE <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control" value="{{ old('code', $documentType->code ?? '') }}" required placeholder="e.g. TOR">
                @error('code') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            
            <div class="col-md-8 mb-3">
                <label class="form-label fw-bold text-muted" style="font-size:0.8rem">DOCUMENT NAME <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $documentType->name ?? '') }}" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold text-muted" style="font-size:0.8rem">FEE (₱) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" name="fee" class="form-control" value="{{ old('fee', $documentType->fee ?? '0.00') }}" required>
                @error('fee') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold text-muted" style="font-size:0.8rem">PROCESSING DAYS</label>
                <input type="number" min="0" name="processing_days" class="form-control" value="{{ old('processing_days', $documentType->processing_days ?? 3) }}">
            </div>
            
            <div class="col-md-12 mb-4 mt-2">
                <h6 class="border-bottom pb-2 mb-3" style="color:#1B6B3A; font-weight:700">Settings</h6>
                
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_printable" id="is_printable" value="1" {{ old('is_printable', $documentType->is_printable ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_printable"><strong>System Printable</strong> - Allow auto-generation of this document via .docx templates.</label>
                </div>
                
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" role="switch" name="has_school_year" id="has_school_year" value="1" {{ old('has_school_year', $documentType->has_school_year ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_school_year"><strong>Requires School Year</strong> - Ask student for academic year/semester.</label>
                </div>
                
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="is_active" value="1" {{ old('is_active', $documentType->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active"><strong>Active</strong> - Show this document in the request portal.</label>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-end gap-2 border-top pt-3">
            <a href="{{ route('registrar.document-types.index') }}" class="btn btn-secondary px-4">Cancel</a>
            <button type="submit" class="btn text-white px-4" style="background-color: #1B6B3A;">{{ isset($documentType) ? 'Update Document' : 'Save Document' }}</button>
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
    .form-check-input:checked {
        background-color: #1B6B3A;
        border-color: #1B6B3A;
    }
</style>
@endpush
