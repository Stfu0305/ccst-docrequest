<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registration Form - {{ $student_name }}</title>
    <style>
        @page {
            margin: 2cm;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1B6B3A;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #1B6B3A;
            font-size: 16px;
            margin: 0 0 5px;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 12px;
            margin: 0;
            font-weight: normal;
        }
        .title {
            text-align: center;
            margin: 15px 0;
            background: #f4f4f4;
            padding: 5px;
        }
        .title h3 {
            font-size: 14px;
            text-transform: uppercase;
            margin: 0;
        }
        .student-info {
            width: 100%;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 10px;
        }
        .student-info td {
            padding: 3px;
        }
        .subjects-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .subjects-table th, .subjects-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .subjects-table th {
            background-color: #f8f9fa;
            color: #1B6B3A;
        }
        .signature-area {
            margin-top: 40px;
        }
        .signature-box {
            text-align: center;
            width: 30%;
            display: inline-block;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 25px;
            padding-top: 5px;
        }
        .footer {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
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
        <h3>CERTIFICATE OF REGISTRATION</h3>
    </div>

    <div class="student-info">
        <table style="width: 100%;">
            <tr>
                <td style="width: 15%;"><strong>Name:</strong></td>
                <td style="width: 35%;">{{ $student_name }}</td>
                <td style="width: 15%;"><strong>Student No:</strong></td>
                <td style="width: 35%;">{{ $student_number }}</td>
            </tr>
            <tr>
                <td><strong>Course/Strand:</strong></td>
                <td>{{ $strand }}</td>
                <td><strong>Year/Grade:</strong></td>
                <td>{{ $grade_level }}</td>
            </tr>
            <tr>
                <td><strong>Semester:</strong></td>
                <td>{{ $semester }}</td>
                <td><strong>School Year:</strong></td>
                <td>{{ $school_year }}</td>
            </tr>
        </table>
    </div>

    <div class="content">
        <p><strong>List of Enrolled Subjects:</strong></p>
        <table class="subjects-table">
            <thead>
                <tr>
                    <th>Subject Code</th>
                    <th>Description</th>
                    <th>Units/Hours</th>
                    <th>Schedule</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>SUBJ-001</td>
                    <td>Example Subject 1</td>
                    <td>3.0</td>
                    <td>MWF 08:00 - 09:00</td>
                </tr>
                <tr>
                    <td>SUBJ-002</td>
                    <td>Example Subject 2</td>
                    <td>3.0</td>
                    <td>TTH 09:30 - 11:00</td>
                </tr>
                <!-- More subjects would be dynamically populated -->
            </tbody>
        </table>
    </div>

    <div class="signature-area">
        <div class="signature-box" style="float: left;">
            <div class="signature-line"></div>
            <div>Student Signature</div>
        </div>
        <div class="signature-box" style="margin-left: 5%; margin-right: 5%;">
            <div class="signature-line"></div>
            <div>{{ $registrar_name }}</div>
            <div style="font-size: 9px;">Registrar</div>
        </div>
        <div class="signature-box" style="float: right;">
            <div class="signature-line"></div>
            <div>Date Issued: {{ $current_date }}</div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="footer">
        <p>{{ $footer_text }}</p>
        <p style="margin-top: 5px;">Reference No: {{ $reference_number }} | Printed via CCST DocRequest</p>
    </div>
</body>
</html>
