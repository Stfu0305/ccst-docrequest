<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Transcript of Records - {{ $student_name }}</title>
    <style>
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: 'Times New Roman', serif;
            font-size: 10px;
            line-height: 1.3;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0 0 5px;
            text-transform: uppercase;
            font-family: serif;
        }
        .header h2 {
            font-size: 12px;
            margin: 0;
            font-weight: normal;
        }
        .header p {
            font-size: 10px;
            margin: 2px 0;
        }
        .title {
            text-align: center;
            margin: 10px 0;
        }
        .title h3 {
            font-size: 14px;
            text-transform: uppercase;
            margin: 0;
            text-decoration: underline;
        }
        .student-info {
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }
        .student-info td {
            padding: 2px;
        }
        .transcript-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .transcript-table th, .transcript-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }
        .transcript-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .transcript-table .subject-name {
            text-align: left;
        }
        .semester-header {
            background-color: #e9e9e9;
            text-align: left !important;
            font-weight: bold;
            padding: 5px !important;
        }
        .grading-system {
            margin-top: 20px;
            font-size: 8px;
            width: 100%;
        }
        .signature-area {
            margin-top: 50px;
            text-align: right;
        }
        .signature-box {
            display: inline-block;
            text-align: center;
            width: 250px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 40px;
            padding-top: 5px;
        }
        .footer {
            position: absolute;
            bottom: 15px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CLARK COLLEGE OF SCIENCE AND TECHNOLOGY</h1>
        <h2>OFFICE OF THE REGISTRAR</h2>
        <p>Dau, Mabalacat City, Pampanga, Philippines</p>
        <p>Official Transcript of Records</p>
    </div>

    <table class="student-info">
        <tr>
            <td style="width: 15%;"><strong>STUDENT NAME:</strong></td>
            <td style="width: 45%;"><strong>{{ strtoupper($student_name) }}</strong></td>
            <td style="width: 15%;"><strong>STUDENT NO:</strong></td>
            <td style="width: 25%;">{{ $student_number }}</td>
        </tr>
        <tr>
            <td><strong>COURSE:</strong></td>
            <td>{{ strtoupper($strand) }}</td>
            <td><strong>DATE OF BIRTH:</strong></td>
            <td>-- / -- / ----</td>
        </tr>
        <tr>
            <td><strong>ENTRANCE DATA:</strong></td>
            <td colspan="3">High School Graduate</td>
        </tr>
    </table>

    <table class="transcript-table">
        <thead>
            <tr>
                <th style="width: 15%;">COURSE CODE</th>
                <th style="width: 55%;">DESCRIPTIVE TITLE</th>
                <th style="width: 10%;">GRADE</th>
                <th style="width: 10%;">UNITS</th>
                <th style="width: 10%;">REMARKS</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="5" class="semester-header">FIRST SEMESTER, SY {{ $school_year }}</td>
            </tr>
            @foreach($grades['subjects'] as $subject)
            <tr>
                <td>{{ $subject['code'] ?? 'SUBJ-' . $loop->iteration }}</td>
                <td class="subject-name">{{ $subject['name'] }}</td>
                <td>{{ $subject['grade'] }}</td>
                <td>3.0</td>
                <td>{{ $subject['remarks'] }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="5" style="text-align: center; font-style: italic;">(Continued on next page if applicable)</td>
            </tr>
        </tbody>
    </table>

    <div class="grading-system">
        <strong>GRADING SYSTEM:</strong> 1.0 (97-100) Excellent; 1.25 (94-96) Very Good; 1.5 (91-93) Very Good; 1.75 (88-90) Good; 2.0 (85-87) Good; 
        2.25 (82-84) Satisfactory; 2.5 (79-81) Satisfactory; 2.75 (76-78) Fair; 3.0 (75) Passed; 5.0 (Below 75) Failed; INC - Incomplete; DR - Dropped
    </div>

    <div class="signature-area">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div><strong>{{ strtoupper($registrar_name) }}</strong></div>
            <div>School Registrar</div>
        </div>
    </div>

    <div class="footer">
        <p>NOT VALID WITHOUT SCHOOL SEAL | Reference No: {{ $reference_number }} | Date Issued: {{ $current_date }}</p>
        <p>Page 1 of 1</p>
    </div>
</body>
</html>
