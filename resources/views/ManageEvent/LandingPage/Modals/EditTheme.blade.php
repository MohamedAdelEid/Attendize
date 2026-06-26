<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Edit Theme: {{ $theme->name }}</h4>
        </div>
        {!! Form::open(['url' => route('updateEventTheme', ['event_id' => $event->id, 'theme_id' => $theme->id]), 'class' => 'ajax', 'files' => true]) !!}
        <div class="modal-body">
            @include('ManageEvent.LandingPage.Partials.ThemeFormFields')
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Save Theme</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>
