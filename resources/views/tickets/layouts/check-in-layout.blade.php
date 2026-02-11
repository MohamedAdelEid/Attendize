<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="event-id" content="{{ $event->id }}">
    <title>{{ $event->title }} - @yield('title', 'Check-in')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
    @stack('styles')
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
                        <a href="{{ route('showCheckIn', ['event_id' => $event->id]) }}"
                           class="flex items-center px-4 py-2 space-x-2 text-sm font-medium transition-all duration-200 rounded-lg {{ request()->routeIs('showCheckIn') ? 'text-white bg-black' : 'text-gray-700 hover:text-black hover:bg-gray-100' }}">
                            <i class="fas fa-camera"></i>
                            <span>Scanner</span>
                        </a>
                        <a href="{{ route('showCheckInDashboard', ['event_id' => $event->id]) }}"
                           class="flex items-center px-4 py-2 space-x-2 text-sm font-medium transition-all duration-200 rounded-lg {{ request()->routeIs('showCheckInDashboard') ? 'text-white bg-black' : 'text-gray-700 hover:text-black hover:bg-gray-100' }}">
                            <i class="fas fa-chart-bar"></i>
                            <span>Reports</span>
                        </a>
                        <a href="{{ route('showEventRegistration', ['event_id' => $event->id]) }}"
                           class="flex items-center px-4 py-2 space-x-2 text-sm font-medium transition-all duration-200 rounded-lg text-gray-700 hover:text-black hover:bg-gray-100">
                            <i class="fas fa-users"></i>
                            <span>Dashboard</span>
                        </a>
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
                <a href="{{ route('showCheckIn', ['event_id' => $event->id]) }}"
                   class="block w-full px-3 py-2 text-base font-medium text-left rounded-md {{ request()->routeIs('showCheckIn') ? 'text-white bg-black' : 'text-gray-700 hover:text-black hover:bg-gray-100' }}">
                    <i class="mr-2 fas fa-camera"></i>Scanner
                </a>
                <a href="{{ route('showCheckInDashboard', ['event_id' => $event->id]) }}"
                   class="block w-full px-3 py-2 text-base font-medium text-left rounded-md {{ request()->routeIs('showCheckInDashboard') ? 'text-white bg-black' : 'text-gray-700 hover:text-black hover:bg-gray-100' }}">
                    <i class="mr-2 fas fa-chart-bar"></i>Reports
                </a>
                <a href="{{ route('showEventRegistration', ['event_id' => $event->id]) }}"
                   class="block w-full px-3 py-2 text-base font-medium text-left rounded-md text-gray-700 hover:text-black hover:bg-gray-100">
                    <i class="mr-2 fas fa-users"></i>Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
        @yield('content')
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

    <script>
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById("mobileMenu");
            mobileMenu.classList.toggle("hidden");
        }

        function showToast(message, type = "success") {
            const container = document.getElementById("toastContainer");
            const toastId = Date.now();
            const isSuccess = type === "success";
            const bgColor = isSuccess ? "bg-green-600" : "bg-red-600";
            const icon = isSuccess ? "fa-check-circle" : "fa-exclamation-circle";

            const toast = document.createElement("div");
            toast.id = `toast-${toastId}`;
            toast.className = `${bgColor} text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 animate-slide-up max-w-sm`;
            toast.innerHTML = `
                <i class="fas ${icon} text-lg"></i>
                <span class="font-medium">${message}</span>
                <button onclick="removeToast('toast-${toastId}')" class="ml-auto text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                removeToast(`toast-${toastId}`);
            }, 5000);
        }

        function removeToast(toastId) {
            const toast = document.getElementById(toastId);
            if (toast) {
                toast.style.opacity = "0";
                toast.style.transform = "translateX(100%)";
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }
        }

        function showLoadingModal() {
            document.getElementById("loadingModal").classList.remove("hidden");
        }

        function hideLoadingModal() {
            document.getElementById("loadingModal").classList.add("hidden");
        }
    </script>

    @stack('scripts')
</body>
</html>