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
                        <li>Upload your Excel file directly</li>
                        <li>Map Excel columns to registration fields</li>
                        <li>Import the mapped users</li>
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
                            <label class="control-label">Default User Types (optional)</label>
                            <div style="max-height: 120px; overflow-y: auto; border: 1px solid #ccc; padding: 8px; border-radius: 4px;">
                                @foreach($userTypes as $id => $name)
                                    <label style="display: block; margin-bottom: 4px;">
                                        <input type="checkbox" name="user_type_ids[]" value="{{ $id }}"> {{ $name }}
                                    </label>
                                @endforeach
                            </div>
                            <small class="help-block">If none selected, "Delegate" is used. You can also use a "user_types" column in Excel (comma-separated names).</small>
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
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="send_mail" value="1">
                                Send approval email to imported approved users
                            </label>
                            <small class="help-block">Emails are only sent when the imported user is approved and has a real email from the sheet.</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('import_file', 'Upload Excel File', ['class' => 'control-label required']) !!}
                            {!! Form::file('import_file', [
                                'class' => 'form-control',
                                'id' => 'import_file',
                                'accept' => '.xlsx,.xls,.csv',
                                'required' => 'required'
                            ]) !!}
                            <small class="help-block">Supported formats: .xlsx, .xls, .csv (Max: 10MB). First row is treated as headers.</small>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="column_mapping" id="column_mapping" value="">

                <div class="row">
                    <div class="col-md-12">
                        <button type="button" id="read-columns-btn" class="btn btn-primary" disabled>
                            <i class="ico-list"></i> Read Columns &amp; Map
                        </button>
                        <span id="mapping-status" class="text-muted ml-2"></span>
                    </div>
                </div>

                <div id="column-mapping-section" class="row" style="display:none; margin-top:20px;">
                    <div class="col-md-12">
                        <div class="well">
                            <strong>Column Mapping</strong>
                            <p class="text-muted">Map each Excel column to a registration field. Use "Full Name" when the sheet has one name column; it will be split into first and last name automatically.</p>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Excel Column</th>
                                            <th>Maps To</th>
                                        </tr>
                                    </thead>
                                    <tbody id="column-mapping-body"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="import-results" class="alert" style="display: none;"></div>
                <div id="errors-results" class="alert" style="display: none;"></div>

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
    let importTargets = [];
    let importSuggestions = {};

    function updateReadColumnsButton() {
        const hasRegistration = !!$('#import_registration_select').val();
        const fileInput = document.getElementById('import_file');
        const hasFile = fileInput && fileInput.files.length > 0;
        $('#read-columns-btn').prop('disabled', !(hasRegistration && hasFile));
        $('#column_mapping').val('');
        $('#column-mapping-section').hide();
    }

    $('#import_registration_select, #import_file').on('change', updateReadColumnsButton);

    $('#read-columns-btn').on('click', function() {
        const registrationId = $('#import_registration_select').val();
        const fileInput = document.getElementById('import_file');
        if (!registrationId || !fileInput.files.length) {
            alert('Please select a registration form and Excel file first.');
            return;
        }

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('registration_id', registrationId);
        formData.append('import_file', fileInput.files[0]);

        const btn = $('#read-columns-btn');
        btn.prop('disabled', true).html('<i class="ico-spinner ico-spin"></i> Reading...');
        $('#mapping-status').text('');
        $('#import-results, #errors-results').hide();

        $.ajax({
            url: '{{ route("readImportColumns", ["event_id" => $event->id]) }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    importTargets = response.targets || [];
                    importSuggestions = response.suggestions || {};
                    renderColumnMapping(response.headers || []);
                    $('#column-mapping-section').show();
                    $('#mapping-status').text('Columns loaded. Review mapping before importing.');
                } else {
                    $('#mapping-status').text(response.message || 'Could not read columns.');
                }
            },
            error: function(xhr) {
                const message = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Could not read columns.';
                $('#mapping-status').text(message);
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="ico-list"></i> Read Columns &amp; Map');
                updateMappingInput();
            }
        });
    });

    function renderColumnMapping(headers) {
        const body = $('#column-mapping-body');
        body.empty();

        headers.forEach(function(header, index) {
            if (!header) {
                return;
            }

            let options = '<option value="">-- Skip --</option>';
            importTargets.forEach(function(target) {
                const selected = importSuggestions[index] === target.value ? ' selected' : '';
                options += '<option value="' + target.value + '"' + selected + '>' + target.label + '</option>';
            });

            body.append(
                '<tr>' +
                    '<td>Column ' + (index + 1) + ': <strong>' + $('<div>').text(header).html() + '</strong></td>' +
                    '<td><select class="form-control import-map-select" data-column="' + index + '">' + options + '</select></td>' +
                '</tr>'
            );
        });

        updateMappingInput();
    }

    $(document).on('change', '.import-map-select', updateMappingInput);

    function updateMappingInput() {
        const mapping = {};
        $('.import-map-select').each(function() {
            const target = $(this).val();
            if (target) {
                mapping[$(this).data('column')] = target;
            }
        });
        $('#column_mapping').val(JSON.stringify(mapping));
    }

    // Handle form submission
    $('#import-form').on('submit', function(e) {
        e.preventDefault();
        updateMappingInput();

        if (!$('#column_mapping').val() || $('#column_mapping').val() === '{}') {
            alert('Please read the Excel columns and map at least one column before importing.');
            return;
        }

        const formData = new FormData(this);
        const submitBtn = $('#import-submit-btn');
        const originalText = submitBtn.text();

        submitBtn.prop('disabled', true).html('<i class="ico-spinner ico-spin"></i> Importing...');
        $('#import-results, #errors-results').hide();

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
                    $('#column_mapping').val('');
                    $('#column-mapping-section').hide();
                    updateReadColumnsButton();

                    if(response.results && response.results.errors) {
                        $('#errors-results')
                            .removeClass('alert-success')
                            .addClass('alert-danger')
                            .html('<i class="ico-warning"></i> ' + response.results.error_details.join('<br>'))
                            .show();
                    } else {
                        $('#errors-results').hide();
                    }

                    // Reload page after 2 seconds
                    if (!response.results.errors) {
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    }
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
