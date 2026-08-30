@extends('AttendeePortal.layouts.app')

@section('title', trans('AttendeePortal.profile'))

@section('content')
<div class="max-w-xl mx-auto space-y-6 animate-fade-in">
    <div>
        <h2 class="text-xl font-bold text-gray-900">@lang('AttendeePortal.profile')</h2>
        <p class="text-sm text-gray-500">@lang('AttendeePortal.profile_subtitle')</p>
    </div>

    <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
        @if($errors->any())
        <div class="p-3 mb-4 text-sm text-red-700 border border-red-200 rounded-lg bg-red-50">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('postAttendeePortalProfile', ['event_id' => $event->id]) }}"
              enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">@lang('AttendeePortal.first_name')</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:outline-none">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">@lang('AttendeePortal.last_name')</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">@lang('AttendeePortal.email')</label>
                <input type="email" value="{{ $user->email }}" disabled
                    class="w-full px-3 py-2 text-sm text-gray-500 border border-gray-200 rounded-lg bg-gray-50">
                <p class="mt-1 text-xs text-gray-500">@lang('AttendeePortal.email_readonly')</p>
            </div>
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">@lang('AttendeePortal.phone')</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:outline-none">
            </div>
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">@lang('AttendeePortal.title')</label>
                <input type="text" name="title" value="{{ old('title', $user->title) }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:outline-none">
            </div>
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">@lang('AttendeePortal.bio')</label>
                <textarea name="bio" rows="3"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:outline-none">{{ old('bio', $user->bio) }}</textarea>
            </div>
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">@lang('AttendeePortal.avatar')</label>
                @if($user->avatar)
                <img src="{{ asset('storage/' . ltrim($user->avatar, '/')) }}" alt="" class="w-16 h-16 mb-2 object-cover rounded-full">
                @endif
                <input type="file" name="avatar" accept="image/*"
                    class="w-full text-sm text-gray-600">
            </div>
            <button type="submit"
                class="w-full px-4 py-3 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                @lang('AttendeePortal.save_profile')
            </button>
        </form>
    </div>
</div>
@endsection
