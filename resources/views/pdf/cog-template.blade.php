<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Certificate of Grades - {{ $student_name }}</title>
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
        }
        .title h3 {
            font-size: 14px;
            text-transform: uppercase;
            margin: 0;
            border: 1px solid #333;
            display: inline-block;
            padding: 5px 15px;
        }
        .student-info {
            width: 100%;
            margin-bottom: 20px;
        }
        .student-info td {
            padding: 2px;
        }
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .grades-table th, .grades-table td {
            border: 1px solid #333;
            padding: 6px;
            text-align: center;
        }
        .grades-table th {
            background-color: #f0f0f0;
        }
        .grades-table .subject-name {
            text-align: left;
        }
        .gpa-row {
            margin-top: 10px;
            text-align: right;
            font-weight: bold;
            font-size: 12px;
        }
        .signature-area {
            margin-top: 40px;
        }
        .signature-box {
            text-align: center;
            width: 45%;
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
        <h3>CERTIFICATE OF GRADES</h3>
    </div>

    <table class="student-info">
        <tr>
            <td style="width: 15%;"><strong>Name:</strong></td>
            <td style="width: 50%;">{{ $student_name }}</td>
            <td style="width: 15%;"><strong>Student No:</strong></td>
            <td style="width: 20%;">{{ $student_number }}</td>
        </tr>
        <tr>
            <td><strong>Course/Strand:</strong></td>
            <td>{{ $strand }}</td>
            <td><strong>Year Level:</strong></td>
            <td>{{ $grade_level }}</td>
        </tr>
        <tr>
            <td><strong>Semester:</strong></td>
            <td>{{ $semester }}</td>
            <td><strong>School Year:</strong></td>
            <td>{{ $school_year }}</td>
        </tr>
    </table>

    <table class="grades-table">
        <thead>
            <tr>
                <th style="width: 20%;">Subject Code</th>
                <th style="width: 50%;">Subject Description</th>
                <th style="width: 15%;">Grade</th>
                <th style="width: 15%;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grades['subjects'] as $subject)
            <tr>
                <td>{{ $subject['code'] ?? 'N/A' }}</td>
                <td class="subject-name">{{ $subject['name'] }}</td>
                <td>{{ $subject['grade'] }}</td>
                <td>{{ $subject['remarks'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="gpa-row">
        GENERAL WEIGHTED AVERAGE: {{ $general_average }}
    </div>

    <div class="signature-area">
        <div class="signature-box" style="float: left;">
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
        <p style="margin-top: 5px;">This is a computer-generated document. Reference No: {{ $reference_number }}</p>
    </div>
</body>
</html>
