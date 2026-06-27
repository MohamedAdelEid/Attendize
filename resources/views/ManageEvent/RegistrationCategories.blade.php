@extends('Shared.Layouts.Master')

@section('title')
    @parent
    @lang("Registration.show_categories")
@stop

@section('top_nav')
    @include('ManageEvent.Partials.TopNav')
@stop

@section('page_title')
    <i class="ico-folder mr5"></i>
    @lang("Registration.show_categories")
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
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
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
        .category-checkbox {
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
    <!-- Toolbar -->
    <div class="btn-toolbar" role="toolbar">
        <div class="btn-group btn-group-responsive">
            <button data-modal-id='CreateCategory'
                    data-href="{{route('showCreateEventRegistrationCategory' , array('event_id'=>$event->id))}}"
                    class='loadModal btn btn-success' type="button"><i class="ico-plus2"></i> @lang("Registration.create_category")
            </button>
        </div>
    </div>
    <!--/ Toolbar -->
</div>
<div class="col-md-3">
    {!! Form::open(array('url' => route('showEventRegistrationCategories', ['event_id'=>$event->id,'sort_by'=>$sort_by]), 'method' => 'get')) !!}
    <div class="input-group">
        <input name='q' value="{{$q or ''}}" placeholder="@lang("Registration.search_tickets")" type="text" class="form-control">
    <span class="input-group-btn">
        <button class="btn btn-default" type="submit"><i class="ico-search"></i></button>
    </span>
        {!!Form::hidden('sort_by', $sort_by)!!}
    </div>
    {!! Form::close() !!}
</div>
@stop

@section('content')
<!--Start Attendees table-->
<div class="row">
    <div class="col-md-12">
        <!-- Bulk Actions Area -->
        <div class="bulk-actions panel panel-default">
            <div class="panel-body">
                <button id="bulkDeleteBtn" class="bulk-delete-btn" disabled>
                    <i class="ico-trash"></i> Delete Selected Categories
                </button>
                <span id="selectedCount" class="text-muted ml-2"></span>
            </div>
        </div>

        @if($categories->count())
            <div class="panel">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="checkbox-cell">
                                    <label>
                                        <input type="checkbox" id="selectAll" class="select-all-checkbox">
                                    </label>
                                </th>
                                <th>
                                {!!Html::sortable_link(trans("Registration.name"), $sort_by, 'name', $sort_order, ['q' => $q , 'page' => $categories->currentPage()])!!}
                                </th>
                                <th>
                                {!!Html::sortable_link(trans("Registration.status"), $sort_by, 'status', $sort_order, ['q' => $q , 'page' => $categories->currentPage()])!!}
                                </th>
                                <th>
                                {!!Html::sortable_link(trans("Registration.start_date"), $sort_by, 'start_date', $sort_order, ['q' => $q , 'page' => $categories->currentPage()])!!}
                                </th>
                                <th>
                                    {!!Html::sortable_link(trans("Registration.end_date"), $sort_by, 'end_date', $sort_order, ['q' => $q , 'page' => $categories->currentPage()])!!}
                                </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr class="category_{{$category->id}} {{$category->status == "inactive" ? 'danger' : ''}}">
                                <td class="checkbox-cell">
                                    <input type="checkbox" class="category-checkbox" data-id="{{$category->id}}" data-name="{{$category->name}}">
                                </td>
                                <td>{{{$category->name}}}</td>
                                <td>
                                    {{{$category->status}}}
                                </td>
                                <td>
                                    {{{$category->start_date ?? 'N/A'}}}
                                </td><td>
                                    {{{$category->end_date ?? 'N/A'}}}
                                </td>
                                <td>
                                    <a
                                        data-modal-id="EditCategory"
                                        href="javascript:void(0);"
                                        data-href="{{route('showEditEventRegistrationCategory', ['event_id'=>$event->id, 'category_id'=>$category->id])}}"
                                        class="loadModal btn btn-xs btn-primary"
                                        > @lang("basic.edit")</a>

                                    <a
                                        data-modal-id="CancelAttendee"
                                        href="javascript:void(0);"
                                        data-href="{{route('showDeleteEventRegistrationCategory', ['event_id'=>$event->id, 'category_id'=>$category->id])}}"
                                        class="loadModal btn btn-xs btn-danger"
                                        > Delete
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
                <i class="ico-folder-open not-found-icon"></i>
                <p class="not-found-message">No categories found.</p>
            </div>
        @endif

        @endif
    </div>
    <div class="col-md-12">
        {!! $categories->appends(['sort_by' => $sort_by, 'sort_order' => $sort_order, 'q' => $q])->render() !!}
    </div>
</div>    <!--/End attendees table-->

<!-- Bulk Delete Confirmation Modal -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Confirm Bulk Delete</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the selected categories?</p>
                <p class="text-danger"><strong>This action cannot be undone.</strong></p>
                <div id="selectedCategoriesList" class="well" style="max-height: 200px; overflow-y: auto;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" id="confirmBulkDelete" class="btn btn-danger">Delete Categories</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('foot')
    @include('ManageEvent.Partials.DatetimeFlatpickr')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#conferences').select2({
                placeholder: "Select options",
                allowClear: true
            });

            // Bulk delete functionality
            let selectedCategories = [];

            // Update UI based on selections
            function updateBulkActionUI() {
                if (selectedCategories.length > 0) {
                    $('.bulk-actions').show();
                    $('#bulkDeleteBtn').prop('disabled', false);
                    $('#selectedCount').text(selectedCategories.length + ' categories selected');
                } else {
                    $('.bulk-actions').hide();
                    $('#bulkDeleteBtn').prop('disabled', true);
                    $('#selectedCount').text('');
                }
            }

            // Handle checkbox clicks
            $(document).on('change', '.category-checkbox', function() {
                const categoryId = $(this).data('id');
                const categoryName = $(this).data('name');

                if ($(this).is(':checked')) {
                    // Add to selected array if not already there
                    if (!selectedCategories.some(cat => cat.id === categoryId)) {
                        selectedCategories.push({
                            id: categoryId,
                            name: categoryName
                        });
                    }
                } else {
                    // Remove from selected array
                    selectedCategories = selectedCategories.filter(cat => cat.id !== categoryId);

                    // Uncheck "select all" if any category is unchecked
                    $('#selectAll').prop('checked', false);
                }

                updateBulkActionUI();
            });

            // Select All checkbox
            $('#selectAll').change(function() {
                const isChecked = $(this).is(':checked');

                // Check/uncheck all category checkboxes
                $('.category-checkbox').prop('checked', isChecked);

                // Update selected categories array
                selectedCategories = [];
                if (isChecked) {
                    $('.category-checkbox').each(function() {
                        selectedCategories.push({
                            id: $(this).data('id'),
                            name: $(this).data('name')
                        });
                    });
                }

                updateBulkActionUI();
            });

            // Show confirmation modal when bulk delete button is clicked
            $('#bulkDeleteBtn').click(function() {
                // Populate selected categories list
                let listHtml = '<ul>';
                selectedCategories.forEach(cat => {
                    listHtml += `<li>${cat.name}</li>`;
                });
                listHtml += '</ul>';

                $('#selectedCategoriesList').html(listHtml);
                $('#bulkDeleteModal').modal('show');
            });

            // Handle confirm bulk delete
            $('#confirmBulkDelete').click(function() {
                const categoryIds = selectedCategories.map(cat => cat.id);

                // Show loading state
                $(this).html('<i class="fa fa-spinner fa-spin"></i> Deleting...');
                $(this).prop('disabled', true);

                // Send AJAX request
                $.ajax({
                    url: "{{ route('postBulkDeleteCategories', ['event_id' => $event->id]) }}",
                    type: 'POST',
                    data: {
                        category_ids: categoryIds,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            // Remove deleted rows from the table
                            categoryIds.forEach(id => {
                                $('.category_' + id).fadeOut(function() {
                                    $(this).remove();
                                });
                            });

                            // Reset selection
                            selectedCategories = [];
                            updateBulkActionUI();
                            $('#selectAll').prop('checked', false);

                            // Show success message
                            showMessage('success', response.message);

                            // Close modal
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
                        // Reset button state
                        $('#confirmBulkDelete').html('Delete Categories');
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

                // Insert alert at the top of the content area
                $('.bulk-actions').after(alertHtml);

                // Auto-dismiss after 5 seconds
                setTimeout(function() {
                    $('.alert').alert('close');
                }, 5000);
            }
        });
    </script>
@stop
