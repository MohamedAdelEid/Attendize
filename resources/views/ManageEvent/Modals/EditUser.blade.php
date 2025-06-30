<div role="dialog" class="modal fade" id="edit-user-modal" style="display: none;">
    {!! Form::open([
        'url' => route('updateUser', ['event_id' => $event->id, 'user_id' => $user->id]),
        'class' => 'ajax',
        'files' => true,
    ]) !!}
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-edit"></i>
                    Edit User: {{ $user->first_name }} {{ $user->last_name }}
                </h3>
            </div>
            <div class="modal-body">
                <!-- User Type - Always at top -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('user_type_id', 'User Type', ['class' => 'control-label']) !!}
                            {!! Form::select('user_type_id', $userTypes, $user->user_type_id, [
                                'class' => 'form-control',
                                'placeholder' => 'Select User Type'
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('status', 'Status', ['class' => 'control-label required']) !!}
                            {!! Form::select('status', [
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected'
                            ], $user->status, [
                                'class' => 'form-control',
                                'required' => 'required'
                            ]) !!}
                        </div>
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('first_name', 'First Name', ['class' => 'control-label required']) !!}
                            {!! Form::text('first_name', $user->first_name, [
                                'class' => 'form-control',
                                'required' => 'required'
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('last_name', 'Last Name', ['class' => 'control-label required']) !!}
                            {!! Form::text('last_name', $user->last_name, [
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
                            {!! Form::email('email', $user->email, [
                                'class' => 'form-control',
                                'required' => 'required'
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('phone', 'Phone', ['class' => 'control-label']) !!}
                            {!! Form::text('phone', $user->phone, [
                                'class' => 'form-control'
                            ]) !!}
                        </div>
                    </div>
                </div>

                {{-- <!-- Fixed Fields (only if NOT in custom fields) -->
                <div class="row">
                    @if(!$hasCountryField)
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('country_id', 'Country', ['class' => 'control-label']) !!}
                                {!! Form::select('country_id', $countries->pluck('name', 'id')->toArray(), $user->country_id, [
                                    'class' => 'form-control',
                                    'placeholder' => 'Select Country'
                                ]) !!}
                            </div>
                        </div>
                    @endif

                    @if(!$hasCityField)
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('city', 'City', ['class' => 'control-label']) !!}
                                {!! Form::text('city', $user->city, [
                                    'class' => 'form-control'
                                ]) !!}
                            </div>
                        </div>
                    @endif
                </div>

                <div class="row">
                    @if(!$hasConferenceField)
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('conference_id', 'Conference', ['class' => 'control-label']) !!}
                                {!! Form::select('conference_id', $conferences, $user->conference_id, [
                                    'class' => 'form-control conference-select',
                                    'placeholder' => 'Select Conference'
                                ]) !!}
                            </div>
                        </div>
                    @endif

                    @if(!$hasProfessionField)
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('profession_id', 'Profession', ['class' => 'control-label']) !!}
                                {!! Form::select('profession_id', $professions, $user->profession_id, [
                                    'class' => 'form-control profession-select',
                                    'placeholder' => 'Select Profession'
                                ]) !!}
                            </div>
                        </div>
                    @endif
                </div> --}}

                <!-- Additional Fields (Custom Fields Only) -->
                @if($user->registration->dynamicFormFields->count() > 0)
                    <hr>
                    <h4>Additional Fields</h4>
                    @foreach($user->registration->dynamicFormFields as $field)
                        @if($field->type != 'user_types')
                            @php
                                $fieldValue = $user->formFieldValues->where('dynamic_form_field_id', $field->id)->first();
                                $value = $fieldValue ? $fieldValue->value : '';

                                // For conference and profession fields, get the value from the user record
                                if ($field->type == 'conference') {
                                    $value = $user->conference_id;
                                } elseif ($field->type == 'profession') {
                                    $value = $user->profession_id;
                                }
                            @endphp
                            <div class="form-group">
                                <label class="control-label {{ $field->is_required ? 'required' : '' }}">
                                    {{ $field->label }}
                                </label>
                                @if($field->type == 'text')
                                    <input type="text" name="fields[{{ $field->id }}]" value="{{ $value }}" class="form-control" {{ $field->is_required ? 'required' : '' }}>
                                @elseif($field->type == 'email')
                                    <input type="email" name="fields[{{ $field->id }}]" value="{{ $value }}" class="form-control" {{ $field->is_required ? 'required' : '' }}>
                                @elseif($field->type == 'tel')
                                    <input type="tel" name="fields[{{ $field->id }}]" value="{{ $value }}" class="form-control" {{ $field->is_required ? 'required' : '' }}>
                                @elseif($field->type == 'number')
                                    <input type="number" name="fields[{{ $field->id }}]" value="{{ $value }}" class="form-control" {{ $field->is_required ? 'required' : '' }}>
                                @elseif($field->type == 'textarea')
                                    <textarea name="fields[{{ $field->id }}]" class="form-control" rows="3" {{ $field->is_required ? 'required' : '' }}>{{ $value }}</textarea>
                                @elseif($field->type == 'select')
                                    <select name="fields[{{ $field->id }}]" class="form-control" {{ $field->is_required ? 'required' : '' }}>
                                        <option value="">Select option</option>
                                        @if(is_array($field->options))
                                            @foreach($field->options as $option)
                                                <option value="{{ $option }}" {{ $value == $option ? 'selected' : '' }}>{{ $option }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                @elseif($field->type == 'country')
                                    <select name="fields[{{ $field->id }}]" class="form-control" {{ $field->is_required ? 'required' : '' }}>
                                        <option value="">Select Country</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" {{ $value == $country->id ? 'selected' : '' }}>{{ $country->name }} - {{ $country->country_code }}</option>
                                        @endforeach
                                    </select>
                                @elseif($field->type == 'city')
                                    <input type="text" name="fields[{{ $field->id }}]" value="{{ $value }}" class="form-control" {{ $field->is_required ? 'required' : '' }}>
                                @elseif($field->type == 'conference')
                                    <select name="fields[{{ $field->id }}]" class="form-control conference-select" data-field-id="{{ $field->id }}" {{ $field->is_required ? 'required' : '' }}>
                                        <option value="">Select Conference</option>
                                        @foreach($conferences as $confId => $confName)
                                            <option value="{{ $confId }}" {{ $value == $confId ? 'selected' : '' }}>{{ $confName }}</option>
                                        @endforeach
                                    </select>
                                @elseif($field->type == 'profession')
                                    <select name="fields[{{ $field->id }}]" class="form-control profession-select" data-field-id="{{ $field->id }}" {{ $field->is_required ? 'required' : '' }}>
                                        <option value="">Select Profession</option>
                                        @if($hasConferenceField && $user->conference_id)
                                            {{-- Load professions for current conference via AJAX --}}
                                        @else
                                            @foreach($professions as $profId => $profName)
                                                <option value="{{ $profId }}" {{ $value == $profId ? 'selected' : '' }}>{{ $profName }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                @elseif($field->type == 'checkbox')
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="fields[{{ $field->id }}]" value="1" {{ $value ? 'checked' : '' }} {{ $field->is_required ? 'required' : '' }}>
                                            {{ $field->description }}
                                        </label>
                                    </div>
                                @elseif($field->type == 'radio')
                                    @if(is_array($field->options))
                                        @foreach($field->options as $option)
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="fields[{{ $field->id }}]" value="{{ trim($option) }}" {{ $value == trim($option) ? 'checked' : '' }} {{ $field->is_required ? 'required' : '' }}>
                                                    {{ trim($option) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    @endif
                                @elseif($field->type == 'date')
                                    <input type="date" name="fields[{{ $field->id }}]" value="{{ $value }}" class="form-control" {{ $field->is_required ? 'required' : '' }}>
                                @elseif($field->type == 'file')
                                    <input type="file" name="fields[{{ $field->id }}]" class="form-control" {{ $field->is_required ? 'required' : '' }}>
                                    @if($value)
                                        <p class="help-block">Current file: {{ $value }}</p>
                                    @endif
                                @endif
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
            <div class="modal-footer">
                {!! Form::button('Cancel', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) !!}
                {!! Form::submit('Update User', ['class' => 'btn btn-success']) !!}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>

<script>
$(document).ready(function() {
    // Conference-Profession dependency for edit modal
    $('#edit-user-modal .conference-select').on('change', function() {
        const conferenceId = $(this).val();
        const professionSelect = $('#edit-user-modal .profession-select');

        if (conferenceId) {
            // Enable profession select and load professions for selected conference
            professionSelect.prop('disabled', false);

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
            // Disable profession select and clear options
            professionSelect.prop('disabled', true).html('<option value="">Select Profession</option>');
        }
    });

    // Load professions on modal open if conference is already selected
    $('#edit-user-modal').on('shown.bs.modal', function() {
        const conferenceSelect = $(this).find('.conference-select');
        const selectedConference = conferenceSelect.val();

        if (selectedConference) {
            conferenceSelect.trigger('change');
        }
    });
});
</script>
