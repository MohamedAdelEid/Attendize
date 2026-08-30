@extends('AttendeePortal.layouts.app')

@section('title', trans('AttendeePortal.upload_final'))

@section('content')
<div class="max-w-xl mx-auto space-y-6 animate-fade-in">
    <div>
        <a href="{{ route('showAttendeePortalAbstracts', ['event_id' => $event->id]) }}" class="text-sm text-indigo-600 hover:underline">
            <i class="mr-1 fas fa-arrow-left"></i>@lang('AttendeePortal.back_to_abstracts')
        </a>
        <h2 class="mt-3 text-xl font-bold text-gray-900">@lang('AttendeePortal.upload_final')</h2>
        <p class="text-sm text-gray-500">@lang('AttendeePortal.upload_final_subtitle')</p>
    </div>

    <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
        <dl class="grid grid-cols-1 gap-3 mb-6 text-sm">
            <div>
                <dt class="text-xs font-medium text-gray-500">@lang('AttendeePortal.abstract_name')</dt>
                <dd class="font-medium text-gray-900">{{ optional($submission->abstractDefinition)->name }}</dd>
            </div>
            @if(optional($submission->category)->name)
            <div>
                <dt class="text-xs font-medium text-gray-500">@lang('AttendeePortal.category')</dt>
                <dd class="text-gray-900">{{ $submission->category->name }}</dd>
            </div>
            @endif
        </dl>

        @if($submission->final_file_path)
        <div class="p-4 mb-6 text-sm text-green-800 border border-green-200 rounded-lg bg-green-50">
            @lang('AttendeePortal.final_already_uploaded')
            <a href="{{ $submission->final_file_url }}" target="_blank" class="ml-1 font-medium underline">
                @lang('AttendeePortal.view_file')
            </a>
        </div>
        @endif

        @if($errors->any())
        <div class="p-3 mb-4 text-sm text-red-700 border border-red-200 rounded-lg bg-red-50">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('postAttendeePortalAbstractUpload', ['event_id' => $event->id, 'submission_id' => $submission->id]) }}"
              enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block mb-1.5 text-sm font-medium text-gray-700">@lang('AttendeePortal.final_file_label')</label>
                <input type="file" name="final_file" required accept=".pdf,.ppt,.pptx,.doc,.docx,.zip"
                    class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="mt-1 text-xs text-gray-500">@lang('AttendeePortal.final_file_help')</p>
            </div>
            <button type="submit"
                class="w-full px-4 py-3 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                @lang('AttendeePortal.upload_button')
            </button>
        </form>
    </div>
</div>
@endsection
