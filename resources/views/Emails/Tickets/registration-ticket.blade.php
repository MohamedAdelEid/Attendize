<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Event Ticket</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .ticket-container {
            width: 800px;
            /* Adjust as needed */
            height: 400px;
            /* Adjust as needed */
            margin: 20px auto;
            background-color: #fff;
            border: 1px solid #ddd;
            position: relative;
            /* For background image positioning */
            @if($event->ticketTemplate && $event->ticketTemplate->background_image_path)
            background-image: url('{{ Storage::url($event->ticketTemplate->background_image_path) }}');
            @endif
            background-size: cover;
            background-position: center;
            padding: 20px;
            box-sizing: border-box;
        }

        .event-title {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .user-details,
        .event-details {
            margin-bottom: 15px;
        }

        .user-details p,
        .event-details p {
            margin: 5px 0;
            font-size: 16px;
        }

        .qr-code {
            text-align: center;
            margin-top: 20px;
        }

        .qr-code img {
            width: 150px;
            /* Adjust as needed */
            height: 150px;
            /* Adjust as needed */
        }

        .unique-code {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
            color: #d9534f;
        }

        .user-name-placeholder {
            position: absolute;
            top: {{ $event->ticketTemplate->name_position_y ?? 'auto' }};
            left: {{ $event->ticketTemplate->name_position_x ?? 'auto' }};
            font-size: {{ $event->ticketTemplate->name_font_size ?? '16px' }};
            color: {{ $event->ticketTemplate->name_font_color ?? '#333' }};
        }
        .unique-code-placeholder {
            position: absolute;
            top: {{ $event->ticketTemplate->code_position_y ?? 'auto' }};
            left: {{ $event->ticketTemplate->code_position_x ?? 'auto' }};
            font-size: {{ $event->ticketTemplate->code_font_size ?? '20px' }};
            color: {{ $event->ticketTemplate->code_font_color ?? '#d9534f' }};
            font-weight: bold;
            text-align: center;
        }
        .qr-code-placeholder {
            position: absolute;
            top: {{ $event->ticketTemplate->qr_position_y ?? 'auto' }};
            left: {{ $event->ticketTemplate->qr_position_x ?? 'auto' }};
            text-align: center;
        }
        .qr-code-placeholder img {
            width: {{ $event->ticketTemplate->qr_size ?? '150px' }};
            height: {{ $event->ticketTemplate->qr_size ?? '150px' }};
        }
    </style>
</head>

<body>
    <div class="ticket-container">
        <div class="event-title">{{ $event->title }}</div>

        <div class="user-details user-name-placeholder">
            <p><strong>Attendee:</strong> {{ $registrationUser->first_name }} {{ $registrationUser->last_name }}</p>
        </div>

        <div class="event-details">
            <p><strong>Date:</strong> {{ $event->start_date->format(config('attendize.default_date_format')) }} -
                {{ $event->end_date->format(config('attendize.default_date_format')) }}</p>
            <p><strong>Location:</strong> {{ $event->venue_name_full }}</p>
        </div>

        <div class="unique-code unique-code-placeholder">
            Unique Code: {{ $registrationUser->unique_code }}
        </div>

        <div class="qr-code qr-code-placeholder">
            @if(isset($qrCodeUrl) && $qrCodeUrl)
                <img src="{{ $qrCodeUrl }}" alt="QR Code">
            @else
                <p>QR Code not available.</p>
            @endif
        </div>

        <!-- You can add more event details or organizer information here -->

    </div>
</body>

</html>