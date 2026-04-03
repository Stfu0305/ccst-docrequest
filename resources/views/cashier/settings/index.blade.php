@extends('layouts.cashier')

@section('title', 'Payment Settings')

@section('content')

<div class="cashier-sticky-header">PAYMENT SETTINGS</div>

<div class="settings-grid">
    @foreach($settings as $setting)
    <div class="setting-card">
        <div class="setting-header">
            <div class="setting-title">
                @if($setting->method === 'gcash')
                    <i class="bi bi-phone"></i> GCash
                @elseif($setting->method === 'bdo')
                    <i class="bi bi-bank"></i> BDO
                @elseif($setting->method === 'bpi')
                    <i class="bi bi-bank"></i> BPI
                @else
                    <i class="bi bi-cash"></i> Cash
                @endif
            </div>
            <div class="setting-toggle">
                <label class="switch">
                    <input type="checkbox" class="toggle-method" data-id="{{ $setting->id }}" {{ $setting->is_active ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
                <span class="toggle-label {{ $setting->is_active ? 'active' : 'inactive' }}">
                    {{ $setting->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>
        <div class="setting-body">
            <form class="settings-form" data-id="{{ $setting->id }}">
                @csrf
                @method('PATCH')
                
                <div class="form-group">
                    <label>Account Name</label>
                    <input type="text" name="account_name" value="{{ $setting->account_name }}" class="form-input">
                </div>
                
                <div class="form-group">
                    <label>Account Number</label>
                    <input type="text" name="account_number" value="{{ $setting->account_number }}" class="form-input">
                </div>
                
                @if(in_array($setting->method, ['bdo', 'bpi']))
                <div class="form-group">
                    <label>Bank Name</label>
                    <input type="text" name="bank_name" value="{{ $setting->bank_name }}" class="form-input" readonly>
                </div>
                <div class="form-group">
                    <label>Branch</label>
                    <input type="text" name="branch" value="{{ $setting->branch }}" class="form-input" placeholder="Enter branch">
                </div>
                @endif
                
                @if($setting->method === 'cash')
                <div class="form-group">
                    <label>Office Location & Hours</label>
                    <textarea name="extra_info" rows="3" class="form-textarea">{{ $setting->extra_info }}</textarea>
                </div>
                @endif
                
                <button type="submit" class="save-settings-btn">
                    <i class="bi bi-save me-1"></i> Save Changes
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>

@endsection

@section('right-panel')
    <div class="rp-date-card">
        <div class="rp-date-day">{{ now()->format('d') }}</div>
        <div class="rp-date-month">{{ now()->format('F Y') }}</div>
        <div class="rp-date-time" id="live-time">--:-- --</div>
    </div>

    <div class="ccst-card mb-0">
        <div class="ccst-card-header yellow">Information</div>
        <div class="ccst-card-body">
            <div style="font-size: 0.8rem; color: rgba(255,255,255,0.9);">
                <p><i class="bi bi-info-circle me-2"></i> Toggle off a payment method to hide it from students on the Request Summary page.</p>
                <hr style="border-color: rgba(255,255,255,0.2);">
                <p><i class="bi bi-shield-check me-2"></i> Bank account details are visible to students only when active.</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .cashier-sticky-header {
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

    .settings-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }

    .setting-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .setting-header {
        background: #1B6B3A;
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .setting-title {
        font-size: 1rem;
        font-weight: 700;
    }

    .setting-title i {
        margin-right: 8px;
    }

    .setting-toggle {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .toggle-label {
        font-size: 0.7rem;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 20px;
    }

    .toggle-label.active {
        background: #D4EDDA;
        color: #155724;
    }

    .toggle-label.inactive {
        background: #F8D7DA;
        color: #721C24;
    }

    /* Toggle Switch */
    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: 0.3s;
        border-radius: 24px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: #1B6B3A;
    }

    input:checked + .slider:before {
        transform: translateX(26px);
    }

    .setting-body {
        padding: 20px;
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
        transition: border-color 0.2s;
    }

    .form-input:focus {
        outline: none;
        border-color: #1B6B3A;
    }

    .form-textarea {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #D0DDD0;
        border-radius: 6px;
        font-size: 0.8rem;
        font-family: 'Poppins', sans-serif;
        resize: vertical;
        min-height: 80px;
    }

    .save-settings-btn {
        width: 100%;
        background: #1A9FE0;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.8rem;
        cursor: pointer;
        transition: background 0.2s;
        margin-top: 10px;
    }

    .save-settings-btn:hover {
        background: #0D7FBF;
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

    .rp-date-day { font-size: 2.8rem; font-weight: 700; line-height: 1; }
    .rp-date-month { font-size: 0.85rem; opacity: 0.85; margin-top: 2px; }
    .rp-date-time { font-size: 1rem; font-weight: 600; margin-top: 6px; }
</style>
@endpush

@push('scripts')
<script>
    // Toggle payment method
    document.querySelectorAll('.toggle-method').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const id = this.dataset.id;
            const isChecked = this.checked;
            const label = this.closest('.setting-toggle').querySelector('.toggle-label');
            
            fetch(`/cashier/settings/${id}/toggle`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (data.is_active) {
                        label.textContent = 'Active';
                        label.classList.remove('inactive');
                        label.classList.add('active');
                    } else {
                        label.textContent = 'Inactive';
                        label.classList.remove('active');
                        label.classList.add('inactive');
                    }
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            })
            .catch(err => {
                console.error('Error:', err);
                this.checked = !isChecked;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update setting. Please try again.'
                });
            });
        });
    });

    // Save settings form
    document.querySelectorAll('.settings-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            fetch(`/cashier/settings/${id}`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin',
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: 'Settings updated successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            })
            .catch(err => {
                console.error('Error:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to save settings. Please try again.'
                });
            });
        });
    });

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