<!DOCTYPE html>
<html>
<head>
    <title>Registration Approved</title>
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
        <h1>Registration Approved</h1>
    </div>
    
    <div class="content">
        <p>Dear {{ $registrationUser->first_name }} {{ $registrationUser->last_name }},</p>
        
        <p>Congratulations! Your registration for <strong>{{ $event->name }}</strong> has been approved.</p>
        
        <p>Registration details:</p>
        <ul>
            <li><strong>Event:</strong> {{ $event->name }}</li>
            <li><strong>Date:</strong> {{ date('F j, Y', strtotime($registrationUser->registration->start_date)) }}</li>
            <li><strong>Status:</strong> Approved</li>
        </ul>
        
        <p>We look forward to seeing you at the event. If you have any questions, please don't hesitate to contact us.</p>
        
        <p>Best regards,<br>
        The {{ $event->name }} Team</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html>
