@extends('layouts.registrar')

@section('title', 'Manage Registrars')

@section('content')

<div class="registrar-sticky-header">MANAGE REGISTRARS</div>

<div class="pending-card">
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h5 class="mb-0" style="color: #1B6B3A; font-weight: 700;">Registrar Accounts</h5>
        </div>
        <div>
            <a href="{{ route('registrar.manage.create') }}" class="btn btn-sm" style="background-color: #1B6B3A; color: white;">
                <i class="bi bi-person-plus"></i> Create New Account
            </a>
        </div>
    </div>

    <div class="table-scroll-body">
        <table class="pending-table">
            <thead>
                <tr>
                    <th style="width: 30%">Name</th>
                    <th style="width: 35%">Email</th>
                    <th style="width: 15%">Role</th>
                    <th style="width: 20%">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registrars as $registrar)
                <tr>
                    <td class="fw-bold">{{ $registrar->full_name ?? $registrar->name }}</td>
                    <td>{{ $registrar->email }}</td>
                    <td>
                        @if($registrar->is_admin)
                            <span class="badge bg-primary">Admin</span>
                        @else
                            <span class="badge bg-secondary">Staff</span>
                        @endif
                    </td>
                    <td>
                        @if($registrar->id !== auth()->id())
                        <div class="action-buttons">
                            <form action="{{ route('registrar.manage.destroy', $registrar->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-reject" onclick="return confirm('Are you sure you want to delete this account? This cannot be undone.')">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                        @else
                            <span class="text-muted fst-italic">You</span>
                        @endif
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">No registrar accounts found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
        font-size: 0.8rem;
        font-weight: 700;
        color: #1B6B3A;
        text-align: left;
    }

    .pending-table td {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.85rem;
        vertical-align: middle;
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
</style>
@endpush
