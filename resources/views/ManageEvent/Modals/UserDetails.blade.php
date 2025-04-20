<style>
    /* General Reset and Base Styles */
    .user-details-modal * {
        box-sizing: border-box;
    }

    /* Modal Styles */
    .modal-lg {
        max-width: 800px;
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
    }

    .btn-yellow {
        background-color: #f59e0b;
        color: white;
    }

    .btn-yellow:hover {
        background-color: #d97706;
    }

    .btn-red {
        background-color: #ef4444;
        color: white;
    }

    .btn-red:hover {
        background-color: #dc2626;
    }

    .btn-gray {
        background-color: white;
        color: #4b5563;
        border-color: #d1d5db;
    }

    .btn-gray:hover {
        background-color: #f9fafb;
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
                            {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
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

                            <div class="info-card grid-span-2">
                                <p class="card-label">Registration</p>
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
                                            <tr>
                                                <td>{{ $response->field->label }}</td>
                                                <td>
                                                    @if ($response->field->type == 'file')
                                                        @if ($response->value)
                                                            <a href="{{ asset('storage/' . $response->value) }}"
                                                                class="link" target="_blank">
                                                                <i class="fa fa-file-o"></i> View File
                                                            </a>
                                                        @else
                                                            <span class="text-muted">No file uploaded</span>
                                                        @endif
                                                    @elseif($response->field->type == 'checkbox' || $response->field->type == 'radio')
                                                        {{ $response->value ?? 'No selection' }}
                                                    @else
                                                        {{ $response->value ?? 'No response' }}
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
</script>
