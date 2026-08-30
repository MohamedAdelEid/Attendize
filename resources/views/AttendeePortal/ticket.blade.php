@extends('AttendeePortal.layouts.app')

@section('title', trans('AttendeePortal.ticket'))

@section('content')
<div class="max-w-2xl mx-auto space-y-6 animate-fade-in">
    <div>
        <h2 class="text-xl font-bold text-gray-900">@lang('AttendeePortal.ticket')</h2>
        <p class="text-sm text-gray-500">@lang('AttendeePortal.ticket_subtitle')</p>
    </div>

    <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
        @if($user->status !== 'approved')
            <div class="p-4 text-sm text-yellow-800 border border-yellow-200 rounded-lg bg-yellow-50">
                @lang('AttendeePortal.ticket_pending_approval')
            </div>
        @elseif($ticketViewUrl)
            <div class="space-y-4 text-center">
                @if($user->qr_code_path)
                <div class="inline-block p-4 bg-white border border-gray-200 rounded-xl">
                    <img src="{{ asset('storage/' . ltrim($user->qr_code_path, '/')) }}" alt="QR" class="w-40 h-40 mx-auto">
                </div>
                @endif
                <p class="text-sm text-gray-600">@lang('AttendeePortal.ticket_ready')</p>
                <div class="flex flex-col gap-3 sm:flex-row sm:justify-center">
                    <a href="{{ $ticketViewUrl }}" target="_blank"
                       class="inline-flex items-center justify-center px-5 py-3 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                        <i class="mr-2 fas fa-eye"></i>@lang('AttendeePortal.view_ticket')
                    </a>
                    @if($ticketDownloadUrl)
                    <a href="{{ $ticketDownloadUrl }}"
                       class="inline-flex items-center justify-center px-5 py-3 text-sm font-semibold text-indigo-700 border border-indigo-200 rounded-lg bg-indigo-50 hover:bg-indigo-100">
                        <i class="mr-2 fas fa-download"></i>@lang('AttendeePortal.download_ticket')
                    </a>
                    @endif
                </div>
            </div>
        @else
            <div class="p-4 text-sm text-gray-600 border border-gray-200 rounded-lg bg-gray-50">
                @lang('AttendeePortal.ticket_generating')
            </div>
        @endif
    </div>
</div>
@endsection
