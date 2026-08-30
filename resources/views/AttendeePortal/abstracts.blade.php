@extends('AttendeePortal.layouts.app')

@section('title', trans('AttendeePortal.abstracts'))

@section('content')
<div class="space-y-6 animate-fade-in">
    <div>
        <h2 class="text-xl font-bold text-gray-900">@lang('AttendeePortal.abstracts')</h2>
        <p class="text-sm text-gray-500">@lang('AttendeePortal.abstracts_subtitle')</p>
    </div>

    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
        @if($submissions->count())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">@lang('AttendeePortal.abstract_name')</th>
                        <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">@lang('AttendeePortal.category')</th>
                        <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">@lang('AttendeePortal.status')</th>
                        <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">@lang('AttendeePortal.submitted_at')</th>
                        <th class="px-4 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">@lang('AttendeePortal.actions')</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($submissions as $submission)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ optional($submission->abstractDefinition)->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ optional($submission->category)->name ?: '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $submission->status === 'approved' ? 'bg-green-100 text-green-700' : ($submission->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ ucfirst($submission->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $submission->submitted_at ? $submission->submitted_at->format('Y-m-d H:i') : '—' }}</td>
                        <td class="px-4 py-3 text-sm text-right">
                            @if($submission->status === 'approved')
                                @if($submission->final_file_path)
                                    <a href="{{ route('downloadAttendeePortalFinalFile', ['event_id' => $event->id, 'submission_id' => $submission->id]) }}"
                                       class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100">
                                        <i class="mr-1.5 fas fa-download"></i>@lang('AttendeePortal.view_final_file')
                                    </a>
                                @else
                                    <a href="{{ route('showAttendeePortalAbstractUpload', ['event_id' => $event->id, 'submission_id' => $submission->id]) }}"
                                       class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                                        <i class="mr-1.5 fas fa-upload"></i>@lang('AttendeePortal.upload_final')
                                    </a>
                                @endif
                            @elseif($submission->status === 'pending')
                                <span class="text-xs text-gray-400">@lang('AttendeePortal.awaiting_review')</span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($submissions->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $submissions->links() }}
        </div>
        @endif
        @else
        <div class="p-8 text-sm text-center text-gray-500">
            @lang('AttendeePortal.no_abstracts')
        </div>
        @endif
    </div>
</div>
@endsection
