@extends('AbstractReview.layouts.app', ['hideNav' => true])

@section('title', trans('Abstract.reviewer_login'))

@section('main_class', 'min-h-full flex items-center justify-center px-4 py-12')

@section('content')
<div class="w-full max-w-md animate-fade-in">
    <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 mb-4 bg-black rounded-xl">
            <i class="text-2xl text-white fas fa-file-alt"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">@lang('Abstract.reviewer_portal')</h1>
        <p class="mt-1 text-sm text-gray-500">{{ $event->title }}</p>
    </div>

    <div class="p-8 bg-white border border-gray-200 shadow-sm rounded-2xl">
        @if($errors->any())
            <div class="p-3 mb-4 text-sm text-red-700 border border-red-200 rounded-lg bg-red-50">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('postAbstractReviewLogin', ['event_id' => $event->id]) }}" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block mb-1.5 text-sm font-medium text-gray-700">@lang('Abstract.email')</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-3 text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent"
                    placeholder="reviewer@example.com">
            </div>
            <div>
                <label for="password" class="block mb-1.5 text-sm font-medium text-gray-700">@lang('Abstract.reviewer_password')</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-3 text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent">
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember" value="1"
                    class="w-4 h-4 text-black border-gray-300 rounded focus:ring-black">
                <label for="remember" class="ml-2 text-sm text-gray-600">@lang('Abstract.remember_me')</label>
            </div>
            <button type="submit"
                class="w-full px-4 py-3 text-sm font-semibold text-white transition bg-black rounded-lg hover:bg-gray-800">
                @lang('Abstract.login')
            </button>
        </form>
    </div>
</div>
@endsection
