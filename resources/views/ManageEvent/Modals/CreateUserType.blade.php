<div role="dialog" class="modal fade" style="display: none;">
    {!! Form::open(['url' => route('postCreateEventUserType', ['event_id' => $event->id]), 'class' => 'ajax']) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-users"></i>
                    Create User Type
                </h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('name', 'User Type Name', ['class' => 'control-label required']) !!}
                            {!! Form::text('name', old('name'), [
                                'class' => 'form-control',
                                'placeholder' => 'Enter user type name (e.g., Student, Professional, VIP)',
                                'required' => 'required'
                            ]) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {!! Form::button(trans('basic.cancel'), ['class' => 'btn modal-close btn-danger', 'data-dismiss' => 'modal']) !!}
                {!! Form::submit('Create User Type', ['class' => 'btn btn-success']) !!}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>
