<div role="dialog" class="modal fade" style="display: none;">
    {!! Form::open([
        'url' => route('postEditEventAbstractCategory', ['event_id' => $event->id, 'category_id' => $category->id]),
        'class' => 'ajax',
    ]) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-folder"></i>
                    @lang('Abstract.edit_category')
                </h3>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('name', trans('Abstract.category_name'), ['class' => 'control-label required']) !!}
                    {!! Form::text('name', $category->name, [
                        'class' => 'form-control',
                        'required' => 'required',
                    ]) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('description', trans('Abstract.category_description'), ['class' => 'control-label']) !!}
                    {!! Form::textarea('description', $category->description, [
                        'class' => 'form-control',
                        'rows' => 3,
                    ]) !!}
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('sort_order', trans('Abstract.sort_order'), ['class' => 'control-label']) !!}
                            {!! Form::number('sort_order', $category->sort_order, ['class' => 'form-control', 'min' => 0]) !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group" style="padding-top: 28px;">
                            <label class="checkbox-inline">
                                {!! Form::checkbox('is_active', 1, $category->is_active) !!}
                                @lang('Abstract.is_active')
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {!! Form::button(trans('basic.cancel'), ['class' => 'btn btn-danger', 'data-dismiss' => 'modal']) !!}
                {!! Form::submit(trans('basic.save_changes'), ['class' => 'btn btn-success']) !!}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>
