<div role="dialog" class="modal fade" style="display: none;">
    {!! Form::open([
        'url' => route('postEditEventAbstractReviewer', ['event_id' => $event->id, 'reviewer_id' => $reviewer->id]),
        'class' => 'ajax',
    ]) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-user"></i>
                    @lang('Abstract.edit_reviewer')
                </h3>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('name', trans('Abstract.reviewer_name'), ['class' => 'control-label required']) !!}
                    {!! Form::text('name', $reviewer->name, ['class' => 'form-control', 'required' => 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('email', trans('Abstract.email'), ['class' => 'control-label required']) !!}
                    {!! Form::email('email', $reviewer->email, ['class' => 'form-control', 'required' => 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('password', trans('Abstract.reviewer_password'), ['class' => 'control-label']) !!}
                    {!! Form::password('password', ['class' => 'form-control', 'autocomplete' => 'new-password', 'placeholder' => trans('Abstract.password_leave_blank')]) !!}
                </div>

                <hr>
                <h5>@lang('Abstract.reviewer_abstract_access')</h5>
                <div class="form-group">
                    <label class="checkbox-inline">
                        {!! Form::checkbox('access_all_abstracts', 1, $reviewer->access_all_abstracts, ['id' => 'access_all_abstracts_edit']) !!}
                        @lang('Abstract.access_all_abstracts')
                    </label>
                    <p class="help-block">@lang('Abstract.access_all_abstracts_help')</p>
                </div>
                <div class="form-group" id="abstract-ids-group-edit">
                    {!! Form::label('abstract_ids', trans('Abstract.select_abstracts'), ['class' => 'control-label']) !!}
                    @php $selectedIds = $reviewer->abstracts->pluck('id')->all(); @endphp
                    <select name="abstract_ids[]" id="abstract_ids_edit" class="form-control" multiple size="6">
                        @foreach($abstracts as $abstract)
                            <option value="{{ $abstract->id }}" {{ in_array($abstract->id, $selectedIds) ? 'selected' : '' }}>
                                {{ $abstract->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="help-block">@lang('Abstract.select_abstracts_help')</p>
                </div>

                <hr>
                <h5>@lang('Abstract.reviewer_permissions')</h5>
                <div class="form-group">
                    <label class="checkbox-inline">
                        {!! Form::checkbox('can_review', 1, $reviewer->can_review) !!}
                        @lang('Abstract.perm_review')
                    </label>
                </div>
                <div class="form-group">
                    <label class="checkbox-inline">
                        {!! Form::checkbox('can_edit', 1, $reviewer->can_edit) !!}
                        <strong>@lang('Abstract.perm_edit')</strong>
                        <span class="text-muted">— @lang('Abstract.perm_edit_help')</span>
                    </label>
                </div>
                <div class="form-group">
                    <label class="checkbox-inline">
                        {!! Form::checkbox('can_delete', 1, $reviewer->can_delete) !!}
                        <strong>@lang('Abstract.perm_delete')</strong>
                        <span class="text-muted">— @lang('Abstract.perm_delete_help')</span>
                    </label>
                </div>
                <div class="form-group">
                    <label class="checkbox-inline">
                        {!! Form::checkbox('is_active', 1, $reviewer->is_active) !!}
                        @lang('Abstract.is_active')
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                {!! Form::button(trans('basic.cancel'), ['class' => 'btn btn-danger', 'data-dismiss' => 'modal']) !!}
                {!! Form::submit(trans('Abstract.update_reviewer'), ['class' => 'btn btn-success']) !!}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>
<script>
(function () {
    function toggleAbstracts() {
        var all = document.getElementById('access_all_abstracts_edit');
        var group = document.getElementById('abstract-ids-group-edit');
        if (!all || !group) return;
        group.style.display = all.checked ? 'none' : 'block';
    }
    var allCb = document.getElementById('access_all_abstracts_edit');
    if (allCb) {
        allCb.addEventListener('change', toggleAbstracts);
        toggleAbstracts();
    }
})();
</script>
