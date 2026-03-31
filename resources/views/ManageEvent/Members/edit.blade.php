@extends('Shared.Layouts.Master')

@section('title')
    @parent
    {{ $event->title }} - Edit Member
@stop

@section('top_nav')
    @include('ManageEvent.Partials.TopNav')
@stop

@section('page_title')
    <i class="ico-pencil mr5"></i> Edit Member
@stop

@section('menu')
    @include('ManageEvent.Partials.Sidebar')
@stop

@section('content')
<div class="row">
    <div class="col-md-12 mb10">
        <a href="{{ route('showEventMembersList', ['event_id' => $event->id]) }}" class="btn btn-default btn-sm">
            <i class="ico-arrow-left"></i> Back to Members List
        </a>
    </div>
    <div class="col-md-8">
        <form method="post" action="{{ route('postEditEventMember', ['event_id' => $event->id, 'member_id' => $member->id]) }}">
            @csrf
            @method('PUT')
            @include('ManageEvent.Members._member_form', ['formTitle' => 'Edit Member'])
            <button type="submit" class="btn btn-primary">Update Member</button>
            <a href="{{ route('showEventMember', ['event_id' => $event->id, 'member_id' => $member->id]) }}" class="btn btn-default">View</a>
        </form>
    </div>
</div>
@stop
