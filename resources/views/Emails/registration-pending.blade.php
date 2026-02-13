<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration Received – Pending Review</title>
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
        .registration-details { background: rgba(255,255,255,0.04); border-radius: 12px; padding: 20px; margin: 20px 0; border: 1px solid rgba(255,255,255,0.08); }
        .registration-details h3 { margin: 0 0 12px; font-size: 16px; font-weight: 600; color: #c9a227; }
        .detail-row { padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.06); font-size: 14px; color: #c5d0e0; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { font-weight: 600; color: #e2e8f0; }
        .status-badge { display: inline-block; background: linear-gradient(135deg, #c9a227, #b8860b); color: #131c2e; font-size: 13px; font-weight: 600; padding: 4px 12px; border-radius: 20px; margin-top: 4px; }
        .note-box { background: rgba(201, 162, 39, 0.12); border: 1px solid rgba(201, 162, 39, 0.3); border-radius: 10px; padding: 16px; margin: 20px 0; }
        .note-box p { margin: 0; font-size: 14px; color: #e2e8f0; }
        .note-box strong { color: #c9a227; }
        .footer { text-align: center; padding: 20px 24px; border-top: 1px solid rgba(255,255,255,0.06); color: #94a3b8; font-size: 12px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <h1>Medicine & Judiciary Symposium</h1>
                <p>SGSS 2026 – Registration Received</p>
            </div>
            <div class="content">
                <p class="greeting">Dear {{ $registrationUser->first_name }} {{ $registrationUser->last_name }},</p>
                <p>Thank you for registering. We have received your registration and it is currently <strong>pending review</strong>.</p>
                <p><strong>We will get back to you soon.</strong> Our team will review your details and contact you via email with the next steps.</p>

                <div class="event-details">
                    <h3>Event details</h3>
                    <ul>
                        <li><strong>Event:</strong> {{ $event->title }}</li>
                        <li><strong>Date:</strong> May 2, 2026</li>
                        <li><strong>Venue:</strong> Crowne Plaza, Jeddah - Crystal Hall</li>
                        <li><strong>Status:</strong> <span class="status-badge">Pending</span></li>
                    </ul>
                </div>

                <div class="registration-details">
                    <h3>Your registration information</h3>
                    <div class="detail-row"><span class="detail-label">First name:</span> {{ $registrationUser->first_name }}</div>
                    <div class="detail-row"><span class="detail-label">Last name:</span> {{ $registrationUser->last_name }}</div>
                    <div class="detail-row"><span class="detail-label">Email:</span> {{ $registrationUser->email }}</div>
                    <div class="detail-row"><span class="detail-label">Phone:</span> {{ $registrationUser->phone ?: '—' }}</div>
                    @if($registrationUser->profession)
                    <div class="detail-row"><span class="detail-label">Profession:</span> {{ $registrationUser->profession->name }}</div>
                    @endif
                    @if($registrationUser->conference)
                    <div class="detail-row"><span class="detail-label">Conference:</span> {{ $registrationUser->conference->name }}</div>
                    @endif
                    @foreach($registrationUser->formFieldValues as $fv)
                        @if($fv->field && $fv->field->type !== 'external_payment' && $fv->field->type !== 'profession' && $fv->field->type !== 'conference')
                        <div class="detail-row">
                            <span class="detail-label">{{ $fv->field->label }}:</span>
                            @if($fv->field->type === 'file' || (is_string($fv->value) && (strpos($fv->value, 'form-uploads') !== false || strpos($fv->value, 'storage/') !== false)))
                                File uploaded
                            @else
                                {{ is_string($fv->value) ? $fv->value : (is_array($fv->value) ? implode(', ', $fv->value) : '—') }}
                            @endif
                        </div>
                        @endif
                    @endforeach
                </div>

                <div class="note-box">
                    <p><strong>What happens next?</strong></p>
                    <p>Your registration will be reviewed by our team. You will receive an email once it has been processed. If you have any questions in the meantime, please contact us.</p>
                </div>

                <p>Thank you for your interest. We look forward to being in touch soon.</p>
                <p>The {{ $event->title }} Team</p>
            </div>
            <div class="footer">
                <p>This is an automated message. Please do not reply directly to this email.</p>
                <p style="margin-top: 10px;">© {{ date('Y') }} SGSS. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
