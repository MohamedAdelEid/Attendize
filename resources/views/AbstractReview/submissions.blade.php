@extends('AbstractReview.layouts.app')

@section('title', trans('Abstract.submissions'))

@section('content')
<div class="space-y-6 animate-fade-in">
    <div>
        <h2 class="text-xl font-bold text-gray-900">@lang('Abstract.submissions')</h2>
        <p class="text-sm text-gray-500">@lang('Abstract.submissions_page_subtitle')</p>
    </div>

    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="flex flex-col gap-4 px-6 py-4 border-b border-gray-200 bg-gray-50 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-base font-semibold text-gray-900">
                @lang('Abstract.submissions')
                <span class="ml-1 text-sm font-normal text-gray-500">({{ $submissions->total() }})</span>
            </h3>
            <div class="flex flex-wrap gap-2">
                @if($reviewer->can_review)
                <button type="button" data-action="approved" class="bulk-btn inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 disabled:opacity-40" disabled>
                    <i class="mr-1.5 fas fa-check"></i>@lang('Abstract.bulk_approve')
                </button>
                <button type="button" data-action="rejected" class="bulk-btn inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-yellow-600 rounded-lg hover:bg-yellow-700 disabled:opacity-40" disabled>
                    <i class="mr-1.5 fas fa-times"></i>@lang('Abstract.bulk_reject')
                </button>
                @endif
                @if($reviewer->can_delete)
                <button type="button" data-action="delete" class="bulk-btn inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-40" disabled>
                    <i class="mr-1.5 fas fa-trash"></i>@lang('Abstract.delete_selected')
                </button>
                @endif
            </div>
        </div>

        <div class="px-6 py-4 border-b border-gray-100">
            <form method="GET" action="{{ route('showAbstractReviewSubmissions', ['event_id' => $event->id]) }}" class="grid grid-cols-1 gap-3 sm:grid-cols-4">
                <input type="text" name="q" value="{{ $searchQuery }}" placeholder="@lang('Abstract.search')"
                    class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none">
                <select name="status" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none">
                    <option value="">@lang('Abstract.all_statuses')</option>
                    <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>@lang('Abstract.pending')</option>
                    <option value="approved" {{ $statusFilter === 'approved' ? 'selected' : '' }}>@lang('Abstract.approved')</option>
                    <option value="rejected" {{ $statusFilter === 'rejected' ? 'selected' : '' }}>@lang('Abstract.rejected')</option>
                </select>
                <select name="abstract_id" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none">
                    <option value="">@lang('Abstract.all_abstracts')</option>
                    @foreach($abstractOptions as $id => $name)
                        <option value="{{ $id }}" {{ (string)$abstractFilter === (string)$id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800">
                    <i class="mr-1 fas fa-filter"></i>@lang('Abstract.filter')
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" id="select-all" class="w-4 h-4 text-black border-gray-300 rounded focus:ring-black">
                        </th>
                        <th class="px-4 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">@lang('Abstract.full_name')</th>
                        <th class="px-4 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">@lang('Abstract.email')</th>
                        <th class="px-4 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">@lang('Abstract.abstract')</th>
                        <th class="px-4 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">@lang('Abstract.status')</th>
                        <th class="px-4 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">@lang('Abstract.submitted_at')</th>
                        <th class="px-4 py-3 text-xs font-semibold tracking-wider text-right text-gray-500 uppercase">@lang('Abstract.actions')</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($submissions as $submission)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <input type="checkbox" class="row-check w-4 h-4 text-black border-gray-300 rounded focus:ring-black" value="{{ $submission->id }}">
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $submission->display_name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $submission->email ?: '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ optional($submission->abstractDefinition)->name }}</td>
                        <td class="px-4 py-3">
                            @php
                                $badge = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                ][$submission->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex px-2.5 py-0.5 text-xs font-medium rounded-full {{ $badge }}">
                                {{ ucfirst($submission->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ $submission->submitted_at ? $submission->submitted_at->format('Y-m-d H:i') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right whitespace-nowrap">
                            <button type="button" class="view-btn inline-flex items-center px-2 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200"
                                data-id="{{ $submission->id }}" title="@lang('Abstract.view')">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if($reviewer->can_review && $submission->status !== 'approved')
                            <button type="button" class="status-btn inline-flex items-center px-2 py-1 text-xs font-medium text-white bg-green-600 rounded-lg hover:bg-green-700"
                                data-id="{{ $submission->id }}" data-status="approved" title="@lang('Abstract.approve')">
                                <i class="fas fa-check"></i>
                            </button>
                            @endif
                            @if($reviewer->can_review && $submission->status !== 'rejected')
                            <button type="button" class="status-btn inline-flex items-center px-2 py-1 text-xs font-medium text-white bg-yellow-600 rounded-lg hover:bg-yellow-700"
                                data-id="{{ $submission->id }}" data-status="rejected" title="@lang('Abstract.reject')">
                                <i class="fas fa-times"></i>
                            </button>
                            @endif
                            @if($reviewer->can_edit)
                            <button type="button" class="edit-btn inline-flex items-center px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700"
                                data-id="{{ $submission->id }}" title="@lang('Abstract.edit')">
                                <i class="fas fa-pen"></i>
                            </button>
                            @endif
                            @if($reviewer->can_delete)
                            <button type="button" class="delete-btn inline-flex items-center px-2 py-1 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700"
                                data-id="{{ $submission->id }}" title="@lang('Abstract.delete')">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500">
                            @lang('Abstract.no_submissions')
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($submissions->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $submissions->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Drawer / Modal --}}
<div id="detail-modal" class="fixed inset-0 z-40 hidden">
    <div class="absolute inset-0 bg-black bg-opacity-40" data-close-modal></div>
    <div class="absolute inset-y-0 right-0 flex w-full max-w-xl">
        <div class="flex flex-col w-full h-full bg-white shadow-2xl animate-slide-up">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900" id="modal-title">@lang('Abstract.submission_details')</h3>
                <button type="button" class="p-2 text-gray-500 rounded-lg hover:bg-gray-100" data-close-modal>
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="modal-body" class="flex-1 overflow-y-auto p-6">
                <div class="flex items-center justify-center py-20 text-gray-400">
                    <i class="text-2xl fas fa-spinner fa-spin"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var eventId = {{ (int) $event->id }};

    function selectedIds() {
        return $('.row-check:checked').map(function () { return $(this).val(); }).get();
    }
    function toggleBulk() {
        $('.bulk-btn').prop('disabled', selectedIds().length === 0);
    }
    $('#select-all').on('change', function () {
        $('.row-check').prop('checked', $(this).is(':checked'));
        toggleBulk();
    });
    $(document).on('change', '.row-check', toggleBulk);

    function openModal() {
        $('#detail-modal').removeClass('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        $('#detail-modal').addClass('hidden');
        document.body.style.overflow = '';
    }
    $('[data-close-modal]').on('click', closeModal);

    function loadSubmission(id, mode) {
        mode = mode || 'view';
        $('#modal-title').text(mode === 'edit' ? @json(trans('Abstract.edit_submission')) : @json(trans('Abstract.submission_details')));
        $('#modal-body').html('<div class="flex items-center justify-center py-20 text-gray-400"><i class="text-2xl fas fa-spinner fa-spin"></i></div>');
        openModal();
        $.get('/event/' + eventId + '/abstract-review/submissions/' + id, { mode: mode })
            .done(function (res) {
                var html = (res && res.html) ? res.html : res;
                $('#modal-body').html(html);
                if (mode === 'edit') {
                    $('#view-panel').addClass('hidden');
                    $('#edit-panel').removeClass('hidden');
                }
            })
            .fail(function () {
                $('#modal-body').html('<p class="text-red-600">Failed to load.</p>');
            });
    }

    $(document).on('click', '.view-btn', function () { loadSubmission($(this).data('id'), 'view'); });
    $(document).on('click', '.edit-btn', function () { loadSubmission($(this).data('id'), 'edit'); });

    $(document).on('click', '.status-btn', function () {
        var id = $(this).data('id'), status = $(this).data('status');
        if (!confirm(@json(trans('Abstract.confirm_status_change')))) return;
        $.post('/event/' + eventId + '/abstract-review/submissions/' + id + '/status', { status: status })
            .done(function (res) {
                showToast(res.message || 'OK', res.status === 'success' ? 'success' : 'error');
                if (res.status === 'success') location.reload();
            })
            .fail(function (xhr) {
                showToast((xhr.responseJSON && xhr.responseJSON.message) || 'Error', 'error');
            });
    });

    $(document).on('click', '.delete-btn', function () {
        var id = $(this).data('id');
        if (!confirm(@json(trans('Abstract.confirm_delete_submission')))) return;
        $.ajax({
            url: '/event/' + eventId + '/abstract-review/submissions/' + id,
            type: 'DELETE'
        }).done(function (res) {
            showToast(res.message || 'OK', 'success');
            if (res.status === 'success') location.reload();
        }).fail(function (xhr) {
            showToast((xhr.responseJSON && xhr.responseJSON.message) || 'Error', 'error');
        });
    });

    $('.bulk-btn').on('click', function () {
        var action = $(this).data('action'), ids = selectedIds();
        if (!ids.length || !confirm(@json(trans('Abstract.confirm_bulk')))) return;
        $.post('/event/' + eventId + '/abstract-review/submissions/bulk', { ids: ids, action: action })
            .done(function (res) {
                showToast(res.message || 'OK', res.status === 'success' ? 'success' : 'error');
                if (res.status === 'success') location.reload();
            })
            .fail(function (xhr) {
                showToast((xhr.responseJSON && xhr.responseJSON.message) || 'Error', 'error');
            });
    });

    $(document).on('submit', '#edit-submission-form', function (e) {
        e.preventDefault();
        var form = this;
        var id = $(form).data('id');
        var fd = new FormData(form);
        $.ajax({
            url: '/event/' + eventId + '/abstract-review/submissions/' + id,
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false
        }).done(function (res) {
            showToast(res.message || 'OK', res.status === 'success' ? 'success' : 'error');
            if (res.status === 'success') location.reload();
        }).fail(function (xhr) {
            var msg = 'Error';
            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
                else if (xhr.responseJSON.messages) {
                    var m = xhr.responseJSON.messages;
                    msg = (m.full_name && m.full_name[0]) || (Object.values(m)[0] && Object.values(m)[0][0]) || msg;
                }
            }
            showToast(msg, 'error');
        });
    });

    $(document).on('click', '.drawer-status-btn', function () {
        var id = $(this).data('id'), status = $(this).data('status');
        var notes = $('#drawer-review-notes').val() || '';
        if (!confirm(@json(trans('Abstract.confirm_status_change')))) return;
        $.post('/event/' + eventId + '/abstract-review/submissions/' + id + '/status', {
            status: status,
            review_notes: notes
        }).done(function (res) {
            showToast(res.message || 'OK', res.status === 'success' ? 'success' : 'error');
            if (res.status === 'success') location.reload();
        }).fail(function (xhr) {
            showToast((xhr.responseJSON && xhr.responseJSON.message) || 'Error', 'error');
        });
    });
})();
</script>
@endpush
