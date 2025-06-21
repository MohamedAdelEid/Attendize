<div role="dialog" class="modal fade" style="display: none;">
    {!! Form::open(['url' => route('postDeleteEventUserType', ['event_id' => $userType->event->id, 'user_type_id' => $userType->id]), 'class' => 'ajax']) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-trash"></i>
                    Delete User Type
                </h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <p>Are you sure you want to delete the user type <strong>{{ $userType->name }}</strong>?</p>
                        <p class="text-danger"><strong>This action cannot be undone.</strong></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {!! Form::button(trans('basic.cancel'), ['class' => 'btn modal-close btn-default', 'data-dismiss' => 'modal']) !!}
                {!! Form::submit('Delete User Type', ['class' => 'btn btn-danger']) !!}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>
