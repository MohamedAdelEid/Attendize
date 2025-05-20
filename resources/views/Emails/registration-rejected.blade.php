<!DOCTYPE html>
<html>
<head>
    <title>Registration Not Approved</title>
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
            background-color: #f5f5f5;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .content {
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        .reason {
            background-color: #f9f9f9;
            padding: 15px;
            border-left: 4px solid #ccc;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Registration Not Approved</h1>
    </div>
    
    <div class="content">
        <p>Dear {{ $registrationUser->first_name }} {{ $registrationUser->last_name }},</p>
        
        <p>We regret to inform you that your registration for <strong>{{ $event->name }}</strong> has not been approved.</p>
        
        <p>If you have any questions or believe this is an error, please contact the event organizers directly.</p>
        
        <p>Best regards,<br>
        The {{ $event->name }} Team</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html>
