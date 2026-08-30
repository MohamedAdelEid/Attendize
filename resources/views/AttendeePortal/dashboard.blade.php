@extends('AttendeePortal.layouts.app')

@section('title', trans('AttendeePortal.dashboard'))

@section('content')
<div class="space-y-8 animate-fade-in">
    <div>
        <h2 class="text-xl font-bold text-gray-900">@lang('AttendeePortal.welcome_heading', ['name' => $user->full_name])</h2>
        <p class="text-sm text-gray-500">@lang('AttendeePortal.dashboard_subtitle')</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('showAttendeePortalRegistration', ['event_id' => $event->id]) }}"
           class="p-5 transition bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md hover:border-indigo-200">
            <div class="flex items-center">
                <div class="flex items-center justify-center w-11 h-11 bg-blue-100 rounded-lg">
                    <i class="text-blue-600 fas fa-clipboard-list"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500">@lang('AttendeePortal.registration')</p>
                    <p class="text-sm font-bold text-gray-900">{{ ucfirst($user->status) }}</p>
                </div>
            </div>
        </a>
        <a href="{{ route('showAttendeePortalTicket', ['event_id' => $event->id]) }}"
           class="p-5 transition bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md hover:border-indigo-200">
            <div class="flex items-center">
                <div class="flex items-center justify-center w-11 h-11 bg-green-100 rounded-lg">
                    <i class="text-green-600 fas fa-ticket-alt"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500">@lang('AttendeePortal.ticket')</p>
                    <p class="text-sm font-bold text-gray-900">
                        {{ $user->ticket_token ? trans('AttendeePortal.available') : trans('AttendeePortal.not_available') }}
                    </p>
                </div>
            </div>
        </a>
        <a href="{{ route('showAttendeePortalAbstracts', ['event_id' => $event->id]) }}"
           class="p-5 transition bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md hover:border-indigo-200">
            <div class="flex items-center">
                <div class="flex items-center justify-center w-11 h-11 bg-purple-100 rounded-lg">
                    <i class="text-purple-600 fas fa-file-alt"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500">@lang('AttendeePortal.abstracts')</p>
                    <p class="text-sm font-bold text-gray-900">{{ $submissions->count() }}</p>
                </div>
            </div>
        </a>
        <a href="{{ route('showAttendeePortalProfile', ['event_id' => $event->id]) }}"
           class="p-5 transition bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md hover:border-indigo-200">
            <div class="flex items-center">
                <div class="flex items-center justify-center w-11 h-11 bg-gray-100 rounded-lg">
                    <i class="text-gray-600 fas fa-cog"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500">@lang('AttendeePortal.profile')</p>
                    <p class="text-sm font-bold text-gray-900">@lang('AttendeePortal.edit_profile')</p>
                </div>
            </div>
        </a>
    </div>

    @if($submissions->count())
    <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-900">@lang('AttendeePortal.recent_abstracts')</h3>
            <a href="{{ route('showAttendeePortalAbstracts', ['event_id' => $event->id]) }}" class="text-sm text-indigo-600 hover:underline">
                @lang('AttendeePortal.view_all')
            </a>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($submissions as $submission)
            <div class="flex items-center justify-between py-3">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ optional($submission->abstractDefinition)->name }}</p>
                    <p class="text-xs text-gray-500">
                        {{ optional($submission->category)->name ?: '—' }}
                        · {{ $submission->submitted_at ? $submission->submitted_at->format('Y-m-d') : '' }}
                    </p>
                </div>
                <span class="px-2 py-1 text-xs font-medium rounded-full
                    {{ $submission->status === 'approved' ? 'bg-green-100 text-green-700' : ($submission->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                    {{ ucfirst($submission->status) }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
