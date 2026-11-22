<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: #0174DF;
            color: #fff;
            text-align: center;
            padding: 20px;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 20px;
        }
        .email-body p {
            line-height: 1.6;
            margin: 10px 0;
        }
        .cta-button {
            display: inline-block;
            background: #0174DF;
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .cta-button:hover {
            background: #0056a3;
        }
        .email-footer {
            background: #f9f9f9;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        .email-footer a {
            color: #0174DF;
            text-decoration: none;
        }
        .email-footer a:hover {
            text-decoration: underline;
        }
    </style>
<link rel="stylesheet" href="{{ asset('css/tantrade-theme.css') }}">
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>Tanzania Trade Development Authority</h1>
            <p>Facilitating Trade for Sustainable Development</p>
        </div>

        <!-- Body -->
        <div class="email-body">
            <p>Dear {{ $name }},</p>
            <p style="text-align: justify">
                {{ $emailContent }}
            </p>
            
            @if ($emailLink!=Null)
            <a href="{{ $emailLink }}" class="cta-button" style="color:white">{{ $linkLable }}</a>
            @endif
            
            <p>
                Thank you for your continued trust in the Tanzania Trade Development Authority. 
                Should you have any questions, feel free to contact us.
            </p>
            <p style="color:red">
                <b>NB: </b> This is a system-generated email. Please do not reply to it.
            </p>
            <p>Best regards,<br><strong>{{ $regardsName }}</strong></p>
            
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p>
                Tanzania Trade Development Authority, Kilwa Road, Dar es Salaam<br>
                Phone: +255 733 002 014 | Email: <a href="mailto:info@tantrade.go.tz">info@tantrade.go.tz</a>
            </p>
            <p>
                Follow us on:
                <a href="https://facebook.com/tantradetz" target="_blank">Facebook</a> | 
                <a href="https://x.com/TanTradepage" target="_blank">Twitter</a> |
                <a href="https://www.instagram.com/tantradetz" target="_blank">Instagram</a> | 
                <a href="https://linkedin.com/company/tantrade" target="_blank">LinkedIn</a>
            </p>
            <p>
                Â© 2024 Tanzania Trade Development Authority. All Rights Reserved.
            </p>
        </div>
    </div>
</body>
</html>

