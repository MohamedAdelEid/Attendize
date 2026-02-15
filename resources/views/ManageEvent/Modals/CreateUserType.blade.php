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
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">
                                <input type="checkbox" name="show_on_landing" value="1" {{ old('show_on_landing', true) ? 'checked' : '' }}> Show on landing / home page
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">Sub-types (optional)</label>
                            <p class="help-block">Add sub-types for this user type (e.g. Type A, Type B). When adding a user, they can pick one of these.</p>
                            <div id="create-option-list">
                                <div class="input-group option-row" style="margin-bottom: 6px;">
                                    <input type="text" name="option_names[]" class="form-control" placeholder="e.g. user_type1">
                                    <span class="input-group-btn"><button type="button" class="btn btn-default btn-remove-option" tabindex="-1">&times;</button></span>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-default" id="add-option-row"><i class="ico-plus"></i> Add sub-type</button>
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
<script>
$(function() {
    $('#add-option-row').on('click', function() {
        var row = '<div class="input-group option-row" style="margin-bottom: 6px;"><input type="text" name="option_names[]" class="form-control" placeholder="e.g. user_type1"><span class="input-group-btn"><button type="button" class="btn btn-default btn-remove-option" tabindex="-1">&times;</button></span></div>';
        $('#create-option-list').append(row);
    });
    $(document).on('click', '.btn-remove-option', function() {
        $(this).closest('.option-row').remove();
    });
});
</script>
