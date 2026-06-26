<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Create Theme</h4>
        </div>
        {!! Form::open(['url' => route('storeEventTheme', ['event_id' => $event->id]), 'class' => 'ajax', 'files' => true]) !!}
        <div class="modal-body">
            @include('ManageEvent.LandingPage.Partials.ThemeFormFields')
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Create Theme</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>
