@extends('layouts.registrar')

@section('title', 'Walk-In Payment')

@section('content')

<div class="registrar-sticky-header">COLLECT PAYMENT - {{ $docRequest->reference_number }}</div>

<div class="ccst-card" style="max-width: 800px; margin: 0 auto;">
    <div class="ccst-card-header green">
        <i class="bi bi-cash-stack me-2"></i> Payment Summary
    </div>
    <div class="ccst-card-body">
        
        <div style="background: #f8fbf8; border: 1px solid #D0DDD0; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div>
                    <div style="font-size: 0.8rem; color: #888;">Student Name</div>
                    <div style="font-weight: 600; color: #333;">{{ $docRequest->full_name }}</div>
                </div>
                <div>
                    <div style="font-size: 0.8rem; color: #888;">Student Number</div>
                    <div style="font-weight: 600; color: #333;">{{ $docRequest->student_number }}</div>
                </div>
            </div>

            <div style="font-size: 0.85rem; font-weight: 700; color: #1B6B3A; margin-bottom: 10px; border-bottom: 1px solid #D0DDD0; padding-bottom: 5px;">Requested Documents</div>
            
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; margin-bottom: 20px;">
                @foreach($docRequest->items as $item)
                <tr>
                    <td style="padding: 5px 0;">{{ $item->copies }}x {{ $item->documentType->name }}</td>
                    <td style="padding: 5px 0; text-align: right;">₱{{ number_format($item->fee * $item->copies, 2) }}</td>
                </tr>
                @endforeach
                <tr style="border-top: 1px solid #D0DDD0; font-weight: 700; font-size: 1.1rem; color: #1B6B3A;">
                    <td style="padding: 10px 0;">TOTAL AMOUNT TO COLLECT</td>
                    <td style="padding: 10px 0; text-align: right;">₱{{ number_format($docRequest->total_fee, 2) }}</td>
                </tr>
            </table>
        </div>

        @if($errors->any())
            <div style="padding: 15px; background: #F8D7DA; color: #721C24; border-radius: 8px; margin-bottom: 20px; font-size: 0.85rem;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('registrar.walkin.payment.complete', $docRequest->id) }}">
            @csrf
            @method('PATCH')
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #555; margin-bottom: 5px;">Receipt Number <span style="color:#DC3545">*</span></label>
                <input type="text" name="receipt_number" required placeholder="e.g. OR-2024-001" 
                       style="width: 100%; padding: 12px; border: 1px solid #D0DDD0; border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 1rem;">
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #555; margin-bottom: 5px;">Cashier / Received By</label>
                <input type="text" name="cashier_name" value="{{ Auth::user()->full_name }}" placeholder="Your Name" 
                       style="width: 100%; padding: 12px; border: 1px solid #D0DDD0; border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 1rem;">
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f0f0f0; padding-top: 20px;">
                <a href="{{ route('registrar.requests.show', $docRequest->id) }}" style="color: #666; text-decoration: none; font-size: 0.85rem;">
                    Skip Payment for Now (Leave Pending)
                </a>
                <button type="submit" class="btn-action" style="background: #1B6B3A; padding: 12px 24px; font-size: 1rem;">
                    <i class="bi bi-check-circle me-1"></i> Confirm Payment & Start Processing
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
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    .btn-action:hover { opacity: 0.85; }
</style>
@endpush
