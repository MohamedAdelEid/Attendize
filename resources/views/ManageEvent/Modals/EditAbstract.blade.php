<div role="dialog" class="modal fade" id="edit-abstract-modal" style="display: none;">
    {!! Form::open([
        'url' => route('postEditEventAbstract', ['event_id' => $event->id, 'abstract_id' => $abstract->id]),
        'class' => 'ajax',
        'id' => 'edit-abstract-form',
        'files' => true,
    ]) !!}
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title"><i class="ico-file"></i> @lang('Abstract.edit_abstract')</h3>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="active"><a href="#abs-edit-settings" role="tab" data-toggle="tab"><i class="ico-info"></i> @lang('Abstract.tab_settings')</a></li>
                    <li><a href="#abs-edit-email" role="tab" data-toggle="tab"><i class="ico-envelope"></i> @lang('Abstract.tab_email')</a></li>
                    <li><a href="#abs-edit-fields" role="tab" data-toggle="tab"><i class="ico-list"></i> @lang('Abstract.tab_fields')</a></li>
                </ul>

                <div class="tab-content" style="padding-top: 15px;">
                    <div role="tabpanel" class="tab-pane active" id="abs-edit-settings">
                        <div class="form-group">
                            {!! Form::label('name', trans('Abstract.abstract_name'), ['class' => 'control-label required']) !!}
                            {!! Form::text('name', $abstract->name, ['class' => 'form-control', 'required' => 'required']) !!}
                            <p class="help-block">Slug: <code>{{ $abstract->slug }}</code></p>
                        </div>

                        <div class="form-group">
                            <label class="control-label required">@lang('Abstract.category_templates')</label>
                            <p class="help-block">@lang('Abstract.category_templates_help')</p>
                            <div id="edit-abstract-templates-list">
                                @foreach($abstract->templates as $index => $tpl)
                                    <div class="panel panel-default abstract-template-row" style="margin-bottom: 10px;" data-template-id="{{ $tpl->id }}">
                                        <div class="panel-body" style="padding: 12px;">
                                            <input type="hidden" name="templates[{{ $index }}][id]" value="{{ $tpl->id }}">
                                            <div class="row">
                                                <div class="col-sm-5">
                                                    <label class="control-label required">@lang('Abstract.category')</label>
                                                    <select name="templates[{{ $index }}][abstract_category_id]" class="form-control" required>
                                                        <option value="">@lang('Abstract.select_category')</option>
                                                        @foreach($categories as $id => $name)
                                                            <option value="{{ $id }}" {{ (int)$tpl->abstract_category_id === (int)$id ? 'selected' : '' }}>{{ $name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-sm-5">
                                                    <label class="control-label">@lang('Abstract.template_file')</label>
                                                    @if($tpl->template_url)
                                                        <p style="margin:0 0 5px;">
                                                            <a href="{{ $tpl->template_url }}" target="_blank"><i class="ico-download"></i> @lang('Abstract.current_template')</a>
                                                        </p>
                                                    @endif
                                                    <input type="file" name="templates[{{ $index }}][template]" class="form-control" accept=".pdf,.ppt,.pptx,.doc,.docx,.zip">
                                                </div>
                                                <div class="col-sm-2" style="padding-top: 24px;">
                                                    <button type="button" class="btn btn-danger btn-sm remove-existing-template-row" data-id="{{ $tpl->id }}"><i class="ico-remove"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div id="deleted-templates-container"></div>
                            <button type="button" class="btn btn-default btn-sm" id="edit-add-template-row-btn">
                                <i class="ico-plus"></i> @lang('Abstract.add_template_row')
                            </button>
                        </div>

                        <div class="form-group">
                            {!! Form::label('instructions', trans('Abstract.instructions'), ['class' => 'control-label']) !!}
                            <textarea name="instructions" id="edit-abstract-instructions" class="form-control summernote-editor" rows="6">{!! $abstract->instructions !!}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('max_submissions_per_user', trans('Abstract.max_submissions_per_user'), ['class' => 'control-label']) !!}
                                    {!! Form::number('max_submissions_per_user', $abstract->max_submissions_per_user, [
                                        'class' => 'form-control', 'min' => 1,
                                        'placeholder' => trans('Abstract.max_submissions_per_user_placeholder'),
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('approval_mode', trans('Abstract.approval_mode'), ['class' => 'control-label required']) !!}
                                    {!! Form::select('approval_mode', [
                                        'manual' => trans('Abstract.approval_manual'),
                                        'automatic' => trans('Abstract.approval_automatic'),
                                    ], $abstract->approval_mode, ['class' => 'form-control', 'required']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('register_condition', trans('Abstract.register_condition'), ['class' => 'control-label required']) !!}
                            {!! Form::select('register_condition', [
                                'open' => trans('Abstract.register_condition_open'),
                                'registered_only' => trans('Abstract.register_condition_registered'),
                            ], $abstract->register_condition, ['class' => 'form-control', 'id' => 'edit_register_condition', 'required']) !!}
                        </div>

                        <div id="edit-registered-only-options" style="display: none;">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('all_event_registrations', 1, $abstract->all_event_registrations, ['id' => 'edit_all_event_registrations']) !!}
                                    @lang('Abstract.all_event_registrations')
                                </label>
                            </div>
                            <div class="form-group" id="edit-registration-forms-group">
                                {!! Form::label('registration_ids', trans('Abstract.select_registration_forms'), ['class' => 'control-label']) !!}
                                {!! Form::select('registration_ids[]', $registrations, $selectedRegistrationIds, [
                                    'class' => 'form-control', 'multiple' => 'multiple', 'style' => 'height: 120px;',
                                ]) !!}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {!! Form::label('status', trans('Abstract.status'), ['class' => 'control-label required']) !!}
                                    {!! Form::select('status', [
                                        'draft' => trans('Abstract.draft'),
                                        'published' => trans('Abstract.published'),
                                    ], $abstract->status, ['class' => 'form-control', 'required']) !!}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {!! Form::label('start_date', trans('Abstract.start_date'), ['class' => 'control-label']) !!}
                                    {!! Form::text('start_date', $abstract->start_date ? $abstract->start_date->format(config('attendize.default_datetime_format')) : '', [
                                        'class' => 'form-control start', 'data-field' => 'datetime',
                                        'data-startend' => 'start', 'data-startendelem' => '.end', 'autocomplete' => 'off',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {!! Form::label('end_date', trans('Abstract.end_date'), ['class' => 'control-label']) !!}
                                    {!! Form::text('end_date', $abstract->end_date ? $abstract->end_date->format(config('attendize.default_datetime_format')) : '', [
                                        'class' => 'form-control end', 'data-field' => 'datetime',
                                        'data-startend' => 'end', 'data-startendelem' => '.start', 'autocomplete' => 'off',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="abs-edit-email">
                        <p class="text-muted">@lang('Abstract.email_placeholders_help')</p>
                        <div class="btn-group" style="margin-bottom: 10px;">
                            @foreach(['{full_name}','{email}','{phone}','{authors}','{details}','{domain}','{event_title}','{abstract_name}','{category_name}','{submission_status}'] as $ph)
                                <button type="button" class="btn btn-xs btn-default insert-placeholder-edit" data-placeholder="{{ $ph }}">{{ $ph }}</button>
                            @endforeach
                        </div>
                        <div class="form-group">
                            {!! Form::label('email_subject', trans('Abstract.email_subject'), ['class' => 'control-label']) !!}
                            {!! Form::text('email_subject', $abstract->email_subject, ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('email_body', trans('Abstract.email_body'), ['class' => 'control-label']) !!}
                            <textarea name="email_body" id="edit-abstract-email-body" class="form-control summernote-editor" rows="8">{!! $abstract->email_body !!}</textarea>
                        </div>
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('email_attach_template', 1, $abstract->email_attach_template) !!}
                                @lang('Abstract.email_attach_template')
                            </label>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="abs-edit-fields">
                        <div class="alert alert-info"><i class="ico-info"></i> @lang('Abstract.dynamic_fields_hint')</div>
                        <button type="button" class="btn btn-primary btn-sm" id="edit-add-abstract-field-btn" style="margin-bottom: 10px;">
                            <i class="ico-plus"></i> Add Field
                        </button>
                        <div id="edit-abstract-dynamic-fields-list">
                            @foreach($abstract->dynamicFormFields as $index => $field)
                                <div class="panel panel-default dynamic-field" style="margin-bottom: 10px;" data-field-id="{{ $field->id }}">
                                    <div class="panel-heading" style="padding: 8px 12px;">
                                        <div class="row">
                                            <div class="col-xs-8">
                                                <span class="drag-handle"><i class="ico-arrow-move"></i></span>
                                                <span class="position-badge badge">#<span class="position-number">{{ $index + 1 }}</span></span>
                                                <strong class="field-title">{{ $field->label }}</strong>
                                            </div>
                                            <div class="col-xs-4 text-right">
                                                <button type="button" class="btn btn-xs btn-danger remove-existing-field-btn" data-id="{{ $field->id }}"><i class="ico-remove"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <input type="hidden" name="dynamic_fields[{{ $index }}][id]" value="{{ $field->id }}">
                                        <input type="hidden" name="dynamic_fields[{{ $index }}][position]" class="field-position" value="{{ $field->sort_order }}">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label required">Label</label>
                                                    <input type="text" name="dynamic_fields[{{ $index }}][label]" class="form-control field-label" value="{{ $field->label }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label required">Type</label>
                                                    <select name="dynamic_fields[{{ $index }}][type]" class="form-control field-type">
                                                        @foreach($fieldTypes as $value => $label)
                                                            <option value="{{ $value }}" {{ $field->type === $value ? 'selected' : '' }}>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Description / Help text</label>
                                            <input type="text" name="dynamic_fields[{{ $index }}][description]" class="form-control" value="{{ $field->description }}">
                                        </div>
                                        <div class="form-group field-options" style="{{ in_array($field->type, ['select','checkbox','radio']) ? '' : 'display:none;' }}">
                                            <label class="control-label">Options</label>
                                            <textarea name="dynamic_fields[{{ $index }}][options]" class="form-control" rows="3">{{ is_array($field->options) ? implode("\n", $field->options) : '' }}</textarea>
                                        </div>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="dynamic_fields[{{ $index }}][is_required]" value="1" {{ $field->is_required ? 'checked' : '' }}>
                                                This field is required
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div id="deleted-fields-container"></div>
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

@include('ManageEvent.Partials.AbstractDynamicFieldBuilder', ['fieldTypes' => $fieldTypes])

<script type="text/template" id="edit-abstract-template-row-tpl">
    <div class="panel panel-default abstract-template-row" style="margin-bottom: 10px;">
        <div class="panel-body" style="padding: 12px;">
            <div class="row">
                <div class="col-sm-5">
                    <label class="control-label required">@lang('Abstract.category')</label>
                    <select name="templates[{INDEX}][abstract_category_id]" class="form-control" required>
                        <option value="">@lang('Abstract.select_category')</option>
                        @foreach($categories as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-5">
                    <label class="control-label required">@lang('Abstract.template_file')</label>
                    <input type="file" name="templates[{INDEX}][template]" class="form-control" accept=".pdf,.ppt,.pptx,.doc,.docx,.zip" required>
                </div>
                <div class="col-sm-2" style="padding-top: 24px;">
                    <button type="button" class="btn btn-danger btn-sm remove-template-row"><i class="ico-remove"></i></button>
                </div>
            </div>
        </div>
    </div>
</script>

<script>
(function () {
    var fieldCounter = {{ $abstract->dynamicFormFields->count() }};
    var templateCounter = {{ $abstract->templates->count() }};

    function addTemplateRow() {
        var html = $('#edit-abstract-template-row-tpl').html().replace(/\{INDEX\}/g, templateCounter++);
        var $row = $(html);
        $('#edit-abstract-templates-list').append($row);
        $row.find('.remove-template-row').on('click', function () {
            if ($('#edit-abstract-templates-list .abstract-template-row').length <= 1) {
                alert('@lang("Abstract.at_least_one_template")');
                return;
            }
            $row.remove();
        });
    }

    function toggleReg() {
        var isReg = $('#edit_register_condition').val() === 'registered_only';
        $('#edit-registered-only-options').toggle(isReg);
        $('#edit-registration-forms-group').toggle(!$('#edit_all_event_registrations').is(':checked'));
    }

    function toggleOptions($s) {
        $s.closest('.dynamic-field').find('.field-options').toggle(['select','checkbox','radio'].indexOf($s.val()) !== -1);
    }

    function updatePositions() {
        $('#edit-abstract-dynamic-fields-list .dynamic-field').each(function (i) {
            $(this).find('.field-position').val(i + 1);
            $(this).find('.position-number').text(i + 1);
        });
    }

    function addField() {
        var html = $('#abstract-field-template').html().replace(/\{INDEX\}/g, fieldCounter++);
        var $field = $(html);
        $('#edit-abstract-dynamic-fields-list').append($field);
        $field.find('.field-type').on('change', function () { toggleOptions($(this)); }).trigger('change');
        $field.find('.field-label').on('input', function () {
            $field.find('.field-title').text($(this).val() || 'New Field');
        });
        $field.find('.remove-field-btn').on('click', function () { $field.remove(); updatePositions(); });
        updatePositions();
    }

    setTimeout(function () {
        if (typeof window.initAbstractSummernote === 'function') {
            window.initAbstractSummernote('#edit-abstract-modal .summernote-editor', 260);
        }
        toggleReg();
        if (!$('#edit-abstract-templates-list .abstract-template-row').length) {
            addTemplateRow();
        }

        $('#edit_register_condition').on('change', toggleReg);
        $('#edit_all_event_registrations').on('change', toggleReg);
        $('#edit-add-template-row-btn').on('click', addTemplateRow);
        $('#edit-add-abstract-field-btn').on('click', addField);

        $('.remove-existing-template-row').on('click', function () {
            if ($('#edit-abstract-templates-list .abstract-template-row').length <= 1) {
                alert('@lang("Abstract.at_least_one_template")');
                return;
            }
            var id = $(this).data('id');
            $('#deleted-templates-container').append('<input type="hidden" name="deleted_templates[]" value="' + id + '">');
            $(this).closest('.abstract-template-row').remove();
        });

        $('#edit-abstract-dynamic-fields-list .field-type').each(function () {
            $(this).on('change', function () { toggleOptions($(this)); });
        });
        $('.remove-existing-field-btn').on('click', function () {
            $('#deleted-fields-container').append('<input type="hidden" name="deleted_fields[]" value="' + $(this).data('id') + '">');
            $(this).closest('.dynamic-field').remove();
            updatePositions();
        });

        $('.insert-placeholder-edit').on('click', function () {
            var ph = $(this).data('placeholder');
            var $body = $('#edit-abstract-email-body');
            if ($body.next('.note-editor').length) {
                $body.summernote('focus');
                $body.summernote('insertText', ph);
            } else {
                $body.val($body.val() + ph);
            }
        });

        if ($.fn.sortable) {
            $('#edit-abstract-dynamic-fields-list').sortable({ handle: '.drag-handle', update: updatePositions });
        }

        $('#edit-abstract-form').on('submit', function () {
            $('#edit-abstract-modal .summernote-editor').each(function () {
                if ($(this).next('.note-editor').length) {
                    $(this).val($(this).summernote('code'));
                }
            });
        });
    }, 150);
})();
</script>
