<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP</title>
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background: #f1f5f9; margin: 0; padding: 0; }
        .wrapper { max-width: 520px; margin: 40px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 30px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 36px 40px; text-align: center; }
        .header .logo { font-size: 22px; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
        .header .logo span { color: #22c55e; }
        .body { padding: 40px; }
        .greeting { font-size: 15px; color: #334155; margin-bottom: 8px; }
        .message { font-size: 14px; color: #64748b; line-height: 1.6; margin-bottom: 32px; }
        .otp-box { background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 12px; text-align: center; padding: 28px 20px; margin-bottom: 28px; }
        .otp-label { font-size: 11px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: #94a3b8; margin-bottom: 12px; }
        .otp-code { font-size: 46px; font-weight: 900; letter-spacing: 0.18em; color: #0f172a; font-family: 'Courier New', monospace; }
        .otp-expiry { font-size: 12px; color: #94a3b8; margin-top: 10px; }
        .warning { background: #fef3c7; border: 1px solid #fde68a; border-radius: 8px; padding: 14px 16px; font-size: 12px; color: #92400e; line-height: 1.5; margin-bottom: 24px; }
        .footer { background: #f8fafc; padding: 24px 40px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { font-size: 11px; color: #94a3b8; margin: 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <div class="logo">Wit Expense<span>Tracker</span></div>
        </div>
        <div class="body">
            <p class="greeting">Hello, <strong>{{ $userName }}</strong> 👋</p>
            <p class="message">
                We received a request to reset your password. Use the OTP below to proceed.
                This code is valid for <strong>10 minutes</strong>.
            </p>

            <div class="otp-box">
                <div class="otp-label">Your One-Time Password</div>
                <div class="otp-code">{{ $otp }}</div>
                <div class="otp-expiry">⏱ Expires in 10 minutes</div>
            </div>

            <div class="warning">
                ⚠️ <strong>Never share this OTP with anyone.</strong>
                If you did not request a password reset, you can safely ignore this email.
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
