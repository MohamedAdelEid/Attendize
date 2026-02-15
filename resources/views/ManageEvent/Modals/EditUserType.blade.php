<div role="dialog" class="modal fade" style="display: none;">
    {!! Form::model($userType, [
        'url' => route('postEditEventUserType', ['event_id' => $event->id, 'user_type_id' => $userType->id]),
        'class' => 'ajax'
    ]) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-edit"></i>
                    Edit User Type
                </h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('name', 'User Type Name', ['class' => 'control-label required']) !!}
                            {!! Form::text('name', null, [
                                'class' => 'form-control',
                                'placeholder' => 'Enter user type name',
                                'required' => 'required'
                            ]) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">
                                <input type="checkbox" name="show_on_landing" value="1" {{ $userType->show_on_landing ? 'checked' : '' }}> Show on landing / home page
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">Sub-types (optional)</label>
                            <p class="help-block">Sub-types for this user type. When adding a user, they can pick one of these.</p>
                            <div id="edit-option-list">
                                @foreach($userType->options as $opt)
                                <div class="input-group option-row" style="margin-bottom: 6px;">
                                    <input type="text" name="option_names[]" class="form-control" value="{{ $opt->name }}" placeholder="e.g. user_type1">
                                    <span class="input-group-btn"><button type="button" class="btn btn-default btn-remove-option" tabindex="-1">&times;</button></span>
                                </div>
                                @endforeach
                                @if($userType->options->count() === 0)
                                <div class="input-group option-row" style="margin-bottom: 6px;">
                                    <input type="text" name="option_names[]" class="form-control" placeholder="e.g. user_type1">
                                    <span class="input-group-btn"><button type="button" class="btn btn-default btn-remove-option" tabindex="-1">&times;</button></span>
                                </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-default" id="add-option-row-edit"><i class="ico-plus"></i> Add sub-type</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {!! Form::button(trans('basic.cancel'), ['class' => 'btn modal-close btn-danger', 'data-dismiss' => 'modal']) !!}
                {!! Form::submit('Update User Type', ['class' => 'btn btn-success']) !!}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>
<script>
$(function() {
    $('#add-option-row-edit').on('click', function() {
        var row = '<div class="input-group option-row" style="margin-bottom: 6px;"><input type="text" name="option_names[]" class="form-control" placeholder="e.g. user_type1"><span class="input-group-btn"><button type="button" class="btn btn-default btn-remove-option" tabindex="-1">&times;</button></span></div>';
        $('#edit-option-list').append(row);
    });
    $(document).on('click', '.btn-remove-option', function() {
        $(this).closest('.option-row').remove();
    });
});
</script>
