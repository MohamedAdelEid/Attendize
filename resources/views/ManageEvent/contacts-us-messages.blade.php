@extends('Shared.Layouts.Master')

@section('title')
@parent

@lang("Event.event_orders")
@stop

@section('top_nav')
@include('ManageEvent.Partials.TopNav')
@stop

@section('menu')
@include('ManageEvent.Partials.Sidebar')
@stop

@section('page_title')
<i class='ico-cart mr5'></i>
@lang("Event.contacts_us_messages")
@stop

@section('head')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<style>
    .message-preview {
        max-width: 300px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .status-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: bold;
    }
    
    .status-new {
        background-color: #e3f2fd;
        color: #1976d2;
    }
    
    .status-read {
        background-color: #e8f5e9;
        color: #388e3c;
    }
    
    .message-modal .modal-body {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .message-info {
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .message-content {
        white-space: pre-wrap;
    }
    
    .action-buttons .btn {
        margin-right: 5px;
    }
</style>
@stop

@section('page_header')
<div class="col-md-9">
    <div class="btn-toolbar">
        <div class="btn-group">
            <button class="btn btn-success" id="markAllRead">
                <i class="ico-checkmark"></i> Mark All as Read
            </button>
        </div>
        <div class="btn-group">
            <button class="btn btn-danger" id="deleteSelected" disabled>
                <i class="ico-trash"></i> Delete Selected
            </button>
        </div>
    </div>
</div>
<div class="col-md-3">
    <div class="input-group">
        <input type="text" class="form-control" placeholder="Search Messages" id="search-messages">
        <span class="input-group-btn">
            <button class="btn btn-default" type="button"><i class="ico-search"></i></button>
        </span>
    </div>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Contact Messages
                    @if(isset($contactMessages) && $contactMessages->count() > 0)
                        <span class="label label-info">{{ $contactMessages->count() }}</span>
                    @endif
                </h3>
            </div>
            <div class="panel-body">
                @if(isset($contactMessages) && $contactMessages->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="contact-messages-table">
                            <thead>
                                <tr>
                                    <th width="20">
                                        <input type="checkbox" id="select-all-messages">
                                    </th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contactMessages as $message)
                                    <tr class="{{ isset($message->read_at) ? '' : 'info' }}" data-message-id="{{ $message->id }}">
                                        <td>
                                            <input type="checkbox" class="message-checkbox" value="{{ $message->id }}">
                                        </td>
                                        <td>{{ $message->name }}</td>
                                        <td>
                                            <a href="mailto:{{ $message->email }}">{{ $message->email }}</a>
                                        </td>
                                        <td>{{ $message->subject ?? 'No Subject' }}</td>
                                        <td>
                                            <div class="message-preview">{{ $message->message }}</div>
                                        </td>
                                        <td>{{ $message->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <span class="status-badge {{ isset($message->read_at) ? 'status-read' : 'status-new' }}">
                                                {{ isset($message->read_at) ? 'Read' : 'New' }}
                                            </span>
                                        </td>
                                        <td class="action-buttons">
                                            <button class="btn btn-xs btn-primary view-message" 
                                                    data-message-id="{{ $message->id }}"
                                                    data-name="{{ $message->name }}"
                                                    data-email="{{ $message->email }}"
                                                    data-subject="{{ $message->subject ?? 'No Subject' }}"
                                                    data-message="{{ $message->message }}"
                                                    data-date="{{ $message->created_at->format('M d, Y H:i') }}">
                                                <i class="ico-eye"></i>
                                            </button>
                                            <button class="btn btn-xs btn-danger delete-message" data-message-id="{{ $message->id }}">
                                                <i class="ico-trash"></i>
                                            </button>
                                            @if(!isset($message->read_at))
                                                <button class="btn btn-xs btn-success mark-read" data-message-id="{{ $message->id }}">
                                                    <i class="ico-checkmark"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if(isset($contactMessages) && method_exists($contactMessages, 'links') && $contactMessages->lastPage() > 1)
                        <div class="text-center">
                            {{ $contactMessages->links() }}
                        </div>
                    @endif
                @else
                    <div class="alert alert-info">
                        <h4><i class="ico-info"></i> No Messages</h4>
                        There are no contact messages for this event yet.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- View Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content message-modal">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="messageModalTitle">Message Details</h4>
            </div>
            <div class="modal-body">
                <div class="message-info">
                    <p><strong>From:</strong> <span id="modal-name"></span> (<span id="modal-email"></span>)</p>
                    <p><strong>Subject:</strong> <span id="modal-subject"></span></p>
                    <p><strong>Date:</strong> <span id="modal-date"></span></p>
                </div>
                <div class="message-content" id="modal-message"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <a href="#" id="modal-reply" class="btn btn-primary">Reply by Email</a>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Confirm Delete</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this message? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Multiple Confirmation Modal -->
<div class="modal fade" id="deleteMultipleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Confirm Delete Multiple</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the selected messages? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteMultiple">Delete</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('footer')
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#contact-messages-table').DataTable({
            "order": [[ 5, "desc" ]],  // Sort by date column by default
            "pageLength": 25,
            "language": {
                "search": "",
                "searchPlaceholder": "Search messages..."
            }
        });
        
        // Custom search box
        $('#search-messages').on('keyup', function() {
            table.search($(this).val()).draw();
        });
        
        // View message details
        $('.view-message').on('click', function() {
            var messageId = $(this).data('message-id');
            var name = $(this).data('name');
            var email = $(this).data('email');
            var subject = $(this).data('subject');
            var message = $(this).data('message');
            var date = $(this).data('date');
            
            $('#modal-name').text(name);
            $('#modal-email').text(email);
            $('#modal-subject').text(subject);
            $('#modal-message').text(message);
            $('#modal-date').text(date);
            $('#modal-reply').attr('href', 'mailto:' + email + '?subject=Re: ' + subject);
            
            $('#messageModal').modal('show');
            
            // Mark as read when viewed
            if (!$(this).closest('tr').hasClass('read')) {
                markAsRead(messageId);
            }
        });
        
        // Delete message
        var deleteMessageId;
        $('.delete-message').on('click', function() {
            deleteMessageId = $(this).data('message-id');
            $('#deleteModal').modal('show');
        });
        
        $('#confirmDelete').on('click', function() {
            deleteMessage(deleteMessageId);
            $('#deleteModal').modal('hide');
        });
        
        // Mark as read
        $('.mark-read').on('click', function() {
            var messageId = $(this).data('message-id');
            markAsRead(messageId);
        });
        
        // Select all messages
        $('#select-all-messages').on('change', function() {
            $('.message-checkbox').prop('checked', $(this).prop('checked'));
            updateDeleteSelectedButton();
        });
        
        // Update delete selected button state
        $('.message-checkbox').on('change', function() {
            updateDeleteSelectedButton();
        });
        
        // Delete selected messages
        $('#deleteSelected').on('click', function() {
            if (getSelectedMessageIds().length > 0) {
                $('#deleteMultipleModal').modal('show');
            }
        });
        
        $('#confirmDeleteMultiple').on('click', function() {
            deleteSelectedMessages();
            $('#deleteMultipleModal').modal('hide');
        });
        
        // Mark all as read
        $('#markAllRead').on('click', function() {
            markAllAsRead();
        });
        
        // Helper functions
        function updateDeleteSelectedButton() {
            var selectedCount = $('.message-checkbox:checked').length;
            $('#deleteSelected').prop('disabled', selectedCount === 0);
            $('#deleteSelected').text(selectedCount > 0 ? 'Delete Selected (' + selectedCount + ')' : 'Delete Selected');
        }
        
        function getSelectedMessageIds() {
            var ids = [];
            $('.message-checkbox:checked').each(function() {
                ids.push($(this).val());
            });
            return ids;
        }
        
      
    });
</script>
@stop