@extends('Shared.Layouts.Master')

@section('title')
    @parent
    Event Themes
@stop

@section('top_nav')
    @include('ManageEvent.Partials.TopNav')
@stop

@section('menu')
    @include('ManageEvent.Partials.Sidebar')
@stop

@section('page_title')
    <i class="ico-palette mr5"></i>
    Event Themes
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Themes
                        <a href="{{ route('createEventTheme', ['event_id' => $event->id]) }}" class="loadModal btn btn-success btn-xs pull-right">Create Theme</a>
                        <a href="{{ route('showEventLandingPage', ['event_id' => $event->id]) }}" class="btn btn-default btn-xs pull-right" style="margin-right:8px">Back to Landing Page</a>
                    </h3>
                </div>
                <div class="panel-body">
                    <p class="text-muted">Themes are reusable across events in your account. Assign a theme on the Landing Page settings page.</p>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Colors</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($themes as $theme)
                            <tr>
                                <td><strong>{{ $theme->name }}</strong><br><small class="text-muted">{{ $theme->description }}</small></td>
                                <td>
                                    <span style="display:inline-block;width:20px;height:20px;background:hsl({{ $theme->color_primary }});border-radius:3px" title="Primary"></span>
                                    <span style="display:inline-block;width:20px;height:20px;background:hsl({{ $theme->color_background }});border-radius:3px;border:1px solid #ccc" title="Background"></span>
                                </td>
                                <td>{{ $theme->is_active ? 'Active' : 'Inactive' }}</td>
                                <td class="text-right">
                                    <a href="{{ route('editEventTheme', ['event_id' => $event->id, 'theme_id' => $theme->id]) }}" class="loadModal btn btn-default btn-xs">Edit</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted">No themes yet. Create one to get started.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
