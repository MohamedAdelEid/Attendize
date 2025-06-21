<div role="dialog" class="modal fade" id="import-users-modal" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-upload"></i>
                    Import Users from Excel
                </h3>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="ico-info-circle"></i>
                    <strong>Instructions:</strong>
                    <ol>
                        <li>Select the registration form to assign users to</li>
                        <li>Download the Excel template</li>
                        <li>Fill in the user data</li>
                        <li>Upload the completed file</li>
                    </ol>
                </div>

                {!! Form::open([
                    'url' => route('importUsers', ['event_id' => $event->id]),
                    'class' => 'ajax',
                    'files' => true,
                    'id' => 'import-form'
                ]) !!}

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('registration_id', 'Registration Form', ['class' => 'control-label required']) !!}
                            {!! Form::select('registration_id', $registrations, null, [
                                'class' => 'form-control',
                                'id' => 'import_registration_select',
                                'required' => 'required',
                                'placeholder' => 'Select Registration Form'
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('user_type_id', 'Default User Type', ['class' => 'control-label']) !!}
                            {!! Form::select('user_type_id', $userTypes, null, [
                                'class' => 'form-control',
                                'placeholder' => 'Select Default User Type (Optional)'
                            ]) !!}
                            <small class="help-block">If not specified, "Delegate" will be used as default</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('approval_status', 'Approval Status', ['class' => 'control-label required']) !!}
                            {!! Form::select('approval_status', [
                                'automatic' => 'Use Registration Form Setting',
                                'approved' => 'Approve All Imported Users',
                                'pending' => 'Set All as Pending',
                                'manual' => 'Manual Approval Required'
                            ], 'automatic', [
                                'class' => 'form-control',
                                'required' => 'required'
                            ]) !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">Excel Template</label>
                            <div class="well">
                                <p><i class="ico-download"></i> Download the Excel template for the selected registration form:</p>
                                <button type="button" id="download-template-btn" class="btn btn-info" disabled>
                                    <i class="ico-download"></i> Download Template
                                </button>
                                <small class="help-block">Please select a registration form first to download the template.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('import_file', 'Upload Excel File', ['class' => 'control-label required']) !!}
                            {!! Form::file('import_file', [
                                'class' => 'form-control',
                                'accept' => '.xlsx,.xls,.csv',
                                'required' => 'required'
                            ]) !!}
                            <small class="help-block">Supported formats: .xlsx, .xls, .csv (Max: 10MB)</small>
                        </div>
                    </div>
                </div>

                <div id="import-results" class="alert" style="display: none;"></div>

                <div class="modal-footer">
                    {!! Form::button('Cancel', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) !!}
                    {!! Form::submit('Import Users', ['class' => 'btn btn-success', 'id' => 'import-submit-btn']) !!}
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#import_registration_select').on('change', function() {
        const registrationId = $(this).val();
        if (registrationId) {
            $('#download-template-btn').prop('disabled', false);
            $('.help-block').text('Click to download the template with all required fields for this registration form.');
        } else {
            $('#download-template-btn').prop('disabled', true);
            $('.help-block').text('Please select a registration form first to download the template.');
        }
    });

    $('#download-template-btn').on('click', function() {
        const registrationId = $('#import_registration_select').val();
        if (registrationId) {
            const url = '{{ route("downloadTemplate", ["event_id" => $event->id]) }}?registration_id=' + registrationId;
            window.location.href = url;
        }
    });

    // Handle form submission
    $('#import-form').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = $('#import-submit-btn');
        const originalText = submitBtn.text();

        submitBtn.prop('disabled', true).html('<i class="ico-spinner ico-spin"></i> Importing...');
        $('#import-results').hide();

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    $('#import-results')
                        .removeClass('alert-danger')
                        .addClass('alert-success')
                        .html('<i class="ico-checkmark"></i> ' + response.message)
                        .show();

                    // Reset form
                    $('#import-form')[0].reset();
                    $('#download-template-btn').prop('disabled', true);

                    // Reload page after 2 seconds
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                } else {
                    $('#import-results')
                        .removeClass('alert-success')
                        .addClass('alert-danger')
                        .html('<i class="ico-close"></i> ' + response.message)
                        .show();
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred during import.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                $('#import-results')
                    .removeClass('alert-success')
                    .addClass('alert-danger')
                    .html('<i class="ico-close"></i> ' + errorMessage)
                    .show();
            },
            complete: function() {
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });
});
</script>
