<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your CCST Account Info</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            overflow: hidden;
            border: 1px solid #eaeaea;
        }
        .header {
            background-color: #1B6B3A;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
            text-align: left;
        }
        .content p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .qr-section {
            background-color: #f8faf9;
            border: 1px solid #e1e8e3;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .qr-section img {
            width: 200px;
            height: 200px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .qr-caption {
            font-size: 14px;
            color: #555;
            margin: 0;
        }
        .credentials {
            background: #fdfdfd;
            border: 1px solid #eee;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .credentials p {
            margin: 5px 0;
            font-size: 14px;
        }
        .footer {
            background-color: #f4f7f6;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to CCST Document Request System</h1>
        </div>
        <div class="content">
            <p>Hi <strong>{{ $user->first_name }}</strong>,</p>
            <p>Thank you for registering. Your account has been created and is currently pending verification by the registrar. Once verified, you will be able to log in and request documents.</p>
            
            <div class="qr-section">
                <img src="{{ $qrUrl }}" alt="Your Information QR Code">
                <p class="qr-caption">Scan this QR code with your phone's camera to securely view your account information and login credentials.</p>
            </div>

            <div class="credentials">
                <p><strong>Your Registration Details:</strong></p>
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Student No:</strong> {{ $user->student_number }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><em>For your security, your password is only available by scanning the QR code above.</em></p>
            </div>

            <p>If you have any questions, please visit the registrar's office.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Clark College of Science and Technology. All rights reserved.
        </div>
    </div>
</body>
</html>
