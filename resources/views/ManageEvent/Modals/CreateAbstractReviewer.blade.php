<div role="dialog" class="modal fade" style="display: none;">
    {!! Form::open([
        'url' => route('postCreateEventAbstractReviewer', ['event_id' => $event->id]),
        'class' => 'ajax',
    ]) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-user-plus"></i>
                    @lang('Abstract.create_reviewer')
                </h3>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('name', trans('Abstract.reviewer_name'), ['class' => 'control-label required']) !!}
                    {!! Form::text('name', old('name'), ['class' => 'form-control', 'required' => 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('email', trans('Abstract.email'), ['class' => 'control-label required']) !!}
                    {!! Form::email('email', old('email'), ['class' => 'form-control', 'required' => 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('password', trans('Abstract.reviewer_password'), ['class' => 'control-label required']) !!}
                    {!! Form::password('password', ['class' => 'form-control', 'required' => 'required', 'autocomplete' => 'new-password']) !!}
                </div>

                <hr>
                <h5>@lang('Abstract.reviewer_abstract_access')</h5>
                <div class="form-group">
                    <label class="checkbox-inline">
                        {!! Form::checkbox('access_all_abstracts', 1, false, ['id' => 'access_all_abstracts']) !!}
                        @lang('Abstract.access_all_abstracts')
                    </label>
                    <p class="help-block">@lang('Abstract.access_all_abstracts_help')</p>
                </div>
                <div class="form-group" id="abstract-ids-group">
                    {!! Form::label('abstract_ids', trans('Abstract.select_abstracts'), ['class' => 'control-label']) !!}
                    <select name="abstract_ids[]" id="abstract_ids" class="form-control" multiple size="6">
                        @foreach($abstracts as $abstract)
                            <option value="{{ $abstract->id }}">{{ $abstract->name }}</option>
                        @endforeach
                    </select>
                    <p class="help-block">@lang('Abstract.select_abstracts_help')</p>
                </div>

                <hr>
                <h5>@lang('Abstract.reviewer_permissions')</h5>
                <div class="form-group">
                    <label class="checkbox-inline">
                        {!! Form::checkbox('can_review', 1, true) !!}
                        @lang('Abstract.perm_review')
                    </label>
                </div>
                <div class="form-group">
                    <label class="checkbox-inline">
                        {!! Form::checkbox('can_edit', 1, false) !!}
                        <strong>@lang('Abstract.perm_edit')</strong>
                        <span class="text-muted">— @lang('Abstract.perm_edit_help')</span>
                    </label>
                </div>
                <div class="form-group">
                    <label class="checkbox-inline">
                        {!! Form::checkbox('can_delete', 1, false) !!}
                        <strong>@lang('Abstract.perm_delete')</strong>
                        <span class="text-muted">— @lang('Abstract.perm_delete_help')</span>
                    </label>
                </div>
                <div class="form-group">
                    <label class="checkbox-inline">
                        {!! Form::checkbox('is_active', 1, true) !!}
                        @lang('Abstract.is_active')
                    </label>
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
<script>
(function () {
    function toggleAbstracts() {
        var all = document.getElementById('access_all_abstracts');
        var group = document.getElementById('abstract-ids-group');
        if (!all || !group) return;
        group.style.display = all.checked ? 'none' : 'block';
    }
    var allCb = document.getElementById('access_all_abstracts');
    if (allCb) {
        allCb.addEventListener('change', toggleAbstracts);
        toggleAbstracts();
    }
})();
</script>
