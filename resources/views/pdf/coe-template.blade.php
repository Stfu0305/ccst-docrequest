<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Certificate of Enrollment - {{ $student_name }}</title>
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
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 200px;
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
        <h2>Senior High School</h2>
        <p>Dau, Mabalacat City, Pampanga</p>
    </div>

    <div class="title">
        <h3>CERTIFICATE OF ENROLLMENT</h3>
    </div>

    <div class="content">
        <p style="text-align: justify; margin-bottom: 20px;">
            This is to certify that <strong>{{ $student_name }}</strong> is officially enrolled as a student of 
            Clark College of Science and Technology - Senior High School for the School Year 
            <strong>{{ $school_year }}</strong>.
        </p>

        <div class="info-row">
            <span class="info-label">Student Number:</span>
            <span>{{ $student_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Strand:</span>
            <span>{{ $strand }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Grade Level:</span>
            <span>{{ $grade_level }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Section:</span>
            <span>{{ $section }}</span>
        </div>

        <p style="text-align: justify; margin-top: 20px;">
            This certification is issued upon the request of the above-named student for 
            <strong>{{ $purpose }}</strong>.
        </p>
    </div>

    <div class="signature-area">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div>Registrar</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div>Date Issued: {{ $current_date }}</div>
        </div>
    </div>

    <div class="footer">
        <p>{{ $footer_text }}</p>
    </div>
</body>
</html>