<div role="dialog" class="modal fade" style="display: none;">
    {!! Form::open([
        'url' => route('postCreateEventRegistrationCategory', ['event_id' => $event->id]),
        'class' => 'ajax',
    ]) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-folder-plus"></i>
                    @lang('Registration.create_category')
                </h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('name', trans('Registration.category_name'), ['class' => 'control-label required']) !!}
                            {!! Form::text('name', old('name'), [
                                'class' => 'form-control',
                                'placeholder' => trans('Registration.category_name_placeholder'),
                            ]) !!}
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    {!! Form::label('status', trans('Registration.category_status'), ['class' => 'control-label required']) !!}
                                    {!! Form::select(
                                        'status',
                                        ['active' => trans('Registration.active'), 'inactive' => trans('Registration.inactive')],
                                        old('status'),
                                        ['class' => 'form-control', 'required'],
                                    ) !!}
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('start_date', trans('Registration.start_date'), ['class' => ' control-label']) !!}
                                    {!! Form::text('start_date', old('start_date', $event->getFormattedDate('start_date')), [
                                        'class' => 'form-control start',
                                        'data-field' => 'datetime',
                                        'data-startend' => 'start',
                                        'data-startendelem' => '.end',
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-sm-6 ">
                                <div class="form-group">
                                    {!! Form::label('end_date', trans('Registration.end_date'), [
                                        'class' => ' control-label ',
                                    ]) !!}
                                    {!! Form::text('end_date', old('end_date', $event->getFormattedDate('end_date')), [
                                        'class' => 'form-control end',
                                        'data-field' => 'datetime',
                                        'data-startend' => 'end',
                                        'data-startendelem' => '.start',
                                    ]) !!}
                                </div>
                            </div>

                        </div>

                        <div class="form-group more-options">
                            {!! Form::label('description', trans('Registration.category_description'), ['class' => 'control-label']) !!}
                            {!! Form::text('description', old('description'), [
                                'class' => 'form-control',
                            ]) !!}
                        </div>

                    </div>


                    <div class="col-md-12">
                        <a href="javascript:void(0);" class="show-more-options">
                            @lang('ManageEvent.more_options')
                        </a>
                    </div>

                </div>

            </div> <!-- /end modal body-->
            <div class="modal-footer">
                {!! Form::button(trans('basic.cancel'), ['class' => 'btn modal-close btn-danger', 'data-dismiss' => 'modal']) !!}
                {!! Form::submit(trans('Registration.create_category'), ['class' => 'btn btn-success']) !!}
            </div>
        </div><!-- /end modal content-->
        {!! Form::close() !!}
    </div>
</div>
