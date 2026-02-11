{{-- resources/views/ManageEvent/RegistrationUsers.blade.php --}}

@extends('Shared.Layouts.Master')

@section('title')
    @parent
    {{ $event->title }} - Registered Users
@stop

@section('top_nav')
    @include('ManageEvent.Partials.TopNav')
@stop

@section('page_title')
    <i class="ico-users mr5"></i>
    @if(isset($registration))
        {{ $registration->name }} - Registered Users
    @else
        All Registered Users
    @endif
@stop

@section('head')
    <style>
        .header-actions {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }

        .header-actions h4 {
            margin: 0 0 15px 0;
            color: #495057;
            font-size: 16px;
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .search-form {
            margin-bottom: 20px;
        }

        .user-status {
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: #f0ad4e;
            color: white;
        }

        .status-approved {
            background-color: #5cb85c;
            color: white;
        }

        .status-rejected {
            background-color: #d9534f;
            color: white;
        }

        .bulk-actions {
            margin-bottom: 15px;
        }

        .user-details {
            margin-bottom: 5px;
        }

        .user-details strong {
            display: inline-block;
            min-width: 100px;
        }

        .user-actions .dropdown-menu {
            min-width: 120px;
        }

        .user-actions .dropdown-menu a {
            padding: 5px 12px;
        }

        .user-actions .dropdown-menu i {
            margin-right: 5px;
        }

        .filter-status {
            margin-right: 10px;
        }

        .filter-status.active {
            font-weight: bold;
            text-decoration: underline;
        }

        .pagination {
            margin-top: 0;
        }

        .table-responsive {
            border: none;
        }

        .table>tbody>tr>td {
            vertical-align: middle;
        }

        .checkbox-column {
            width: 40px;
            text-align: center;
        }

        .status-column {
            width: 100px;
        }

        .actions-column {
            width: 120px;
            text-align: right;
        }

        .no-results {
            padding: 50px 0;
            text-align: center;
        }

        .no-results i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 15px;
            display: block;
        }

        .no-results h4 {
            color: #888;
            margin-bottom: 10px;
        }

        .no-results p {
            color: #999;
        }

        .new-registration {
            background-color: #fffde7;
            position: relative;
        }

        .new-registration::after {
            content: 'NEW';
            position: absolute;
            top: 0;
            right: 0;
            background-color: #ff4136;
            color: white;
            font-size: 10px;
            padding: 2px 5px;
            font-weight: bold;
        }

        .page-title-buttons {
            display: inline-block;
            margin-left: 20px;
        }

        .code-display {
            font-family: monospace;
            font-weight: bold;
            color: #007bff;
        }

        .pagination-controls {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .pagination-controls label {
            margin: 0;
            font-weight: 500;
        }

        .pagination-controls select {
            width: 80px;
        }

        .results-info {
            color: #666;
            font-size: 14px;
        }

        .bulk-actions-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .export-selected-btn {
            background-color: #17a2b8;
            border-color: #17a2b8;
        }

        .export-selected-btn:hover {
            background-color: #138496;
            border-color: #117a8b;
        }
    </style>
@stop

@section('menu')
    @include('ManageEvent.Partials.Sidebar')
@stop

@section('page_header')
    <div class="col-md-12">
        <span class="page-title-buttons">
            <a href="{{ route('showEventRegistration', ['event_id' => $event->id]) }}" class="btn btn-default btn-sm">
                <i class="ico-arrow-left"></i> Back to Registrations
            </a>
            @if(isset($registration))
                <a href="{{ route('showEventRegistrationUsers', ['event_id' => $event->id]) }}" class="btn btn-info btn-sm">
                    <i class="ico-users"></i> View All Event Users
                </a>
            @endif
            <a href="{{ route('showEventRegistrationUsers', ['event_id' => $event->id, 'mark_as_viewed' => 1]) }}"
                class="btn btn-success btn-sm">
                <i class="ico-checkmark"></i> Mark All as Viewed
            </a>
        </span>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Header Actions -->
            <div class="header-actions">
                <h4><i class="ico-plus"></i> User Management</h4>
                <div class="action-buttons">
                    <button class="btn btn-success loadModal"
                            data-modal-id="AddUser"
                            data-href="{{ route('showAddUser', ['event_id' => $event->id, 'registration_id' => $registration->id ?? null]) }}">
                        <i class="ico-user-plus"></i> Add User
                    </button>
                    <button class="btn btn-primary loadModal"
                            data-modal-id="ImportUsers"
                            data-href="{{ route('showImportUsers', ['event_id' => $event->id]) }}">
                        <i class="ico-upload"></i> Import Users
                    </button>
                    <button type="button" class="btn btn-success" id="btn-open-whatsapp-modal" style="background:#25D366; border-color:#25D366;">
                        <i class="fa fa-whatsapp"></i> Send WhatsApp
                    </button>
                </div>
            </div>

            <!-- Filters Panel -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="ico-filter"></i> Filter Users
                    </h3>
                </div>
                <div class="panel-body">
                    <form class="search-form" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="search">Search</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="search" name="search"
                                            placeholder="Search by name, email, or code" value="{{ $filters['search'] ?? '' }}">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="submit">
                                                <i class="ico-search"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @if(!isset($registration))
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="registration_id">Registration Form</label>
                                    <select name="registration_id" id="registration_id" class="form-control">
                                        <option value="">All Registration Forms</option>
                                        @foreach ($registrations as $id => $name)
                                            <option value="{{ $id }}"
                                                {{ isset($filters['registration_id']) && $filters['registration_id'] == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Status</label>
                                    <div>
                                        <a href="{{ request()->fullUrlWithQuery(['status' => null, 'page' => null]) }}"
                                            class="filter-status {{ empty($filters['status']) ? 'active' : '' }}">All</a>
                                        <a href="{{ request()->fullUrlWithQuery(['status' => 'pending', 'page' => null]) }}"
                                            class="filter-status {{ isset($filters['status']) && $filters['status'] === 'pending' ? 'active' : '' }}">Pending</a>
                                        <a href="{{ request()->fullUrlWithQuery(['status' => 'approved', 'page' => null]) }}"
                                            class="filter-status {{ isset($filters['status']) && $filters['status'] === 'approved' ? 'active' : '' }}">Approved</a>
                                        <a href="{{ request()->fullUrlWithQuery(['status' => 'rejected', 'page' => null]) }}"
                                            class="filter-status {{ isset($filters['status']) && $filters['status'] === 'rejected' ? 'active' : '' }}">Rejected</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <a href="{{ request()->url() }}" class="btn btn-default">
                                    <i class="ico-undo"></i> Clear Filters
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="ico-search"></i> Apply Filters
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Users Table -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="ico-users"></i> Registered Users ({{ $users->total() }})
                    </h3>
                </div>
                <div class="panel-body">
                    @if ($users->count() > 0)
                        <form id="bulk-action-form">
                            <div class="bulk-actions-container">
                                <div class="bulk-actions">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            Bulk Actions <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a href="javascript:void(0);" class="bulk-action" data-action="approve"><i
                                                        class="ico-checkmark"></i> Approve Selected</a></li>
                                            <li><a href="javascript:void(0);" class="bulk-action" data-action="reject"><i
                                                        class="ico-close"></i> Reject Selected</a></li>
                                            <li role="separator" class="divider"></li>
                                            <li><a href="javascript:void(0);" class="bulk-action" data-action="delete"><i
                                                        class="ico-trash"></i> Delete Selected</a></li>
                                            <li role="separator" class="divider"></li>
                                            <li><a href="javascript:void(0);" class="bulk-action" data-action="send-approve-email"><i class="ico-mail"></i> Send Approve Email</a></li>
                                            <li><a href="javascript:void(0);" class="bulk-action" data-action="send-reject-email"><i class="ico-mail"></i> Send Reject Email</a></li>
                                            <li role="separator" class="divider"></li>
                                            <li><a href="javascript:void(0);" class="bulk-action-open-whatsapp"><i class="fa fa-whatsapp"></i> Send WhatsApp</a></li>
                                            <li role="separator" class="divider"></li>
                                            <li><a href="javascript:void(0);" class="bulk-action" data-action="export-selected"><i class="ico-download"></i> Export Selected</a></li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="export-actions">
                                    <button type="button" class="btn btn-info export-selected-btn" id="export-selected-btn" disabled>
                                        <i class="ico-download"></i> Export Selected (<span id="selected-count">0</span>)
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="checkbox-column">
                                                <input type="checkbox" id="select-all-checkbox">
                                            </th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            @if(!isset($registration))
                                                <th>Registration Form</th>
                                            @endif
                                            <th>User Type</th>
                                            <th class="status-column">Status</th>
                                            <th>CR Code</th>
                                            <th>Registered On</th>
                                            <th class="actions-column">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                            <tr class="{{ $user->is_new ? 'new-registration' : '' }}">
                                                <td class="checkbox-column">
                                                    <input type="checkbox" class="user-checkbox" name="user_ids[]"
                                                        value="{{ $user->id }}">
                                                </td>
                                                <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                                <td>{{ $user->email }}</td>
                                                @if(!isset($registration))
                                                    <td>
                                                        <a href="{{ route('showRegistrationUsers', ['event_id' => $event->id, 'registration_id' => $user->registration_id]) }}">
                                                            {{ $user->registration->name }}
                                                        </a>
                                                    </td>
                                                @endif
                                                <td>
                                                    @if($user->userType)
                                                        <span class="badge badge-info">{{ $user->userType->name }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="status-column">
                                                    <span class="user-status status-{{ $user->status }}">
                                                        {{ ucfirst($user->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($user->status === 'approved' && $user->unique_code)
                                                        <span class="code-display">{{ $user->unique_code }}</span>
                                                        @if($user->ticket_token)
                                                            <br>
                                                            <a href="{{ route('downloadUserTicket', ['event_id' => $event->id, 'user_id' => $user->id]) }}"
                                                               class="btn btn-xs btn-success" title="Download Ticket">
                                                                <i class="ico-download"></i>
                                                            </a>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
                                                <td class="actions-column">
                                                    <div class="btn-group user-actions">
                                                        <button type="button"
                                                            class="btn btn-xs btn-default dropdown-toggle"
                                                            data-toggle="dropdown">
                                                            Actions <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                                            <li>
                                                                <a href="javascript:void(0);"
                                                                    data-href="{{ route('getUserDetails', ['event_id' => $event->id, 'user_id' => $user->id]) }}"
                                                                    class="loadModal view-user"
                                                                    data-modal-id="UserDetails">
                                                                    <i class="ico-eye"></i> View Details
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:void(0);"
                                                                    data-href="{{ route('showEditUserResgistration', ['event_id' => $event->id, 'user_id' => $user->id]) }}"
                                                                    class="loadModal edit-user"
                                                                    data-modal-id="EditUser">
                                                                    <i class="ico-edit"></i> Edit
                                                                </a>
                                                            </li>
                                                            @if ($user->status !== 'approved')
                                                                <li>
                                                                    <a href="javascript:void(0);" class="update-status"
                                                                        data-user-id="{{ $user->id }}"
                                                                        data-status="approved"
                                                                        data-url="{{ route('updateUserStatus', ['event_id' => $event->id, 'user_id' => $user->id]) }}">
                                                                        <i class="ico-checkmark"></i> Approve
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            @if ($user->status !== 'rejected')
                                                                <li>
                                                                    <a href="javascript:void(0);" class="update-status"
                                                                        data-user-id="{{ $user->id }}"
                                                                        data-status="rejected"
                                                                        data-url="{{ route('updateUserStatus', ['event_id' => $event->id, 'user_id' => $user->id]) }}">
                                                                        <i class="ico-close"></i> Reject
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            <li class="divider"></li>
                                                            <li>
                                                                <a href="javascript:void(0);" class="delete-user"
                                                                    data-user-id="{{ $user->id }}">
                                                                    <i class="ico-trash"></i> Delete
                                                                </a>
                                                            </li>
                                                            <li role="separator" class="divider"></li>
                                                            <li>
                                                                <a href="javascript:void(0);" class="send-single-email"
                                                                    data-user-id="{{ $user->id }}"
                                                                    data-action="approve"
                                                                    data-url="{{ route('sendApprovalEmail', ['event_id' => $event->id, 'user_id' => $user->id]) }}">
                                                                    <i class="ico-mail"></i> Send Approve Email
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="javascript:void(0);" class="send-single-email"
                                                                    data-user-id="{{ $user->id }}"
                                                                    data-action="reject"
                                                                    data-url="{{ route('sendRejectionEmail', ['event_id' => $event->id, 'user_id' => $user->id]) }}">
                                                                    <i class="ico-mail"></i> Send Reject Email
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>

                        <!-- Pagination -->
                        <div class="row">
                            <div class="col-md-10">
                                {{ $users->links() }}
                            </div>
                            <div class="col-md-2">
                                <!-- Pagination Controls -->
                                <div class="pagination-controls">
                                    <select id="per-page-select" class="form-control" onchange="changePerPage(this.value)">
                                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                        <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                                        <option value="300" {{ $perPage == 300 ? 'selected' : '' }}>300</option>
                                    </select>
                                    <span class="results-info">
                                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
                                    </span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="no-results">
                            <i class="ico-users"></i>
                            <h4>No users found</h4>
                            <p>
                                @if (!empty($filters))
                                    No users match your search criteria. <a href="{{ request()->url() }}">Clear filters</a>
                                @else
                                    No users have registered for this event yet.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Send WhatsApp Modal -->
    <div class="modal fade" id="whatsapp-modal" tabindex="-1" role="dialog" aria-labelledby="whatsapp-modal-title" style="display: none;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h3 class="modal-title" id="whatsapp-modal-title">
                        <i class="fa fa-whatsapp" style="color:#25D366;"></i> Send WhatsApp Message
                    </h3>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Select users using the checkboxes in the table, then write your message below. Use placeholders to personalize (click to insert).</p>
                    <div class="form-group">
                        <label class="control-label">Placeholders (click to insert)</label>
                        <div class="placeholder-chips" style="margin-bottom:10px;">
                            <button type="button" class="btn btn-xs btn-default placeholder-btn" data-placeholder="@first_name">@first_name</button>
                            <button type="button" class="btn btn-xs btn-default placeholder-btn" data-placeholder="@last_name">@last_name</button>
                            <button type="button" class="btn btn-xs btn-default placeholder-btn" data-placeholder="@email">@email</button>
                            <button type="button" class="btn btn-xs btn-default placeholder-btn" data-placeholder="@phone">@phone</button>
                            <button type="button" class="btn btn-xs btn-default placeholder-btn" data-placeholder="@unique_code">@unique_code</button>
                            <button type="button" class="btn btn-xs btn-default placeholder-btn" data-placeholder="@event_title">@event_title</button>
                            <button type="button" class="btn btn-xs btn-default placeholder-btn" data-placeholder="@registration_name">@registration_name</button>
                            <button type="button" class="btn btn-xs btn-default placeholder-btn" data-placeholder="@user_type">@user_type</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="whatsapp-message" class="control-label required">Message</label>
                        <textarea id="whatsapp-message" class="form-control" rows="6" placeholder="e.g. مرحبا @first_name @last_name ندعوكم لحضور @event_title ..." maxlength="4000"></textarea>
                        <small class="help-block">Recipients must have a phone number. Max 4000 characters.</small>
                    </div>
                    <p class="text-info"><strong>Recipients: <span id="whatsapp-recipient-count">0</span> selected</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="whatsapp-send-btn" style="background:#25D366; border-color:#25D366;">
                        <i class="fa fa-whatsapp"></i> Send to <span id="whatsapp-send-count">0</span> recipients
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('foot')
    <script>
        $(document).ready(function() {
            // Select all checkbox functionality
            $('#select-all-checkbox').change(function() {
                $('.user-checkbox').prop('checked', this.checked);
                updateSelectedCount();
            });

            // Individual checkbox change
            $('.user-checkbox').change(function() {
                updateSelectedCount();

                // Update select all checkbox state
                const totalCheckboxes = $('.user-checkbox').length;
                const checkedCheckboxes = $('.user-checkbox:checked').length;

                $('#select-all-checkbox').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
                $('#select-all-checkbox').prop('checked', checkedCheckboxes === totalCheckboxes);
            });

            // Update selected count display
            function updateSelectedCount() {
                const selectedCount = $('.user-checkbox:checked').length;
                $('#selected-count').text(selectedCount);
                $('#export-selected-btn').prop('disabled', selectedCount === 0);
            }

            // Bulk actions
            $('.bulk-action').on('click', function(e) {
                e.preventDefault();

                const action = $(this).data('action');
                const selectedUsers = $('.user-checkbox:checked');

                if (selectedUsers.length === 0) {
                    alert('Please select at least one user');
                    return;
                }

                let confirmMessage = '';

                switch (action) {
                    case 'approve':
                        confirmMessage = 'Are you sure you want to approve the selected users?';
                        break;
                    case 'reject':
                        confirmMessage = 'Are you sure you want to reject the selected users?';
                        break;
                    case 'delete':
                        confirmMessage =
                            'Are you sure you want to delete the selected users? This action cannot be undone.';
                        break;
                        case 'send-approve-email':
                            confirmMessage = 'Are you sure you want to send approval emails to the selected users?';
                            break;
                        case 'send-reject-email':
                            confirmMessage = 'Are you sure you want to send rejection emails to the selected users?';
                            break;
                        case 'export-selected':
                            exportSelectedUsers();
                            return;
                }

                if (confirm(confirmMessage)) {
                    const userIds = [];
                    selectedUsers.each(function() {
                        userIds.push($(this).val());
                    });

                    let url = '';
                    let data = {
                        _token: '{{ csrf_token() }}',
                        user_ids: userIds
                    };

                    switch (action) {
                        case 'approve':
                        case 'reject':
                        case 'delete':
                            url = '{{ route('bulkUpdateUsers', ['event_id' => $event->id]) }}';
                            data.action = action;
                            break;
                        case 'send-approve-email':
                            url = '{{ route('sendApprovalEmails', ['event_id' => $event->id]) }}';
                            break;
                        case 'send-reject-email':
                            url = '{{ route('sendRejectionEmails', ['event_id' => $event->id]) }}';
                            break;
                    }

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: data,
                        success: function(response) {
                            if (response.status === 'success') {
                                alert(response.message);
                                if (action === 'approve' || action === 'reject' || action === 'delete') {
                                    window.location.reload();
                                }
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr);
                            alert('An error occurred. Please try again.');
                        }
                    });
                }
            });

            // Export selected users function
            function exportSelectedUsers() {
                const selectedUsers = $('.user-checkbox:checked');

                if (selectedUsers.length === 0) {
                    alert('Please select at least one user to export.');
                    return;
                }

                const userIds = [];
                selectedUsers.each(function() {
                    userIds.push($(this).val());
                });

                // Create a form and submit it to trigger download
                const form = $('<form>', {
                    method: 'POST',
                    action: '{{ route('exportSelectedUsers', ['event_id' => $event->id]) }}'
                });

                form.append($('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: '{{ csrf_token() }}'
                }));

                userIds.forEach(function(id) {
                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'user_ids[]',
                        value: id
                    }));
                });

                $('body').append(form);
                form.submit();
                form.remove();

                alert(`Exporting ${userIds.length} selected users...`);
            }

            // Export selected button click
            $('#export-selected-btn').on('click', function() {
                exportSelectedUsers();
            });

            // Update user status
            $('.update-status').on('click', function(e) {
                e.preventDefault();

                const url = $(this).data('url');
                const status = $(this).data('status');

                let confirmMessage = '';

                switch (status) {
                    case 'approved':
                        confirmMessage = 'Are you sure you want to approve this user?';
                        break;
                    case 'rejected':
                        confirmMessage = 'Are you sure you want to reject this user?';
                        break;
                }

                if (confirm(confirmMessage)) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: status
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                alert(response.message);
                                window.location.reload();
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            alert('An error occurred. Please try again.');
                        }
                    });
                }
            });

            // Delete user
            $('.delete-user').on('click', function(e) {
                e.preventDefault();

                const userId = $(this).data('user-id');

                if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                    let url = '{{ route('deleteUser', ['event_id' => $event->id, 'user_id' => '__USER_ID__']) }}';
                    url = url.replace('__USER_ID__', userId);

                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                alert(response.message);
                                window.location.reload();
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr);
                            alert('An error occurred. Please try again.');
                        }
                    });
                }
            });

            // Handle single email actions
            $('.send-single-email').on('click', function(e) {
                e.preventDefault();

                const url = $(this).data('url');
                const action = $(this).data('action');

                let confirmMessage = '';

                switch (action) {
                    case 'approve':
                        confirmMessage = 'Are you sure you want to send an approval email to this user?';
                        break;
                    case 'reject':
                        confirmMessage = 'Are you sure you want to send a rejection email to this user?';
                        break;
                }

                if (confirm(confirmMessage)) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                alert(response.message);
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            alert('An error occurred. Please try again.');
                        }
                    });
                }
            });

            // Open WhatsApp modal (from header button or bulk "Send WhatsApp")
            function openWhatsAppModal() {
                var n = $('.user-checkbox:checked').length;
                $('#whatsapp-recipient-count').text(n);
                $('#whatsapp-send-count').text(n);
                $('#whatsapp-send-btn').prop('disabled', n === 0);
                $('#whatsapp-modal').modal('show');
            }
            $('#btn-open-whatsapp-modal').on('click', function() {
                openWhatsAppModal();
            });
            $('.bulk-action-open-whatsapp').on('click', function(e) {
                e.preventDefault();
                if ($('.user-checkbox:checked').length === 0) {
                    alert('Please select at least one user.');
                    return;
                }
                openWhatsAppModal();
            });
            $('#whatsapp-modal').on('show.bs.modal', function() {
                var n = $('.user-checkbox:checked').length;
                $('#whatsapp-recipient-count').text(n);
                $('#whatsapp-send-count').text(n);
                $('#whatsapp-send-btn').prop('disabled', n === 0);
            });
            // Insert placeholder at cursor
            $('.placeholder-btn').on('click', function() {
                var placeholder = $(this).data('placeholder');
                var ta = document.getElementById('whatsapp-message');
                var start = ta.selectionStart;
                var end = ta.selectionEnd;
                var text = $('#whatsapp-message').val();
                $('#whatsapp-message').val(text.substring(0, start) + placeholder + text.substring(end));
                ta.selectionStart = ta.selectionEnd = start + placeholder.length;
                ta.focus();
            });
            // Send WhatsApp
            $('#whatsapp-send-btn').on('click', function() {
                var userIds = [];
                $('.user-checkbox:checked').each(function() { userIds.push($(this).val()); });
                if (userIds.length === 0) {
                    alert('Please select at least one user.');
                    return;
                }
                var message = $('#whatsapp-message').val().trim();
                if (!message) {
                    alert('Please enter a message.');
                    return;
                }
                var btn = $(this);
                btn.prop('disabled', true).html('<i class="ico-spinner"></i> Sending...');
                $.ajax({
                    url: '{{ route('sendBulkWhatsApp', ['event_id' => $event->id]) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        user_ids: userIds,
                        message: message
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            if (typeof toastr !== 'undefined') toastr.success(response.message);
                            else alert(response.message);
                            $('#whatsapp-modal').modal('hide');
                            $('#whatsapp-message').val('');
                        } else {
                            if (typeof toastr !== 'undefined') toastr.error(response.message || 'Error');
                            else alert(response.message || 'Error');
                        }
                    },
                    error: function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'An error occurred.';
                        if (typeof toastr !== 'undefined') toastr.error(msg);
                        else alert(msg);
                    },
                    complete: function() {
                        btn.prop('disabled', false).html('<i class="fa fa-whatsapp"></i> Send to <span id="whatsapp-send-count">' + userIds.length + '</span> recipients');
                    }
                });
            });

            // Initialize selected count
            updateSelectedCount();
        });

        // Change per page function
        function changePerPage(perPage) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', perPage);
            url.searchParams.delete('page'); // Reset to first page
            window.location.href = url.toString();
        }
    </script>
@stop
