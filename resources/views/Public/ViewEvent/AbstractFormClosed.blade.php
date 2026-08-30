<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $abstract->name ?? 'Abstract' }} — {{ $event->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="h-full font-sans antialiased bg-gray-50">
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-xl px-4 mx-auto sm:px-6">
            <div class="flex items-center h-16 space-x-3">
                <div class="flex items-center justify-center w-10 h-10 bg-black rounded-lg">
                    <i class="text-white fas fa-file-alt"></i>
                </div>
                <div>
                    <h1 class="text-base font-bold text-gray-900">{{ $event->title }}</h1>
                    <p class="text-xs text-gray-500">Abstract submission</p>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-xl px-4 py-16 mx-auto text-center sm:px-6">
        <div class="p-8 bg-white border border-gray-200 shadow-sm rounded-xl sm:p-10">
            <div class="flex items-center justify-center w-14 h-14 mx-auto mb-5 bg-gray-100 rounded-full">
                <i class="text-xl text-gray-500 fas fa-lock"></i>
            </div>
            <h2 class="mb-2 text-xl font-bold text-gray-900">{{ $abstract->name ?? 'Abstract' }}</h2>
            <p class="mb-4 text-sm text-gray-600">{{ $message }}</p>

            @if(!empty($abstract->start_date) || !empty($abstract->end_date))
                <p class="mb-6 text-xs text-gray-400">
                    @if($abstract->start_date)
                        Opens {{ $abstract->start_date->format('M d, Y H:i') }}
                    @endif
                    @if($abstract->start_date && $abstract->end_date) · @endif
                    @if($abstract->end_date)
                        Closes {{ $abstract->end_date->format('M d, Y H:i') }}
                    @endif
                </p>
            @endif

            <a href="{{ route('showEventPage', ['event_id' => $event->id, 'event_slug' => \Illuminate\Support\Str::slug($event->title)]) }}"
               class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-white transition bg-black rounded-xl hover:bg-gray-800">
                <i class="mr-2 fas fa-arrow-left"></i>
                Back to Event
            </a>
        </div>
    </main>
</body>
</html>
