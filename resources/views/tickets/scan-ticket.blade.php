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
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="h-full bg-gray-50 font-sans antialiased">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-black rounded-lg flex items-center justify-center">
                            <i class="fas fa-qrcode text-white text-lg"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">{{ $event->title }}</h1>
                        <p class="text-sm text-gray-500">Streamlined event check-in/check-out</p>
                    </div>
                </div>
                
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <button onclick="showTab('scanner')" 
                                class="nav-btn active bg-black text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-800 flex items-center space-x-2">
                            <i class="fas fa-camera"></i>
                            <span>Scanner</span>
                        </button>
                        <button onclick="showTab('dashboard')" 
                                class="nav-btn text-gray-700 hover:text-black hover:bg-gray-100 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center space-x-2">
                            <i class="fas fa-users"></i>
                            <span>Dashboard</span>
                        </button>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button onclick="toggleMobileMenu()" class="text-gray-700 hover:text-black p-2">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-white border-t border-gray-200">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <button onclick="showTab('scanner')" 
                        class="mobile-nav-btn active block w-full text-left px-3 py-2 rounded-md text-base font-medium bg-black text-white">
                    <i class="fas fa-camera mr-2"></i>Scanner
                </button>
                <button onclick="showTab('dashboard')" 
                        class="mobile-nav-btn block w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-black hover:bg-gray-100">
                    <i class="fas fa-users mr-2"></i>Dashboard
                </button>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-gray-700 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Registrations</p>
                        <p class="text-2xl font-bold text-gray-900" >{{ $event->registrations->sum(function($registration) { return $registration->registrationUsers()->count(); }) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-sign-in-alt text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Checked In</p>
                        <p class="text-2xl font-bold text-gray-900" >{{ $event->registrations->sum(function($registration) { return $registration->registrationUsers()->where('check_in','!=',null)->where('check_out', null)->count(); }) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-sign-out-alt text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Checked Out</p>
                        <p class="text-2xl font-bold text-gray-900" >{{ $event->registrations->sum(function($registration) { return $registration->registrationUsers()->where('check_out','!=',null)->count(); }) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-qrcode text-purple-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">QR Codes Generated</p>
                        <p class="text-2xl font-bold text-gray-900" >{{ $event->registrations->sum(function($registration) { return $registration->registrationUsers()->where('unique_code','!=',null)->count(); }) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Approval</p>
                        <p class="text-2xl font-bold text-gray-900" >{{ $event->registrations->sum(function($registration) { return $registration->registrationUsers()->where('status','!=','approved')->count(); }) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scanner Tab -->
        <div id="scannerTab" class="tab-content">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- QR Scanner -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-camera mr-3 text-gray-700"></i>
                            QR Code Scanner
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="scanner-container relative w-full h-80 bg-black rounded-lg overflow-hidden mb-4">
                            <video id="qrVideo" class="w-full h-full object-cover hidden" autoplay playsinline></video>
                            <div id="scannerPlaceholder" class="absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                                <i class="fas fa-qrcode text-6xl mb-4 opacity-50"></i>
                                <p class="text-lg font-medium">Camera not active</p>
                                <p class="text-sm">Click start to begin scanning</p>
                            </div>
                            <canvas id="qrCanvas" class="hidden"></canvas>
                            
                            <!-- Scanner overlay -->
                            <div id="scannerOverlay" class="absolute inset-0 pointer-events-none hidden">
                                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-48 h-48 border-2 border-white rounded-lg">
                                    <div class="absolute -top-1 -left-1 w-6 h-6 border-l-4 border-t-4 border-green-400 rounded-tl-lg"></div>
                                    <div class="absolute -top-1 -right-1 w-6 h-6 border-r-4 border-t-4 border-green-400 rounded-tr-lg"></div>
                                    <div class="absolute -bottom-1 -left-1 w-6 h-6 border-l-4 border-b-4 border-green-400 rounded-bl-lg"></div>
                                    <div class="absolute -bottom-1 -right-1 w-6 h-6 border-r-4 border-b-4 border-green-400 rounded-br-lg"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <button id="startCameraBtn" onclick="startCamera()" 
                                    class="w-full bg-black text-white py-3 px-4 rounded-lg font-medium hover:bg-gray-800 transition-colors duration-200 flex items-center justify-center space-x-2">
                                <i class="fas fa-camera"></i>
                                <span>Start Camera</span>
                            </button>
                            <button id="stopCameraBtn" onclick="stopCamera()" 
                                    class="w-full bg-gray-100 text-gray-700 py-3 px-4 rounded-lg font-medium hover:bg-gray-200 transition-colors duration-200 flex items-center justify-center space-x-2 hidden">
                                <i class="fas fa-camera-slash"></i>
                                <span>Stop Camera</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Manual Check-in/Check-out -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-keyboard mr-3 text-gray-700"></i>
                            Manual Check-in/Check-out
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">First scan: Check-in • Second scan: Check-out</p>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="{{ route('PostScanTicket', ['event_id' => $event->id]) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label for="uniqueCode" class="block text-sm font-medium text-gray-700 mb-2">
                                    Unique Code
                                </label>
                                <input type="text"
                                       name="unique_code"
                                       id="uniqueCode" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent transition-colors duration-200 text-lg font-mono uppercase"
                                       placeholder="Enter unique code (e.g., ABC123)" 
                                       required>
                            </div>
                            <button type="submit" 
                                    id="manualCheckInBtn"
                                    class="w-full bg-green-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-green-700 transition-colors duration-200 flex items-center justify-center space-x-2">
                                <i class="fas fa-exchange-alt"></i>
                                <span>Check In/Out</span>
                            </button>

                            @if(session('success'))
                            <div class="mt-6 p-4 rounded-lg border-l-4 {{ session('action') === 'check_out' ? 'bg-blue-50 border-blue-400' : 'bg-green-50 border-green-400' }}">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas {{ session('action') === 'check_out' ? 'fa-sign-out-alt text-blue-400' : 'fa-sign-in-alt text-green-400' }} text-xl"></i>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm font-medium {{ session('action') === 'check_out' ? 'text-blue-800' : 'text-green-800' }}">
                                            {{ session('success') }}
                                        </p>
                                        @if(session('user'))
                                            <div class="mt-3 p-3 bg-white rounded-lg border border-gray-200">
                                                <div class="flex items-center space-x-3">
                                                    <div class="flex-shrink-0">
                                                        <div class="w-10 h-10 {{ session('action') === 'check_out' ? 'bg-blue-100' : 'bg-green-100' }} rounded-full flex items-center justify-center">
                                                            <i class="fas {{ session('action') === 'check_out' ? 'fa-sign-out-alt text-blue-600' : 'fa-sign-in-alt text-green-600' }}"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-1">
                                                        <p class="text-sm font-medium text-gray-900">
                                                            {{ session('user')->first_name }} {{ session('user')->last_name }}
                                                        </p>
                                                        <p class="text-sm text-gray-500">{{ session('user')->email }}</p>
                                                        <p class="text-xs {{ session('action') === 'check_out' ? 'text-blue-600' : 'text-green-600' }} mt-1">
                                                            <i class="fas fa-clock mr-1"></i>
                                                            {{ session('action') === 'check_out' ? 'Checked out' : 'Checked in' }} successfully
                                                        </p>
                                                        @if(session('user')->check_in && session('user')->check_out)
                                                            <div class="mt-2 text-xs text-gray-600">
                                                                <div>Check-in: {{ \Carbon\Carbon::parse(session('user')->check_in)->format('M d, Y H:i:s') }}</div>
                                                                <div>Check-out: {{ \Carbon\Carbon::parse(session('user')->check_out)->format('M d, Y H:i:s') }}</div>
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

                            @if(session('error'))
                                <div class="mt-6 p-4 rounded-lg border-l-4 bg-red-50 border-red-400">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-times-circle text-red-400 text-xl"></i>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <p class="text-sm font-medium text-red-800">
                                                {{ session('error') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </form>

                        <!-- Check-in Result -->
                        <div id="checkInResult" class="mt-6 hidden"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Tab -->
        <div id="dashboardTab" class="tab-content hidden">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-users mr-3 text-gray-700"></i>
                            Registration Management
                        </h3>
                    </div>
                </div>
                
                <div class="p-6">
                    <!-- Search and Filter -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" 
                                   id="searchInput" 
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent transition-colors duration-200"
                                   placeholder="Search by name, email, or code..." 
                                   onkeyup="filterRegistrations()">
                        </div>
                        <select id="statusFilter" 
                                onchange="filterRegistrations()"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent transition-colors duration-200">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                    <!-- Registrations Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unique Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($event->registrations as $registration)
                                    @foreach ($registration->registrationUsers as $registrationUser)
                                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $registrationUser->first_name }} {{ $registrationUser->last_name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">{{ $registrationUser->email }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($registrationUser->status == 'pending')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                                @elseif ($registrationUser->status == 'approved')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Approved</span>
                                                @elseif ($registrationUser->status == 'rejected')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rejected</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($registrationUser->unique_code)
                                                    <code class="px-2 py-1 text-xs font-mono bg-gray-100 rounded">
                                                        {{ $registrationUser->unique_code }}
                                                    </code>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($registrationUser->check_out)
                                                    <div class="flex items-center text-blue-600">
                                                        <i class="fas fa-sign-out-alt mr-2"></i>
                                                        <div>
                                                            <span class="text-sm font-medium">Checked Out</span>
                                                            <div class="text-xs text-gray-500">
                                                                {{ \Carbon\Carbon::parse($registrationUser->check_out)->format('M d, H:i') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @elseif ($registrationUser->check_in)
                                                    <div class="flex items-center text-green-600">
                                                        <i class="fas fa-sign-in-alt mr-2"></i>
                                                        <div>
                                                            <span class="text-sm font-medium">Checked In</span>
                                                            <div class="text-xs text-gray-500">
                                                                {{ \Carbon\Carbon::parse($registrationUser->check_in)->format('M d, H:i') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="flex items-center text-gray-400">
                                                        <i class="fas fa-minus-circle mr-2"></i>
                                                        <span class="text-sm font-medium">Not Checked In</span>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div id="noRegistrations" class="text-center py-12 hidden">
                        <i class="fas fa-users text-gray-300 text-6xl mb-4"></i>
                        <p class="text-gray-500 text-lg font-medium">No registrations found</p>
                        <p class="text-gray-400 text-sm">Try adjusting your search criteria</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div id="loadingModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl p-8 max-w-sm mx-4 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-black mx-auto mb-4"></div>
            <p class="text-gray-700 font-medium">Processing...</p>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

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
    </script>
</body>
</html>
