<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="event-id" content="{{ $event->id }}">
    <title>@yield('title', trans('Abstract.reviewer_portal')) — {{ $event->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f8f9fa', 100: '#f1f3f4', 200: '#e8eaed', 300: '#dadce0',
                            400: '#bdc1c6', 500: '#9aa0a6', 600: '#80868b', 700: '#5f6368',
                            800: '#3c4043', 900: '#202124', 950: '#171717'
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out',
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
    @unless($hideNav ?? false)
    <nav class="bg-white border-b border-gray-200 shadow-sm">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-black rounded-lg">
                        <i class="text-lg text-white fas fa-file-alt"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">{{ $event->title }}</h1>
                        <p class="text-xs text-gray-500">@lang('Abstract.reviewer_portal')</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @isset($reviewer)
                        <div class="hidden md:flex items-center space-x-1">
                            <a href="{{ route('showAbstractReviewDashboard', ['event_id' => $event->id]) }}"
                               class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('showAbstractReviewDashboard') ? 'text-white bg-black' : 'text-gray-700 hover:text-black hover:bg-gray-100' }}">
                                <i class="mr-2 fas fa-chart-pie"></i>@lang('Abstract.dashboard')
                            </a>
                            <a href="{{ route('showAbstractReviewSubmissions', ['event_id' => $event->id]) }}"
                               class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('showAbstractReviewSubmissions') || request()->routeIs('showAbstractReviewSubmission') ? 'text-white bg-black' : 'text-gray-700 hover:text-black hover:bg-gray-100' }}">
                                <i class="mr-2 fas fa-list"></i>@lang('Abstract.submissions')
                            </a>
                            <a href="{{ route('showAbstractReviewSettings', ['event_id' => $event->id]) }}"
                               class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('showAbstractReviewSettings') ? 'text-white bg-black' : 'text-gray-700 hover:text-black hover:bg-gray-100' }}">
                                <i class="mr-2 fas fa-cog"></i>@lang('Abstract.settings')
                            </a>
                        </div>
                        <span class="hidden text-sm text-gray-600 lg:inline">
                            <i class="mr-1 fas fa-user"></i>{{ $reviewer->name }}
                        </span>
                        <form method="POST" action="{{ route('postAbstractReviewLogout', ['event_id' => $event->id]) }}">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition bg-black rounded-lg hover:bg-gray-800">
                                <i class="mr-2 fas fa-sign-out-alt"></i>@lang('Abstract.logout')
                            </button>
                        </form>
                    @endisset
                </div>
            </div>
            @isset($reviewer)
            <div class="flex pb-3 space-x-1 md:hidden">
                <a href="{{ route('showAbstractReviewDashboard', ['event_id' => $event->id]) }}"
                   class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('showAbstractReviewDashboard') ? 'text-white bg-black' : 'text-gray-700 bg-gray-100' }}">
                    <i class="mr-2 fas fa-chart-pie"></i>@lang('Abstract.dashboard')
                </a>
                <a href="{{ route('showAbstractReviewSubmissions', ['event_id' => $event->id]) }}"
                   class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('showAbstractReviewSubmissions') || request()->routeIs('showAbstractReviewSubmission') ? 'text-white bg-black' : 'text-gray-700 bg-gray-100' }}">
                    <i class="mr-2 fas fa-list"></i>@lang('Abstract.submissions')
                </a>
                <a href="{{ route('showAbstractReviewSettings', ['event_id' => $event->id]) }}"
                   class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('showAbstractReviewSettings') ? 'text-white bg-black' : 'text-gray-700 bg-gray-100' }}">
                    <i class="mr-2 fas fa-cog"></i>@lang('Abstract.settings')
                </a>
            </div>
            @endisset
        </div>
    </nav>
    @endunless

    <main class="@yield('main_class', 'px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8')">
        @yield('content')
    </main>

    <div id="toast-stack" class="fixed z-50 space-y-2 top-4 right-4"></div>

    <script>
        function showToast(message, type) {
            type = type || 'success';
            var colors = {
                success: 'bg-green-600',
                error: 'bg-red-600',
                info: 'bg-gray-800'
            };
            var el = document.createElement('div');
            el.className = 'px-4 py-3 text-sm text-white rounded-lg shadow-lg animate-slide-up ' + (colors[type] || colors.info);
            el.textContent = message;
            document.getElementById('toast-stack').appendChild(el);
            setTimeout(function () { el.remove(); }, 3500);
        }
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
    </script>
    @stack('scripts')
</body>
</html>
