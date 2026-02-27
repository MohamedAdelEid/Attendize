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
            width: 100%;
            min-height: 100%;
            background: transparent;
        }
        .ticket-page {
            width: 100%;
            min-height: 100%;
            page-break-after: avoid;
            page-break-inside: avoid;
        }
        .ticket-page img {
            display: block;
            width: 100%;
            height: auto;
            max-width: 100%;
            vertical-align: top;
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