@extends('Shared.Layouts.Master')

@section('title')
    @parent
    {{ $event->title }} - Members
@stop

@section('top_nav')
    @include('ManageEvent.Partials.TopNav')
@stop

@section('page_title')
    <i class="ico-user mr5"></i> Members
@stop

@section('menu')
    @include('ManageEvent.Partials.Sidebar')
@stop

@section('page_header')
    <div class="col-md-12">
        <a href="{{ route('showEventRegistrationCategories', ['event_id' => $event->id]) }}" class="btn btn-default btn-sm">
            <i class="ico-arrow-left"></i> Categories
        </a>
        <span class="text-muted ml-2">Define member fields, import members (stored separately from registrations), then map member fields to the Members registration form.</span>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-tabs">
            <li class="{{ ($activeTab ?? 'list') === 'fields' ? 'active' : '' }}">
                <a href="{{ route('showEventMembersFields', ['event_id' => $event->id]) }}">Member Fields</a>
            </li>
            <li class="{{ ($activeTab ?? 'list') === 'list' ? 'active' : '' }}">
                <a href="{{ route('showEventMembersList', ['event_id' => $event->id]) }}">Members List</a>
            </li>
            <li class="{{ ($activeTab ?? 'list') === 'import' ? 'active' : '' }}">
                <a href="{{ route('showEventMembersImport', ['event_id' => $event->id]) }}">Import from Excel</a>
            </li>
            <li class="{{ ($activeTab ?? 'list') === 'mapping' ? 'active' : '' }}">
                <a href="{{ route('showEventMembersMapping', ['event_id' => $event->id]) }}">Field Mapping</a>
            </li>
        </ul>

        <div class="tab-content panel panel-default" style="border-top: none;">
            <!-- Tab 1: Member Fields -->
            <div id="tab-fields" class="tab-pane {{ ($activeTab ?? 'list') === 'fields' ? 'active' : '' }}">
                <div class="panel-body">
                    <p class="text-muted">Define the data structure for members in this event. <strong>full_name</strong> is required for import. Add fields like membership_number, expiration_date, etc.</p>
                    <p class="text-info small"><strong>Display & search:</strong> Fields marked here are shown on the event page and used for member lookup. You can mark more than one — then a single input will search by <em>any</em> of them (e.g. value in Membership number <strong>or</strong> Email).</p>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Key</th>
                                    <th>Label</th>
                                    <th>Type</th>
                                    <th>Required</th>
                                    <th>Display & search</th>
                                    <th width="100"></th>
                                </tr>
                            </thead>
                            <tbody id="member-fields-tbody">
                                @forelse($event->eventMemberFields as $f)
                                <tr data-id="{{ $f->id }}">
                                    <td><code>{{ $f->field_key }}</code></td>
                                    <td class="field-label">{{ $f->label }}</td>
                                    <td class="field-type">{{ $f->type }}</td>
                                    <td class="field-required" data-value="{{ $f->is_required ? 1 : 0 }}">{{ $f->is_required ? 'Yes' : 'No' }}</td>
                                    <td class="field-unique" data-value="{{ $f->is_unique ? 1 : 0 }}">{{ $f->is_unique ? 'Yes' : 'No' }}</td>
                                    <td>
                                        <button type="button" class="btn btn-xs btn-primary btn-edit-field" data-id="{{ $f->id }}">Edit</button>
                                        <button type="button" class="btn btn-xs btn-danger btn-delete-field" data-id="{{ $f->id }}">Delete</button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-muted">No fields defined. Add one below.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <h5>Add field</h5>
                    <form id="form-add-field" class="form-inline">
                        @csrf
                        <input type="text" name="field_key" placeholder="field_key (e.g. full_name)" class="form-control" required pattern="[a-z0-9_]+" title="Lowercase letters, numbers, underscore only">
                        <input type="text" name="label" placeholder="Label" class="form-control" required>
                        <select name="type" class="form-control">
                            <option value="text">Text</option>
                            <option value="number">Number</option>
                            <option value="date">Date</option>
                            <option value="datetime">DateTime</option>
                        </select>
                        <label><input type="checkbox" name="is_required" value="1"> Required</label>
                        <label><input type="checkbox" name="is_unique" value="1"> Display & search</label>
                        <button type="submit" class="btn btn-success">Add</button>
                    </form>
                </div>
            </div>

            <!-- Tab 2: Members List (from event_members only; not registration_users) -->
            <div id="tab-list" class="tab-pane {{ ($activeTab ?? 'list') === 'list' ? 'active' : '' }}">
                <div class="panel-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <form method="get" action="{{ route('showEventMembersList', ['event_id' => $event->id]) }}" class="form-inline">
                                <input type="hidden" name="per_page" value="{{ $perPage ?? 20 }}">
                                <input type="text" class="form-control" name="q" value="{{ $search ?? '' }}" placeholder="Search members by any field..." style="min-width: 320px;">
                                <button type="submit" class="btn btn-primary">Search</button>
                                @if(!empty($search))
                                <a href="{{ route('showEventMembersList', ['event_id' => $event->id]) }}" class="btn btn-default">Clear</a>
                                @endif
                            </form>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="{{ route('showCreateEventMember', ['event_id' => $event->id]) }}" class="btn btn-success">
                                <i class="ico-plus"></i> Add Member
                            </a>
                        </div>
                    </div>
                    @if($members->isEmpty())
                        <p class="text-muted">No members yet. Use the Import tab to add members from Excel. Members are stored separately from User Registration.</p>
                    @else
                        <div class="form-inline mb-3">
                            <button type="button" id="btn-bulk-delete" class="btn btn-danger btn-sm" disabled title="Delete selected members">
                                <i class="ico-trash"></i> Delete selected
                            </button>
                            <button type="button" id="btn-delete-all" class="btn btn-danger btn-sm ml-2" title="Delete all members in this event">
                                <i class="ico-trash"></i> Delete all
                            </button>
                        </div>
                        <form id="form-members-list" action="{{ route('showEventMembers', ['event_id' => $event->id]) }}" method="get">
                            <input type="hidden" name="per_page" id="per-page-input" value="{{ $perPage ?? 20 }}">
                        </form>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" id="select-all-members" title="Select all on this page">
                                        </th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        @foreach($event->eventMemberFields as $f)
                                        <th>{{ $f->label }}</th>
                                        @endforeach
                                        <th>Status</th>
                                        <th width="180">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($members as $m)
                                    @php $dataByKey = $m->data->pluck('value', 'field_key'); @endphp
                                    <tr>
                                        <td><input type="checkbox" class="member-row-cb" name="member_ids[]" value="{{ $m->id }}"></td>
                                        <td>{{ $dataByKey->get('full_name') ?? $dataByKey->get('first_name') . ' ' . $dataByKey->get('last_name') ?: '-' }}</td>
                                        <td>{{ $dataByKey->get('email') ?? '-' }}</td>
                                        @foreach($event->eventMemberFields as $f)
                                        <td>{{ $dataByKey->get($f->field_key) ?? '-' }}</td>
                                        @endforeach
                                        <td>{{ $m->status }}</td>
                                        <td>
                                            <a href="{{ route('showEventMember', ['event_id' => $event->id, 'member_id' => $m->id]) }}" class="btn btn-xs btn-default">Show</a>
                                            <a href="{{ route('showEditEventMember', ['event_id' => $event->id, 'member_id' => $m->id]) }}" class="btn btn-xs btn-primary">Edit</a>
                                            <form method="post" action="{{ route('postDeleteEventMember', ['event_id' => $event->id, 'member_id' => $m->id]) }}" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Delete this member?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                {{ $members->appends(request()->except('page'))->links() }}
                            </div>
                            <div class="col-md-6 text-right">
                                <label class="control-label mr-2">Show per page:</label>
                                <select id="per-page-select" class="form-control input-sm" style="width: auto; display: inline-block;">
                                    @foreach([10, 15, 25, 50, 100, 300] as $n)
                                    <option value="{{ $n }}" {{ ($perPage ?? 20) == $n ? 'selected' : '' }}>{{ $n }}</option>
                                    @endforeach
                                </select>
                                <span class="text-muted ml-2">Showing {{ $members->firstItem() }}–{{ $members->lastItem() }} of {{ $members->total() }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tab 3: Import from Excel (saves to event_members only; no registration_users) -->
            <div id="tab-import" class="tab-pane {{ ($activeTab ?? 'list') === 'import' ? 'active' : '' }}">
                <div class="panel-body">
                    @if($event->eventMemberFields->isEmpty())
                        <p class="text-warning">Define member fields in the first tab before importing. You need at least <strong>full_name</strong>.</p>
                    @else
                        <p class="text-muted">Upload an Excel file (xlsx, xls, csv). Map columns to member fields. Imported rows are stored in <strong>Members</strong> only.</p>
                        <div class="form-group">
                            <label>Excel file</label>
                            <input type="file" id="import-file" accept=".xlsx,.xls,.csv" class="form-control">
                        </div>
                        <div id="upload-import-status" class="form-group hidden">
                            <span class="upload-import-spinner" style="display:none;"><i class="ico-spinner ico-spin"></i></span>
                            <span class="upload-import-text"></span>
                        </div>
                        <button type="button" id="btn-upload-excel" class="btn btn-primary">Upload &amp; Map Columns</button>

                        <div id="mapping-section" class="hidden" style="margin-top:20px;">
                            <h5>Map Excel columns to member fields</h5>
                            <p class="text-muted">First row of your file is treated as headers. Select which Excel column goes to each field.</p>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Member field</th>
                                        <th>Excel column</th>
                                    </tr>
                                </thead>
                                <tbody id="mapping-tbody"></tbody>
                            </table>
                            <button type="button" id="btn-process-import" class="btn btn-success">Import Now</button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tab 4: Field Mapping (member field -> registration field for Members form) -->
            <div id="tab-mapping" class="tab-pane {{ ($activeTab ?? 'list') === 'mapping' ? 'active' : '' }}">
                <div class="panel-body">
                    @if(!$membersRegistration)
                        <p class="text-warning">Set one registration as &quot;Members form&quot; in Event → Registrations (edit registration, check &quot;Members form&quot;) so we know where to map member data when a member registers.</p>
                    @elseif($event->eventMemberFields->isEmpty())
                        <p class="text-warning">Define member fields in the first tab first.</p>
                    @else
                        <p class="text-muted">When a member registers (e.g. from the symposium Members tab), their data is copied to the Members registration form using this mapping.</p>
                        <div class="form-group">
                            <label>Members registration</label>
                            <input type="text" class="form-control" value="{{ $membersRegistration->name }} ({{ $membersRegistration->category->name ?? '-' }})" readonly>
                            <input type="hidden" id="mapping-registration-id" value="{{ $membersRegistration->id }}">
                        </div>
                        <p class="text-muted small mb-3">You can map one member field to multiple registration fields. E.g. map <strong>full_name</strong> to both <strong>First Name</strong> and <strong>Last Name</strong> — the value will be split automatically (first word → First Name, rest → Last Name).</p>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Member field</th>
                                    <th>Maps to (Registration field) — multiple allowed</th>
                                </tr>
                            </thead>
                            <tbody id="field-mapping-tbody">
                                @foreach($event->eventMemberFields as $f)
                                @php
                                    $existingList = $event->eventMemberFieldMappings->where('registration_id', $membersRegistration->id)->where('member_field_key', $f->field_key);
                                    $existingValues = $existingList->map(function($m) {
                                        return $m->target_type === 'dynamic_field' ? 'dynamic_field:' . $m->target_dynamic_form_field_id : $m->target_type;
                                    })->values()->all();
                                @endphp
                                <tr>
                                    <td><code>{{ $f->field_key }}</code> — {{ $f->label }}</td>
                                    <td>
                                        <select class="form-control mapping-target-multi" data-member-key="{{ $f->field_key }}" multiple size="4">
                                            <option value="first_name" {{ in_array('first_name', $existingValues) ? 'selected' : '' }}>First Name</option>
                                            <option value="last_name" {{ in_array('last_name', $existingValues) ? 'selected' : '' }}>Last Name</option>
                                            <option value="email" {{ in_array('email', $existingValues) ? 'selected' : '' }}>Email</option>
                                            <option value="phone" {{ in_array('phone', $existingValues) ? 'selected' : '' }}>Phone</option>
                                            @foreach($membersRegistration->dynamicFormFields ?? [] as $dyn)
                                            <option value="dynamic_field:{{ $dyn->id }}" {{ in_array('dynamic_field:'.$dyn->id, $existingValues) ? 'selected' : '' }}>Dynamic: {{ $dyn->label }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Hold Ctrl/Cmd to select multiple (e.g. First Name + Last Name for full_name)</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="button" id="btn-save-mappings" class="btn btn-success">Save mapping</button>
                        <span id="mapping-save-status" class="ml-2 text-muted"></span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-field-modal" tabindex="-1" role="dialog" aria-labelledby="editFieldModalLabel">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="form-edit-field">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="editFieldModalLabel">Edit Member Field</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-field-id" value="">
                    <div class="form-group">
                        <label for="edit-field-label">Label</label>
                        <input type="text" id="edit-field-label" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-field-type">Type</label>
                        <select id="edit-field-type" class="form-control">
                            <option value="text">Text</option>
                            <option value="number">Number</option>
                            <option value="date">Date</option>
                            <option value="datetime">DateTime</option>
                        </select>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="edit-field-required" value="1"> Required
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="edit-field-unique" value="1"> Display &amp; search
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('foot')
<style>
#upload-import-status .upload-import-spinner i { display: inline-block; animation: members-spin 0.8s linear infinite; }
@keyframes members-spin { to { transform: rotate(360deg); } }
</style>
<script>
(function() {
    var eventId = {{ $event->id }};
    var baseUrl = '{{ url('/') }}';
    var token = '{{ csrf_token() }}';
    var memberFields = @json($event->eventMemberFields);
    var excelHeaders = [];
    var uploadedFile = null;

    $('#form-add-field').on('submit', function(e) {
        e.preventDefault();
        var fd = new FormData(this);
        $.ajax({
            url: baseUrl + '/event/' + eventId + '/registration/members/fields',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.status === 'success') {
                    if (typeof toastr !== 'undefined') toastr.success(res.message);
                    else alert(res.message);
                    window.location.reload();
                } else {
                    alert(res.message || 'Error');
                }
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error adding field.';
                alert(msg);
            }
        });
    });

    $(document).on('click', '.btn-delete-field', function() {
        if (!confirm('Delete this field? Member data for this field will not be deleted.')) return;
        var id = $(this).data('id');
        $.ajax({
            url: baseUrl + '/event/' + eventId + '/registration/members/fields/' + id,
            type: 'DELETE',
            data: { _token: token },
            success: function(res) {
                if (res.status === 'success') {
                    $('tr[data-id="' + id + '"]').remove();
                    if (typeof toastr !== 'undefined') toastr.success(res.message);
                }
            }
        });
    });

    $(document).on('click', '.btn-edit-field', function() {
        var id = $(this).data('id');
        var $row = $('tr[data-id="' + id + '"]');
        if (!$row.length) return;

        $('#edit-field-id').val(id);
        $('#edit-field-label').val($.trim($row.find('.field-label').text()));
        $('#edit-field-type').val($.trim($row.find('.field-type').text()).toLowerCase());
        $('#edit-field-required').prop('checked', parseInt($row.find('.field-required').data('value') || 0, 10) === 1);
        $('#edit-field-unique').prop('checked', parseInt($row.find('.field-unique').data('value') || 0, 10) === 1);
        $('#edit-field-modal').modal('show');
    });

    $('#form-edit-field').on('submit', function(e) {
        e.preventDefault();
        var id = $('#edit-field-id').val();
        var newLabel = $.trim($('#edit-field-label').val());
        var newType = $.trim($('#edit-field-type').val()).toLowerCase();
        var isRequired = $('#edit-field-required').is(':checked') ? 1 : 0;
        var isUnique = $('#edit-field-unique').is(':checked') ? 1 : 0;

        if (!newLabel) {
            alert('Label is required.');
            return;
        }

        $.ajax({
            url: baseUrl + '/event/' + eventId + '/registration/members/fields/' + id,
            type: 'PUT',
            data: {
                _token: token,
                label: newLabel,
                type: newType,
                is_required: isRequired,
                is_unique: isUnique
            },
            success: function(res) {
                if (res.status === 'success') {
                    $('#edit-field-modal').modal('hide');
                    if (typeof toastr !== 'undefined') toastr.success(res.message);
                    else alert(res.message);
                    window.location.reload();
                } else {
                    alert(res.message || 'Failed to update field.');
                }
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to update field.';
                alert(msg);
            }
        });
    });

    function showUploadImportLoading(msg, isError) {
        var $s = $('#upload-import-status');
        $s.removeClass('hidden').find('.upload-import-text').text(msg);
        $s.find('.upload-import-spinner').show();
        if (isError) $s.find('.upload-import-text').addClass('text-danger'); else $s.find('.upload-import-text').removeClass('text-danger text-success');
    }
    function hideUploadImportLoading(msg, isSuccess) {
        var $s = $('#upload-import-status');
        $s.find('.upload-import-spinner').hide();
        if (msg) {
            $s.find('.upload-import-text').text(msg);
            $s.find('.upload-import-text').toggleClass('text-success', !!isSuccess).toggleClass('text-danger', !isSuccess);
        } else {
            $s.find('.upload-import-text').text('').removeClass('text-success text-danger');
        }
        if (!msg) $s.addClass('hidden');
    }

    $('#btn-upload-excel').on('click', function() {
        var fileInput = document.getElementById('import-file');
        if (!fileInput.files.length) {
            alert('Please select a file.');
            return;
        }
        var fd = new FormData();
        fd.append('file', fileInput.files[0]);
        fd.append('_token', token);
        uploadedFile = fileInput.files[0];
        var $btn = $('#btn-upload-excel');
        $btn.prop('disabled', true);
        showUploadImportLoading('Uploading file and reading columns… Please wait (large files may take 1–2 minutes).');
        $.ajax({
            url: baseUrl + '/event/' + eventId + '/registration/members/upload-excel',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            timeout: 300000,
            success: function(res) {
                if (res.status === 'success' && res.headers && res.headers.length) {
                    excelHeaders = res.headers;
                    renderMapping(res.headers);
                    $('#mapping-section').removeClass('hidden');
                    hideUploadImportLoading();
                } else {
                    hideUploadImportLoading(res.message || 'Failed', false);
                }
            },
            error: function(xhr, textStatus) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : (textStatus === 'timeout' ? 'Request timed out. The file may be too large — try a smaller file or try again.' : 'Upload failed.');
                hideUploadImportLoading(msg, false);
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });

    function renderMapping(headers) {
        var tbody = $('#mapping-tbody');
        tbody.empty();
        var fields = memberFields.slice();
        if (!fields.find(function(f) { return f.field_key === 'full_name'; })) {
            fields.unshift({ field_key: 'full_name', label: 'Full Name (required)' });
        }
        fields.push({ field_key: 'email', label: 'Email (optional)' });
        fields.push({ field_key: 'phone', label: 'Phone (optional)' });
        fields.forEach(function(f) {
            var opts = '<option value="">-- Skip --</option>';
            headers.forEach(function(h, i) {
                opts += '<option value="' + i + '">Column ' + (i + 1) + ': ' + (h || '') + '</option>';
            });
            tbody.append('<tr><td>' + (f.label || f.field_key) + '</td><td><select class="form-control map-select" data-field="' + f.field_key + '">' + opts + '</select></td></tr>');
        });
    }

    $('#btn-process-import').on('click', function() {
        if (!uploadedFile) {
            alert('Please upload a file first.');
            return;
        }
        var mapping = {};
        $('.map-select').each(function() {
            var field = $(this).data('field');
            var col = $(this).val();
            if (col !== '') mapping[col] = field;
        });
        var hasFullName = Object.keys(mapping).some(function(k) { return mapping[k] === 'full_name'; });
        if (!hasFullName) {
            alert('Please map at least one column to Full Name.');
            return;
        }
        var fd = new FormData();
        fd.append('file', uploadedFile);
        fd.append('_token', token);
        fd.append('mapping', JSON.stringify(mapping));
        var $btn = $('#btn-process-import');
        $btn.prop('disabled', true);
        showUploadImportLoading('Importing members… This may take several minutes for large files.');
        $.ajax({
            url: baseUrl + '/event/' + eventId + '/registration/members/process-import',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            timeout: 600000,
            success: function(res) {
                if (res.status === 'success') {
                    hideUploadImportLoading(res.message, true);
                    if (typeof toastr !== 'undefined') toastr.success(res.message);
                    if (res.errors && res.errors.length) {
                        console.warn('Import errors:', res.errors);
                    }
                    setTimeout(function() { window.location.reload(); }, 1500);
                } else {
                    hideUploadImportLoading(res.message || 'Error', false);
                }
            },
            error: function(xhr, textStatus) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : (textStatus === 'timeout' ? 'Import timed out. Try a smaller file or try again.' : 'Import failed.');
                hideUploadImportLoading(msg, false);
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });

    // --- Members List: bulk actions & per-page ---
    $('#select-all-members').on('change', function() {
        $('.member-row-cb').prop('checked', this.checked);
        $('#btn-bulk-delete').prop('disabled', !$('.member-row-cb:checked').length);
    });
    $(document).on('change', '.member-row-cb', function() {
        var any = $('.member-row-cb:checked').length > 0;
        $('#btn-bulk-delete').prop('disabled', !any);
        $('#select-all-members').prop('checked', $('.member-row-cb').length === $('.member-row-cb:checked').length);
    });
    $('#per-page-select').on('change', function() {
        var val = $(this).val();
        var url = new URL(window.location.href);
        url.searchParams.set('per_page', val);
        url.searchParams.delete('page');
        window.location.href = url.toString();
    });
    $('#btn-bulk-delete').on('click', function() {
        var ids = $('.member-row-cb:checked').map(function() { return $(this).val(); }).get();
        if (!ids.length) return;
        if (!confirm('Delete ' + ids.length + ' selected member(s)?')) return;
        var $btn = $(this);
        $btn.prop('disabled', true);
        $.ajax({
            url: baseUrl + '/event/' + eventId + '/registration/members/bulk-delete',
            type: 'POST',
            data: { _token: token, ids: ids },
            success: function(res) {
                if (res.status === 'success') {
                    if (typeof toastr !== 'undefined') toastr.success(res.message);
                    else alert(res.message);
                    window.location.reload();
                }
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to delete.';
                alert(msg);
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });
    $('#btn-delete-all').on('click', function() {
        var total = {{ $members->total() ?? 0 }};
        if (total === 0) return;
        if (!confirm('Delete ALL ' + total + ' members in this event? This cannot be undone.')) return;
        if (!confirm('Are you sure? This will remove every member.')) return;
        var $btn = $(this);
        $btn.prop('disabled', true);
        $.ajax({
            url: baseUrl + '/event/' + eventId + '/registration/members/delete-all',
            type: 'POST',
            data: { _token: token },
            success: function(res) {
                if (res.status === 'success') {
                    if (typeof toastr !== 'undefined') toastr.success(res.message);
                    else alert(res.message);
                    window.location.reload();
                }
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to delete.';
                alert(msg);
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });

    $('#btn-save-mappings').on('click', function() {
        var regId = $('#mapping-registration-id').val();
        if (!regId) return;
        var mappings = [];
        $('.mapping-target-multi').each(function() {
            var memberKey = $(this).data('member-key');
            var selected = $(this).val(); // array when multiple
            if (!selected || !selected.length) return;
            selected.forEach(function(val) {
                var targetType = val;
                var targetDynId = null;
                if (val.indexOf('dynamic_field:') === 0) {
                    targetType = 'dynamic_field';
                    targetDynId = val.replace('dynamic_field:', '');
                }
                mappings.push({
                    member_field_key: memberKey,
                    target_type: targetType,
                    target_dynamic_form_field_id: targetDynId || null
                });
            });
        });
        var $btn = $('#btn-save-mappings');
        var $status = $('#mapping-save-status');
        $btn.prop('disabled', true);
        $status.text('Saving…');
        $.ajax({
            url: baseUrl + '/event/' + eventId + '/registration/members/field-mappings',
            type: 'POST',
            data: {
                _token: token,
                registration_id: regId,
                mappings: mappings
            },
            success: function(res) {
                if (res.status === 'success') {
                    $status.text('Saved.').css('color', 'green');
                    if (typeof toastr !== 'undefined') toastr.success(res.message);
                } else {
                    $status.text(res.message || 'Error').css('color', 'red');
                }
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to save.';
                $status.text(msg).css('color', 'red');
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });
})();
</script>
@stop
