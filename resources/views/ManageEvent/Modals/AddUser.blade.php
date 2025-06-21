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
                            {!! Form::label('user_type_id', 'User Type', ['class' => 'control-label']) !!}
                            {!! Form::select('user_type_id', $userTypes, null, [
                                'class' => 'form-control',
                                'placeholder' => 'Select User Type (Default: Delegate)'
                            ]) !!}
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
                                    <label class="control-label {{ $field->is_required ? 'required' : '' }}">
                                        {{ $field->label }}
                                    </label>
                                    @if($field->type == 'text')
                                        <input type="text" name="fields[{{ $field->id }}]" class="form-control" {{ $field->is_required ? 'required' : '' }}>
                                    @elseif($field->type == 'email')
                                        <input type="email" name="fields[{{ $field->id }}]" class="form-control" {{ $field->is_required ? 'required' : '' }}>
                                    @elseif($field->type == 'textarea')
                                        <textarea name="fields[{{ $field->id }}]" class="form-control" rows="3" {{ $field->is_required ? 'required' : '' }}></textarea>
                                    @elseif($field->type == 'select')
                                        <select name="fields[{{ $field->id }}]" class="form-control" {{ $field->is_required ? 'required' : '' }}>
                                            <option value="">Select option</option>
                                            @if(is_array($field->options))
                                                @foreach($field->options as $option)
                                                    <option value="{{ $option }}">{{ $option }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    @elseif($field->type == 'country')
                                        <select name="fields[{{ $field->id }}]" class="form-control" {{ $field->is_required ? 'required' : '' }}>
                                            <option value="">Select Country</option>
                                            @foreach($countries as $country)
                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    @elseif($field->type == 'city')
                                        <input type="text" name="fields[{{ $field->id }}]" class="form-control" placeholder="Enter city name" {{ $field->is_required ? 'required' : '' }}>
                                    @elseif($field->type == 'conference')
                                        <select name="fields[{{ $field->id }}]" class="form-control conference-select" {{ $field->is_required ? 'required' : '' }}>
                                            <option value="">Select Conference</option>
                                            @foreach($conferences as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    @elseif($field->type == 'profession')
                                        <select name="fields[{{ $field->id }}]" class="form-control profession-select" {{ $field->is_required ? 'required' : '' }}>
                                            <option value="">Select Profession</option>
                                            @foreach($professions as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
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
                                fieldsHtml += '<label class="control-label ' + (field.is_required ? 'required' : '') + '">' + field.label + '</label>';

                                if (field.type === 'text') {
                                    fieldsHtml += '<input type="text" name="fields[' + field.id + ']" class="form-control" ' + (field.is_required ? 'required' : '') + '>';
                                } else if (field.type === 'email') {
                                    fieldsHtml += '<input type="email" name="fields[' + field.id + ']" class="form-control" ' + (field.is_required ? 'required' : '') + '>';
                                } else if (field.type === 'textarea') {
                                    fieldsHtml += '<textarea name="fields[' + field.id + ']" class="form-control" rows="3" ' + (field.is_required ? 'required' : '') + '></textarea>';
                                } else if (field.type === 'select') {
                                    fieldsHtml += '<select name="fields[' + field.id + ']" class="form-control" ' + (field.is_required ? 'required' : '') + '>';
                                    fieldsHtml += '<option value="">Select option</option>';
                                    if (field.options && Array.isArray(field.options)) {
                                        field.options.forEach(function(option) {
                                            fieldsHtml += '<option value="' + option + '">' + option + '</option>';
                                        });
                                    }
                                    fieldsHtml += '</select>';
                                } else if (field.type === 'country') {
                                    fieldsHtml += '<select name="fields[' + field.id + ']" class="form-control" ' + (field.is_required ? 'required' : '') + '>';
                                    fieldsHtml += '<option value="">Select Country</option>';
                                    @foreach($countries as $country)
                                        fieldsHtml += '<option value="{{ $country->id }}">{{ $country->name }}</option>';
                                    @endforeach
                                    fieldsHtml += '</select>';
                                } else if (field.type === 'city') {
                                    fieldsHtml += '<input type="text" name="fields[' + field.id + ']" class="form-control" placeholder="Enter city name" ' + (field.is_required ? 'required' : '') + '>';
                                } else if (field.type === 'conference') {
                                    fieldsHtml += '<select name="fields[' + field.id + ']" class="form-control conference-select" ' + (field.is_required ? 'required' : '') + '>';
                                    fieldsHtml += '<option value="">Select Conference</option>';
                                    @foreach($conferences as $id => $name)
                                        fieldsHtml += '<option value="{{ $id }}">{{ $name }}</option>';
                                    @endforeach
                                    fieldsHtml += '</select>';
                                } else if (field.type === 'profession') {
                                    fieldsHtml += '<select name="fields[' + field.id + ']" class="form-control profession-select" ' + (field.is_required ? 'required' : '') + '>';
                                    fieldsHtml += '<option value="">Select Profession</option>';
                                    @foreach($professions as $id => $name)
                                        fieldsHtml += '<option value="{{ $id }}">{{ $name }}</option>';
                                    @endforeach
                                    fieldsHtml += '</select>';
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

    // Initial binding
    bindConferenceChange();
});
</script>
