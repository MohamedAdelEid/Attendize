@extends('AttendeePortal.layouts.app', ['hideNav' => true])

@section('title', trans('AttendeePortal.login'))

@section('main_class', 'min-h-full flex items-center justify-center px-4 py-12')

@section('content')
<div class="w-full max-w-md animate-fade-in">
    <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 mb-4 bg-indigo-600 rounded-xl">
            <i class="text-2xl text-white fas fa-user-circle"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">@lang('AttendeePortal.portal')</h1>
        <p class="mt-1 text-sm text-gray-500">{{ $event->title }}</p>
    </div>

    <div class="p-8 bg-white border border-gray-200 shadow-sm rounded-2xl">
        @if($errors->any())
            <div class="p-3 mb-4 text-sm text-red-700 border border-red-200 rounded-lg bg-red-50">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('postAttendeePortalLogin', ['event_id' => $event->id]) }}" class="space-y-5">
            @csrf
            <div>
                <label for="identifier" class="block mb-1.5 text-sm font-medium text-gray-700">@lang('AttendeePortal.identifier_label')</label>
                <input type="text" name="identifier" id="identifier" value="{{ old('identifier') }}" required autofocus
                    class="w-full px-4 py-3 text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent"
                    placeholder="@lang('AttendeePortal.identifier_placeholder')">
                <p class="mt-1.5 text-xs text-gray-500">@lang('AttendeePortal.identifier_help')</p>
            </div>
            <button type="submit"
                class="w-full px-4 py-3 text-sm font-semibold text-white transition bg-indigo-600 rounded-lg hover:bg-indigo-700">
                @lang('AttendeePortal.send_code')
            </button>
        </form>
    </div>
</div>
@endsection
