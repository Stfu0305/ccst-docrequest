@extends('layouts.registrar')

@section('title', 'Create Walk-In Request')

@section('content')

<div class="registrar-sticky-header">CREATE REQUEST FOR {{ strtoupper($student->full_name) }}</div>

<div class="ccst-card">
    <div class="ccst-card-header blue">
        <i class="bi bi-file-earmark-text me-2"></i> Select Documents
    </div>
    <div class="ccst-card-body">
        
        @if($errors->any())
            <div style="padding: 15px; background: #F8D7DA; color: #721C24; border-radius: 8px; margin-bottom: 20px; font-size: 0.85rem;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('registrar.walkin.request') }}" id="createRequestForm">
            @csrf
            <input type="hidden" name="student_id" value="{{ $student->id }}">

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px; margin-bottom: 24px;">
                @foreach($documentTypes as $doc)
                <label class="doc-card" style="display: block; border: 1px solid #D0DDD0; border-radius: 8px; padding: 15px; cursor: pointer; transition: all 0.2s;">
                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <input type="checkbox" name="documents[]" value="{{ $doc->id }}" class="doc-checkbox" data-fee="{{ $doc->fee }}" style="margin-top: 4px; transform: scale(1.2);">
                        <div style="flex: 1;">
                            <div style="font-weight: 700; color: #1B6B3A; font-size: 0.9rem; margin-bottom: 4px;">{{ $doc->name }}</div>
                            <div style="font-size: 0.8rem; color: #666; margin-bottom: 8px;">₱{{ number_format($doc->fee, 2) }} per copy</div>
                            
                            <div class="copies-wrapper" style="display: none; align-items: center; gap: 10px; margin-top: 10px;">
                                <span style="font-size: 0.8rem; color: #555;">Copies:</span>
                                <input type="number" name="copies[{{ $doc->id }}]" value="1" min="1" max="10" class="copy-input"
                                       style="width: 60px; padding: 4px 8px; border: 1px solid #D0DDD0; border-radius: 4px; text-align: center;">
                            </div>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>

            <div style="background: #F0F7F0; border: 1px solid #D0DDD0; border-radius: 8px; padding: 20px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 0.85rem; color: #555; font-weight: 600;">Total Amount</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: #1B6B3A;" id="totalFeeDisplay">₱0.00</div>
                </div>
                <button type="submit" class="btn-action" style="background: #1B6B3A; padding: 12px 24px; font-size: 1rem;">
                    Proceed to Payment <i class="bi bi-arrow-right ms-1"></i>
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

    .doc-card:hover {
        border-color: #1B6B3A !important;
        background: #f8fbf8;
    }
    .doc-card.selected {
        border-color: #1B6B3A !important;
        background: #F0F7F0;
        box-shadow: 0 2px 8px rgba(27, 107, 58, 0.1);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.doc-checkbox');
        const copyInputs = document.querySelectorAll('.copy-input');
        const totalDisplay = document.getElementById('totalFeeDisplay');

        function updateTotal() {
            let total = 0;
            checkboxes.forEach(cb => {
                const card = cb.closest('.doc-card');
                const copiesWrapper = card.querySelector('.copies-wrapper');
                
                if (cb.checked) {
                    card.classList.add('selected');
                    copiesWrapper.style.display = 'flex';
                    
                    const fee = parseFloat(cb.dataset.fee);
                    const copyInput = card.querySelector('.copy-input');
                    const copies = parseInt(copyInput.value) || 1;
                    
                    total += (fee * copies);
                } else {
                    card.classList.remove('selected');
                    copiesWrapper.style.display = 'none';
                }
            });
            
            totalDisplay.textContent = '₱' + total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateTotal);
        });

        copyInputs.forEach(input => {
            input.addEventListener('input', updateTotal);
        });
        
        // Form validation
        document.getElementById('createRequestForm').addEventListener('submit', function(e) {
            let checkedCount = 0;
            checkboxes.forEach(cb => {
                if(cb.checked) checkedCount++;
            });
            
            if (checkedCount === 0) {
                e.preventDefault();
                Swal.fire('Required', 'Please select at least one document.', 'warning');
            }
        });
    });
</script>
@endpush
