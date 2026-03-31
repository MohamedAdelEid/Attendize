@extends('Shared.Layouts.Master')

@section('title')
    @parent
    {{ $event->title }} - Add Member
@stop

@section('top_nav')
    @include('ManageEvent.Partials.TopNav')
@stop

@section('page_title')
    <i class="ico-user-plus mr5"></i> Add Member
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
        <form method="post" action="{{ route('postCreateEventMember', ['event_id' => $event->id]) }}">
            @csrf
            @include('ManageEvent.Members._member_form', ['formTitle' => 'Create Member'])
            <button type="submit" class="btn btn-success">Save Member</button>
        </form>
    </div>
</div>
@stop
