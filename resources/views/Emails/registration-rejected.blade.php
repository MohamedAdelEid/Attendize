<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration Not Approved</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        
        .card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        .header {
            background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }
        
        .header h1 {
            margin: 0;
            font-weight: 700;
            font-size: 24px;
            letter-spacing: -0.5px;
        }
        
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        
        .content {
            padding: 30px;
        }
        
        .content p {
            margin-bottom: 16px;
            color: #4b5563;
            font-size: 16px;
        }
        
        .event-details {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #7c3aed;
        }
        
        .event-details h3 {
            margin-top: 0;
            color: #111827;
            font-size: 18px;
            font-weight: 600;
        }
        
        .event-details ul {
            padding-left: 20px;
            margin-bottom: 0;
        }
        
        .event-details li {
            margin-bottom: 8px;
            color: #4b5563;
        }
        
        .button {
            display: inline-block;
            background-color: #7c3aed;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            margin-top: 10px;
            text-align: center;
        }
        
        .button.secondary {
            background-color: #f5f3ff;
            color: #6d28d9;
            border: 1px solid #ddd6fe;
            margin-left: 10px;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 14px;
            border-top: 1px solid #e5e7eb;
        }
        
        .social-links {
            margin-top: 15px;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 8px;
            color: #6b7280;
            text-decoration: none;
        }
        
        .status-badge {
            display: inline-block;
            background-color: #7c3aed;
            color: white;
            font-size: 14px;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .alternatives-box {
            background-color: #f5f3ff;
            border: 1px solid #ede9fe;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        
        .alternatives-box h3 {
            margin-top: 0;
            color: #5b21b6;
            font-size: 16px;
            font-weight: 600;
        }
        
        .alternatives-box ul {
            margin-bottom: 0;
            color: #6d28d9;
        }
        
        .alternatives-box ul li {
            color: #6b7280;
        }
        
        @media (max-width: 600px) {
            .container {
                padding: 10px;
            }
            .content {
                padding: 20px;
            }
            .header {
                padding: 20px;
            }
            .button, .button.secondary {
                display: block;
                margin: 10px 0 0 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>Registration Not Approved</h1>
                <p>Update regarding your event registration</p>
            </div>
            
            <div class="content">
                <p>Dear {{ $registrationUser->first_name }} {{ $registrationUser->last_name }},</p>
                
                <p>Thank you for your interest in <strong>{{ $event->title }}</strong>. After careful review, we regret to inform you that your registration could not be approved at this time.</p>
                
                <div class="event-details">
                    <h3>Registration Details</h3>
                    <ul>
                        <li><strong>Event:</strong> {{ $event->title }}</li>
                        <li><strong>Date:</strong> {{ date('F j, Y', strtotime($registrationUser->registration->start_date)) }}</li>
                        <li><strong>Status:</strong> <span class="status-badge">Not Approved</span></li>
                    </ul>
                </div>
                
                <p>There could be several reasons for this decision, including:</p>
                <ul>
                    <li>The event has reached maximum capacity</li>
                    <li>Eligibility criteria for the specific event category</li>
                    <li>Incomplete registration information</li>
                </ul>
                
                <div class="alternatives-box">
                    <h3>Alternative Options</h3>
                    <ul>
                        <li>Check out our other upcoming events that might interest you</li>
                        <li>Join our mailing list to be notified of future events</li>
                        <li>Contact our support team if you believe this was in error</li>
                    </ul>
                </div>
                
                <p>We appreciate your understanding and hope to see you at future events.</p>
                
                <div>
                    <a href="{{route('showEventPage', $event->id)}}" class="button">View Other Events</a>
                    <a href="#" class="button secondary">Contact Support</a>
                </div>
                
                <p style="margin-top: 25px;">Best regards,<br>
                The {{ $event->title }} Team</p>
            </div>
            
            <div class="footer">
                <p>This is an automated message. Please do not reply to this email.</p>
                <div class="social-links">
                    <a href="{{ $event->organiser->facebook }}">Facebook</a> • 
                    <a href="{{ $event->organiser->twitter }}">Twitter</a>
                </div>
                <p style="margin-top: 15px; font-size: 12px;">© {{ date('Y') }} {{ $event->title }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>