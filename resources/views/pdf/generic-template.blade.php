<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $document_name }} - {{ $student_name }}</title>
    <style>
        @page {
            margin: 2.5cm;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1B6B3A;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #1B6B3A;
            font-size: 18px;
            margin: 0 0 5px;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 14px;
            margin: 0;
            font-weight: normal;
        }
        .header p {
            font-size: 11px;
            color: #666;
            margin: 5px 0 0;
        }
        .title {
            text-align: center;
            margin: 30px 0;
        }
        .title h3 {
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 0;
        }
        .content {
            margin: 20px 0;
        }
        .info-row {
            margin-bottom: 12px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
            display: inline-block;
        }
        .signature-area {
            margin-top: 50px;
            position: relative;
        }
        .signature-box {
            text-align: center;
            width: 200px;
            display: inline-block;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 30px;
            padding-top: 5px;
        }
        .footer {
            position: absolute;
            bottom: 30px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CLARK COLLEGE OF SCIENCE AND TECHNOLOGY</h1>
        <h2>Office of the Registrar</h2>
        <p>Dau, Mabalacat City, Pampanga</p>
    </div>

    <div class="title">
        <h3>{{ strtoupper($document_name) }}</h3>
    </div>

    <div class="content">
        <p style="text-align: justify; margin-bottom: 20px;">
            This document serves as an official certification for <strong>{{ $student_name }}</strong> regarding their 
            requested document: <strong>{{ $document_name }}</strong>.
        </p>

        <div class="info-row">
            <span class="info-label">Student Number:</span>
            <span>{{ $student_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Strand/Program:</span>
            <span>{{ $strand }}</span>
        </div>
        @if(isset($grade_level))
        <div class="info-row">
            <span class="info-label">Grade/Year Level:</span>
            <span>{{ $grade_level }}</span>
        </div>
        @endif
        @if(isset($section))
        <div class="info-row">
            <span class="info-label">Section:</span>
            <span>{{ $section }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Reference No.:</span>
            <span>{{ $reference_number }}</span>
        </div>

        <p style="text-align: justify; margin-top: 20px;">
            This document is issued upon the request of the above-named student for 
            <strong>{{ $purpose ?? 'Official Purposes' }}</strong>.
        </p>
    </div>

    <div class="signature-area">
        <div class="signature-box" style="float: left;">
            <div class="signature-line"></div>
            <div>{{ $registrar_name }}</div>
            <div style="font-size: 10px; color: #666;">Registrar</div>
        </div>
        <div class="signature-box" style="float: right;">
            <div class="signature-line"></div>
            <div>Date Issued: {{ $current_date }}</div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="footer">
        <p>{{ $footer_text }}</p>
        <p style="margin-top: 5px;">CCST DocRequest System - Verified Document</p>
    </div>
</body>
</html>
