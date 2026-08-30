@extends('AttendeePortal.layouts.app')

@section('title', trans('AttendeePortal.registration'))

@section('content')
<div class="max-w-3xl mx-auto space-y-6 animate-fade-in">
    <div>
        <h2 class="text-xl font-bold text-gray-900">@lang('AttendeePortal.registration')</h2>
        <p class="text-sm text-gray-500">@lang('AttendeePortal.registration_subtitle')</p>
    </div>

    <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-medium text-gray-500">@lang('AttendeePortal.full_name')</dt>
                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $user->full_name }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500">@lang('AttendeePortal.email')</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500">@lang('AttendeePortal.phone')</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $user->phone ?: '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500">@lang('AttendeePortal.status')</dt>
                <dd class="mt-1">
                    <span class="px-2 py-1 text-xs font-medium rounded-full
                        {{ $user->status === 'approved' ? 'bg-green-100 text-green-700' : ($user->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </dd>
            </div>
            @if($user->unique_code)
            <div>
                <dt class="text-xs font-medium text-gray-500">@lang('AttendeePortal.registration_code')</dt>
                <dd class="mt-1 font-mono text-sm text-gray-900">{{ $user->unique_code }}</dd>
            </div>
            @endif
            @if(optional($user->category)->name)
            <div>
                <dt class="text-xs font-medium text-gray-500">@lang('AttendeePortal.category')</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $user->category->name }}</dd>
            </div>
            @endif
        </dl>

        @if($user->formFieldValues->count())
        <div class="pt-6 mt-6 border-t border-gray-100">
            <h3 class="mb-4 text-sm font-semibold text-gray-900">@lang('AttendeePortal.form_answers')</h3>
            <dl class="space-y-3">
                @foreach($user->formFieldValues as $value)
                <div class="p-3 rounded-lg bg-gray-50">
                    <dt class="text-xs font-medium text-gray-500">{{ optional($value->field)->label ?: 'Field' }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $value->value ?: '—' }}</dd>
                </div>
                @endforeach
            </dl>
        </div>
        @endif
    </div>
</div>
@endsection
