<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            width: 100%;
            height: 100vh; /* Full height */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #dac681; /* Background color */
        }
        .cover-image {
            margin-top:80px;
            width: 100%; /* Cover the full width */
            height: auto; /* Maintain aspect ratio */
            flex-grow: 1; /* Fill remaining space */
        }
    </style>
</head>
<body>

	 <img src="{{ storage_path('app/public/' . $ticket_image) }}" class="cover-image" alt="Cover Image">
</body>
</html>