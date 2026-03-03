<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration Approved</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap');
        body { margin: 0; padding: 0; font-family: 'Inter', Arial, sans-serif; background-color: #131c2e; color: #fafafa; line-height: 1.6; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 24px; background-color: #131c2e; }
        .card { background: linear-gradient(145deg, #1a2744 0%, #151d32 100%); border: 1px solid rgba(201, 162, 39, 0.25); border-radius: 16px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,0.3); }
        .header { background: linear-gradient(135deg, #b8860b 0%, #c9a227 50%, #b8860b 100%); padding: 32px 24px; text-align: center; }
        .header h1 { margin: 0; font-family: 'Playfair Display', Georgia, serif; font-size: 24px; font-weight: 700; color: #131c2e; }
        .header p { margin: 8px 0 0; font-size: 15px; color: #131c2e; opacity: 0.95; }
        .content { padding: 28px 24px; }
        .content p { margin: 0 0 14px; color: #c5d0e0; font-size: 15px; }
        .greeting { font-size: 16px; color: #fafafa; margin-bottom: 16px !important; }
        .event-details { background: rgba(201, 162, 39, 0.08); border: 1px solid rgba(201, 162, 39, 0.2); border-radius: 12px; padding: 20px; margin: 20px 0; }
        .event-details h3 { margin: 0 0 12px; font-size: 16px; font-weight: 600; color: #c9a227; }
        .event-details ul { margin: 0; padding-left: 20px; color: #c5d0e0; font-size: 14px; }
        .event-details li { margin-bottom: 6px; }
        .status-badge { display: inline-block; background: linear-gradient(135deg, #10b981, #059669); color: #fff; font-size: 13px; font-weight: 600; padding: 4px 12px; border-radius: 20px; margin-top: 4px; }
        .btn-download { display: inline-block; background: linear-gradient(135deg, #c9a227, #b8860b); color: #131c2e; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; margin: 16px 0; }
        .footer { text-align: center; padding: 20px 24px; border-top: 1px solid rgba(255,255,255,0.06); color: #94a3b8; font-size: 12px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <h1>{{ $event->title }}</h1>
                <p>Registration Approved</p>
            </div>
            <div class="content">
                <p class="greeting">Dear {{ $user->first_name }} {{ $user->last_name }},</p>
                <p>Your registration has been <strong>approved</strong>. We look forward to seeing you at the event.</p>

                <div class="event-details">
                    <h3>Event details</h3>
                    <ul>
                        <li><strong>Event:</strong> {{ $event->title }}</li>
                        {{-- <li><strong>Date:</strong> {{ \Carbon\Carbon::parse($event->start_date)->format('F j, Y') }}</li> --}}
                        <li><strong>Date:</strong> May 2, 2026</li>
                        @if($event->venue_name)
                        <li><strong>Venue:</strong> {{ $event->venue_name }}</li>
                        @endif
                        <li><strong>Status:</strong> <span class="status-badge">Approved</span></li>
                    </ul>
                </div>

                <p>You can download your ticket using the button below:</p>
                <div style="text-align: center;">
                    <a href="{{ $downloadUrl }}" class="btn-download">Download your ticket</a>
                </div>
                <p style="font-size: 13px; color: #94a3b8;">If the button does not work, copy and paste this link into your browser:<br><span style="word-break: break-all;">{{ $downloadUrl }}</span></p>

                <p style="margin-top: 20px;">Thank you for registering. If you have any questions, please contact us.</p>
                <p>The {{ $event->title }} Team</p>
            </div>
            <div class="footer">
                <p>This is an automated message. Please do not reply directly to this email.</p>
                <p style="margin-top: 10px;">© {{ date('Y') }} {{ $event->title }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
