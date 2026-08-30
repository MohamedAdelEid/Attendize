@extends('AbstractReview.layouts.app')

@section('title', trans('Abstract.reviewer_settings'))

@section('content')
<div class="max-w-xl mx-auto space-y-6 animate-fade-in">
    <div>
        <h2 class="text-xl font-bold text-gray-900">@lang('Abstract.reviewer_settings')</h2>
        <p class="text-sm text-gray-500">@lang('Abstract.reviewer_settings_subtitle')</p>
    </div>

    <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
        @if(session('success'))
        <div class="p-3 mb-4 text-sm text-green-800 border border-green-200 rounded-lg bg-green-50">
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="p-3 mb-4 text-sm text-red-700 border border-red-200 rounded-lg bg-red-50">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('postAbstractReviewSettings', ['event_id' => $event->id]) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">@lang('Abstract.reviewer_name')</label>
                <input type="text" name="name" value="{{ old('name', $reviewer->name) }}" required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none">
            </div>
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">@lang('Abstract.email')</label>
                <input type="email" name="email" value="{{ old('email', $reviewer->email) }}" required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none">
            </div>
            <hr class="border-gray-200">
            <p class="text-sm font-medium text-gray-700">@lang('Abstract.change_password')</p>
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">@lang('Abstract.current_password')</label>
                <input type="password" name="current_password"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none">
            </div>
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">@lang('Abstract.new_password')</label>
                <input type="password" name="password"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none">
            </div>
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">@lang('Abstract.confirm_password')</label>
                <input type="password" name="password_confirmation"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none">
            </div>
            <button type="submit"
                class="w-full px-4 py-3 text-sm font-semibold text-white bg-black rounded-lg hover:bg-gray-800">
                @lang('Abstract.save_changes')
            </button>
        </form>
    </div>
</div>
@endsection
