@extends('Shared.Layouts.Master')

@section('title')
    @parent
    @lang('Abstract.show_categories')
@stop

@section('top_nav')
    @include('ManageEvent.Partials.TopNav')
@stop

@section('page_title')
    <i class="ico-folder mr5"></i>
    @lang('Abstract.show_categories')
@stop

@section('menu')
    @include('ManageEvent.Partials.Sidebar')
@stop

@section('page_header')
<div class="col-md-9">
    <div class="btn-toolbar" role="toolbar">
        <div class="btn-group btn-group-responsive">
            <button data-modal-id="CreateAbstractCategory"
                    data-href="{{ route('showCreateEventAbstractCategory', ['event_id' => $event->id]) }}"
                    class="loadModal btn btn-success" type="button">
                <i class="ico-plus2"></i> @lang('Abstract.create_category')
            </button>
        </div>
    </div>
</div>
<div class="col-md-3">
    {!! Form::open(['url' => route('showEventAbstractCategories', ['event_id' => $event->id]), 'method' => 'get']) !!}
    <div class="input-group">
        <input name="q" value="{{ $q ?? '' }}" placeholder="@lang('Abstract.search')" type="text" class="form-control">
        <span class="input-group-btn">
            <button class="btn btn-default" type="submit"><i class="ico-search"></i></button>
        </span>
    </div>
    {!! Form::close() !!}
</div>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
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
                                <td>
                                    @if($category->description)
                                        {{ \Illuminate\Support\Str::limit($category->description, 80) }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
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
                                            class="loadModal btn btn-xs btn-primary" type="button">
                                        <i class="ico-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-xs btn-danger delete-abstract-category"
                                            data-id="{{ $category->id }}" data-name="{{ $category->name }}">
                                        <i class="ico-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer">
                    {!! $categories->appends(request()->query())->render() !!}
                </div>
            </div>
        @else
            <div class="panel">
                <div class="panel-body text-center" style="padding: 50px 0;">
                    <div style="font-size: 4em; color: #ccc;"><i class="ico-folder"></i></div>
                    <p class="text-muted" style="font-size: 1.3em;">@lang('Abstract.no_categories')</p>
                </div>
            </div>
        @endif
    </div>
</div>
@stop

@section('foot')
<script>
$(function () {
    $('.delete-abstract-category').on('click', function () {
        var id = $(this).data('id');
        var name = $(this).data('name');
        if (!confirm('Delete category "' + name + '"?')) return;
        $.ajax({
            url: '{{ url("event/{$event->id}/abstracts/categories") }}/' + id,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function (res) {
                if (res.status === 'success') {
                    location.reload();
                } else {
                    alert(res.message || 'Error');
                }
            },
            error: function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error';
                alert(msg);
            }
        });
    });
});
</script>
@stop
