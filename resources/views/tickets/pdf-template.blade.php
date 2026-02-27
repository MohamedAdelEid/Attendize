<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ticket</title>
    <style>
        @page { margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            background: transparent;
        }
        .ticket-page {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        .ticket-page img {
            display: block;
            width: 100%;
            height: 100%;
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            object-position: top left;
        }
    </style>
</head>
<body>
@if($ticket_image)
    <div class="ticket-page">
        <img src="{{ storage_path('app/public/' . $ticket_image) }}" alt="Ticket">
    </div>
@endif
</body>
</html>