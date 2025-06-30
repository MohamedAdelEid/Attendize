<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $event->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $event->title }}</h1>
    </div>

    <div class="content">
        <p>Dear {{ $user->first_name }} {{ $user->last_name }},</p>

        <div style="white-space: pre-line;">{{ $emailBody }}</div>

        <p>Best regards,<br>
        {{ $event->title }} Team</p>
    </div>

    <div class="footer">
        <p>This email was sent regarding your registration for {{ $event->title }}.</p>
        <p>If you have any questions, please contact the event organizers.</p>
    </div>
</body>
</html>
