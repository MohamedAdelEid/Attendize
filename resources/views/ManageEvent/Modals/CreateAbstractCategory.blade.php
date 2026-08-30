<div role="dialog" class="modal fade" style="display: none;">
    {!! Form::open([
        'url' => route('postCreateEventAbstractCategory', ['event_id' => $event->id]),
        'class' => 'ajax',
    ]) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-folder-plus"></i>
                    @lang('Abstract.create_category')
                </h3>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('name', trans('Abstract.category_name'), ['class' => 'control-label required']) !!}
                    {!! Form::text('name', old('name'), [
                        'class' => 'form-control',
                        'placeholder' => trans('Abstract.category_name_placeholder'),
                        'required' => 'required',
                    ]) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('description', trans('Abstract.category_description'), ['class' => 'control-label']) !!}
                    {!! Form::textarea('description', old('description'), [
                        'class' => 'form-control',
                        'rows' => 3,
                    ]) !!}
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('sort_order', trans('Abstract.sort_order'), ['class' => 'control-label']) !!}
                            {!! Form::number('sort_order', old('sort_order', 0), ['class' => 'form-control', 'min' => 0]) !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group" style="padding-top: 28px;">
                            <label class="checkbox-inline">
                                {!! Form::checkbox('is_active', 1, true) !!}
                                @lang('Abstract.is_active')
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {!! Form::button(trans('basic.cancel'), ['class' => 'btn btn-danger', 'data-dismiss' => 'modal']) !!}
                {!! Form::submit(trans('basic.submit'), ['class' => 'btn btn-success']) !!}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>
