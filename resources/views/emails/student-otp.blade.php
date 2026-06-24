<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your OTP Code</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            color: #333333;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            -ms-text-size-adjust: none;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #e1e8ed;
        }
        .header {
            background: linear-gradient(135deg, #5B2D8B 0%, #3d1e5e 100%);
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 40px 30px;
            line-height: 1.6;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 20px;
        }
        .message {
            font-size: 15px;
            color: #4a5568;
            margin-bottom: 30px;
        }
        .otp-container {
            background-color: #f7fafc;
            border: 2px dashed #5B2D8B;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 36px;
            font-weight: 800;
            color: #5B2D8B;
            letter-spacing: 6px;
            margin: 0;
        }
        .warning {
            font-size: 13px;
            color: #e53e3e;
            font-weight: 500;
            text-align: center;
            margin-top: 15px;
        }
        .footer {
            background-color: #f7fafc;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #a0aec0;
            border-top: 1px solid #edf2f7;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>EMM System</h1>
        </div>
        <div class="content">
            <div class="greeting">Hello {{ $studentName }},</div>
            <div class="message">
                We received a request to reset the password for your Student account. 
                Please use the One-Time Password (OTP) below to complete the reset process:
            </div>
            
            <div class="otp-container">
                <div class="otp-code">{{ $otp }}</div>
                <div class="warning">This OTP code is only valid for 15 minutes.</div>
            </div>
            
            <div class="message">
                If you did not request a password reset, you can safely ignore this email. Your password will remain unchanged.
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Event Merit Management System (EMMS). All rights reserved.</p>
            <p>This is an automated system email. Please do not reply directly to this message.</p>
        </div>
    </div>
</body>
</html>
