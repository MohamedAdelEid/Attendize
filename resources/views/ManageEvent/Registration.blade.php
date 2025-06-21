@extends('Shared.Layouts.Master')

@section('title')
    @parent
    @lang('Registration.show_registrations')
@stop

@section('top_nav')
    @include('ManageEvent.Partials.TopNav')
@stop

@section('page_title')
    <i class="ico-file-text mr5"></i>
    Show Registrations
@stop

@section('head')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .bulk-actions {
            display: none;
            margin-bottom: 10px;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .checkbox-cell {
            width: 30px;
            text-align: center;
        }

        .select-all-container {
            margin-bottom: 10px;
        }

        .select-all-checkbox {
            margin-right: 5px;
        }

        .bulk-delete-btn {
            background-color: #d9534f;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 3px;
            transition: background-color 0.3s;
        }

        .bulk-delete-btn:hover {
            background-color: #c9302c;
        }

        .bulk-delete-btn i {
            margin-right: 5px;
        }

        .registration-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .not-found-container {
            text-align: center;
            padding: 50px 0;
        }

        .not-found-icon {
            font-size: 5em;
            color: #ccc;
            margin-bottom: 20px;
        }

        .not-found-message {
            font-size: 2em;
            margin-top: 10px;
            color: #777;
        }

        .filter-section {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .filter-section select {
            margin-right: 10px;
        }

        .registration-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-active {
            background-color: #28a745;
            color: white;
        }

        .status-inactive {
            background-color: #dc3545;
            color: white;
        }

        .approval-pending {
            background-color: #ffc107;
            color: black;
        }

        .approval-approved {
            background-color: #28a745;
            color: white;
        }

        .approval-rejected {
            background-color: #dc3545;
            color: white;
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #ff4136;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        .position-relative {
            position: relative;
        }

        .register-btn {
            margin-left: 10px;
            padding: 2px 8px;
            font-size: 12px;
        }

        .page-title-buttons {
            display: inline-block;
            margin-left: 20px;
        }

        .users-count-btn {
            position: relative;
            padding-right: 25px;
        }

        .notification-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #ff4136;
            border-radius: 50%;
            margin-left: 5px;
            animation: pulse 1.5s infinite;
        }

        .page-title-buttons {
            display: inline-block;
            margin-left: 20px;
        }

        .users-count-cell {
            position: relative;
        }

        .users-count-btn {
            position: relative;
            padding-right: 25px;
        }

        .users-count-cell {
            position: relative;
        }

        .users-count-btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            background-color: #2563eb;
            color: #fff;
            font-size: 14px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
            text-decoration: none;
        }

        .users-count-btn:hover {
            background-color: #1e40af;
        }

        .users-count-btn i {
            margin-right: 6px;
        }

        .badge-notification {
            position: absolute;
            top: -14px;
            right: -14px;
            background-color: #ef4444;
            color: #fff;
            border-radius: 9999px;
            padding: 2px 6px;
            font-size: 11px;
            font-weight: bold;
            min-width: 18px;
            text-align: center;
            box-shadow: 0 0 0 2px white;
        }

        @keyframes pulse {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(255, 65, 54, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 5px rgba(255, 65, 54, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(255, 65, 54, 0);
            }
        }

        /* User Types Button Styling */
        .user-types-btn {
            background-color: #17a2b8;
            color: white;
            border: 1px solid #17a2b8;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .user-types-btn:hover {
            background-color: #138496;
            border-color: #117a8b;
            color: white;
            text-decoration: none;
        }

        .user-types-btn i {
            margin-right: 6px;
        }
    </style>
@stop

@section('menu')
    @include('ManageEvent.Partials.Sidebar')
@stop

@section('page_header')
    <div class="col-md-9">
        <div class="btn-toolbar" role="toolbar">
            <div class="btn-group btn-group-responsive">
                <button class='loadModal btn btn-success' data-modal-id='CreateRegistration'
                    data-href="{{ route('showCreateEventRegistration', ['event_id' => $event->id]) }}" type="button">
                    <i class="ico-file-plus"></i> @lang('Registration.create_registration_form')
                </button>
            </div>
            <div class="btn-group btn-group-responsive">
                <a href="{{ route('showEventRegistrationCategories', ['event_id' => $event->id]) }}" class='btn btn-success'
                    type="button"><i class="ico-folder"></i> @lang('Registration.show_categories')
                </a>
            </div>
            <div class="btn-group btn-group-responsive">
                <a href="{{ route('showEventRegistrationConferences', ['event_id' => $event->id]) }}"
                    class='btn btn-success' type="button"><i class="ico-users"></i> @lang('Registration.show_conferences_register')
                </a>
            </div>
            <div class="btn-group btn-group-responsive">
                <a href="{{ route('showEventUserTypes', ['event_id' => $event->id]) }}"
                   class='btn btn-success' type="button">
                    <i class="ico-user"></i> User Types
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-3" style="display: flex; justify-content: end;">
        <span class="page-title-buttons">
            <a href="{{ route('showEventRegistrationUsers', ['event_id' => $event->id]) }}" class="btn btn-sm btn-success" style="position: relative">
                <i class="ico-users"></i> All Users
                @if($newRegistrationsCount > 0)
                    <span class="badge-notification">{{ $newRegistrationsCount }}</span>
                @endif
            </a>
        </span>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Filters -->
            <div class="row">
                <div class="col-md-9">
                    <div class="filter-section">
                        <form id="filter-form" class="form-inline">
                            <select name="status" class="form-control" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>

                    <select name="approval_status" class="form-control" onchange="this.form.submit()">
                        <option value="">All Approval Status</option>
                        <option value="automatic" {{ request('approval_status') == 'automatic' ? 'selected' : '' }}>
                            Automatic
                        </option>
                        <option value="manual" {{ request('approval_status') == 'manual' ? 'selected' : '' }}>Manual
                        </option>
                    </select>
                        </form>
                    </div>
                </div>

                <div class="col-md-3">
                    {!! Form::open([
                        'url' => route('showEventRegistration', ['event_id' => $event->id]),
                        'method' => 'get',
                        'id' => 'search-form',
                    ]) !!}
                    <div class="input-group">
                        <input name='q' value="{{ $q ?? '' }}" placeholder="@lang('Registration.search_registrations')" type="text"
                            class="form-control">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit"><i class="ico-search"></i></button>
                        </span>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>

            <!-- Bulk Actions Area -->
            <div class="bulk-actions panel panel-default">
                <div class="panel-body">
                    <button id="bulkDeleteBtn" class="bulk-delete-btn" disabled>
                        <i class="ico-trash"></i> Delete Selected Registrations
                    </button>
                    <span id="selectedCount" class="text-muted ml-2"></span>
                </div>
            </div>

            @if ($registration->count())
                <div class="panel">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="checkbox-cell">
                                        <input type="checkbox" id="selectAll" class="select-all-checkbox">
                                    </th>
                                    <th>
                                        {!! Html::sortable_link('Image & Name', $sort_by, 'name', $sort_order, ['q' => $q]) !!}
                                    </th>
                                    <th>Category</th>
                                    <th>
                                        {!! Html::sortable_link('Max Participants', $sort_by, 'max_participants', $sort_order, ['q' => $q]) !!}
                                    </th>
                                    <th>
                                        {!! Html::sortable_link('Status', $sort_by, 'status', $sort_order, ['q' => $q]) !!}
                                    </th>
                                    <th>
                                        {!! Html::sortable_link('Start Date', $sort_by, 'start_date', $sort_order, ['q' => $q]) !!}
                                    </th>
                                    <th>
                                        {!! Html::sortable_link('End Date', $sort_by, 'end_date', $sort_order, ['q' => $q]) !!}
                                    </th>
                                    <th>
                                        {!! Html::sortable_link('Approval Status', $sort_by, 'approval_status', $sort_order, ['q' => $q]) !!}
                                    </th>
                                    <th>Registrations</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($registration as $reg)
                                    <tr
                                        class="registration_{{ $reg->id }} {{ $reg->status == 'inactive' ? 'danger' : '' }}">
                                        <td class="checkbox-cell">
                                            <input type="checkbox" class="registration-checkbox"
                                                data-id="{{ $reg->id }}" data-name="{{ $reg->name }}">
                                        </td>
                                        <td>
                                            @if ($reg->image)
                                                <img src="{{ asset('storage/' . $reg->image) }}" alt="{{ $reg->name }}"
                                                    class="registration-image">
                                            @endif
                                            <span class="ml-2">{{ $reg->name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge">{{ $reg->category->name }}</span>
                                        </td>
                                        <td>
                                            {{{$reg->max_participants ?? 'N/A'}}}
                                        </td>
                                        <td>
                                            <span
                                                class="status-badge {{ $reg->status == 'active' ? 'status-active' : 'status-inactive' }}">
                                                {{ ucfirst($reg->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $reg->getFormattedDate('start_date') }}</td>
                                        <td>{{ $reg->getFormattedDate('end_date') }}</td>
                                        <td>
                                            <span class="status-badge approval-{{ $reg->approval_status }}">
                                                {{ ucfirst($reg->approval_status) }}
                                            </span>
                                        </td>
                                        <td class="users-count-cell">
                                            <a href="{{ route('showRegistrationUsers', ['event_id' => $event->id, 'registration_id' => $reg->id]) }}" class="btn btn-xs btn-info users-count-btn">
                                                <i class="ico-users"></i> {{ $reg->registrationUsers()->count() }}
                                                @if($reg->new_users_count > 0)
                                                    <span class="badge-notification">{{ $reg->new_users_count }}</span>
                                                @endif
                                            </a>
                                        </td>
                                        <td>
                                            <!-- View button -->
                                            <a data-modal-id="ViewRegistration"
                                                href="{{ route('showEventRegistrationDetails', ['event_id' => $event->id, 'registration_id' => $reg->id]) }}"
                                                class="btn btn-xs btn-success">
                                            <i class="ico-eye"></i> View
                                            </a>

                                            <!-- Edit button -->
                                            <a href="javascript:void(0);" data-modal-id="EditRegistration"
                                                data-href="{{ route('showEditEventRegistration', ['event_id' => $event->id, 'registration_id' => $reg->id]) }}"
                                                class="loadModal btn btn-xs btn-primary">
                                                <i class="ico-edit"></i> Edit
                                            </a>

                                            <!-- Delete button -->
                                            <a href="javascript:void(0);" data-modal-id="DeleteRegistration"
                                                data-href="{{ route('showDeleteEventRegistration', ['event_id' => $event->id, 'registration_id' => $reg->id]) }}"
                                                class="loadModal btn btn-xs btn-danger">
                                                <i class="ico-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                @if (!empty($q))
                    @include('Shared.Partials.NoSearchResults')
                @else
                    <div class="not-found-container">
                        <i class="ico-file-text not-found-icon"></i>
                        <p class="not-found-message">No registrations found.</p>
                    </div>
                @endif
            @endif
        </div>
        <div class="col-md-12">
            {!! $registration->appends(['sort_by' => $sort_by, 'sort_order' => $sort_order, 'q' => $q])->render() !!}
        </div>
    </div>

    <!-- Bulk Delete Confirmation Modal -->
    <div class="modal fade" id="bulkDeleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Confirm Bulk Delete</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the selected registrations?</p>
                    <p class="text-danger"><strong>This action cannot be undone.</strong></p>
                    <div id="selectedRegistrationsList" class="well" style="max-height: 200px; overflow-y: auto;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmBulkDelete" class="btn btn-danger">Delete Registrations</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('foot')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize select2
            $('.select2').select2();

            // Bulk delete functionality
            let selectedRegistrations = [];

            // Update UI based on selections
            function updateBulkActionUI() {
                if (selectedRegistrations.length > 0) {
                    $('.bulk-actions').show();
                    $('#bulkDeleteBtn').prop('disabled', false);
                    $('#selectedCount').text(selectedRegistrations.length + ' registrations selected');
                } else {
                    $('.bulk-actions').hide();
                    $('#bulkDeleteBtn').prop('disabled', true);
                    $('#selectedCount').text('');
                }
            }

            // Handle checkbox clicks
            $(document).on('change', '.registration-checkbox', function() {
                const registrationId = $(this).data('id');
                const registrationName = $(this).data('name');

                if ($(this).is(':checked')) {
                    if (!selectedRegistrations.some(reg => reg.id === registrationId)) {
                        selectedRegistrations.push({
                            id: registrationId,
                            name: registrationName
                        });
                    }
                } else {
                    selectedRegistrations = selectedRegistrations.filter(reg => reg.id !== registrationId);
                    $('#selectAll').prop('checked', false);
                }

                updateBulkActionUI();
            });

            // Select All checkbox
            $('#selectAll').change(function() {
                const isChecked = $(this).is(':checked');
                $('.registration-checkbox').prop('checked', isChecked);

                selectedRegistrations = [];
                if (isChecked) {
                    $('.registration-checkbox').each(function() {
                        selectedRegistrations.push({
                            id: $(this).data('id'),
                            name: $(this).data('name')
                        });
                    });
                }

                updateBulkActionUI();
            });

            // Show confirmation modal
            $('#bulkDeleteBtn').click(function() {
                let listHtml = '<ul>';
                selectedRegistrations.forEach(reg => {
                    listHtml += `<li>${reg.name}</li>`;
                });
                listHtml += '</ul>';

                $('#selectedRegistrationsList').html(listHtml);
                $('#bulkDeleteModal').modal('show');
            });

            // Handle bulk delete
            $('#confirmBulkDelete').click(function() {
                const registrationIds = selectedRegistrations.map(reg => reg.id);

                $(this).html('<i class="fa fa-spinner fa-spin"></i> Deleting...');
                $(this).prop('disabled', true);

                $.ajax({
                    url: "{{ route('postBulkDeleteRegistrations', ['event_id' => $event->id]) }}",
                    type: 'POST',
                    data: {
                        registration_ids: registrationIds,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            registrationIds.forEach(id => {
                                $('.registration_' + id).fadeOut(function() {
                                    $(this).remove();
                                });
                            });

                            selectedRegistrations = [];
                            updateBulkActionUI();
                            $('#selectAll').prop('checked', false);
                            showMessage('success', response.message);
                            $('#bulkDeleteModal').modal('hide');
                        } else {
                            showMessage('error', response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        showMessage('error', 'An error occurred while processing your request');
                    },
                    complete: function() {
                        $('#confirmBulkDelete').html('Delete Registrations');
                        $('#confirmBulkDelete').prop('disabled', false);
                    }
                });
            });

            // Helper function to show messages
            function showMessage(type, message) {
                let alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                let alertHtml = `
                    <div class="alert ${alertClass} alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        ${message}
                    </div>
                `;

                $('.bulk-actions').after(alertHtml);

                setTimeout(function() {
                    $('.alert').alert('close');
                }, 5000);
            }

            // Handle filter form submission
            $('#filter-form select').change(function() {
                $('#filter-form').submit();
            });
        });
    </script>
@stop
