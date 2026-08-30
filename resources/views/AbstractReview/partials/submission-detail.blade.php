@php
    $ru = $submission->registrationUser;
@endphp
<div id="view-panel" class="space-y-6">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-xs font-medium text-gray-500">@lang('Abstract.full_name')</p>
            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $submission->display_name }}</p>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-500">@lang('Abstract.email')</p>
            <p class="mt-1 text-sm text-gray-900">{{ $submission->email ?: '—' }}</p>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-500">@lang('Abstract.phone')</p>
            <p class="mt-1 text-sm text-gray-900">{{ $submission->phone ?: '—' }}</p>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-500">@lang('Abstract.status')</p>
            <p class="mt-1 text-sm font-semibold text-gray-900">{{ ucfirst($submission->status) }}</p>
        </div>
        <div class="col-span-2">
            <p class="text-xs font-medium text-gray-500">@lang('Abstract.abstract')</p>
            <p class="mt-1 text-sm text-gray-900">{{ optional($submission->abstractDefinition)->name }}</p>
        </div>
        @if(optional($submission->category)->name)
        <div>
            <p class="text-xs font-medium text-gray-500">@lang('Abstract.category')</p>
            <p class="mt-1 text-sm text-gray-900">{{ $submission->category->name }}</p>
        </div>
        @endif
        @if($submission->authors)
        <div class="col-span-2">
            <p class="text-xs font-medium text-gray-500">@lang('Abstract.authors')</p>
            <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $submission->authors }}</p>
        </div>
        @endif
        @if($submission->details)
        <div class="col-span-2">
            <p class="text-xs font-medium text-gray-500">@lang('Abstract.details')</p>
            <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $submission->details }}</p>
        </div>
        @endif
        @if($submission->domain)
        <div>
            <p class="text-xs font-medium text-gray-500">@lang('Abstract.domain')</p>
            <p class="mt-1 text-sm text-gray-900">{{ $submission->domain }}</p>
        </div>
        @endif
        <div>
            <p class="text-xs font-medium text-gray-500">@lang('Abstract.submitted_at')</p>
            <p class="mt-1 text-sm text-gray-900">{{ $submission->submitted_at ? $submission->submitted_at->format('Y-m-d H:i') : '—' }}</p>
        </div>
        @if($submission->file_path)
        <div class="col-span-2">
            <p class="text-xs font-medium text-gray-500">@lang('Abstract.file_upload')</p>
            <a href="{{ $submission->file_url }}" target="_blank"
               class="inline-flex items-center mt-1 text-sm font-medium text-black hover:underline">
                <i class="mr-2 fas fa-download"></i>@lang('Abstract.download_file')
            </a>
        </div>
        @endif
        @if($submission->final_file_path)
        <div class="col-span-2">
            <p class="text-xs font-medium text-gray-500">@lang('Abstract.final_submission')</p>
            <a href="{{ $submission->final_file_url }}" target="_blank"
               class="inline-flex items-center mt-1 text-sm font-medium text-green-700 hover:underline">
                <i class="mr-2 fas fa-download"></i>@lang('Abstract.download_final_file')
            </a>
        </div>
        @endif
    </div>

    @if($ru)
    <div class="p-4 border border-gray-200 rounded-xl bg-gray-50">
        <h4 class="mb-3 text-sm font-semibold text-gray-900">
            <i class="mr-1 fas fa-user"></i>@lang('Abstract.registration_user')
        </h4>
        <div class="grid grid-cols-2 gap-3 text-sm">
            <div>
                <p class="text-xs text-gray-500">@lang('Abstract.full_name')</p>
                <p class="font-medium text-gray-900">{{ trim($ru->first_name . ' ' . $ru->last_name) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">@lang('Abstract.email')</p>
                <p class="text-gray-900">{{ $ru->email }}</p>
            </div>
            @if(!empty($ru->unique_code))
            <div>
                <p class="text-xs text-gray-500">@lang('Abstract.registration_code')</p>
                <p class="font-mono text-gray-900">{{ $ru->unique_code }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    @if($submission->formFieldValues->count())
    <div>
        <h4 class="mb-3 text-sm font-semibold text-gray-900">@lang('Abstract.dynamic_answers')</h4>
        <div class="space-y-3">
            @foreach($submission->formFieldValues as $value)
                <div class="p-3 border border-gray-100 rounded-lg bg-gray-50">
                    <p class="text-xs font-medium text-gray-500">{{ optional($value->field)->label ?: 'Field' }}</p>
                    <p class="mt-1 text-sm text-gray-900">
                        @if(optional($value->field)->type === 'file' && $value->value)
                            <a href="{{ asset('storage/' . ltrim($value->value, '/')) }}" target="_blank" class="text-black hover:underline">
                                @lang('Abstract.download_file')
                            </a>
                        @else
                            {{ $value->value ?: '—' }}
                        @endif
                    </p>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($submission->review_notes)
    <div>
        <p class="text-xs font-medium text-gray-500">@lang('Abstract.review_notes')</p>
        <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $submission->review_notes }}</p>
    </div>
    @endif

    @if($submission->reviewedBy)
    <p class="text-xs text-gray-500">
        @lang('Abstract.reviewed_by'): {{ $submission->reviewedBy->name }}
        @if($submission->reviewed_at)
            — {{ $submission->reviewed_at->format('Y-m-d H:i') }}
        @endif
    </p>
    @endif

    @if($reviewer->can_review)
    <div class="pt-4 border-t border-gray-200 space-y-3">
        <label class="block text-xs font-medium text-gray-500">@lang('Abstract.review_notes')</label>
        <textarea id="drawer-review-notes" rows="3"
            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none">{{ $submission->review_notes }}</textarea>
        <div class="flex flex-wrap gap-2">
            @if($submission->status !== 'approved')
            <button type="button" class="drawer-status-btn inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700"
                data-id="{{ $submission->id }}" data-status="approved">
                <i class="mr-2 fas fa-check"></i>@lang('Abstract.approve')
            </button>
            @endif
            @if($submission->status !== 'rejected')
            <button type="button" class="drawer-status-btn inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-lg hover:bg-yellow-700"
                data-id="{{ $submission->id }}" data-status="rejected">
                <i class="mr-2 fas fa-times"></i>@lang('Abstract.reject')
            </button>
            @endif
        </div>
    </div>
    @endif
</div>

@if($reviewer->can_edit)
<div id="edit-panel" class="hidden space-y-4">
    <form id="edit-submission-form" data-id="{{ $submission->id }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label class="block mb-1 text-xs font-medium text-gray-500">@lang('Abstract.full_name')</label>
            <input type="text" name="full_name" value="{{ $submission->full_name }}"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none">
        </div>
        <div>
            <label class="block mb-1 text-xs font-medium text-gray-500">@lang('Abstract.email')</label>
            <input type="email" name="email" value="{{ $submission->email }}"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none">
        </div>
        <div>
            <label class="block mb-1 text-xs font-medium text-gray-500">@lang('Abstract.phone')</label>
            <input type="text" name="phone" value="{{ $submission->phone }}"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none">
        </div>
        <div>
            <label class="block mb-1 text-xs font-medium text-gray-500">@lang('Abstract.authors')</label>
            <textarea name="authors" rows="2"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none">{{ $submission->authors }}</textarea>
        </div>
        <div>
            <label class="block mb-1 text-xs font-medium text-gray-500">@lang('Abstract.details')</label>
            <textarea name="details" rows="3"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none">{{ $submission->details }}</textarea>
        </div>
        <div>
            <label class="block mb-1 text-xs font-medium text-gray-500">@lang('Abstract.domain')</label>
            <input type="text" name="domain" value="{{ $submission->domain }}"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none">
        </div>
        <div>
            <label class="block mb-1 text-xs font-medium text-gray-500">@lang('Abstract.review_notes')</label>
            <textarea name="review_notes" rows="2"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none">{{ $submission->review_notes }}</textarea>
        </div>
        <div>
            <label class="block mb-1 text-xs font-medium text-gray-500">@lang('Abstract.replace_file')</label>
            <input type="file" name="file"
                class="w-full text-sm text-gray-600">
        </div>
        <button type="submit"
            class="w-full px-4 py-2.5 text-sm font-semibold text-white bg-black rounded-lg hover:bg-gray-800">
            @lang('Abstract.save_changes')
        </button>
    </form>
</div>
@endif
