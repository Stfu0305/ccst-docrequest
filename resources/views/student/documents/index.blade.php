@extends('layouts.student')

@section('title', 'Available Documents')

@section('content')

    {{-- Heading --}}
    <div class="docs-heading-wrap">
        <h2 class="docs-heading">AVAILABLE DOCUMENTS</h2>
        <div class="docs-heading-line"></div>
    </div>

    {{-- Document cards grid --}}
    <div class="docs-grid">
        @foreach($documentTypes as $doc)
        <div class="doc-card">
            <div class="doc-icon-circle">
                @php
                    $docImages = [
                        'REG'  => 'registration-form.png',
                        'COG'  => 'certificate-of-grades.png',
                        'COE'  => 'certificate-of-enrollment.png',
                        'TOR'  => 'transcript-of-records.png',
                        'CGMC' => 'good-moral-certificate.png',
                    ];
                    $imgFile = $docImages[$doc->code] ?? 'document-icon.png';
                @endphp
                <img src="{{ asset('images/' . $imgFile) }}" alt="{{ $doc->name }}">
            </div>
            <div class="doc-name">
                {{ $doc->name }}
                <span class="doc-code">({{ $doc->code }})</span>
            </div>
        </div>
        @endforeach
    </div>

    {{-- REQUEST NOW button --}}
    <div class="docs-action">
        <a href="{{ route('student.requests.create') }}" class="btn-request-now">
            REQUEST NOW &rsaquo;
        </a>
    </div>

@endsection

@push('styles')
<style>
    .docs-heading-wrap {
        margin-bottom: 32px;
    }

    .docs-heading {
        font-family: 'Volkhov', serif;
        font-weight: 700;
        font-size: 1.8rem;
        color: #1A1A1A;
        text-transform: uppercase;
        margin-bottom: 10px;
        letter-spacing: 0.5px;
    }

    .docs-heading-line {
        width: 100%;
        height: 2px;
        background: #1A1A1A;
        border-radius: 2px;
    }

    /* 3-column grid — 5 cards wrap naturally into 3+2 */
    .docs-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 28px 32px;
        margin-bottom: 53px;
        max-width: 660px;
    }

    .doc-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }

    /* Amber circle matching mockup exactly */
    .doc-icon-circle {
        width: 125px;
        height: 125px;
        border-radius: 50%;
        background: linear-gradient(135deg, #F5A623, #E08A00);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 3px 10px rgba(245,166,35,0.35);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .doc-card:hover .doc-icon-circle {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(245,166,35,0.45);
    }

    .doc-icon-circle img {
        width: 75px;
        height: 75px;
        object-fit: contain;
        /* no filter — shows icon in its natural color */
    }

    .doc-name {
        text-align: center;
        font-size: 0.82rem;
        font-weight: 600;
        color: #1A1A1A;
        line-height: 1.4;
    }

    .doc-code {
        display: block;
        font-weight: 400;
        color: #666;
        font-size: 0.78rem;
    }

    .docs-action { margin-top: 4px; }

    .btn-request-now {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #1A9FE0;
        color: white;
        font-weight: 700;
        font-size: 1.0rem;
        padding: 16px 30px;
        border-radius: 6px;
        text-decoration: none;
        letter-spacing: 0.3px;
        transition: background 0.2s;
        margin-left: 24px;
    }

    .btn-request-now:hover { background: #0D7FBF; color: white; }
</style>
@endpush
