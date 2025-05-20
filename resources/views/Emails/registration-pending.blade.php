<!DOCTYPE html>
<html>
<head>
    <title>Registration Pending</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Registration Pending Approval</h1>
    </div>
    
    <div class="content">
        <p>Dear {{ $registrationUser->first_name }} {{ $registrationUser->last_name }},</p>
        
        <p>Thank you for registering for <strong>{{ $event->name }}</strong>. Your registration is currently pending approval.</p>
        
        <p>Registration details:</p>
        <ul>
            <li><strong>Event:</strong> {{ $event->name }}</li>
            <li><strong>Date:</strong> {{ date('F j, Y', strtotime($registrationUser->registration->start_date)) }}</li>
            <li><strong>Status:</strong> Pending Approval</li>
        </ul>
        
        <p>You will receive another email once your registration has been reviewed and approved. If you have any questions, please contact the event organizers.</p>
        
        <p>Thank you for your patience.</p>
        
        <p>Best regards,<br>
        The {{ $event->name }} Team</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html>
