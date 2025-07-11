<!DOCTYPE html dir="rtl" >
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration Pending</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            direction: rtl;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
             direction: rtl;
        }
        
        .card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        .header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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
            border-left: 4px solid #f59e0b;
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
            background-color: #f59e0b;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            margin-top: 10px;
            text-align: center;
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
            background-color: #f59e0b;
            color: white;
            font-size: 14px;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .note-box {
            background-color: #fffbeb;
            border: 1px solid #fef3c7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            color: #92400e;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>تأكيد استلام طلب التسجيل  وقبول الدعوة</h1>
                <p>حفل اليوم الوطني للذكرى  73 لثورة يوليو 1952م</p>
            </div>
            
            <div class="content">
                <p>عزيزي {{ $registrationUser->first_name }} {{ $registrationUser->last_name }},</p>
                
                <p>نشكركم على قبول الدعوه  ونفيدكم  بأنه تم استلام طلب التسجيل الخاص بكم لحضور حفل اليوم الوطني  73 لذكرى ثورة 23 يوليو 1952م، ويجري حالياً مراجعته من قبل فريقنا المختص</p>
              
                <div class="event-details">
                    <h3>تفاصيل الفعالية</h3>
                    <ul>
                        <li><strong>الفعالية:</strong> {{ $event->title }}</li>
                        <li><strong>التاريخ:</strong> {{ date('F j, Y H:i', strtotime($event->start_date)) }}</li>
                       <li><strong>المكان:</strong> فندق انتركونتيننتال (قاعة السلطان) </li>
                        <li><strong>حالة الطلب:</strong> <span class="status-badge">قي المراجعة</span></li>
                    </ul>
                </div>
                
                <div class="note-box">
                    <p style="margin: 0;"><strong>ما الذي سيحدث لاحقًا؟</strong></p>
                    <p style="margin-top: 8px; margin-bottom: 0;">سيقوم فريقنا بمراجعة طلبكم بعناية، وستصلكم رسالة تأكيد عبر البريد الإلكتروني . علمًا أن هذه العملية تستغرق عادة من يوم إلى يومي عمل.</p>
                </div>
                

                
                <p style="margin-top: 25px;">شاكرين لكم اهتمامكم،,<br>
                وتفضلوا بقبول فائق الاحترام والتقدير.</p>
            </div>
            
            <div class="footer">
                <p>This is an automated message. Please do not reply to this email.</p>
                
                <p style="margin-top: 15px; font-size: 12px;">© {{ date('Y') }} Four-Links. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>