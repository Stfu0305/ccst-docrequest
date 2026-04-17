@extends('layouts.cashier')

@section('title', 'Payment Settings')

@section('content')

<div class="cashier-sticky-header">PAYMENT SETTINGS</div>

{{-- Add New Payment Method Button --}}
<div class="add-method-section">
    <button type="button" class="btn-add-method" onclick="openAddMethodModal()">
        <i class="bi bi-plus-circle"></i> Add Payment Method
    </button>
</div>

<div class="settings-scroll">
    <div class="settings-grid">
        @foreach($settings as $setting)
        <div class="setting-card" data-id="{{ $setting->id }}">
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
                <div class="setting-actions">
                    @if(!in_array($setting->method, ['gcash', 'cash']))
                    <button type="button" class="btn-delete-method" onclick="deleteMethod({{ $setting->id }}, '{{ $setting->method }}')" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                    @endif
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
            </div>
            <div class="setting-body">
                <form class="settings-form" data-id="{{ $setting->id }}">
                    @csrf
                    @method('PATCH')
                    
                    <div class="form-group">
                        <label>Account Name <span class="required">*</span></label>
                        <input type="text" name="account_name" value="{{ $setting->account_name }}" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Account Number <span class="required">*</span></label>
                        <input type="text" name="account_number" value="{{ $setting->account_number }}" class="form-input" required>
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
                    
                    <div class="form-actions">
                        <button type="button" class="btn-edit-settings" onclick="editSettings(this)">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        <button type="submit" class="save-settings-btn" style="display:none;">
                            <i class="bi bi-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Add New Payment Method Modal --}}
<div id="addMethodModal" class="modal-overlay" style="display:none;">
    <div class="modal-container" style="max-width: 500px;">
        <div class="modal-header-custom">
            <h4><i class="bi bi-plus-circle me-2"></i>Add Payment Method</h4>
            <button type="button" class="modal-close" onclick="closeAddMethodModal()">&times;</button>
        </div>
        <form id="addMethodForm">
            @csrf
            <div class="modal-body-custom">
                <div class="form-group">
                    <label>Payment Method <span class="required">*</span></label>
                    <select name="method" id="newMethodType" class="form-input" required>
                        <option value="">Select payment method</option>
                        <option value="gcash">GCash</option>
                        <option value="bdo">BDO (Bank Transfer)</option>
                        <option value="bpi">BPI (Bank Transfer)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Account Name <span class="required">*</span></label>
                    <input type="text" name="account_name" id="newAccountName" class="form-input" placeholder="e.g., CCST Registrar" required>
                </div>
                
                <div class="form-group">
                    <label>Account Number <span class="required">*</span></label>
                    <input type="text" name="account_number" id="newAccountNumber" class="form-input" placeholder="e.g., 09XX-XXX-XXXX" required>
                </div>
                
                <div class="form-group bank-fields" style="display:none;">
                    <label>Bank Name</label>
                    <input type="text" name="bank_name" id="newBankName" class="form-input" placeholder="e.g., BDO Unibank">
                </div>
                
                <div class="form-group bank-fields" style="display:none;">
                    <label>Branch</label>
                    <input type="text" name="branch" id="newBranch" class="form-input" placeholder="e.g., Dau Branch">
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn-cancel-modal" onclick="closeAddMethodModal()">Cancel</button>
                <button type="submit" class="btn-save-modal">Add Payment Method</button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteConfirmModal" class="modal-overlay" style="display:none;">
    <div class="modal-container" style="max-width: 400px;">
        <div class="modal-header-custom" style="background:#DC3545;">
            <h4><i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete</h4>
            <button type="button" class="modal-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <div class="modal-body-custom">
            <p>Are you sure you want to delete <strong id="deleteMethodName"></strong>?</p>
            <p class="text-muted" style="font-size:0.75rem;">This action cannot be undone.</p>
        </div>
        <div class="modal-footer-custom">
            <button type="button" class="btn-cancel-modal" onclick="closeDeleteModal()">Cancel</button>
            <button type="button" class="btn-delete-confirm" id="confirmDeleteBtn">Delete Permanently</button>
        </div>
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
        <div class="ccst-card-header yellow">Information</div>
        <div class="ccst-card-body">
            <div style="font-size: 0.8rem; color: rgba(255,255,255,0.9);">
                <p><i class="bi bi-info-circle me-2"></i> Toggle off a payment method to hide it from students on the Request Summary page.</p>
                <hr style="border-color: rgba(255,255,255,0.2);">
                <p><i class="bi bi-shield-check me-2"></i> Bank account details are visible to students only when active.</p>
                <hr style="border-color: rgba(255,255,255,0.2);">
                <p><i class="bi bi-pencil me-2"></i> Click Edit to modify, then Save Changes.</p>
                <hr style="border-color: rgba(255,255,255,0.2);">
                <p><i class="bi bi-plus-circle me-2"></i> Add new payment methods as needed.</p>
                <hr style="border-color: rgba(255,255,255,0.2);">
                <p><i class="bi bi-trash me-2"></i> Delete unused payment methods (GCash and Cash are protected).</p>
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
        margin-bottom: 20px;
    }

    .add-method-section {
        margin-bottom: 20px;
        display: flex;
        justify-content: flex-end;
    }

    .btn-add-method {
        background: #1B6B3A;
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s;
    }

    .btn-add-method:hover {
        background: #0C5A2E;
    }

    .settings-scroll {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 4px;
        margin-bottom: 20px;
        max-height: calc(100vh - 200px);
    }

    .settings-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .settings-scroll::-webkit-scrollbar-track {
        background: #f0f0f0;
        border-radius: 3px;
    }

    .settings-scroll::-webkit-scrollbar-thumb {
        background: #1B6B3A;
        border-radius: 3px;
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

    .setting-actions {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .btn-delete-method {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }

    .btn-delete-method:hover {
        background: #DC3545;
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

    .required {
        color: #DC3545;
    }

    .form-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #D0DDD0;
        border-radius: 6px;
        font-size: 0.8rem;
        font-family: 'Poppins', sans-serif;
        background: #f8f9fa;
        transition: border-color 0.2s;
    }

    .form-input:focus {
        outline: none;
        border-color: #1B6B3A;
    }

    .form-input.editable {
        background: white;
        border-color: #1A9FE0;
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
        background: #f8f9fa;
    }

    .form-textarea.editable {
        background: white;
        border-color: #1A9FE0;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 15px;
    }

    .btn-edit-settings {
        background: #F5C518;
        color: #1A1A1A;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.8rem;
        cursor: pointer;
        transition: background 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-edit-settings:hover {
        background: #e6b800;
    }

    .save-settings-btn {
        background: #1B6B3A;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.8rem;
        cursor: pointer;
        transition: background 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .save-settings-btn:hover {
        background: #0C5A2E;
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1050;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-container {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }

    .modal-header-custom {
        background: #1B6B3A;
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header-custom h4 {
        margin: 0;
        font-size: 1rem;
    }

    .modal-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.3rem;
        cursor: pointer;
    }

    .modal-body-custom {
        padding: 20px;
    }

    .modal-footer-custom {
        padding: 15px 20px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        border-top: 1px solid #f0f0f0;
    }

    .btn-cancel-modal {
        background: #f0f0f0;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
    }

    .btn-save-modal {
        background: #1B6B3A;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
    }

    .btn-delete-confirm {
        background: #DC3545;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
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

    .main-content {
        overflow-y: hidden !important;
        display: flex;
        flex-direction: column;
        height: calc(100vh - var(--header-h) - var(--footer-h));
        padding-bottom: 20px;
    }

    .main-content > .cashier-sticky-header {
        flex-shrink: 0;
    }

    .main-content > .add-method-section {
        flex-shrink: 0;
    }

    .main-content > .settings-scroll {
        flex: 1;
        min-height: 0;
    }

    @media (max-width: 1000px) {
        .settings-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let deleteId = null;
    let deleteName = null;

    // Edit button functionality - enables fields and shows Save button
    function editSettings(btn) {
        const form = btn.closest('.settings-form');
        const inputs = form.querySelectorAll('.form-input, .form-textarea');
        const editBtn = form.querySelector('.btn-edit-settings');
        const saveBtn = form.querySelector('.save-settings-btn');
        
        // Enable all inputs and remove readonly style
        inputs.forEach(input => {
            input.disabled = false;
            input.classList.add('editable');
            // For textarea specifically
            if (input.tagName === 'TEXTAREA') {
                input.style.background = 'white';
            }
        });
        
        // Hide Edit button, show Save button
        editBtn.style.display = 'none';
        saveBtn.style.display = 'inline-flex';
    }

    // Reset form to read-only mode (called after successful save without reload)
    function resetToReadOnly(form) {
        const inputs = form.querySelectorAll('.form-input, .form-textarea');
        const editBtn = form.querySelector('.btn-edit-settings');
        const saveBtn = form.querySelector('.save-settings-btn');
        
        inputs.forEach(input => {
            input.disabled = true;
            input.classList.remove('editable');
            if (input.tagName === 'TEXTAREA') {
                input.style.background = '#f8f9fa';
            }
        });
        
        editBtn.style.display = 'inline-flex';
        saveBtn.style.display = 'none';
    }

    // Show/hide bank fields based on method selection
    document.getElementById('newMethodType')?.addEventListener('change', function() {
        const bankFields = document.querySelectorAll('.bank-fields');
        const isBank = this.value === 'bdo' || this.value === 'bpi';
        bankFields.forEach(field => {
            field.style.display = isBank ? 'block' : 'none';
        });
    });

    // Add new payment method
    document.getElementById('addMethodForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        
        fetch('/cashier/settings', {
            method: 'POST',
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
                    title: 'Added!',
                    text: 'Payment method added successfully.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Failed to add payment method.'
                });
            }
        })
        .catch(err => {
            console.error('Error:', err);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to add payment method.'
            });
        });
    });

    // Delete payment method
    function deleteMethod(id, name) {
        deleteId = id;
        deleteName = name;
        document.getElementById('deleteMethodName').textContent = name.toUpperCase();
        document.getElementById('deleteConfirmModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteConfirmModal').style.display = 'none';
        deleteId = null;
    }

    document.getElementById('confirmDeleteBtn')?.addEventListener('click', function() {
        if (!deleteId) return;
        
        fetch(`/cashier/settings/${deleteId}`, {
            method: 'DELETE',
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
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'Payment method removed successfully.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Cannot delete this payment method.'
                });
            }
        })
        .catch(err => {
            console.error('Error:', err);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to delete payment method.'
            });
        });
        closeDeleteModal();
    });

    function openAddMethodModal() {
        document.getElementById('addMethodModal').style.display = 'flex';
    }

    function closeAddMethodModal() {
        document.getElementById('addMethodModal').style.display = 'none';
        document.getElementById('addMethodForm').reset();
        document.querySelectorAll('.bank-fields').forEach(f => f.style.display = 'none');
    }

    // Toggle payment method
    document.querySelectorAll('.toggle-method').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const id = this.dataset.id;
            const isChecked = this.checked;
            const label = this.closest('.setting-actions').querySelector('.toggle-label');
            const card = this.closest('.setting-card');
            
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
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    // Refresh page after short delay to show updated state
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
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

    // Save settings form (Update) - without page reload
    document.querySelectorAll('.settings-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            // Show loading state
            const saveBtn = this.querySelector('.save-settings-btn');
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';
            saveBtn.disabled = true;
            
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
                    // Reset the form to read-only mode
                    resetToReadOnly(form);
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: 'Settings updated successfully.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to save settings.'
                    });
                    // Reset button
                    saveBtn.innerHTML = originalText;
                    saveBtn.disabled = false;
                }
            })
            .catch(err => {
                console.error('Error:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to save settings. Please try again.'
                });
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
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