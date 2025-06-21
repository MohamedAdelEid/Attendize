@extends('Shared.Layouts.Master')

@section('title')
    @parent
    User Types - {{ $event->title }}
@stop

@section('top_nav')
    @include('ManageEvent.Partials.TopNav')
@stop

@section('page_title')
    <i class="ico-users mr5"></i>
    User Types - {{ $event->title }}
@stop

@section('head')
    <style>
        .bulk-actions {
            display: none;
            margin-bottom: 10px;
            animation: fadeIn 0.3s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .checkbox-cell {
            width: 30px;
            text-align: center;
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
        .user-type-checkbox {
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
    </style>
@stop

@section('menu')
    @include('ManageEvent.Partials.Sidebar')
@stop

@section('page_header')
<div class="col-md-9">
    <div class="btn-toolbar" role="toolbar">
        <div class="btn-group btn-group-responsive">
            <button data-modal-id='CreateUserType'
                    data-href="{{ route('showCreateEventUserType', ['event_id' => $event->id]) }}"
                    class='loadModal btn btn-success' type="button">
                <i class="ico-plus2"></i> Create User Type
            </button>
        </div>
        <div class="btn-group btn-group-responsive">
            <a href="{{ route('showEventRegistration', ['event_id' => $event->id]) }}" class='btn btn-default' type="button">
                <i class="ico-arrow-left"></i> Back to Registrations
            </a>
        </div>
    </div>
</div>
<div class="col-md-3">
    {!! Form::open(['url' => route('showEventUserTypes', ['event_id' => $event->id, 'sort_by' => $sort_by]), 'method' => 'get']) !!}
    <div class="input-group">
        <input name='q' value="{{ $q }}" placeholder="Search user types..." type="text" class="form-control">
        <span class="input-group-btn">
            <button class="btn btn-default" type="submit"><i class="ico-search"></i></button>
        </span>
        {!! Form::hidden('sort_by', $sort_by) !!}
    </div>
    {!! Form::close() !!}
</div>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Bulk Actions Area -->
        <div class="bulk-actions panel panel-default">
            <div class="panel-body">
                <button id="bulkDeleteBtn" class="bulk-delete-btn" disabled>
                    <i class="ico-trash"></i> Delete Selected User Types
                </button>
                <span id="selectedCount" class="text-muted ml-2"></span>
            </div>
        </div>

        @if($userTypes->count())
            <div class="panel">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="checkbox-cell">
                                    <input type="checkbox" id="selectAll" class="select-all-checkbox">
                                </th>
                                <th>
                                    {!! Html::sortable_link('Name', $sort_by, 'name', $sort_order, ['q' => $q, 'page' => $userTypes->currentPage()]) !!}
                                </th>
                                <th>
                                    {!! Html::sortable_link('Created', $sort_by, 'created_at', $sort_order, ['q' => $q, 'page' => $userTypes->currentPage()]) !!}
                                </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($userTypes as $userType)
                            <tr class="user_type_{{ $userType->id }}">
                                <td class="checkbox-cell">
                                    <input type="checkbox" class="user-type-checkbox" data-id="{{ $userType->id }}" data-name="{{ $userType->name }}">
                                </td>
                                <td>{{ $userType->name }}</td>
                                <td>{{ $userType->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a data-modal-id="EditUserType"
                                       href="javascript:void(0);"
                                       data-href="{{ route('showEditEventUserType', ['event_id' => $event->id, 'user_type_id' => $userType->id]) }}"
                                       class="loadModal btn btn-xs btn-primary">
                                        <i class="ico-edit"></i> Edit
                                    </a>

                                    <a data-modal-id="DeleteUserType"
                                       href="javascript:void(0);"
                                       data-href="{{ route('showDeleteEventUserType', ['event_id' => $event->id, 'user_type_id' => $userType->id]) }}"
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
            @if(!empty($q))
                @include('Shared.Partials.NoSearchResults')
            @else
                <div class="not-found-container">
                    <i class="ico-users not-found-icon"></i>
                    <p class="not-found-message">No user types found.</p>
                </div>
            @endif
        @endif
    </div>
    <div class="col-md-12">
        {!! $userTypes->appends(['sort_by' => $sort_by, 'sort_order' => $sort_order, 'q' => $q])->render() !!}
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
                <p>Are you sure you want to delete the selected user types?</p>
                <p class="text-danger"><strong>This action cannot be undone.</strong></p>
                <div id="selectedUserTypesList" class="well" style="max-height: 200px; overflow-y: auto;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" id="confirmBulkDelete" class="btn btn-danger">Delete User Types</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('foot')
<script>
$(document).ready(function() {
    let selectedUserTypes = [];

    function updateBulkActionUI() {
        if (selectedUserTypes.length > 0) {
            $('.bulk-actions').show();
            $('#bulkDeleteBtn').prop('disabled', false);
            $('#selectedCount').text(selectedUserTypes.length + ' user types selected');
        } else {
            $('.bulk-actions').hide();
            $('#bulkDeleteBtn').prop('disabled', true);
            $('#selectedCount').text('');
        }
    }

    $(document).on('change', '.user-type-checkbox', function() {
        const userTypeId = $(this).data('id');
        const userTypeName = $(this).data('name');

        if ($(this).is(':checked')) {
            if (!selectedUserTypes.some(ut => ut.id === userTypeId)) {
                selectedUserTypes.push({
                    id: userTypeId,
                    name: userTypeName
                });
            }
        } else {
            selectedUserTypes = selectedUserTypes.filter(ut => ut.id !== userTypeId);
            $('#selectAll').prop('checked', false);
        }

        updateBulkActionUI();
    });

    $('#selectAll').change(function() {
        const isChecked = $(this).is(':checked');
        $('.user-type-checkbox').prop('checked', isChecked);

        selectedUserTypes = [];
        if (isChecked) {
            $('.user-type-checkbox').each(function() {
                selectedUserTypes.push({
                    id: $(this).data('id'),
                    name: $(this).data('name')
                });
            });
        }

        updateBulkActionUI();
    });

    $('#bulkDeleteBtn').click(function() {
        let listHtml = '<ul>';
        selectedUserTypes.forEach(ut => {
            listHtml += `<li>${ut.name}</li>`;
        });
        listHtml += '</ul>';

        $('#selectedUserTypesList').html(listHtml);
        $('#bulkDeleteModal').modal('show');
    });

    $('#confirmBulkDelete').click(function() {
        const userTypeIds = selectedUserTypes.map(ut => ut.id);

        $(this).html('<i class="fa fa-spinner fa-spin"></i> Deleting...');
        $(this).prop('disabled', true);

        $.ajax({
            url: "{{ route('postBulkDeleteUserTypes', ['event_id' => $event->id]) }}",
            type: 'POST',
            data: {
                user_type_ids: userTypeIds,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.status === 'success') {
                    userTypeIds.forEach(id => {
                        $('.user_type_' + id).fadeOut(function() {
                            $(this).remove();
                        });
                    });

                    selectedUserTypes = [];
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
                $('#confirmBulkDelete').html('Delete User Types');
                $('#confirmBulkDelete').prop('disabled', false);
            }
        });
    });

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
});
</script>
@stop
