@extends('layouts.registrar')

@section('title', 'Prepare Document - ' . $documentType->name)

@section('content')
<div class="registrar-sticky-header">PREPARE DOCUMENT</div>

<div class="request-detail-scroll">
    <div class="request-detail-card">
        <div class="detail-section">
            <h3><i class="bi bi-file-earmark-text me-2"></i>{{ $documentType->name }}</h3>
            <p class="text-muted">Review and edit the information below before generating the document. These fields will replace the placeholders in your <code>.docx</code> template.</p>
            
            <form action="{{ route('registrar.documents.generate', [$requestId, $documentTypeId]) }}" method="POST">
                @csrf
                <div class="row">
                    @foreach($data as $key => $value)
                        @if(!is_array($value))
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ str_replace('_', ' ', strtoupper($key)) }}</label>
                                <input type="text" name="{{ $key }}" class="form-control" value="{{ $value }}">
                                <small class="text-muted">Placeholder: {<span>{{ $key }}</span>}</small>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="mt-4 border-top pt-3 d-flex justify-content-between">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-file-earmark-word me-1"></i> Generate & Download
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .form-control {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 10px 12px;
    }
    .form-control:focus {
        border-color: #1B6B3A;
        box-shadow: 0 0 0 0.25rem rgba(27, 107, 58, 0.25);
    }
    .form-label {
        font-weight: 600;
        color: #495057;
        font-size: 0.85rem;
    }
</style>
@endsection
