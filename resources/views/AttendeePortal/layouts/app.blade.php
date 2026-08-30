<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', trans('AttendeePortal.portal')) — {{ $event->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-in-out',
                        'pop': 'pop 0.2s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        pop: {
                            '0%': { transform: 'scale(0.92)' },
                            '100%': { transform: 'scale(1)' }
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
                    <div class="flex items-center justify-center w-10 h-10 bg-indigo-600 rounded-lg">
                        <i class="text-lg text-white fas fa-user-circle"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">{{ $event->title }}</h1>
                        <p class="text-xs text-gray-500">@lang('AttendeePortal.portal')</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    @isset($user)
                        <span class="hidden text-sm text-gray-600 md:inline">
                            <i class="mr-1 fas fa-user"></i>{{ $user->full_name }}
                        </span>
                        <form method="POST" action="{{ route('postAttendeePortalLogout', ['event_id' => $event->id]) }}">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition bg-indigo-600 rounded-lg hover:bg-indigo-700">
                                <i class="mr-2 fas fa-sign-out-alt"></i>@lang('AttendeePortal.logout')
                            </button>
                        </form>
                    @endisset
                </div>
            </div>
            @isset($user)
            <div class="flex pb-3 space-x-1 overflow-x-auto">
                @php
                    $navItems = [
                        ['route' => 'showAttendeePortalDashboard', 'icon' => 'fa-home', 'label' => trans('AttendeePortal.dashboard'), 'match' => ['showAttendeePortalDashboard']],
                        ['route' => 'showAttendeePortalRegistration', 'icon' => 'fa-clipboard-list', 'label' => trans('AttendeePortal.registration'), 'match' => ['showAttendeePortalRegistration']],
                        ['route' => 'showAttendeePortalTicket', 'icon' => 'fa-ticket-alt', 'label' => trans('AttendeePortal.ticket'), 'match' => ['showAttendeePortalTicket']],
                        ['route' => 'showAttendeePortalAbstracts', 'icon' => 'fa-file-alt', 'label' => trans('AttendeePortal.abstracts'), 'match' => ['showAttendeePortalAbstracts', 'showAttendeePortalAbstractUpload']],
                        ['route' => 'showAttendeePortalProfile', 'icon' => 'fa-cog', 'label' => trans('AttendeePortal.profile'), 'match' => ['showAttendeePortalProfile']],
                    ];
                @endphp
                @foreach($navItems as $item)
                    <a href="{{ route($item['route'], ['event_id' => $event->id]) }}"
                       class="flex-shrink-0 inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs($item['match']) ? 'text-white bg-indigo-600' : 'text-gray-700 bg-gray-100 hover:bg-gray-200' }}">
                        <i class="mr-2 fas {{ $item['icon'] }}"></i>{{ $item['label'] }}
                    </a>
                @endforeach
            </div>
            @endisset
        </div>
    </nav>
    @endunless

    <main class="@yield('main_class', 'px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8')">
        @unless($hideNav ?? false)
            @if(session('success'))
                <div class="p-4 mb-6 text-sm text-green-800 border border-green-200 rounded-lg bg-green-50 animate-fade-in">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 mb-6 text-sm text-red-800 border border-red-200 rounded-lg bg-red-50 animate-fade-in">
                    {{ session('error') }}
                </div>
            @endif
        @endunless
        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>
