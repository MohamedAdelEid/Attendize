@extends('Shared.Layouts.Master')

@section('title')
    @parent
    @lang('Abstract.show_submissions')
@stop

@section('top_nav')
    @include('ManageEvent.Partials.TopNav')
@stop

@section('page_title')
    <i class="ico-list mr5"></i>
    @lang('Abstract.show_submissions')
@stop

@section('menu')
    @include('ManageEvent.Partials.Sidebar')
@stop

@section('page_header')
<div class="col-md-8">
    <div class="btn-toolbar" role="toolbar">
        <div class="btn-group">
            <button type="button" class="btn btn-success bulk-action-btn" data-action="approved" disabled>
                <i class="ico-checkmark"></i> @lang('Abstract.bulk_approve')
            </button>
            <button type="button" class="btn btn-warning bulk-action-btn" data-action="rejected" disabled>
                <i class="ico-close"></i> @lang('Abstract.bulk_reject')
            </button>
            <button type="button" class="btn btn-danger bulk-action-btn" data-action="delete" disabled>
                <i class="ico-trash"></i> @lang('Abstract.delete_selected')
            </button>
        </div>
    </div>
</div>
<div class="col-md-4">
    {!! Form::open(['url' => route('showEventAbstractSubmissions', ['event_id' => $event->id]), 'method' => 'get']) !!}
    <div class="input-group">
        <input name="q" value="{{ $q ?? '' }}" placeholder="@lang('Abstract.search')" type="text" class="form-control">
        <span class="input-group-btn">
            <button class="btn btn-default" type="submit"><i class="ico-search"></i></button>
        </span>
    </div>
    {!! Form::hidden('status', $statusFilter) !!}
    {!! Form::hidden('abstract_id', $abstractFilter) !!}
    {!! Form::close() !!}
</div>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default" style="margin-bottom: 15px;">
            <div class="panel-body form-inline">
                {!! Form::open(['url' => route('showEventAbstractSubmissions', ['event_id' => $event->id]), 'method' => 'get', 'class' => 'form-inline']) !!}
                <select name="abstract_id" class="form-control" onchange="this.form.submit()">
                    <option value="">All Abstracts</option>
                    @foreach($abstracts as $id => $name)
                        <option value="{{ $id }}" {{ (string)$abstractFilter === (string)$id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="status" class="form-control" onchange="this.form.submit()" style="margin-left: 8px;">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>@lang('Abstract.pending')</option>
                    <option value="approved" {{ $statusFilter === 'approved' ? 'selected' : '' }}>@lang('Abstract.approved')</option>
                    <option value="rejected" {{ $statusFilter === 'rejected' ? 'selected' : '' }}>@lang('Abstract.rejected')</option>
                </select>
                {!! Form::hidden('q', $q) !!}
                {!! Form::close() !!}
            </div>
        </div>

        @if($submissions->count())
            <div class="panel">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width:30px;"><input type="checkbox" id="select-all-submissions"></th>
                                <th>@lang('Abstract.full_name')</th>
                                <th>@lang('Abstract.email')</th>
                                <th>@lang('Abstract.abstract')</th>
                                <th>@lang('Abstract.status')</th>
                                <th>@lang('Abstract.submitted_at')</th>
                                <th>@lang('Abstract.file_upload')</th>
                                <th>@lang('Abstract.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($submissions as $submission)
                            <tr data-id="{{ $submission->id }}">
                                <td><input type="checkbox" class="submission-checkbox" value="{{ $submission->id }}"></td>
                                <td>{{ $submission->display_name }}</td>
                                <td>{{ $submission->email }}</td>
                                <td>{{ optional($submission->abstractDefinition)->name }}</td>
                                <td>
                                    @if($submission->status === 'approved')
                                        <span class="label label-success">@lang('Abstract.approved')</span>
                                    @elseif($submission->status === 'rejected')
                                        <span class="label label-danger">@lang('Abstract.rejected')</span>
                                    @else
                                        <span class="label label-warning">@lang('Abstract.pending')</span>
                                    @endif
                                </td>
                                <td>{{ $submission->submitted_at ? $submission->submitted_at->format('Y-m-d H:i') : '—' }}</td>
                                <td>
                                    @if($submission->file_path)
                                        <a href="{{ $submission->file_url }}" target="_blank" class="btn btn-xs btn-default">
                                            <i class="ico-download"></i>
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <button data-modal-id="AbstractSubmissionDetails"
                                            data-href="{{ route('showEventAbstractSubmissionDetails', ['event_id' => $event->id, 'submission_id' => $submission->id]) }}"
                                            class="loadModal btn btn-xs btn-info" type="button">
                                        <i class="ico-eye"></i>
                                    </button>
                                    @if($submission->status !== 'approved')
                                        <button type="button" class="btn btn-xs btn-success status-btn" data-id="{{ $submission->id }}" data-status="approved">
                                            <i class="ico-checkmark"></i>
                                        </button>
                                    @endif
                                    @if($submission->status !== 'rejected')
                                        <button type="button" class="btn btn-xs btn-warning status-btn" data-id="{{ $submission->id }}" data-status="rejected">
                                            <i class="ico-close"></i>
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-xs btn-danger delete-submission" data-id="{{ $submission->id }}">
                                        <i class="ico-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer">
                    {!! $submissions->appends(request()->query())->render() !!}
                </div>
            </div>
        @else
            <div class="panel">
                <div class="panel-body text-center" style="padding: 50px 0;">
                    <p class="text-muted" style="font-size: 1.3em;">@lang('Abstract.no_submissions')</p>
                </div>
            </div>
        @endif
    </div>
</div>
@stop

@section('foot')
<script>
$(function () {
    function selectedIds() {
        return $('.submission-checkbox:checked').map(function () { return $(this).val(); }).get();
    }

    function toggleBulk() {
        var n = selectedIds().length;
        $('.bulk-action-btn').prop('disabled', n === 0);
    }

    $('#select-all-submissions').on('change', function () {
        $('.submission-checkbox').prop('checked', $(this).is(':checked'));
        toggleBulk();
    });
    $(document).on('change', '.submission-checkbox', toggleBulk);

    $('.status-btn').on('click', function () {
        var id = $(this).data('id');
        var status = $(this).data('status');
        if (!confirm('Mark as ' + status + '?')) return;
        $.post('{{ url("event/{$event->id}/abstracts/submissions") }}/' + id + '/status', {
            _token: '{{ csrf_token() }}',
            status: status
        }).done(function (res) {
            if (res.status === 'success') location.reload();
            else alert(res.message || 'Error');
        });
    });

    $('.delete-submission').on('click', function () {
        var id = $(this).data('id');
        if (!confirm('Delete this submission?')) return;
        $.ajax({
            url: '{{ url("event/{$event->id}/abstracts/submissions") }}/' + id,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function (res) {
                if (res.status === 'success') location.reload();
                else alert(res.message || 'Error');
            }
        });
    });

    $('.bulk-action-btn').on('click', function () {
        var action = $(this).data('action');
        var ids = selectedIds();
        if (!ids.length) return;
        if (!confirm('Apply "' + action + '" to ' + ids.length + ' submission(s)?')) return;
        $.post('{{ route('bulkUpdateAbstractSubmissions', ['event_id' => $event->id]) }}', {
            _token: '{{ csrf_token() }}',
            ids: ids,
            action: action
        }).done(function (res) {
            if (res.status === 'success') location.reload();
            else alert(res.message || 'Error');
        });
    });
});
</script>
@stop
