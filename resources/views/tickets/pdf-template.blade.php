<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ticket</title>
    <style>
        @page {
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
        }
        html, body {
            margin: 0;
            padding: 0;
            width: {{ $page_width_px ?? 397 }}px;
            height: {{ $page_height_px ?? 559 }}px;
        }
        .ticket-page {
            position: fixed;
            top: 0;
            left: 0;
            width: {{ $page_width_px ?? 397 }}px;
            height: {{ $page_height_px ?? 559 }}px;
            overflow: hidden;
        }
        .ticket-page img {
            display: block;
            width: {{ $page_width_px ?? 397 }}px;
            height: {{ $page_height_px ?? 559 }}px;
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
