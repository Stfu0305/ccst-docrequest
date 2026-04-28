<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cashier List - {{ $date }}</title>
    <style>
        @page {
            margin: 1.5cm;
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
            font-size: 10px;
            color: #666;
            margin: 5px 0 0;
        }
        .info-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 8px 12px;
            background: #f5f5f5;
            border-radius: 6px;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background: #1B6B3A;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 9px;
            vertical-align: top;
        }
        .checkbox-col {
            width: 30px;
            text-align: center;
        }
        .checkbox-col span {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 1px solid #999;
            background: white;
        }
        .amount-col {
            text-align: right;
            font-weight: bold;
        }
        .student-name {
            font-weight: bold;
        }
        .document-list {
            font-size: 8px;
            color: #666;
            margin-top: 2px;
        }
        .summary {
            margin-top: 20px;
            padding: 10px;
            background: #f9f9f9;
            border-left: 3px solid #1B6B3A;
        }
        .summary p {
            margin: 3px 0;
            font-size: 10px;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 20px;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 30px;
            padding-top: 5px;
            font-size: 9px;
        }
        .footer {
            text-align: center;
            font-size: 8px;
            color: #999;
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CLARK COLLEGE OF SCIENCE AND TECHNOLOGY</h1>
        <h2>Cashier Payment Receipts</h2>
        <p>{{ $date }}</p>
    </div>

    <div class="info-bar">
        <span><strong>Printed By:</strong> {{ $printed_by }}</span>
        <span><strong>Generated:</strong> {{ now()->format('h:i A') }}</span>
        <span><strong>Total Students:</strong> {{ $totalStudents }}</span>
        <span><strong>Total Amount:</strong> ₱{{ number_format($totalAmount, 2) }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="checkbox-col">PAID</th>
                <th style="width: 12%">Time Slot</th>
                <th style="width: 25%">Student Name</th>
                <th style="width: 12%">Student No.</th>
                <th style="width: 15%">Reference No.</th>
                <th style="width: 12%">Documents</th>
                <th class="amount-col" style="width: 10%">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($todayAppointments as $appointment)
            <tr>
                <td class="checkbox-col"><span>☐</span></td>
                <td>{{ $appointment->time_slot }}</td>
                <td>
                    <div class="student-name">{{ $appointment->student_name }}</div>
                    <div class="document-list">{{ $appointment->strand }} | {{ $appointment->grade_section }}</div>
                </td>
                <td>{{ $appointment->student_number }}</td>
                <td>{{ $appointment->reference_number }}</td>
                <td>
                    <div style="font-size: 8px;">{{ $appointment->documents }}</div>
                </td>
                <td class="amount-col">₱{{ number_format($appointment->amount, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 30px;">
                    No appointments scheduled for today.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <p><strong>SUMMARY:</strong></p>
        <p>Total Students: {{ $totalStudents }}</p>
        <p>Total Collection: ₱{{ number_format($totalAmount, 2) }}</p>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div>Cashier Signature</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div>Date</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div>Registrar Signature</div>
        </div>
    </div>

    <div class="footer">
        <p>Instructions: Mark ☐ after collecting payment. Give the stamped receipt to student.</p>
        <p>© {{ date('Y') }} CCST Document Request and Tracking System</p>
    </div>
</body>
</html>