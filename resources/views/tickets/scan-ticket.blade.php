<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="event-id" content="{{ $event->id }}">
    <title>{{ $event->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f8f9fa',
                            100: '#f1f3f4',
                            200: '#e8eaed',
                            300: '#dadce0',
                            400: '#bdc1c6',
                            500: '#9aa0a6',
                            600: '#80868b',
                            700: '#5f6368',
                            800: '#3c4043',
                            900: '#202124',
                            950: '#171717'
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'pulse-slow': 'pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': {
                                opacity: '0',
                                transform: 'translateY(10px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'translateY(0)'
                            }
                        },
                        slideUp: {
                            '0%': {
                                opacity: '0',
                                transform: 'translateY(20px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'translateY(0)'
                            }
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="h-full font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200 shadow-lg">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-10 h-10 bg-black rounded-lg">
                            <i class="text-lg text-white fas fa-qrcode"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">{{ $event->title }}</h1>
                        <p class="text-sm text-gray-500">Streamlined event check-in/check-out</p>
                    </div>
                </div>

                <div class="hidden md:block">
                    <div class="flex items-baseline ml-10 space-x-4">
                        <button onclick="showTab('scanner')"
                            class="flex items-center px-4 py-2 space-x-2 text-sm font-medium text-white transition-all duration-200 bg-black rounded-lg nav-btn active hover:bg-gray-800">
                            <i class="fas fa-camera"></i>
                            <span>Scanner</span>
                        </button>
                        <button onclick="showTab('dashboard')"
                            class="flex items-center px-4 py-2 space-x-2 text-sm font-medium text-gray-700 transition-all duration-200 rounded-lg nav-btn hover:text-black hover:bg-gray-100">
                            <i class="fas fa-users"></i>
                            <span>Dashboard</span>
                        </button>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button onclick="toggleMobileMenu()" class="p-2 text-gray-700 hover:text-black">
                        <i class="text-lg fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobileMenu" class="hidden bg-white border-t border-gray-200 md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <button onclick="showTab('scanner')"
                    class="block w-full px-3 py-2 text-base font-medium text-left text-white bg-black rounded-md mobile-nav-btn active">
                    <i class="mr-2 fas fa-camera"></i>Scanner
                </button>
                <button onclick="showTab('dashboard')"
                    class="block w-full px-3 py-2 text-base font-medium text-left text-gray-700 rounded-md mobile-nav-btn hover:text-black hover:bg-gray-100">
                    <i class="mr-2 fas fa-users"></i>Dashboard
                </button>
            </div>
        </div>
    </nav>

    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-5">
            <div
                class="p-6 transition-shadow duration-200 bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-lg">
                            <i class="text-xl text-gray-700 fas fa-users"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Registrations</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $event->registrations->sum(function ($registration) {return $registration->registrationUsers()->count();}) }}
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="p-6 transition-shadow duration-200 bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                            <i class="text-xl text-green-600 fas fa-sign-in-alt"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Checked In</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $event->registrations->sum(function ($registration) {return $registration->registrationUsers()->where('check_in', '!=', null)->count();}) }}
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="p-6 transition-shadow duration-200 bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                            <i class="text-xl text-blue-600 fas fa-sign-out-alt"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Checked Out</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $event->registrations->sum(function ($registration) {return $registration->registrationUsers()->where('check_out', '!=', null)->count();}) }}
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="p-6 transition-shadow duration-200 bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-lg">
                            <i class="text-xl text-yellow-600 fas fa-users"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Currently in Event</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $event->registrations->sum(function ($registration) {
                                return $registration->registrationUsers()->whereNotNull('check_in')->whereNull('check_out')->count();
                            }) }}
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="p-6 transition-shadow duration-200 bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-lg">
                            <i class="text-xl text-yellow-600 fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Approval</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $event->registrations->sum(function ($registration) {return $registration->registrationUsers()->where('status', '!=', 'approved')->count();}) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scanner Tab -->
        <div id="scannerTab" class="tab-content">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                <!-- QR Scanner -->
                <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="flex items-center text-lg font-semibold text-gray-900">
                            <i class="mr-3 text-gray-700 fas fa-camera"></i>
                            QR Code Scanner
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="relative w-full mb-4 overflow-hidden bg-black rounded-lg scanner-container h-80">
                            <video id="qrVideo" class="hidden object-cover w-full h-full" autoplay
                                playsinline></video>
                            <div id="scannerPlaceholder"
                                class="absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                                <i class="mb-4 text-6xl opacity-50 fas fa-qrcode"></i>
                                <p class="text-lg font-medium">Camera not active</p>
                                <p class="text-sm">Click start to begin scanning</p>
                            </div>
                            <canvas id="qrCanvas" class="hidden"></canvas>

                            <!-- Scanner overlay -->
                            <div id="scannerOverlay" class="absolute inset-0 hidden pointer-events-none">
                                <div
                                    class="absolute w-48 h-48 transform -translate-x-1/2 -translate-y-1/2 border-2 border-white rounded-lg top-1/2 left-1/2">
                                    <div
                                        class="absolute w-6 h-6 border-t-4 border-l-4 border-green-400 rounded-tl-lg -top-1 -left-1">
                                    </div>
                                    <div
                                        class="absolute w-6 h-6 border-t-4 border-r-4 border-green-400 rounded-tr-lg -top-1 -right-1">
                                    </div>
                                    <div
                                        class="absolute w-6 h-6 border-b-4 border-l-4 border-green-400 rounded-bl-lg -bottom-1 -left-1">
                                    </div>
                                    <div
                                        class="absolute w-6 h-6 border-b-4 border-r-4 border-green-400 rounded-br-lg -bottom-1 -right-1">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <button id="startCameraBtn" onclick="startCamera()"
                                class="flex items-center justify-center w-full px-4 py-3 space-x-2 font-medium text-white transition-colors duration-200 bg-black rounded-lg hover:bg-gray-800">
                                <i class="fas fa-camera"></i>
                                <span>Start Camera</span>
                            </button>
                            <button id="stopCameraBtn" onclick="stopCamera()"
                                class="flex items-center justify-center hidden w-full px-4 py-3 space-x-2 font-medium text-gray-700 transition-colors duration-200 bg-gray-100 rounded-lg hover:bg-gray-200">
                                <i class="fas fa-camera-slash"></i>
                                <span>Stop Camera</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Manual Check-in/Check-out -->
                <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="flex items-center text-lg font-semibold text-gray-900">
                            <i class="mr-3 text-gray-700 fas fa-keyboard"></i>
                            Manual Check-in/Check-out
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">Enter unique code or email. First scan: Check-in • Second
                            scan: Check-out</p>
                    </div>
                    <div class="p-6">
                        <form id="scanTicketForm" method="POST"
                            action="{{ route('PostScanTicket', ['event_id' => $event->id]) }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="event_id" value="{{ $event->id }}">
                            <div>
                                <label for="uniqueCode" class="block mb-2 text-sm font-medium text-gray-700">
                                    Unique Code or Email
                                </label>
                                <input type="text" name="unique_code" id="uniqueCode"
                                    class="w-full px-4 py-3 font-mono text-lg transition-colors duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent"
                                    placeholder="Enter unique code or email" autocomplete="off">
                            </div>
                            <button type="submit" id="manualCheckInBtn"
                                class="flex items-center justify-center w-full px-4 py-3 space-x-2 font-medium text-white transition-colors duration-200 bg-green-600 rounded-lg hover:bg-green-700 disabled:opacity-70 disabled:cursor-not-allowed">
                                <i class="fas fa-exchange-alt"></i>
                                <span id="manualCheckInBtnText">Check In/Out</span>
                            </button>

                            <!-- AJAX result (no page reload) -->
                            <div id="checkInResult" class="mt-6"></div>

                            @if (session('success'))
                                <div
                                    class="mt-6 p-4 rounded-lg border-l-4 {{ session('action') === 'check_out' ? 'bg-blue-50 border-blue-400' : 'bg-green-50 border-green-400' }}">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <i
                                                class="fas {{ session('action') === 'check_out' ? 'fa-sign-out-alt text-blue-400' : 'fa-sign-in-alt text-green-400' }} text-xl"></i>
                                        </div>
                                        <div class="flex-1 ml-3">
                                            <p
                                                class="text-sm font-medium {{ session('action') === 'check_out' ? 'text-blue-800' : 'text-green-800' }}">
                                                {{ session('success') }}
                                            </p>
                                            @if (session('user'))
                                                <div class="p-3 mt-3 bg-white border border-gray-200 rounded-lg">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="flex-shrink-0">
                                                            <div
                                                                class="w-10 h-10 {{ session('action') === 'check_out' ? 'bg-blue-100' : 'bg-green-100' }} rounded-full flex items-center justify-center">
                                                                <i
                                                                    class="fas {{ session('action') === 'check_out' ? 'fa-sign-out-alt text-blue-600' : 'fa-sign-in-alt text-green-600' }}"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-1">
                                                            <p class="text-sm font-medium text-gray-900">
                                                                {{ session('user')->first_name }}
                                                                {{ session('user')->last_name }}
                                                            </p>
                                                            <p class="text-sm text-gray-500">
                                                                {{ session('user')->email }}</p>
                                                            <p
                                                                class="text-xs {{ session('action') === 'check_out' ? 'text-blue-600' : 'text-green-600' }} mt-1">
                                                                <i class="mr-1 fas fa-clock"></i>
                                                                {{ session('action') === 'check_out' ? 'Checked out' : 'Checked in' }}
                                                                successfully
                                                            </p>
                                                            @if (session('user')->check_in && session('user')->check_out)
                                                                <div class="mt-2 text-xs text-gray-600">
                                                                    <div>Check-in:
                                                                        {{ \Carbon\Carbon::parse(session('user')->check_in)->format('M d, Y H:i:s') }}
                                                                    </div>
                                                                    <div>Check-out:
                                                                        {{ \Carbon\Carbon::parse(session('user')->check_out)->format('M d, Y H:i:s') }}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="p-4 mt-6 border-l-4 border-red-400 rounded-lg bg-red-50">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <i class="text-xl text-red-400 fas fa-times-circle"></i>
                                        </div>
                                        <div class="flex-1 ml-3">
                                            <p class="text-sm font-medium text-red-800">
                                                {{ session('error') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Tab -->
        <div id="dashboardTab" class="hidden tab-content">
            <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex flex-col space-y-3 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                        <h3 class="flex items-center text-lg font-semibold text-gray-900">
                            <i class="mr-3 text-gray-700 fas fa-users"></i>
                            Registration Management
                        </h3>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Search and Filter -->
                    <form method="GET" action="{{ route('showCheckIn', ['event_id' => $event->id]) }}"
                        id="filterForm">
                        <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-3">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i class="text-gray-400 fas fa-search"></i>
                                </div>
                                <input type="text" name="search" id="searchInput"
                                    value="{{ request()->get('search') }}"
                                    class="w-full py-3 pl-10 pr-4 transition-colors duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent"
                                    placeholder="Search by name, email, or code...">
                            </div>
                            <select name="status" id="statusFilter"
                                class="w-full px-4 py-3 transition-colors duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent">
                                <option value="">All Status</option>
                                <option value="pending" {{ request()->get('status') == 'pending' ? 'selected' : '' }}>
                                    Pending</option>
                                <option value="approved"
                                    {{ request()->get('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected"
                                    {{ request()->get('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                            <select name="attendance" id="attendanceFilter"
                                class="w-full px-4 py-3 transition-colors duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent">
                                <option value="">All Attendance</option>
                                <option value="not_checked_in"
                                    {{ request()->get('attendance') == 'not_checked_in' ? 'selected' : '' }}>Not
                                    Checked In</option>
                                <option value="checked_in"
                                    {{ request()->get('attendance') == 'checked_in' ? 'selected' : '' }}>Checked In
                                </option>
                                <option value="checked_out"
                                    {{ request()->get('attendance') == 'checked_out' ? 'selected' : '' }}>Checked Out
                                </option>
                            </select>
                        </div>
                        <div class="flex justify-end mb-6 space-x-2">
                            <button type="submit"
                                class="px-4 py-2 font-medium text-white transition-colors duration-200 bg-black rounded-lg hover:bg-gray-800">
                                <i class="mr-2 fas fa-filter"></i>Apply Filters
                            </button>
                            <a href="{{ route('showCheckIn', ['event_id' => $event->id]) }}"
                                class="px-4 py-2 font-medium text-gray-700 transition-colors duration-200 bg-gray-100 rounded-lg hover:bg-gray-200">
                                <i class="mr-2 fas fa-times"></i>Clear
                            </a>
                        </div>
                    </form>

                    <!-- Registrations Table -->
                    @if (isset($registrationUsers) && $registrationUsers->total() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Name</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Email</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Unique Code</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Attendance Status</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="registrationsTableBody">
                                    @foreach ($registrationUsers as $registrationUser)
                                        <tr class="transition-colors duration-150 hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $registrationUser->first_name }}
                                                    {{ $registrationUser->last_name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">{{ $registrationUser->email }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($registrationUser->status == 'pending')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                                @elseif ($registrationUser->status == 'approved')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Approved</span>
                                                @elseif ($registrationUser->status == 'rejected')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rejected</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($registrationUser->unique_code)
                                                    <code class="px-2 py-1 font-mono text-xs bg-gray-100 rounded">
                                                        {{ $registrationUser->unique_code }}
                                                    </code>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $latestLog = $registrationUser
                                                        ->checkInCheckOutLogs()
                                                        ->orderBy('action_time', 'desc')
                                                        ->first();
                                                @endphp
                                                @if ($latestLog && $latestLog->action == 'check_out')
                                                    <div class="flex items-center text-blue-600">
                                                        <i class="mr-2 fas fa-sign-out-alt"></i>
                                                        <div>
                                                            <span class="text-sm font-medium">Checked Out</span>
                                                            <div class="text-xs text-gray-500">
                                                                {{ \Carbon\Carbon::parse($latestLog->action_time)->format('M d, H:i') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @elseif ($latestLog && $latestLog->action == 'check_in')
                                                    <div class="flex items-center text-green-600">
                                                        <i class="mr-2 fas fa-sign-in-alt"></i>
                                                        <div>
                                                            <span class="text-sm font-medium">Checked In</span>
                                                            <div class="text-xs text-gray-500">
                                                                {{ \Carbon\Carbon::parse($latestLog->action_time)->format('M d, H:i') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="flex items-center text-gray-400">
                                                        <i class="mr-2 fas fa-minus-circle"></i>
                                                        <span class="text-sm font-medium">Not Checked In</span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="#"
                                                    onclick="viewLogs({{ $registrationUser->id }}); return false;"
                                                    class="text-blue-600 hover:text-blue-900">
                                                    <i class="fas fa-history mr-1"></i>View History
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div
                            class="flex items-center justify-between px-4 py-3 mt-4 bg-white border-t border-gray-200 sm:px-6">
                            <div class="flex justify-between flex-1 sm:hidden">
                                @if ($registrationUsers->onFirstPage())
                                    <span
                                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-default rounded-md">
                                        Previous
                                    </span>
                                @else
                                    <a href="{{ $registrationUsers->appends(request()->query())->previousPageUrl() }}"
                                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                        Previous
                                    </a>
                                @endif

                                @if ($registrationUsers->hasMorePages())
                                    <a href="{{ $registrationUsers->appends(request()->query())->nextPageUrl() }}"
                                        class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                        Next
                                    </a>
                                @else
                                    <span
                                        class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-default rounded-md">
                                        Next
                                    </span>
                                @endif
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Showing
                                        <span class="font-medium">{{ $registrationUsers->firstItem() }}</span>
                                        to
                                        <span class="font-medium">{{ $registrationUsers->lastItem() }}</span>
                                        of
                                        <span class="font-medium">{{ $registrationUsers->total() }}</span>
                                        results
                                    </p>
                                </div>
                                <nav class="relative z-0 inline-flex -space-x-px rounded-md shadow-sm"
                                    aria-label="Pagination">
                                    @if ($registrationUsers->onFirstPage())
                                        <span
                                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-400 cursor-default">
                                            <span class="sr-only">Previous</span>
                                            <i class="fas fa-chevron-left"></i>
                                        </span>
                                    @else
                                        <a href="{{ $registrationUsers->appends(request()->query())->previousPageUrl() }}"
                                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            <span class="sr-only">Previous</span>
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    @endif

                                    @php
                                        $currentPage = $registrationUsers->currentPage();
                                        $lastPage = $registrationUsers->lastPage();
                                        $startPage = max(1, $currentPage - 2);
                                        $endPage = min($lastPage, $currentPage + 2);
                                    @endphp

                                    @if ($startPage > 1)
                                        <a href="{{ $registrationUsers->appends(request()->query())->url(1) }}"
                                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            1
                                        </a>
                                        @if ($startPage > 2)
                                            <span
                                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                                ...
                                            </span>
                                        @endif
                                    @endif

                                    @for ($page = $startPage; $page <= $endPage; $page++)
                                        @if ($page == $currentPage)
                                            <span aria-current="page"
                                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-black text-sm font-medium text-white">
                                                {{ $page }}
                                            </span>
                                        @else
                                            <a href="{{ $registrationUsers->appends(request()->query())->url($page) }}"
                                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                                {{ $page }}
                                            </a>
                                        @endif
                                    @endfor

                                    @if ($endPage < $lastPage)
                                        @if ($endPage < $lastPage - 1)
                                            <span
                                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                                ...
                                            </span>
                                        @endif
                                        <a href="{{ $registrationUsers->appends(request()->query())->url($lastPage) }}"
                                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            {{ $lastPage }}
                                        </a>
                                    @endif

                                    @if ($registrationUsers->hasMorePages())
                                        <a href="{{ $registrationUsers->appends(request()->query())->nextPageUrl() }}"
                                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            <span class="sr-only">Next</span>
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    @else
                                        <span
                                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-400 cursor-default">
                                            <span class="sr-only">Next</span>
                                            <i class="fas fa-chevron-right"></i>
                                        </span>
                                    @endif
                                </nav>
                            </div>
                        </div>
                    @else
                        <div class="py-12 text-center">
                            <i class="mb-4 text-6xl text-gray-300 fas fa-users"></i>
                            <p class="text-lg font-medium text-gray-500">No registrations found</p>
                            <p class="text-sm text-gray-400">Try adjusting your search criteria</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div id="loadingModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="max-w-sm p-8 mx-4 text-center bg-white rounded-xl">
            <div class="w-12 h-12 mx-auto mb-4 border-b-2 border-black rounded-full animate-spin"></div>
            <p class="font-medium text-gray-700">Processing...</p>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toastContainer" class="fixed z-50 space-y-2 top-4 right-4"></div>

    <script src="{{ asset('js/script.js') }}"></script>
    <script>
        function getStatusBadge(status) {
            const badges = {
                pending: '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>',
                approved: '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Approved</span>',
                rejected: '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rejected</span>',
            }
            return badges[status] || status
        }

        // Auto-submit form on filter change
        document.getElementById('statusFilter')?.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        document.getElementById('attendanceFilter')?.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        // View logs function
        function viewLogs(userId) {
            // This would typically open a modal with the check-in/check-out history
            // For now, we'll show an alert with the logs
            fetch(`/events/{{ $event->id }}/registration-user/${userId}/logs`)
                .then(response => response.json())
                .then(data => {
                    if (data.logs && data.logs.length > 0) {
                        let logText = 'Check-in/Check-out History:\n\n';
                        data.logs.forEach(log => {
                            const date = new Date(log.action_time);
                            logText +=
                                `${log.action === 'check_in' ? '✓ Check-in' : '✗ Check-out'}: ${date.toLocaleString()}\n`;
                        });
                        alert(logText);
                    } else {
                        alert('No check-in/check-out history found for this user.');
                    }
                })
                .catch(error => {
                    console.error('Error fetching logs:', error);
                    alert('Error loading check-in/check-out history.');
                });
        }

        // Debounce search input
        let searchTimeout;
        document.getElementById('searchInput')?.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500);
        });

        // Scan ticket form: AJAX submit so the code stays in the input (no reload)
        (function() {
            const form = document.getElementById('scanTicketForm');
            const resultEl = document.getElementById('checkInResult');
            const btn = document.getElementById('manualCheckInBtn');
            const btnText = document.getElementById('manualCheckInBtnText');
            const inputEl = document.getElementById('uniqueCode');
            const url = form.action;
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const value = (inputEl.value || '').trim();
                if (!value) {
                    resultEl.innerHTML =
                        '<div class="p-4 border-l-4 border-red-400 rounded-lg bg-red-50"><p class="text-sm font-medium text-red-800">Please enter a unique code or email.</p></div>';
                    resultEl.classList.remove('hidden');
                    return;
                }

                btn.disabled = true;
                btnText.textContent = 'Processing...';

                const body = new FormData(form);
                body.set('unique_code', value);

                fetch(url, {
                        method: 'POST',
                        body: body,
                        headers: {
                            'X-CSRF-TOKEN': token || '',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(function(res) {
                        return res.json();
                    })
                    .then(function(data) {
                        if (data.status === 'success') {
                            const isCheckOut = data.action === 'check_out';
                            const user = data.user || {};
                            const checkInStr = user.check_in ? new Date(user.check_in).toLocaleString() :
                            '';
                            const checkOutStr = user.check_out ? new Date(user.check_out).toLocaleString() :
                                '';
                            resultEl.innerHTML =
                                '<div class="p-4 rounded-lg border-l-4 ' + (isCheckOut ?
                                    'bg-blue-50 border-blue-400' : 'bg-green-50 border-green-400') + '">' +
                                '<div class="flex items-start">' +
                                '<div class="flex-shrink-0"><i class="fas ' + (isCheckOut ?
                                    'fa-sign-out-alt text-blue-400' : 'fa-sign-in-alt text-green-400') +
                                ' text-xl"></i></div>' +
                                '<div class="flex-1 ml-3">' +
                                '<p class="text-sm font-medium ' + (isCheckOut ? 'text-blue-800' :
                                    'text-green-800') + '">' + (data.message || '') + '</p>' +
                                '<div class="p-3 mt-3 bg-white border border-gray-200 rounded-lg">' +
                                '<p class="text-sm font-medium text-gray-900">' + (user.first_name || '') +
                                ' ' + (user.last_name || '') + '</p>' +
                                '<p class="text-sm text-gray-500">' + (user.email || '') + '</p>' +
                                '<p class="text-xs ' + (isCheckOut ? 'text-blue-600' : 'text-green-600') +
                                ' mt-1"><i class="mr-1 fas fa-clock"></i>' + (isCheckOut ? 'Checked out' :
                                    'Checked in') + ' successfully</p>' +
                                (checkInStr && checkOutStr ?
                                    '<div class="mt-2 text-xs text-gray-600"><div>Check-in: ' + checkInStr +
                                    '</div><div>Check-out: ' + checkOutStr + '</div></div>' : '') +
                                '</div></div></div></div>';
                        } else {
                            resultEl.innerHTML =
                                '<div class="p-4 border-l-4 border-red-400 rounded-lg bg-red-50">' +
                                '<div class="flex items-start"><div class="flex-shrink-0"><i class="text-xl text-red-400 fas fa-times-circle"></i></div>' +
                                '<div class="flex-1 ml-3"><p class="text-sm font-medium text-red-800">' + (
                                    data.message || 'Something went wrong.') + '</p></div></div></div>';
                        }
                        resultEl.classList.remove('hidden');
                        // Keep the value in the input so user doesn't have to retype
                    })
                    .catch(function(err) {
                        resultEl.innerHTML =
                            '<div class="p-4 border-l-4 border-red-400 rounded-lg bg-red-50">' +
                            '<p class="text-sm font-medium text-red-800">Network error. Please try again.</p></div>';
                        resultEl.classList.remove('hidden');
                    })
                    .finally(function() {
                        btn.disabled = false;
                        btnText.textContent = 'Check In/Out';
                    });
            });
        })();
    </script>
</body>

</html>
