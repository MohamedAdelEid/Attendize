<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->title }} — Your Ticket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            body { background: #fff !important; }
            .no-print { display: none !important; }
            .ticket-wrap { padding: 0 !important; box-shadow: none !important; }
            .ticket-wrap img { max-height: none !important; }
        }
    </style>
</head>
<body class="min-h-screen bg-slate-100 py-6 px-4 print:py-0 print:px-0 print:bg-white">
    <div class="max-w-4xl mx-auto">
        <div class="no-print flex flex-wrap items-center justify-between gap-3 mb-4">
            <h1 class="text-lg font-semibold text-slate-800">{{ $event->title }} — Ticket</h1>
            <div class="flex items-center gap-2">
                <button type="button" onclick="window.print();" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-white bg-emerald-600 hover:bg-emerald-700 transition-colors">
                    <i class="fas fa-print"></i>
                    Print Ticket
                </button>
                <a href="{{ route('downloadTicket', ['token' => $user->ticket_token]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">
                    <i class="fas fa-download"></i>
                    Download PDF
                </a>
            </div>
        </div>
        <div class="ticket-wrap bg-white rounded-xl shadow-lg overflow-hidden print:rounded-none print:shadow-none">
            <img src="{{ asset('storage/' . $ticket_image_path) }}" alt="Your Ticket" class="w-full h-auto max-h-[90vh] object-contain print:max-h-none">
        </div>
    </div>
</body>
</html>
