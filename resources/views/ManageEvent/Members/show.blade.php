@extends('Shared.Layouts.Master')

@section('title')
    @parent
    {{ $event->title }} - Member Details
@stop

@section('top_nav')
    @include('ManageEvent.Partials.TopNav')
@stop

@section('page_title')
    <i class="ico-user mr5"></i> Member Details
@stop

@section('menu')
    @include('ManageEvent.Partials.Sidebar')
@stop

@section('content')
@php $memberData = $member->getDataByKey(); @endphp
<div class="row">
    <div class="col-md-12 mb10">
        <a href="{{ route('showEventMembersList', ['event_id' => $event->id]) }}" class="btn btn-default btn-sm">
            <i class="ico-arrow-left"></i> Back to Members List
        </a>
        <a href="{{ route('showEditEventMember', ['event_id' => $event->id, 'member_id' => $member->id]) }}" class="btn btn-primary btn-sm">
            <i class="ico-pencil"></i> Edit
        </a>
    </div>

    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Member #{{ $member->id }}</h3>
            </div>
            <table class="table table-striped mb0">
                <tbody>
                    <tr>
                        <th width="220">Status</th>
                        <td>{{ $member->status }}</td>
                    </tr>
                    @foreach($fields as $field)
                    <tr>
                        <th>{{ $field->label }} <small class="text-muted">({{ $field->field_key }})</small></th>
                        <td>{{ $memberData->get($field->field_key) ?: '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
