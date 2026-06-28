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
            height: 100%;
            overflow: hidden;
        }
        .ticket-wrap {
            width: 100%;
            height: 100%;
            overflow: hidden;
            page-break-after: avoid;
            page-break-inside: avoid;
        }
        .ticket-wrap img {
            display: block;
            width: 100%;
            height: 100%;
            max-width: 100%;
            max-height: 100%;
            vertical-align: top;
        }
    </style>
</head>
<body>
@if($ticket_image)
<div class="ticket-wrap">
    <img src="{{ storage_path('app/public/' . $ticket_image) }}" alt="Ticket">
</div>
@endif
</body>
</html>
