<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Event Ticket</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .ticket-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            position: relative;
        }
        .ticket-header {
            background-color: #0284c7;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .ticket-content {
            padding: 20px;
            position: relative;
        }
        .ticket-footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .attendee-info {
            margin-bottom: 20px;
        }
        .event-info {
            margin-bottom: 20px;
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
        .unique-code {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #0284c7;
            padding: 10px;
            background-color: #f0f9ff;
            border: 1px dashed #0284c7;
            display: inline-block;
            margin: 10px 0;
        }
        .ticket-image {
            width: 100%;
            height: auto;
        }
        .ticket-with-template {
            position: relative;
            width: 100%;
            height: 400px;
            overflow: hidden;
        }
        .ticket-with-template img {
            width: 100%;
            height: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #e5e7eb;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f9fafb;
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        @if(isset($ticket_image))
            <div class="ticket-with-template">
                <img src="{{ storage_path('app/public/' . $ticket_image) }}" alt="Ticket" class="ticket-image">
            </div>
        @else
            <div class="ticket-header">
                <h1>{{ $event->title }}</h1>
                <p>{{ $event->start_date->format('F j, Y') }} - {{ $event->end_date->format('F j, Y') }}</p>
            </div>

            <div class="ticket-content">
                <div class="attendee-info">
                    <h2>Attendee Information</h2>
                    <table>
                        <tr>
                            <th>Name</th>
                            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        @if($user->phone)
                        <tr>
                            <th>Phone</th>
                            <td>{{ $user->phone }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Registration Type</th>
                            <td>{{ $user->registration->name }}</td>
                        </tr>
                        @if($user->conference)
                        <tr>
                            <th>Conference</th>
                            <td>{{ $user->conference->name }}</td>
                        </tr>
                        @endif
                    </table>
                </div>

                <div class="event-info">
                    <h2>Event Details</h2>
                    <table>
                        <tr>
                            <th>Event</th>
                            <td>{{ $event->title }}</td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <td>{{ $event->start_date->format('F j, Y') }} - {{ $event->end_date->format('F j, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Time</th>
                            <td>{{ $event->start_date->format('g:i A') }} - {{ $event->end_date->format('g:i A') }}</td>
                        </tr>
                        @if($event->location)
                        <tr>
                            <th>Location</th>
                            <td>{{ $event->location }}</td>
                        </tr>
                        @endif
                    </table>
                </div>

                <div style="text-align: center;">
                    <h2>Registration Code</h2>
                    <div class="unique-code">{{ $user->unique_code }}</div>
                </div>

                <div class="qr-code">
                    <img src="{{ storage_path('app/public/' . $user->qr_code_path) }}" alt="QR Code" style="width: 200px; height: 200px;">
                </div>
            </div>

            <div class="ticket-footer">
                <p>Please present this ticket (printed or digital) at the event entrance.</p>
                <p>&copy; {{ date('Y') }} {{ $event->organiser->name }}. All rights reserved.</p>
            </div>
        @endif
    </div>
</body>
</html>
