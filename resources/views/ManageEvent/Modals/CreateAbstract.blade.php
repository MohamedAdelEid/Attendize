<div role="dialog" class="modal fade" id="create-abstract-modal" style="display: none;">
    {!! Form::open([
        'url' => route('postCreateEventAbstract', ['event_id' => $event->id]),
        'class' => 'ajax',
        'id' => 'create-abstract-form',
        'files' => true,
    ]) !!}
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title"><i class="ico-file"></i> @lang('Abstract.create_abstract')</h3>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="active"><a href="#abs-settings" role="tab" data-toggle="tab"><i class="ico-info"></i> @lang('Abstract.tab_settings')</a></li>
                    <li><a href="#abs-email" role="tab" data-toggle="tab"><i class="ico-envelope"></i> @lang('Abstract.tab_email')</a></li>
                    <li><a href="#abs-fields" role="tab" data-toggle="tab"><i class="ico-list"></i> @lang('Abstract.tab_fields')</a></li>
                </ul>

                <div class="tab-content" style="padding-top: 15px;">
                    <div role="tabpanel" class="tab-pane active" id="abs-settings">
                        <div class="form-group">
                            {!! Form::label('name', trans('Abstract.abstract_name'), ['class' => 'control-label required']) !!}
                            {!! Form::text('name', old('name'), [
                                'class' => 'form-control',
                                'placeholder' => trans('Abstract.abstract_name_placeholder'),
                                'required' => 'required',
                            ]) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label required">@lang('Abstract.category_templates')</label>
                            <p class="help-block">@lang('Abstract.category_templates_help')</p>
                            <div id="abstract-templates-list"></div>
                            <button type="button" class="btn btn-default btn-sm" id="add-template-row-btn">
                                <i class="ico-plus"></i> @lang('Abstract.add_template_row')
                            </button>
                        </div>

                        <div class="form-group">
                            {!! Form::label('instructions', trans('Abstract.instructions'), ['class' => 'control-label']) !!}
                            <textarea name="instructions" id="abstract-instructions" class="form-control summernote-editor" rows="6">{{ old('instructions') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('max_submissions_per_user', trans('Abstract.max_submissions_per_user'), ['class' => 'control-label']) !!}
                                    {!! Form::number('max_submissions_per_user', old('max_submissions_per_user'), [
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
                                    ], old('approval_mode', 'manual'), ['class' => 'form-control', 'required']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('register_condition', trans('Abstract.register_condition'), ['class' => 'control-label required']) !!}
                            {!! Form::select('register_condition', [
                                'open' => trans('Abstract.register_condition_open'),
                                'registered_only' => trans('Abstract.register_condition_registered'),
                            ], old('register_condition', 'open'), ['class' => 'form-control', 'id' => 'register_condition', 'required']) !!}
                        </div>

                        <div id="registered-only-options" style="display: none;">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('all_event_registrations', 1, false, ['id' => 'all_event_registrations']) !!}
                                    @lang('Abstract.all_event_registrations')
                                </label>
                                <p class="help-block">@lang('Abstract.all_event_registrations_help')</p>
                            </div>
                            <div class="form-group" id="registration-forms-group">
                                {!! Form::label('registration_ids', trans('Abstract.select_registration_forms'), ['class' => 'control-label']) !!}
                                {!! Form::select('registration_ids[]', $registrations, old('registration_ids'), [
                                    'class' => 'form-control', 'multiple' => 'multiple', 'id' => 'registration_ids', 'style' => 'height: 120px;',
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
                                    ], old('status', 'draft'), ['class' => 'form-control', 'required']) !!}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {!! Form::label('start_date', trans('Abstract.start_date'), ['class' => 'control-label']) !!}
                                    {!! Form::text('start_date', old('start_date'), [
                                        'class' => 'form-control start', 'data-field' => 'datetime',
                                        'data-startend' => 'start', 'data-startendelem' => '.end', 'autocomplete' => 'off',
                                        'placeholder' => trans('Abstract.dates_optional_help'),
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {!! Form::label('end_date', trans('Abstract.end_date'), ['class' => 'control-label']) !!}
                                    {!! Form::text('end_date', old('end_date'), [
                                        'class' => 'form-control end', 'data-field' => 'datetime',
                                        'data-startend' => 'end', 'data-startendelem' => '.start', 'autocomplete' => 'off',
                                        'placeholder' => trans('Abstract.dates_optional_help'),
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="abs-email">
                        <p class="text-muted">@lang('Abstract.email_placeholders_help')</p>
                        <div class="btn-group" style="margin-bottom: 10px; flex-wrap: wrap;">
                            @foreach(['{full_name}','{email}','{phone}','{authors}','{details}','{domain}','{event_title}','{abstract_name}','{category_name}','{submission_status}'] as $ph)
                                <button type="button" class="btn btn-xs btn-default insert-placeholder" data-placeholder="{{ $ph }}">{{ $ph }}</button>
                            @endforeach
                        </div>
                        <div class="form-group">
                            {!! Form::label('email_subject', trans('Abstract.email_subject'), ['class' => 'control-label']) !!}
                            {!! Form::text('email_subject', old('email_subject', 'Your abstract submission for {abstract_name}'), ['class' => 'form-control', 'id' => 'email_subject']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('email_body', trans('Abstract.email_body'), ['class' => 'control-label']) !!}
                            <textarea name="email_body" id="abstract-email-body" class="form-control summernote-editor" rows="8">{!! old('email_body', '<p>Dear {full_name},</p><p>Your abstract submission for <strong>{abstract_name}</strong> has been <strong>{submission_status}</strong>.</p><p>Best regards,<br>{event_title}</p>') !!}</textarea>
                        </div>
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('email_attach_template', 1, true) !!}
                                @lang('Abstract.email_attach_template')
                            </label>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="abs-fields">
                        <div class="alert alert-info"><i class="ico-info"></i> @lang('Abstract.dynamic_fields_hint')</div>
                        <button type="button" class="btn btn-primary btn-sm" id="add-abstract-field-btn" style="margin-bottom: 10px;">
                            <i class="ico-plus"></i> Add Field
                        </button>
                        <div id="abstract-dynamic-fields-list"></div>
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

@include('ManageEvent.Partials.AbstractDynamicFieldBuilder', ['fieldTypes' => $fieldTypes])

<script type="text/template" id="abstract-template-row-tpl">
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
    var fieldCounter = 0;
    var templateCounter = 0;
    var categoriesEmpty = {{ $categories->isEmpty() ? 'true' : 'false' }};

    function addTemplateRow() {
        var html = $('#abstract-template-row-tpl').html().replace(/\{INDEX\}/g, templateCounter++);
        var $row = $(html);
        $('#abstract-templates-list').append($row);
        $row.find('.remove-template-row').on('click', function () {
            if ($('#abstract-templates-list .abstract-template-row').length <= 1) {
                alert('@lang("Abstract.at_least_one_template")');
                return;
            }
            $row.remove();
        });
    }

    function toggleRegisteredOptions() {
        var isRegistered = $('#register_condition').val() === 'registered_only';
        $('#registered-only-options').toggle(isRegistered);
        $('#registration-forms-group').toggle(!$('#all_event_registrations').is(':checked'));
    }

    function toggleOptions($select) {
        $select.closest('.dynamic-field').find('.field-options')
            .toggle(['select', 'checkbox', 'radio'].indexOf($select.val()) !== -1);
    }

    function updatePositions() {
        $('#abstract-dynamic-fields-list .dynamic-field').each(function (i) {
            $(this).find('.field-position').val(i + 1);
            $(this).find('.position-number').text(i + 1);
        });
    }

    function addField() {
        var html = $('#abstract-field-template').html().replace(/\{INDEX\}/g, fieldCounter++);
        var $field = $(html);
        $('#abstract-dynamic-fields-list').append($field);
        $field.find('.field-type').on('change', function () { toggleOptions($(this)); }).trigger('change');
        $field.find('.field-label').on('input', function () {
            $field.find('.field-title').text($(this).val() || 'New Field');
        });
        $field.find('.remove-field-btn').on('click', function () { $field.remove(); updatePositions(); });
        updatePositions();
    }

    setTimeout(function () {
        if (typeof window.initAbstractSummernote === 'function') {
            window.initAbstractSummernote('#create-abstract-modal .summernote-editor', 260);
        }
        addTemplateRow();
        toggleRegisteredOptions();

        $('#register_condition').on('change', toggleRegisteredOptions);
        $('#all_event_registrations').on('change', toggleRegisteredOptions);
        $('#add-template-row-btn').on('click', addTemplateRow);
        $('#add-abstract-field-btn').on('click', addField);

        $(document).off('click.abstractPh').on('click.abstractPh', '.insert-placeholder', function () {
            var ph = $(this).data('placeholder');
            var $body = $('#abstract-email-body');
            if ($body.next('.note-editor').length) {
                $body.summernote('focus');
                $body.summernote('insertText', ph);
            } else {
                $body.val($body.val() + ph);
            }
        });

        if ($.fn.sortable) {
            $('#abstract-dynamic-fields-list').sortable({ handle: '.drag-handle', update: updatePositions });
        }

        $('#create-abstract-form').on('submit', function () {
            if (categoriesEmpty) {
                alert('@lang("Abstract.no_categories")');
                return false;
            }
            $('#create-abstract-modal .summernote-editor').each(function () {
                if ($(this).next('.note-editor').length) {
                    $(this).val($(this).summernote('code'));
                }
            });
        });

        $('#create-abstract-modal').on('hidden.bs.modal', function () {
            if (typeof window.destroyAbstractSummernote === 'function') {
                window.destroyAbstractSummernote('#create-abstract-modal .summernote-editor');
            }
        });
    }, 150);
})();
</script>
