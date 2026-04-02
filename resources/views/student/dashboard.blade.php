@extends('layouts.student')

@section('title', 'Dashboard')

@section('content')

    {{-- ── Hero Section ── --}}
    <div class="dash-hero">

        <h1 class="dash-heading">
            SKIP THE LINE.<br>REQUEST ONLINE.
        </h1>

        <p class="dash-subtext">
            No more waiting in line. Request your school documents anytime, anywhere.
        </p>

        <a href="{{ route('student.requests.create') }}" class="btn-request-now">
            REQUEST NOW &rsaquo;
        </a>

    </div>

    {{-- ── Announcement + Transaction Days ── --}}
    <div class="dash-cards">

        {{-- Announcement --}}
        <div class="ccst-card">
            <div class="ccst-card-header">Announcement</div>
            <div class="ccst-card-body">
                @if($announcement && $announcement->is_published)
                    {!! nl2br(e($announcement->content)) !!}
                @else
                    <span class="text-muted fst-italic">No announcement currently published.</span>
                @endif
            </div>
        </div>

        {{-- Transaction Days --}}
        <div class="ccst-card">
            <div class="ccst-card-header outline">Transaction Days</div>
            <div class="ccst-card-body">
                @if($transactionDays && $transactionDays->is_published)
                    {!! nl2br(e($transactionDays->content)) !!}
                @else
                    <span class="text-muted fst-italic">No transaction day changes at this time.</span>
                @endif
            </div>
        </div>

    </div>

@endsection

@push('styles')
<style>
    /* ── Hero ── */
    .dash-hero {
        margin-bottom: 32px;    /* space below entire hero before the cards */
    }

    .dash-heading {
        font-family: 'Volkhov', sans-serif;
        font-weight: 650;
        font-size: 2.7rem;
        color: #1A1A1A;
        line-height: 1.2;
        text-transform: uppercase;
        margin-bottom: 23px;    /* space between heading and subtitle */
    }

    .dash-subtext {
        font-size: 1.1rem;
        color: #444;
        margin-bottom: 40px;    /* space between subtitle and REQUEST NOW button */
    }

    /* ── REQUEST NOW Button ── */
    .btn-request-now {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #1A9FE0;
        color: white;
        font-weight: 700;
        font-size: 1.0rem;
        padding: 16px 35px;     /* top/bottom: 12px | left/right: 25px */
        margin-top: 15px;        /* space above button */
        margin-bottom: 25px;     /* space below button — increase to add gap before cards */
        border-radius: 6px;
        text-decoration: none;
        letter-spacing: 0.3px;
        transition: background 0.2s;
    }

    .btn-request-now:hover {
        background: #0D7FBF;
        color: white;
    }

    /* ── Cards row ── */
    .dash-cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;              /* space between the two cards */
    }
</style>
@endpush