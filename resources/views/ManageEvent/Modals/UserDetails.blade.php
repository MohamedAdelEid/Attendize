<style>
    /* General Reset and Base Styles */
    .user-details-modal * {
        box-sizing: border-box;
    }

    /* Modal Styles */
    .modal-lg {
        max-width: 900px;
        margin: 30px auto;
    }

    /* Header Styles */
    .modal-header-custom {
        background: linear-gradient(135deg, #6e45e2 0%, #6c73af 100%);
        color: white;
        padding: 15px 20px;
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title-custom {
        font-size: 20px;
        font-weight: 600;
        margin: 0;
    }

    .close-custom {
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        opacity: 0.8;
        transition: opacity 0.2s;
    }

    .close-custom:hover {
        opacity: 1;
    }

    /* User Profile Header */
    .user-header {
        background-color: #f9fafb;
        padding: 24px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
    }

    @media (min-width: 768px) {
        .user-header {
            flex-direction: row;
        }
    }

    .user-avatar {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6c73af 0%, #6c73af 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 16px;
    }

    @media (min-width: 768px) {
        .user-avatar {
            margin-right: 24px;
            margin-bottom: 0;
        }
    }

    .user-info {
        text-align: center;
        flex: 1;
    }

    @media (min-width: 768px) {
        .user-info {
            text-align: left;
        }
    }

    .user-name {
        font-size: 24px;
        font-weight: bold;
        color: #1f2937;
        margin: 0 0 8px 0;
    }

    .user-registered {
        color: #6b7280;
        margin: 0 0 8px 0;
        font-size: 14px;
    }

    /* QR Code Section */
    .qr-section {
        position: absolute;
        top: 20px;
        right: 20px;
        text-align: center;
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .qr-code {
        width: 120px;
        height: 120px;
        border: 2px solid #e5e7eb;
        border-radius: 4px;
        margin-bottom: 8px;
    }

    .registration-code {
        font-family: monospace;
        font-weight: bold;
        color: #007bff;
        font-size: 14px;
        background: #f8f9fa;
        padding: 4px 8px;
        border-radius: 4px;
        border: 1px solid #dee2e6;
    }

    /* Status Badge Styles */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 14px;
        font-weight: 500;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 6px;
    }

    .status-pending {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status-pending .status-dot {
        background-color: #f59e0b;
    }

    .status-approved {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-approved .status-dot {
        background-color: #10b981;
    }

    .status-rejected {
        background-color: #fee2e2;
        color: #b91c1c;
    }

    .status-rejected .status-dot {
        background-color: #ef4444;
    }

    /* Section Styles */
    .section {
        padding: 24px;
        border-bottom: 1px solid #e5e7eb;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
        margin: 0 0 16px 0;
        display: flex;
        align-items: center;
    }

    .section-title i {
        color: #5829bc;
        margin-right: 8px;
    }

    /* Grid Layout */
    .grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
    }

    @media (min-width: 768px) {
        .grid-2-cols {
            grid-template-columns: 1fr 1fr;
        }
    }

    .grid-span-2 {
        grid-column: span 1;
    }

    @media (min-width: 768px) {
        .grid-span-2 {
            grid-column: span 2;
        }
    }

    /* Card Styles */
    .info-card {
        background-color: white;
        padding: 16px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .card-label {
        font-size: 14px;
        color: #6b7280;
        margin: 0 0 4px 0;
    }

    .card-value {
        font-weight: 500;
        margin: 0;
    }

    /* Table Styles */
    .table-container {
        overflow-x: auto;
        background-color: white;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
    }

    .custom-table th {
        background-color: #f9fafb;
        padding: 12px 24px;
        text-align: left;
        font-size: 12px;
        font-weight: 500;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e5e7eb;
    }

    .custom-table td {
        padding: 16px 24px;
        font-size: 14px;
        border-bottom: 1px solid #e5e7eb;
    }

    .custom-table tr:last-child td {
        border-bottom: none;
    }

    /* Alert Styles */
    .alert {
        padding: 16px;
        border-radius: 8px;
    }

    .alert-info {
        background-color: #eff6ff;
        border-left: 4px solid #3b82f6;
        color: #1e40af;
    }

    .alert-flex {
        display: flex;
    }

    .alert-icon {
        flex-shrink: 0;
        color: #3b82f6;
    }

    .alert-content {
        margin-left: 12px;
    }

    /* Email Management Section */
    .email-management {
        background-color: #f8f9fa;
        padding: 24px;
        border-top: 1px solid #e5e7eb;
    }

    .email-buttons {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    /* Button Container */
    .actions-container {
        display: flex;
        justify-content: space-between;
        padding: 24px;
        background-color: #f9fafb;
        flex-wrap: wrap;
        gap: 8px;
    }

    /* Button Styles */
    .btn-custom {
        display: inline-flex;
        align-items: center;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-custom i {
        margin-right: 8px;
    }

    .btn-green {
        background-color: #10b981;
        color: white;
    }

    .btn-green:hover {
        background-color: #059669;
        color: white;
    }

    .btn-yellow {
        background-color: #f59e0b;
        color: white;
    }

    .btn-yellow:hover {
        background-color: #d97706;
        color: white;
    }

    .btn-red {
        background-color: #ef4444;
        color: white;
    }

    .btn-red:hover {
        background-color: #dc2626;
        color: white;
    }

    .btn-blue {
        background-color: #3b82f6;
        color: white;
    }

    .btn-blue:hover {
        background-color: #2563eb;
        color: white;
    }

    .btn-gray {
        background-color: white;
        color: #4b5563;
        border-color: #d1d5db;
    }

    .btn-gray:hover {
        background-color: #f9fafb;
        color: #4b5563;
    }

    /* Link Styles */
    .link {
        color: #5829bc;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .link:hover {
        text-decoration: underline;
    }

    .link i {
        margin-right: 4px;
    }

    /* Text Colors */
    .text-muted {
        color: #9ca3af;
    }

    /* Responsive adjustments */
    @media (max-width: 767px) {
        .qr-section {
            position: static;
            margin-top: 20px;
            width: 100%;
        }

        .user-header {
            padding-right: 24px;
        }
    }
</style>

<div role="dialog" class="modal fade user-details-modal" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header-custom">
                <h3 class="modal-title-custom">
                    <i class="fa fa-user-circle"></i> User Details
                </h3>
                <button type="button" class="close-custom" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body" style="padding: 0;">
                <div class="user-profile">
                    <!-- User Header Section -->
                    <div class="user-header">
                        <div class="user-avatar">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                            @else
                                {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                            @endif
                        </div>
                        <div class="user-info">
                            <h2 class="user-name">{{ $user->first_name }} {{ $user->last_name }}</h2>
                            <p class="user-registered">
                                <i class="fa fa-clock-o"></i> Registered on
                                {{ $user->created_at->format('M d, Y H:i') }}
                            </p>

                            @php
                                $statusClasses = [
                                    'pending' => 'status-pending',
                                    'approved' => 'status-approved',
                                    'rejected' => 'status-rejected',
                                ];
                                $statusClass = $statusClasses[$user->status] ?? '';
                            @endphp

                            <span class="status-badge {{ $statusClass }}">
                                <span class="status-dot"></span>
                                {{ ucfirst($user->status) }}
                            </span>
                        </div>

                        <!-- QR Code Section (only for approved users) -->
                        @if($user->status === 'approved' && $user->unique_code)
                            <div class="qr-section">
                                <div class="qr-code">
                                    {!! QrCode::size(116)->generate($user->unique_code) !!}
                                </div>
                                <div class="registration-code">
                                    {{ $user->unique_code }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Basic Information Section -->
                    <div class="section">
                        <h4 class="section-title">
                            <i class="fa fa-info-circle"></i> Basic Information
                        </h4>

                        <div class="grid grid-2-cols">
                            <div class="info-card">
                                <p class="card-label">Email Address</p>
                                <p class="card-value">{{ $user->email }}</p>
                            </div>

                            <div class="info-card">
                                <p class="card-label">Phone Number</p>
                                <p class="card-value">{{ $user->phone ?? 'Not provided' }}</p>
                            </div>

                            @php $userTypeOptionNames = $userTypeOptionNames ?? []; @endphp
                            @if($user->userTypes && $user->userTypes->count() > 0)
                                <div class="info-card">
                                    <p class="card-label">User Types</p>
                                    <p class="card-value">
                                        @foreach($user->userTypes as $ut)
                                            <span class="badge badge-info">{{ $ut->name }}@if(!empty($ut->pivot->user_type_option_id) && isset($userTypeOptionNames[$ut->pivot->user_type_option_id])) – {{ $userTypeOptionNames[$ut->pivot->user_type_option_id] }}@endif</span>
                                        @endforeach
                                    </p>
                                </div>
                            @endif

                            @if($user->status === 'approved' && $user->unique_code)
                                <div class="info-card">
                                    <p class="card-label">Registration Code</p>
                                    <p class="card-value">
                                        <span class="registration-code">{{ $user->unique_code }}</span>
                                    </p>
                                </div>
                            @endif

                            <div class="info-card grid-span-2">
                                <p class="card-label">Registration Form</p>
                                <p class="card-value">{{ $user->registration->name }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Responses Section -->
                    <div class="section">
                        <h4 class="section-title">
                            <i class="fa fa-list-alt"></i> Form Responses
                        </h4>

                        @if ($user->formFieldValues->count() > 0)
                            <div class="table-container">
                                <table class="custom-table">
                                    <thead>
                                        <tr>
                                            <th>Field</th>
                                            <th>Response</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($user->formFieldValues as $response)
                                            @php
                                                $field = $response->field;
                                                $label = $field ? $field->label : ('Field #' . $response->dynamic_form_field_id);
                                                $type = $field ? $field->type : '';
                                                $value = $response->value;
                                                $isFile = $type === 'file' || $type === 'attachment' || (is_string($value) && (strpos($value, 'form-uploads') !== false || strpos($value, 'storage/') !== false || strpos($value, 'storage\\') !== false));
                                            @endphp
                                            <tr>
                                                <td>{{ $label }}</td>
                                                <td>
                                                    @if ($isFile)
                                                        @if ($value)
                                                            @php
                                                                $fileUrl = (strpos($value, 'http') === 0 || strpos($value, '//') === 0) ? $value : asset('storage/' . ltrim(str_replace('\\', '/', $value), '/'));
                                                            @endphp
                                                            <a href="{{ $fileUrl }}" class="link" target="_blank" rel="noopener">
                                                                <i class="fa fa-file-o"></i> View File
                                                            </a>
                                                        @else
                                                            <span class="text-muted">No file uploaded</span>
                                                        @endif
                                                    @elseif($type === 'checkbox' || $type === 'radio')
                                                        {{ $value ?? 'No selection' }}
                                                    @else
                                                        {{ is_array($value) ? implode(', ', $value) : ($value ?? '—') }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info alert-flex">
                                <div class="alert-icon">
                                    <i class="fa fa-info-circle"></i>
                                </div>
                                <div class="alert-content">
                                    <p>No form responses found.</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Payment History Section -->
                    <div class="section">
                        <h4 class="section-title">
                            <i class="fa fa-credit-card"></i> Payment History
                        </h4>
                        @if ($user->payments && $user->payments->count() > 0)
                            <div class="table-container">
                                <table class="custom-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Payment Gateway</th>
                                            <th>Transaction ID</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($user->payments as $p)
                                            <tr>
                                                <td>{{ $p->created_at->format('Y-m-d H:i') }}</td>
                                                <td>{{ number_format($p->amount, 2) }} {{ $p->currency }}</td>
                                                <td>
                                                    @if ($p->status === 'captured')
                                                        <span class="label label-success">Captured</span>
                                                    @elseif ($p->status === 'pending')
                                                        <span class="label label-warning">Pending</span>
                                                    @elseif ($p->status === 'failed')
                                                        <span class="label label-danger">Failed</span>
                                                    @else
                                                        <span class="label label-default">{{ $p->status }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $p->payment_gateway ?? '-' }}</td>
                                                <td><small>{{ $p->transaction_id ?? '-' }}</small></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info alert-flex">
                                <div class="alert-icon">
                                    <i class="fa fa-info-circle"></i>
                                </div>
                                <div class="alert-content">
                                    <p>No payments recorded for this user.</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Email Management Section -->
                    <div class="email-management">
                        <h4 class="section-title">
                            <i class="fa fa-envelope"></i> Email Management
                        </h4>
                        <div class="email-buttons">
                            <button type="button" class="btn-custom btn-green send-email-action"
                                data-user-id="{{ $user->id }}" data-action="approve">
                                <i class="fa fa-check"></i> Send Approve Email
                            </button>
                            <button type="button" class="btn-custom btn-yellow send-email-action"
                                data-user-id="{{ $user->id }}" data-action="reject">
                                <i class="fa fa-times"></i> Send Reject Email
                            </button>
                            <button type="button" class="btn-custom btn-blue loadModal"
                                data-modal-id="CustomEmail"
                                data-href="{{ route('showCustomEmail', ['event_id' => $event->id, 'user_id' => $user->id]) }}">
                                <i class="fa fa-envelope-o"></i> Send Custom Email
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="actions-container">
                        <div>
                            @if ($user->status !== 'approved')
                                <button type="button" class="btn-custom btn-green update-status-modal"
                                    data-user-id="{{ $user->id }}" data-status="approved">
                                    <i class="fa fa-check"></i> Approve
                                </button>
                            @endif

                            @if ($user->status !== 'rejected')
                                <button type="button" class="btn-custom btn-yellow update-status-modal"
                                    data-user-id="{{ $user->id }}" data-status="rejected">
                                    <i class="fa fa-times"></i> Reject
                                </button>
                            @endif

                            <button type="button" class="btn-custom btn-red delete-user-modal"
                                data-user-id="{{ $user->id }}">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </div>

                        <button type="button" class="btn-custom btn-gray" data-dismiss="modal">
                            <i class="fa fa-times"></i> Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Handle status update from modal
    $('.update-status-modal').on('click', function(e) {
        e.preventDefault();

        const userId = $(this).data('user-id');
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

        let url = `/event/{{ $user->registration->event_id }}/registrations/users/${userId}/status`;
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
                        // Show toast notification if available, otherwise use alert
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message);
                        } else {
                            alert(response.message);
                        }
                        window.location.reload();
                    }
                },
                error: function(xhr) {
                    console.error(xhr);
                    if (typeof toastr !== 'undefined') {
                        toastr.error('An error occurred. Please try again.');
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                }
            });
        }
    });

    // Handle delete from modal
    $('.delete-user-modal').on('click', function(e) {
        e.preventDefault();

        const userId = $(this).data('user-id');

        if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
            $.ajax({
                url: `${deleteUserBaseUrl}/${userId}`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Show toast notification if available, otherwise use alert
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message);
                        } else {
                            alert(response.message);
                        }
                        window.location.reload();
                    }
                },
                error: function(xhr) {
                    console.error(xhr);
                    if (typeof toastr !== 'undefined') {
                        toastr.error('An error occurred. Please try again.');
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                }
            });
        }
    });

    // Handle email actions from user details modal
    $('.send-email-action').on('click', function(e) {
        e.preventDefault();

        const userId = $(this).data('user-id');
        const action = $(this).data('action');

        let confirmMessage = '';
        let url = '';

        switch (action) {
            case 'approve':
                confirmMessage = 'Are you sure you want to send an approval email to this user?';
                url = '{{ route('sendApprovalEmail', ['event_id' => $event->id, 'user_id' => $user->id]) }}';
                break;
            case 'reject':
                confirmMessage = 'Are you sure you want to send a rejection email to this user?';
                url = '{{ route('sendRejectionEmail', ['event_id' => $event->id, 'user_id' => $user->id]) }}';
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
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message);
                        } else {
                            alert(response.message);
                        }
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    if (typeof toastr !== 'undefined') {
                        toastr.error('An error occurred. Please try again.');
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                }
            });
        }
    });
</script>
    