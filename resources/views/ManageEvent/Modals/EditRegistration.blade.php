{{-- resources/views/ManageEvent/Modals/EditRegistration.blade.php --}}
<div role="dialog" class="modal fade" id="edit-registration-modal">
    {!! Form::open([
        'url' => route('postEditRegistration', ['event_id' => $event_id, 'registration_id' => $registration->id]),
        'class' => 'ajax',
        'files' => true,
    ]) !!}
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-edit"></i>
                    Edit Registration: {{ $registration->name }}
                </h3>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#basic-info-edit" aria-controls="basic-info-edit" role="tab" data-toggle="tab">
                            <i class="ico-info"></i> Basic Information
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#dynamic-fields-edit" aria-controls="dynamic-fields-edit" role="tab" data-toggle="tab">
                            <i class="ico-list"></i> Custom Fields
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="basic-info-edit">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="form-group">
                                    {!! Form::label('name', 'Registration Name', ['class' => 'control-label required']) !!}
                                    {!! Form::text('name', $registration->name, [
                                        'class' => 'form-control',
                                        'placeholder' => 'Enter Registration Name',
                                        'required' => 'required',
                                    ]) !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('image', 'Image', ['class' => 'control-label']) !!}
                                    <div class="input-group">
                                        {!! Form::styledFile('image') !!}
                                    </div>
                                    @if($registration->image)
                                        <div class="help-block">
                                            <img src="{{ asset('storage/' . $registration->image) }}" alt="{{ $registration->name }}" style="max-width: 100px; max-height: 100px;">
                                            <p><small>Current image. Upload a new one to replace it.</small></p>
                                        </div>
                                    @endif
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            {!! Form::label('max_participants', trans('Registration.max_participants'), [
                                                'class' => 'control-label',
                                            ]) !!}
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="ico-users"></i>
                                                </span>
                                                {!! Form::number('max_participants', $registration->max_participants, [
                                                    'class' => 'form-control',
                                                    'placeholder' => trans('Registration.max_participants_placeholder'),
                                                ]) !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            {!! Form::label('category_id', trans('Registration.categories'), ['class' => 'control-label required']) !!}
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="ico-tag"></i>
                                                </span>
                                                {!! Form::select('category_id', $categories, $registration->category_id, [
                                                    'class' => 'form-control',
                                                    'style' => 'width: 100%',
                                                    'required' => 'required',
                                                ]) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('start_date', trans('Event.event_start_date'), ['class' => 'required control-label']) !!}
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="ico-calendar"></i>
                                                </span>
                                                {!! Form::text('start_date', $registration->getFormattedDate('start_date'), [
                                                    'class' => 'form-control start hasDatepicker',
                                                    'data-field' => 'datetime',
                                                    'data-startend' => 'start',
                                                    'data-startendelem' => '.end',
                                                    'readonly' => '',
                                                ]) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('end_date', trans('Event.event_end_date'), [
                                                'class' => 'control-label',
                                            ]) !!}
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="ico-calendar"></i>
                                                </span>
                                                {!! Form::text('end_date', $registration->getFormattedDate('end_date'), [
                                                    'class' => 'form-control end hasDatepicker',
                                                    'data-field' => 'datetime',
                                                    'data-startend' => 'end',
                                                    'data-startendelem' => '.start',
                                                    'readonly' => '',
                                                ]) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('approval_status', 'Approval Status', ['class' => 'control-label required']) !!}
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="ico-checkmark"></i>
                                                </span>
                                                {!! Form::select('approval_status', ['automatic' => 'Automatic', 'manual' => 'Manual'], $registration->approval_status, [
                                                    'class' => 'form-control',
                                                ]) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('status', 'Status', ['class' => 'control-label required']) !!}
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="ico-switch"></i>
                                                </span>
                                                {!! Form::select('status', ['active' => 'Active', 'inactive' => 'Inactive'], $registration->status, [
                                                    'class' => 'form-control',
                                                ]) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="dynamic-fields-edit">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    <i class="ico-list"></i> Custom Registration Fields
                                    <span class="pull-right">
                                        <a href="javascript:void(0);" class="btn btn-xs btn-success" id="add-field-btn">
                                            <i class="ico-plus"></i> Add Field
                                        </a>
                                    </span>
                                </h3>
                            </div>
                            <div class="panel-body">
                                <p class="text-muted">
                                    <i class="ico-info-circle"></i> Add or edit custom fields to collect additional information
                                    from registrants.
                                </p>

                                <div class="custom-fields-container">
                                    <div class="alert alert-info">
                                        <i class="ico-info-circle"></i> Default fields (First Name, Last Name, Email,
                                        Phone) will be automatically added.
                                    </div>

                                    <div id="dynamic-fields-list">
                                        <!-- Existing dynamic fields will be loaded here -->
                                        @foreach($registration->dynamicFormFields as $index => $field)
                                            <div class="dynamic-field panel panel-default" data-field-index="{{ $index }}" data-field-id="{{ $field->id }}">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        <span class="drag-handle" title="Drag to reorder"><i class="ico-arrows-v"></i></span>
                                                        <i class="ico-list"></i> <span class="field-title">{{ $field->label }}</span>
                                                        <span class="position-badge">Position: <span class="position-number">{{ $index + 1 }}</span></span>
                                                        <span class="pull-right">
                                                            <div class="btn-group position-controls">
                                                                <button type="button" class="btn btn-xs btn-default move-up-btn" title="Move Up">
                                                                    <i class="ico-arrow-up"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-xs btn-default move-down-btn" title="Move Down">
                                                                    <i class="ico-arrow-down"></i>
                                                                </button>
                                                            </div>
                                                            <a href="javascript:void(0);" class="btn btn-xs btn-danger remove-field-btn">
                                                                <i class="ico-trash"></i>
                                                            </a>
                                                        </span>
                                                    </h4>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label required">Field Label</label>
                                                                <input type="text" name="dynamic_fields[{{ $index }}][label]" class="form-control field-label"
                                                                    placeholder="Enter field label" value="{{ $field->label }}" required>
                                                                <input type="hidden" name="dynamic_fields[{{ $index }}][id]" value="{{ $field->id }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label required">Field Type</label>
                                                                <select name="dynamic_fields[{{ $index }}][type]" class="form-control field-type" required>
                                                                    <option value="text" {{ $field->type == 'text' ? 'selected' : '' }}>Text</option>
                                                                    <option value="email" {{ $field->type == 'email' ? 'selected' : '' }}>Email</option>
                                                                    <option value="number" {{ $field->type == 'number' ? 'selected' : '' }}>Number</option>
                                                                    <option value="tel" {{ $field->type == 'tel' ? 'selected' : '' }}>Telephone</option>
                                                                    <option value="date" {{ $field->type == 'date' ? 'selected' : '' }}>Date</option>
                                                                    <option value="time" {{ $field->type == 'time' ? 'selected' : '' }}>Time</option>
                                                                    <option value="datetime-local" {{ $field->type == 'datetime-local' ? 'selected' : '' }}>Date & Time</option>
                                                                    <option value="url" {{ $field->type == 'url' ? 'selected' : '' }}>URL</option>
                                                                    <option value="textarea" {{ $field->type == 'textarea' ? 'selected' : '' }}>Text Area</option>
                                                                    <option value="select" {{ $field->type == 'select' ? 'selected' : '' }}>Dropdown</option>
                                                                    <option value="checkbox" {{ $field->type == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                                                                    <option value="radio" {{ $field->type == 'radio' ? 'selected' : '' }}>Radio Button</option>
                                                                    <option value="file" {{ $field->type == 'file' ? 'selected' : '' }}>File Upload</option>
                                                                    <option value="country" {{ $field->type == 'country' ? 'selected' : '' }}>Country</option>
                                                                    <option value="city" {{ $field->type == 'city' ? 'selected' : '' }}>City</option>
                                                                    <option value="user_types" {{ $field->type == 'user_types' ? 'selected' : '' }}>User Types</option>
                                                                    <option value="conference" {{ $field->type == 'conference' ? 'selected' : '' }}>Conference</option>
                                                                    <option value="profession" {{ $field->type == 'profession' ? 'selected' : '' }}>Profession</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Hidden field to store position -->
                                                    <input type="hidden" name="dynamic_fields[{{ $index }}][position]" class="field-position" value="{{ $index + 1 }}">

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group field-options" style="{{ in_array($field->type, ['select', 'checkbox', 'radio']) ? 'display: block;' : 'display: none;' }}">
                                                                <label class="control-label required">Options</label>
                                                                <textarea name="dynamic_fields[{{ $index }}][options]" class="form-control" rows="3"
                                                                    placeholder="Enter one option per line">{{ is_array($field->options) ? implode("\n", $field->options) : $field->options }}</textarea>
                                                                <p class="help-block"><small>Enter one option per line. These will be used for select,
                                                                        checkbox, and radio fields.</small></p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="checkbox">
                                                                <label>
                                                                    <input type="checkbox" name="dynamic_fields[{{ $index }}][is_required]" value="1" {{ $field->is_required ? 'checked' : '' }}>
                                                                    This field is required
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-12">
                        {!! Form::button(trans('basic.cancel'), ['class' => 'btn modal-close btn-danger', 'data-dismiss' => 'modal']) !!}
                        {!! Form::submit('Save Changes', ['class' => 'btn btn-success']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>

<!-- Field Template (Hidden) -->
<div id="field-template" style="display: none;">
    <div class="dynamic-field panel panel-default" data-field-index="{INDEX}">
        <div class="panel-heading">
            <h4 class="panel-title">
                <span class="drag-handle" title="Drag to reorder"><i class="ico-arrows-v"></i></span>
                <i class="ico-list"></i> <span class="field-title">New Field</span>
                <span class="position-badge">Position: <span class="position-number">1</span></span>
                <span class="pull-right">
                    <div class="btn-group position-controls">
                        <button type="button" class="btn btn-xs btn-default move-up-btn" title="Move Up">
                            <i class="ico-arrow-up"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-default move-down-btn" title="Move Down">
                            <i class="ico-arrow-down"></i>
                        </button>
                    </div>
                    <a href="javascript:void(0);" class="btn btn-xs btn-danger remove-field-btn">
                        <i class="ico-trash"></i>
                    </a>
                </span>
            </h4>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label required">Field Label</label>
                        <input type="text" name="dynamic_fields[{INDEX}][label]" class="form-control field-label"
                            placeholder="Enter field label" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label required">Field Type</label>
                        <select name="dynamic_fields[{INDEX}][type]" class="form-control field-type" required>
                            <option value="text">Text</option>
                            <option value="email">Email</option>
                            <option value="number">Number</option>
                            <option value="tel">Telephone</option>
                            <option value="date">Date</option>
                            <option value="time">Time</option>
                            <option value="datetime-local">Date & Time</option>
                            <option value="url">URL</option>
                            <option value="textarea">Text Area</option>
                            <option value="select">Dropdown</option>
                            <option value="checkbox">Checkbox</option>
                            <option value="radio">Radio Button</option>
                            <option value="file">File Upload</option>
                            <option value="country">Country</option>
                            <option value="city">City</option>
                            <option value="user_types">User Types</option>
                            <option value="conference">Conference</option>
                            <option value="profession">Profession</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Hidden field to store position -->
            <input type="hidden" name="dynamic_fields[{INDEX}][position]" class="field-position" value="0">

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group field-options" style="display: none;">
                        <label class="control-label required">Options</label>
                        <textarea name="dynamic_fields[{INDEX}][options]" class="form-control" rows="3"
                            placeholder="Enter one option per line"></textarea>
                        <p class="help-block"><small>Enter one option per line. These will be used for select,
                                checkbox, and radio fields.</small></p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="dynamic_fields[{INDEX}][is_required]" value="1">
                            This field is required
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Wait for the document to be fully loaded
$(document).ready(function() {
    // Initialize field counter - start after the highest existing index
    let fieldCounter = {{ $registration->dynamicFormFields->count() }};

    // Add field button click handler
    $('#add-field-btn').on('click', function() {
        addNewField();
    });

    // Function to add a new field
    function addNewField() {
        // Get the template
        const template = $('#field-template').html();

        // Replace the index placeholder
        const fieldHtml = template.replace(/{INDEX}/g, fieldCounter);

        // Append the field to the list
        $('#dynamic-fields-list').append(fieldHtml);

        // Initialize the field type change handler for the new field
        const $newField = $('#dynamic-fields-list .dynamic-field').last();

        // Set up field type change handler
        $newField.find('.field-type').on('change', function() {
            toggleOptionsField($(this));
        });

        // Set up field label change handler
        $newField.find('.field-label').on('input', function() {
            $newField.find('.field-title').text($(this).val() || 'New Field');
        });

        // Set up remove button handler
        $newField.find('.remove-field-btn').on('click', function() {
            $newField.remove();
            updatePositionNumbers();
        });

        // Increment the counter
        fieldCounter++;

        // Show the new field (ensure it's visible)
        $newField.show();

        // Trigger the change event to properly show/hide options field
        $newField.find('.field-type').trigger('change');

        // Update position numbers for all fields
        updatePositionNumbers();
    }

    // Function to toggle options field based on field type
    function toggleOptionsField($selectElement) {
        const $fieldContainer = $selectElement.closest('.dynamic-field');
        const $optionsField = $fieldContainer.find('.field-options');
        const optionsTypes = ['select', 'checkbox', 'radio'];

        if (optionsTypes.includes($selectElement.val())) {
            $optionsField.show();
        } else {
            $optionsField.hide();
        }
    }

    // Function to update position numbers for all fields
    function updatePositionNumbers() {
        const $fields = $('#dynamic-fields-list .dynamic-field');
        $fields.each(function(index) {
            $(this).find('.field-position').val(index + 1);
            $(this).find('.position-number').text(index + 1);
        });
    }

    // Make the fields sortable
    $('#dynamic-fields-list').sortable({
        handle: '.drag-handle',
        update: function(event, ui) {
            updatePositionNumbers();
        }
    });

    // Delegate event handler for the move up button
    $(document).on('click', '.move-up-btn', function() {
        const $currentField = $(this).closest('.dynamic-field');
        const $prevField = $currentField.prev('.dynamic-field');

        if ($prevField.length) {
            $currentField.insertBefore($prevField);
            updatePositionNumbers();
        }
    });

    // Delegate event handler for the move down button
    $(document).on('click', '.move-down-btn', function() {
        const $currentField = $(this).closest('.dynamic-field');
        const $nextField = $currentField.next('.dynamic-field');

        if ($nextField.length) {
            $currentField.insertAfter($nextField);
            updatePositionNumbers();
        }
    });

    // Initialize existing fields
    $('.field-type').each(function() {
        // Set up field type change handler for existing fields
        $(this).on('change', function() {
            toggleOptionsField($(this));
        });
    });

    // Set up field label change handler for existing fields
    $('.field-label').each(function() {
        const $field = $(this).closest('.dynamic-field');
        $(this).on('input', function() {
            $field.find('.field-title').text($(this).val() || 'Field');
        });
    });

    // Set up remove button handler for existing fields
    $('.remove-field-btn').each(function() {
        const $field = $(this).closest('.dynamic-field');
        $(this).on('click', function() {
            // If this is an existing field, add a hidden input to mark it for deletion
            const fieldId = $field.data('field-id');
            if (fieldId) {
                $('#dynamic-fields-list').append('<input type="hidden" name="deleted_fields[]" value="' + fieldId + '">');
            }
            $field.remove();
            updatePositionNumbers();
        });
    });

    // Call updatePositionNumbers once at initialization to ensure all positions are set correctly
    updatePositionNumbers();

    // Add some CSS for better UX
    $('<style>')
        .text(`
            .drag-handle {
                cursor: move;
                margin-right: 5px;
                color: #999;
            }
            .position-badge {
                margin-left: 10px;
                font-size: 12px;
                color: #777;
            }
            .ui-sortable-helper {
                background: #f8f8f8;
                box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            }
            .ui-sortable-placeholder {
                visibility: visible !important;
                background: #f0f9ff;
                border: 1px dashed #bce8f1;
                height: 100px;
                margin-bottom: 10px;
            }
        `)
        .appendTo('head');
});
</script>

<style>
    .modal-lg {
        width: 90%;
        max-width: 1000px;
    }

    .nav-tabs {
        margin-bottom: 20px;
    }

    .nav-tabs>li>a {
        background-color: #f8f8f8;
        color: #555;
        border: 1px solid #ddd;
        border-bottom-color: transparent;
    }

    .nav-tabs>li.active>a,
    .nav-tabs>li.active>a:hover,
    .nav-tabs>li.active>a:focus {
        background-color: #fff;
        color: #333;
        font-weight: bold;
    }

    .panel {
        border-radius: 3px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .panel-heading {
        background-color: #f8f8f8;
        border-bottom: 1px solid #eee;
    }

    .panel-title {
        font-size: 16px;
        font-weight: 600;
    }

    .dynamic-field {
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 3px;
    }

    .dynamic-field .panel-heading {
        background-color: #f5f5f5;
        padding: 10px 15px;
        cursor: move;
    }

    .dynamic-field .panel-body {
        padding: 15px;
    }

    .input-group-addon {
        background-color: #f8f8f8;
    }

    .form-control {
        border-radius: 3px;
        box-shadow: none;
        border: 1px solid #ddd;
    }

    .form-control:focus {
        border-color: #66afe9;
        box-shadow: 0 0 8px rgba(102, 175, 233, 0.6);
    }

    .btn {
        border-radius: 3px;
        font-weight: 600;
    }

    .btn-danger {
        background-color: #d9534f;
        border-color: #d43f3a;
    }

    .modal-footer {
        background-color: #f8f8f8;
        border-top: 1px solid #e5e5e5;
        padding: 15px;
    }

    .alert {
        border-radius: 3px;
    }

    .help-block {
        color: #737373;
        margin-top: 5px;
    }

    .custom-fields-container {
        max-height: 400px;
        overflow-y: auto;
        padding-right: 5px;
    }
</style>
