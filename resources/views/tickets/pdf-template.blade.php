<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ticket</title>
    <style>
        @page { margin: 0; size: 105mm 148mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            width: 105mm;
            height: 148mm;
            overflow: hidden;
            background: transparent;
        }
        .ticket-page {
            width: 105mm;
            height: 148mm;
            overflow: hidden;
            page-break-after: avoid;
            page-break-inside: avoid;
        }
        .ticket-page img {
            display: block;
            width: 100%;
            height: 100%;
            max-width: 105mm;
            max-height: 148mm;
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