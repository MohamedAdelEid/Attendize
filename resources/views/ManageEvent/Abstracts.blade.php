@extends('Shared.Layouts.Master')

@section('title')
    @parent
    @lang('Abstract.abstract')
@stop

@section('top_nav')
    @include('ManageEvent.Partials.TopNav')
@stop

@section('page_title')
    <i class="ico-file mr5"></i>
    @lang('Abstract.abstract')
@stop

@section('menu')
    @include('ManageEvent.Partials.Sidebar')
@stop

@section('head')
    @include('ManageEvent.Partials.SummernoteAssets')
@stop

@section('page_header')
<div class="col-md-12">
    <ul class="nav nav-tabs" style="margin-bottom: 15px;">
        <li class="{{ $tab === 'abstracts' ? 'active' : '' }}">
            <a href="{{ route('showEventAbstracts', ['event_id' => $event->id, 'tab' => 'abstracts']) }}">
                <i class="ico-file"></i> @lang('Abstract.abstracts')
            </a>
        </li>
        <li class="{{ $tab === 'categories' ? 'active' : '' }}">
            <a href="{{ route('showEventAbstracts', ['event_id' => $event->id, 'tab' => 'categories']) }}">
                <i class="ico-folder"></i> @lang('Abstract.categories')
            </a>
        </li>
        <li class="{{ $tab === 'submissions' ? 'active' : '' }}">
            <a href="{{ route('showEventAbstracts', ['event_id' => $event->id, 'tab' => 'submissions']) }}">
                <i class="ico-list"></i> @lang('Abstract.submissions')
            </a>
        </li>
        <li class="{{ $tab === 'reviewers' ? 'active' : '' }}">
            <a href="{{ route('showEventAbstracts', ['event_id' => $event->id, 'tab' => 'reviewers']) }}">
                <i class="ico-users"></i> @lang('Abstract.reviewers')
            </a>
        </li>
    </ul>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">

        @if($tab === 'abstracts')
            <div class="row" style="margin-bottom: 15px;">
                <div class="col-md-8">
                    <button data-modal-id="CreateAbstract"
                            data-href="{{ route('showCreateEventAbstract', ['event_id' => $event->id]) }}"
                            class="loadModal btn btn-success" type="button">
                        <i class="ico-plus2"></i> @lang('Abstract.create_abstract')
                    </button>
                </div>
                <div class="col-md-4">
                    {!! Form::open(['url' => route('showEventAbstracts', ['event_id' => $event->id]), 'method' => 'get']) !!}
                    {!! Form::hidden('tab', 'abstracts') !!}
                    <div class="input-group">
                        <input name="q" value="{{ $q }}" placeholder="@lang('Abstract.search')" class="form-control">
                        <span class="input-group-btn"><button class="btn btn-default" type="submit"><i class="ico-search"></i></button></span>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>

            @if($abstracts->count())
                <div class="panel">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>@lang('Abstract.name')</th>
                                    <th>@lang('Abstract.categories')</th>
                                    <th>@lang('Abstract.register_condition')</th>
                                    <th>@lang('Abstract.status')</th>
                                    <th>@lang('Abstract.submissions')</th>
                                    <th>@lang('Abstract.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($abstracts as $abstract)
                                <tr>
                                    <td>
                                        <strong>{{ $abstract->name }}</strong>
                                        <br><small class="text-muted">{{ $abstract->slug }}</small>
                                    </td>
                                    <td>
                                        @forelse($abstract->templates as $tpl)
                                            <div>
                                                <span class="label label-default">{{ optional($tpl->category)->name ?: '—' }}</span>
                                                @if($tpl->template_url)
                                                    <a href="{{ $tpl->template_url }}" target="_blank" class="btn btn-xs btn-link"><i class="ico-download"></i></a>
                                                @endif
                                            </div>
                                        @empty
                                            <span class="text-muted">—</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        @if($abstract->register_condition === 'open')
                                            <span class="label label-info">@lang('Abstract.register_condition_open')</span>
                                        @else
                                            <span class="label label-warning">@lang('Abstract.register_condition_registered')</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($abstract->status === 'published')
                                            <span class="label label-success">@lang('Abstract.published')</span>
                                        @else
                                            <span class="label label-default">@lang('Abstract.draft')</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('showEventAbstracts', ['event_id' => $event->id, 'tab' => 'submissions', 'abstract_id' => $abstract->id]) }}">
                                            {{ $abstract->submissions_count }}
                                        </a>
                                    </td>
                                    <td>
                                        <button data-modal-id="EditAbstract"
                                                data-href="{{ route('showEditEventAbstract', ['event_id' => $event->id, 'abstract_id' => $abstract->id]) }}"
                                                class="loadModal btn btn-xs btn-primary" type="button"><i class="ico-edit"></i></button>
                                        <button type="button" class="btn btn-xs btn-{{ $abstract->status === 'published' ? 'warning' : 'success' }} toggle-publish"
                                                data-id="{{ $abstract->id }}"><i class="ico-{{ $abstract->status === 'published' ? 'eye-blocked' : 'eye' }}"></i></button>
                                        @if($abstract->status === 'published')
                                            <button type="button" class="btn btn-xs btn-default copy-link" data-url="{{ $abstract->public_url }}"><i class="ico-link"></i></button>
                                        @endif
                                        <button type="button" class="btn btn-xs btn-danger delete-abstract" data-id="{{ $abstract->id }}" data-name="{{ $abstract->name }}"><i class="ico-trash"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer">{!! $abstracts->appends(request()->query())->render() !!}</div>
                </div>
            @else
                <div class="panel"><div class="panel-body text-center" style="padding:40px;">
                    <p class="text-muted">@lang('Abstract.no_abstracts')</p>
                </div></div>
            @endif

        @elseif($tab === 'categories')
            <div class="row" style="margin-bottom: 15px;">
                <div class="col-md-8">
                    <button data-modal-id="CreateAbstractCategory"
                            data-href="{{ route('showCreateEventAbstractCategory', ['event_id' => $event->id]) }}"
                            class="loadModal btn btn-success" type="button">
                        <i class="ico-plus2"></i> @lang('Abstract.create_category')
                    </button>
                </div>
                <div class="col-md-4">
                    {!! Form::open(['url' => route('showEventAbstracts', ['event_id' => $event->id]), 'method' => 'get']) !!}
                    {!! Form::hidden('tab', 'categories') !!}
                    <div class="input-group">
                        <input name="q" value="{{ $q }}" placeholder="@lang('Abstract.search')" class="form-control">
                        <span class="input-group-btn"><button class="btn btn-default" type="submit"><i class="ico-search"></i></button></span>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>

            @if($categories->count())
                <div class="panel">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>@lang('Abstract.name')</th>
                                    <th>@lang('Abstract.category_description')</th>
                                    <th>@lang('Abstract.sort_order')</th>
                                    <th>@lang('Abstract.status')</th>
                                    <th>@lang('Abstract.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                <tr class="{{ !$category->is_active ? 'danger' : '' }}">
                                    <td><strong>{{ $category->name }}</strong></td>
                                    <td>{{ $category->description ? \Illuminate\Support\Str::limit($category->description, 80) : '—' }}</td>
                                    <td>{{ $category->sort_order }}</td>
                                    <td>
                                        @if($category->is_active)
                                            <span class="label label-success">@lang('Abstract.active')</span>
                                        @else
                                            <span class="label label-default">@lang('Abstract.inactive')</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button data-modal-id="EditAbstractCategory"
                                                data-href="{{ route('showEditEventAbstractCategory', ['event_id' => $event->id, 'category_id' => $category->id]) }}"
                                                class="loadModal btn btn-xs btn-primary" type="button"><i class="ico-edit"></i></button>
                                        <button type="button" class="btn btn-xs btn-danger delete-abstract-category"
                                                data-id="{{ $category->id }}" data-name="{{ $category->name }}"><i class="ico-trash"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer">{!! $categories->appends(request()->query())->render() !!}</div>
                </div>
            @else
                <div class="panel"><div class="panel-body text-center" style="padding:40px;">
                    <p class="text-muted">@lang('Abstract.no_categories')</p>
                </div></div>
            @endif

        @elseif($tab === 'submissions')
            <div class="row" style="margin-bottom: 15px;">
                <div class="col-md-8">
                    <div class="btn-group">
                        <button type="button" class="btn btn-success bulk-action-btn" data-action="approved" disabled><i class="ico-checkmark"></i> @lang('Abstract.bulk_approve')</button>
                        <button type="button" class="btn btn-warning bulk-action-btn" data-action="rejected" disabled><i class="ico-close"></i> @lang('Abstract.bulk_reject')</button>
                        <button type="button" class="btn btn-danger bulk-action-btn" data-action="delete" disabled><i class="ico-trash"></i> @lang('Abstract.delete_selected')</button>
                    </div>
                </div>
                <div class="col-md-4">
                    {!! Form::open(['url' => route('showEventAbstracts', ['event_id' => $event->id]), 'method' => 'get', 'class' => 'form-inline text-right']) !!}
                    {!! Form::hidden('tab', 'submissions') !!}
                    <select name="abstract_id" class="form-control" onchange="this.form.submit()" style="max-width:45%; display:inline-block;">
                        <option value="">All Abstracts</option>
                        @foreach($abstractOptions as $id => $name)
                            <option value="{{ $id }}" {{ (string)$abstractFilter === (string)$id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="form-control" onchange="this.form.submit()" style="max-width:30%; display:inline-block;">
                        <option value="">Status</option>
                        <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>@lang('Abstract.pending')</option>
                        <option value="approved" {{ $statusFilter === 'approved' ? 'selected' : '' }}>@lang('Abstract.approved')</option>
                        <option value="rejected" {{ $statusFilter === 'rejected' ? 'selected' : '' }}>@lang('Abstract.rejected')</option>
                    </select>
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
                                <tr>
                                    <td><input type="checkbox" class="submission-checkbox" value="{{ $submission->id }}"></td>
                                    <td>{{ $submission->display_name }}</td>
                                    <td>{{ $submission->email }}</td>
                                    <td>{{ optional($submission->abstractDefinition)->name }}</td>
                                    <td>
                                        <span class="label label-{{ $submission->status === 'approved' ? 'success' : ($submission->status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($submission->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $submission->submitted_at ? $submission->submitted_at->format('Y-m-d H:i') : '—' }}</td>
                                    <td>
                                        @if($submission->file_path)
                                            <a href="{{ $submission->file_url }}" target="_blank" class="btn btn-xs btn-default"><i class="ico-download"></i></a>
                                        @else — @endif
                                    </td>
                                    <td>
                                        <button data-modal-id="AbstractSubmissionDetails"
                                                data-href="{{ route('showEventAbstractSubmissionDetails', ['event_id' => $event->id, 'submission_id' => $submission->id]) }}"
                                                class="loadModal btn btn-xs btn-info" type="button"><i class="ico-eye"></i></button>
                                        @if($submission->status !== 'approved')
                                            <button type="button" class="btn btn-xs btn-success status-btn" data-id="{{ $submission->id }}" data-status="approved"><i class="ico-checkmark"></i></button>
                                        @endif
                                        @if($submission->status !== 'rejected')
                                            <button type="button" class="btn btn-xs btn-warning status-btn" data-id="{{ $submission->id }}" data-status="rejected"><i class="ico-close"></i></button>
                                        @endif
                                        <button type="button" class="btn btn-xs btn-danger delete-submission" data-id="{{ $submission->id }}"><i class="ico-trash"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer">{!! $submissions->appends(request()->query())->render() !!}</div>
                </div>
            @else
                <div class="panel"><div class="panel-body text-center" style="padding:40px;">
                    <p class="text-muted">@lang('Abstract.no_submissions')</p>
                </div></div>
            @endif

        @elseif($tab === 'reviewers')
            <div class="row" style="margin-bottom: 15px;">
                <div class="col-md-8">
                    <button data-modal-id="CreateAbstractReviewer"
                            data-href="{{ route('showCreateEventAbstractReviewer', ['event_id' => $event->id]) }}"
                            class="loadModal btn btn-success" type="button">
                        <i class="ico-plus2"></i> @lang('Abstract.create_reviewer')
                    </button>
                    <button type="button" class="btn btn-default copy-reviewer-portal"
                            data-url="{{ route('showAbstractReviewLogin', ['event_id' => $event->id]) }}">
                        <i class="ico-link"></i> @lang('Abstract.copy_reviewer_portal_link')
                    </button>
                </div>
                <div class="col-md-4">
                    {!! Form::open(['url' => route('showEventAbstracts', ['event_id' => $event->id]), 'method' => 'get']) !!}
                    {!! Form::hidden('tab', 'reviewers') !!}
                    <div class="input-group">
                        <input name="q" value="{{ $q }}" placeholder="@lang('Abstract.search')" class="form-control">
                        <span class="input-group-btn"><button class="btn btn-default" type="submit"><i class="ico-search"></i></button></span>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>

            @if($reviewers->count())
                <div class="panel">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>@lang('Abstract.reviewer_name')</th>
                                    <th>@lang('Abstract.email')</th>
                                    <th>@lang('Abstract.reviewer_abstract_access')</th>
                                    <th>@lang('Abstract.reviewer_permissions')</th>
                                    <th>@lang('Abstract.status')</th>
                                    <th>@lang('Abstract.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reviewers as $reviewer)
                                <tr class="{{ !$reviewer->is_active ? 'danger' : '' }}">
                                    <td><strong>{{ $reviewer->name }}</strong></td>
                                    <td>{{ $reviewer->email }}</td>
                                    <td>
                                        @if($reviewer->access_all_abstracts)
                                            <span class="label label-info">@lang('Abstract.access_all_abstracts')</span>
                                        @else
                                            {{ $reviewer->abstracts->pluck('name')->implode(', ') ?: '—' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($reviewer->can_review)<span class="label label-success">@lang('Abstract.perm_review_short')</span>@endif
                                        @if($reviewer->can_edit)<span class="label label-warning">@lang('Abstract.perm_edit_short')</span>@endif
                                        @if($reviewer->can_delete)<span class="label label-danger">@lang('Abstract.perm_delete_short')</span>@endif
                                    </td>
                                    <td>
                                        @if($reviewer->is_active)
                                            <span class="label label-success">@lang('Abstract.active')</span>
                                        @else
                                            <span class="label label-default">@lang('Abstract.inactive')</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button data-modal-id="EditAbstractReviewer"
                                                data-href="{{ route('showEditEventAbstractReviewer', ['event_id' => $event->id, 'reviewer_id' => $reviewer->id]) }}"
                                                class="loadModal btn btn-xs btn-primary" type="button"><i class="ico-edit"></i></button>
                                        <button type="button" class="btn btn-xs btn-danger delete-abstract-reviewer"
                                                data-id="{{ $reviewer->id }}" data-name="{{ $reviewer->name }}"><i class="ico-trash"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer">{!! $reviewers->appends(request()->query())->render() !!}</div>
                </div>
            @else
                <div class="panel"><div class="panel-body text-center" style="padding:40px;">
                    <p class="text-muted">@lang('Abstract.no_reviewers')</p>
                </div></div>
            @endif
        @endif
    </div>
</div>
@stop

@section('foot')
<script>
$(function () {
    $('.delete-abstract').on('click', function () {
        if (!confirm('Delete abstract "' + $(this).data('name') + '"?')) return;
        $.ajax({
            url: '{{ url("event/{$event->id}/abstracts") }}/' + $(this).data('id'),
            type: 'DELETE', data: { _token: '{{ csrf_token() }}' },
            success: function (res) { if (res.status === 'success') location.reload(); else alert(res.message || 'Error'); }
        });
    });
    $('.toggle-publish').on('click', function () {
        $.post('{{ url("event/{$event->id}/abstracts") }}/' + $(this).data('id') + '/publish', { _token: '{{ csrf_token() }}' })
            .done(function (res) { if (res.status === 'success') location.reload(); });
    });
    $('.copy-link').on('click', function () {
        var url = $(this).data('url');
        if (navigator.clipboard) navigator.clipboard.writeText(url).then(function () { alert('Link copied!'); });
        else prompt('Copy link:', url);
    });
    $('.delete-abstract-category').on('click', function () {
        if (!confirm('Delete category "' + $(this).data('name') + '"?')) return;
        $.ajax({
            url: '{{ url("event/{$event->id}/abstracts/categories") }}/' + $(this).data('id'),
            type: 'DELETE', data: { _token: '{{ csrf_token() }}' },
            success: function (res) { if (res.status === 'success') location.reload(); else alert(res.message || 'Error'); },
            error: function (xhr) { alert((xhr.responseJSON && xhr.responseJSON.message) || 'Error'); }
        });
    });

    function selectedIds() {
        return $('.submission-checkbox:checked').map(function () { return $(this).val(); }).get();
    }
    function toggleBulk() { $('.bulk-action-btn').prop('disabled', selectedIds().length === 0); }
    $('#select-all-submissions').on('change', function () {
        $('.submission-checkbox').prop('checked', $(this).is(':checked')); toggleBulk();
    });
    $(document).on('change', '.submission-checkbox', toggleBulk);
    $('.status-btn').on('click', function () {
        if (!confirm('Mark as ' + $(this).data('status') + '?')) return;
        $.post('{{ url("event/{$event->id}/abstracts/submissions") }}/' + $(this).data('id') + '/status', {
            _token: '{{ csrf_token() }}', status: $(this).data('status')
        }).done(function (res) { if (res.status === 'success') location.reload(); else alert(res.message || 'Error'); });
    });
    $('.delete-submission').on('click', function () {
        if (!confirm('Delete this submission?')) return;
        $.ajax({
            url: '{{ url("event/{$event->id}/abstracts/submissions") }}/' + $(this).data('id'),
            type: 'DELETE', data: { _token: '{{ csrf_token() }}' },
            success: function (res) { if (res.status === 'success') location.reload(); }
        });
    });
    $('.bulk-action-btn').on('click', function () {
        var action = $(this).data('action'), ids = selectedIds();
        if (!ids.length || !confirm('Apply "' + action + '" to ' + ids.length + ' submission(s)?')) return;
        $.post('{{ route('bulkUpdateAbstractSubmissions', ['event_id' => $event->id]) }}', {
            _token: '{{ csrf_token() }}', ids: ids, action: action
        }).done(function (res) { if (res.status === 'success') location.reload(); else alert(res.message || 'Error'); });
    });
    $('.delete-abstract-reviewer').on('click', function () {
        if (!confirm('Delete reviewer "' + $(this).data('name') + '"?')) return;
        $.ajax({
            url: '{{ url("event/{$event->id}/abstracts/reviewers") }}/' + $(this).data('id'),
            type: 'DELETE', data: { _token: '{{ csrf_token() }}' },
            success: function (res) { if (res.status === 'success') location.reload(); else alert(res.message || 'Error'); },
            error: function (xhr) { alert((xhr.responseJSON && xhr.responseJSON.message) || 'Error'); }
        });
    });
    $('.copy-reviewer-portal').on('click', function () {
        var url = $(this).data('url');
        if (navigator.clipboard) navigator.clipboard.writeText(url).then(function () { alert('Portal link copied!'); });
        else prompt('Copy link:', url);
    });
});
</script>
@stop
