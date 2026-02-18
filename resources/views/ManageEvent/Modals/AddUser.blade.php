<div role="dialog" class="modal fade" id="add-user-modal" style="display: none;">
    {!! Form::open([
        'url' => route('storeUser', ['event_id' => $event->id]),
        'class' => 'ajax',
        'files' => true,
    ]) !!}
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-user-plus"></i>
                    Add New User
                </h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('registration_id', 'Registration Form', ['class' => 'control-label required']) !!}
                            {!! Form::select('registration_id', $registrations, $selectedRegistration ? $selectedRegistration->id : null, [
                                'class' => 'form-control',
                                'id' => 'registration_select',
                                'required' => 'required',
                                'placeholder' => 'Select Registration Form'
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">User Types (default: Delegate if none selected)</label>
                            <div class="checkbox-group" style="max-height: 200px; overflow-y: auto; border: 1px solid #ccc; padding: 8px; border-radius: 4px;">
                                @foreach(isset($userTypesWithOptions) ? $userTypesWithOptions : [] as $ut)
                                    <div class="user-type-row" style="margin-bottom: 8px;">
                                        <label style="display: block; margin-bottom: 2px;">
                                            <input type="checkbox" name="user_type_ids[]" value="{{ $ut->id }}" class="ut-checkbox"> {{ $ut->name }}
                                        </label>
                                        @if($ut->options && $ut->options->count() > 0)
                                            <select name="user_type_option_{{ $ut->id }}" class="form-control input-sm ut-option-select" style="margin-left: 20px; width: auto; display: none;">
                                                <option value="">— Select sub-type —</option>
                                                @foreach($ut->options as $opt)
                                                    <option value="{{ $opt->id }}">{{ $opt->name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                        <span style="margin-left: 20px;">Position (optional):</span>
                                        <input type="number" name="user_type_position_{{ $ut->id }}" class="form-control input-sm" style="width: 70px; display: inline-block; margin-left: 4px;" min="0" placeholder="#" title="Display order on user type page">
                                    </div>
                                @endforeach
                                @if(!isset($userTypesWithOptions) || $userTypesWithOptions->isEmpty())
                                    @foreach($userTypes as $id => $name)
                                        <div class="user-type-row" style="margin-bottom: 8px;">
                                            <label style="display: block; margin-bottom: 2px;">
                                                <input type="checkbox" name="user_type_ids[]" value="{{ $id }}"> {{ $name }}
                                            </label>
                                            <span style="margin-left: 20px;">Position (optional):</span>
                                            <input type="number" name="user_type_position_{{ $id }}" class="form-control input-sm" style="width: 70px; display: inline-block; margin-left: 4px;" min="0" placeholder="#">
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('avatar', 'Photo (optional)', ['class' => 'control-label']) !!}
                            <input type="file" name="avatar" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('first_name', 'First Name', ['class' => 'control-label required']) !!}
                            {!! Form::text('first_name', null, [
                                'class' => 'form-control',
                                'required' => 'required'
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('last_name', 'Last Name', ['class' => 'control-label required']) !!}
                            {!! Form::text('last_name', null, [
                                'class' => 'form-control',
                                'required' => 'required'
                            ]) !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('email', 'Email', ['class' => 'control-label required']) !!}
                            {!! Form::email('email', null, [
                                'class' => 'form-control',
                                'required' => 'required'
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('phone', 'Phone', ['class' => 'control-label']) !!}
                            {!! Form::text('phone', null, [
                                'class' => 'form-control'
                            ]) !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('status', 'Status', ['class' => 'control-label required']) !!}
                            {!! Form::select('status', [
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected'
                            ], 'pending', [
                                'class' => 'form-control',
                                'required' => 'required'
                            ]) !!}
                        </div>
                    </div>
                </div>

                <!-- Dynamic Fields Container -->
                <div id="dynamic-fields-container">
                    @if($selectedRegistration && $formFields->count() > 0)
                        <hr>
                        <h4>Additional Fields</h4>
                        @foreach($formFields as $field)
                            @if($field->type !== 'user_types') {{-- Skip user_types as it's already shown above --}}
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ $field->label }}
                                    </label>
                                    @if($field->type == 'text')
                                        <input type="text" name="fields[{{ $field->id }}]" class="form-control">
                                    @elseif($field->type == 'email')
                                        <input type="email" name="fields[{{ $field->id }}]" class="form-control">
                                    @elseif($field->type == 'textarea')
                                        <textarea name="fields[{{ $field->id }}]" class="form-control" rows="3"></textarea>
                                    @elseif($field->type == 'select')
                                        <select name="fields[{{ $field->id }}]" class="form-control">
                                            <option value="">Select option</option>
                                            @if(is_array($field->options))
                                                @foreach($field->options as $option)
                                                    <option value="{{ $option }}">{{ $option }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    @elseif($field->type == 'country')
                                        <select name="fields[{{ $field->id }}]" class="form-control">
                                            <option value="">Select Country</option>
                                            @foreach($countries as $country)
                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    @elseif($field->type == 'city')
                                        <input type="text" name="fields[{{ $field->id }}]" class="form-control" placeholder="Enter city name">
                                    @elseif($field->type == 'conference')
                                        <select name="fields[{{ $field->id }}]" class="form-control conference-select">
                                            <option value="">Select Conference</option>
                                            @foreach($conferences as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    @elseif($field->type == 'profession')
                                        <select name="fields[{{ $field->id }}]" class="form-control profession-select">
                                            <option value="">Select Profession</option>
                                            @foreach($professions as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    @elseif($field->type == 'external_payment')
                                        {{-- External payment: file upload for receipt (optional in admin) --}}
                                        <input type="file" name="fields[{{ $field->id }}]" class="form-control" accept="image/*,.pdf">
                                        <p class="help-block">Upload receipt file (optional).</p>
                                    @elseif($field->type == 'file')
                                        <input type="file" name="fields[{{ $field->id }}]" class="form-control">
                                    @elseif($field->type == 'checkbox')
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="fields[{{ $field->id }}]" value="1">
                                                {{ $field->description ?? '' }}
                                            </label>
                                        </div>
                                    @elseif($field->type == 'radio')
                                        @if(is_array($field->options))
                                            @foreach($field->options as $option)
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="fields[{{ $field->id }}]" value="{{ trim($option) }}">
                                                        {{ trim($option) }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        @endif
                                    @elseif($field->type == 'date')
                                        <input type="date" name="fields[{{ $field->id }}]" class="form-control">
                                    @elseif($field->type == 'tel')
                                        <input type="tel" name="fields[{{ $field->id }}]" class="form-control">
                                    @elseif($field->type == 'number')
                                        <input type="number" name="fields[{{ $field->id }}]" class="form-control">
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                {!! Form::button('Cancel', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) !!}
                {!! Form::submit('Add User', ['class' => 'btn btn-success']) !!}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>

<script>
$(document).ready(function() {
    $('#registration_select').on('change', function() {
        const registrationId = $(this).val();
        if (registrationId) {
            // Load dynamic fields for selected registration
            $.ajax({
                url: '{{ route("getRegistrationFields", ["event_id" => $event->id, "registration_id" => "__ID__"]) }}'.replace('__ID__', registrationId),
                type: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        let fieldsHtml = '';
                        if (response.fields.length > 0) {
                            fieldsHtml = '<hr><h4>Additional Fields</h4>';
                            response.fields.forEach(function(field) {
                                // Skip user_types as it's already shown above
                                if (field.type === 'user_types') {
                                    return;
                                }

                                fieldsHtml += '<div class="form-group">';
                                fieldsHtml += '<label class="control-label">' + field.label + '</label>';

                                if (field.type === 'text') {
                                    fieldsHtml += '<input type="text" name="fields[' + field.id + ']" class="form-control">';
                                } else if (field.type === 'email') {
                                    fieldsHtml += '<input type="email" name="fields[' + field.id + ']" class="form-control">';
                                } else if (field.type === 'textarea') {
                                    fieldsHtml += '<textarea name="fields[' + field.id + ']" class="form-control" rows="3"></textarea>';
                                } else if (field.type === 'select') {
                                    fieldsHtml += '<select name="fields[' + field.id + ']" class="form-control">';
                                    fieldsHtml += '<option value="">Select option</option>';
                                    if (field.options && Array.isArray(field.options)) {
                                        field.options.forEach(function(option) {
                                            fieldsHtml += '<option value="' + option + '">' + option + '</option>';
                                        });
                                    }
                                    fieldsHtml += '</select>';
                                } else if (field.type === 'country') {
                                    fieldsHtml += '<select name="fields[' + field.id + ']" class="form-control">';
                                    fieldsHtml += '<option value="">Select Country</option>';
                                    @foreach($countries as $country)
                                        fieldsHtml += '<option value="{{ $country->id }}">{{ $country->name }}</option>';
                                    @endforeach
                                    fieldsHtml += '</select>';
                                } else if (field.type === 'city') {
                                    fieldsHtml += '<input type="text" name="fields[' + field.id + ']" class="form-control" placeholder="Enter city name">';
                                } else if (field.type === 'conference') {
                                    fieldsHtml += '<select name="fields[' + field.id + ']" class="form-control conference-select">';
                                    fieldsHtml += '<option value="">Select Conference</option>';
                                    @foreach($conferences as $id => $name)
                                        fieldsHtml += '<option value="{{ $id }}">{{ $name }}</option>';
                                    @endforeach
                                    fieldsHtml += '</select>';
                                } else if (field.type === 'profession') {
                                    fieldsHtml += '<select name="fields[' + field.id + ']" class="form-control profession-select">';
                                    fieldsHtml += '<option value="">Select Profession</option>';
                                    @foreach($professions as $id => $name)
                                        fieldsHtml += '<option value="{{ $id }}">{{ $name }}</option>';
                                    @endforeach
                                    fieldsHtml += '</select>';
                                } else if (field.type === 'external_payment') {
                                    fieldsHtml += '<input type="file" name="fields[' + field.id + ']" class="form-control" accept="image/*,.pdf">';
                                    fieldsHtml += '<p class="help-block">Upload receipt file (optional).</p>';
                                } else if (field.type === 'file') {
                                    fieldsHtml += '<input type="file" name="fields[' + field.id + ']" class="form-control">';
                                } else if (field.type === 'checkbox') {
                                    fieldsHtml += '<div class="checkbox"><label><input type="checkbox" name="fields[' + field.id + ']" value="1">' + (field.description || '') + '</label></div>';
                                } else if (field.type === 'radio') {
                                    if (field.options && Array.isArray(field.options)) {
                                        field.options.forEach(function(option) {
                                            fieldsHtml += '<div class="radio"><label><input type="radio" name="fields[' + field.id + ']" value="' + option.trim() + '">' + option.trim() + '</label></div>';
                                        });
                                    }
                                } else if (field.type === 'date') {
                                    fieldsHtml += '<input type="date" name="fields[' + field.id + ']" class="form-control">';
                                } else if (field.type === 'tel') {
                                    fieldsHtml += '<input type="tel" name="fields[' + field.id + ']" class="form-control">';
                                } else if (field.type === 'number') {
                                    fieldsHtml += '<input type="number" name="fields[' + field.id + ']" class="form-control">';
                                }

                                fieldsHtml += '</div>';
                            });
                        }
                        $('#dynamic-fields-container').html(fieldsHtml);

                        // Re-bind conference change event
                        bindConferenceChange();
                    }
                },
                error: function(xhr) {
                    console.error('Error loading fields:', xhr);
                }
            });
        } else {
            $('#dynamic-fields-container').html('');
        }
    });

    // Function to bind conference change event
    function bindConferenceChange() {
        $(document).off('change', '.conference-select').on('change', '.conference-select', function() {
            const conferenceId = $(this).val();
            const professionSelect = $('.profession-select');

            if (conferenceId) {
                // Load professions for selected conference
                $.ajax({
                    url: '{{ route("getConferenceProfessions", ["event_id" => $event->id, "conference_id" => "__ID__"]) }}'.replace('__ID__', conferenceId),
                    type: 'GET',
                    success: function(response) {
                        if (response.status === 'success') {
                            professionSelect.html('<option value="">Select Profession</option>');
                            $.each(response.professions, function(id, name) {
                                professionSelect.append('<option value="' + id + '">' + name + '</option>');
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading professions:', xhr);
                    }
                });
            } else {
                professionSelect.html('<option value="">Select Profession</option>');
            }
        });
    }

    // User type option dropdown: show when checkbox checked
    $('.ut-checkbox').each(function() {
        var $row = $(this).closest('.user-type-row');
        var $sel = $row.find('.ut-option-select');
        if ($(this).is(':checked')) $sel.show(); else $sel.hide();
    });
    $(document).on('change', '.ut-checkbox', function() {
        var $row = $(this).closest('.user-type-row');
        $row.find('.ut-option-select').toggle($(this).is(':checked'));
    });

    // Initial binding
    bindConferenceChange();
});
</script>
