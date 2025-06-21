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

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('country', 'Country', ['class' => 'control-label']) !!}
                            {!! Form::select('country', $countries, $user->country, [
                                'class' => 'form-control',
                                'placeholder' => 'Select Country'
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('city', 'City', ['class' => 'control-label']) !!}
                            {!! Form::text('city', $user->city, [
                                'class' => 'form-control'
                            ]) !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('conference_id', 'Conference', ['class' => 'control-label']) !!}
                            {!! Form::select('conference_id', $conferences, $user->conference_id, [
                                'class' => 'form-control',
                                'placeholder' => 'Select Conference'
                            ]) !!}
                        </div>
                    </div>
                     <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('profession_id', 'Profession', ['class' => 'control-label']) !!}
                            {!! Form::select('profession_id', $professions, $user->profession_id, [
                                'class' => 'form-control',
                                'placeholder' => 'Select Profession'
                            ]) !!}
                        </div>
                    </div>
                </div>

                @if($user->registration->dynamicFormFields->count() > 0)
                    <hr>
                    <h4>Additional Fields</h4>
                    @foreach($user->registration->dynamicFormFields as $field)
                        @if($field->type != 'user_types')
                            @php
                                $fieldValue = $user->formFieldValues->where('dynamic_form_field_id', $field->id)->first();
                                $value = $fieldValue ? $fieldValue->value : '';
                            @endphp
                            <div class="form-group">
                                <label class="control-label {{ $field->is_required ? 'required' : '' }}">
                                    {{ $field->label }}
                                </label>
                                @if($field->type == 'text')
                                    <input type="text" name="fields[{{ $field->id }}]" value="{{ $value }}" class="form-control" {{ $field->is_required ? 'required' : '' }}>
                                @elseif($field->type == 'email')
                                    <input type="email" name="fields[{{ $field->id }}]" value="{{ $value }}" class="form-control" {{ $field->is_required ? 'required' : '' }}>
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
                                        @foreach($countries as $countryCode => $countryName)
                                            <option value="{{ $countryCode }}" {{ $value == $countryCode ? 'selected' : '' }}>{{ $countryName }}</option>
                                        @endforeach
                                    </select>
                                @elseif($field->type == 'city')
                                    <input type="text" name="fields[{{ $field->id }}]" value="{{ $value }}" class="form-control" {{ $field->is_required ? 'required' : '' }}>
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
