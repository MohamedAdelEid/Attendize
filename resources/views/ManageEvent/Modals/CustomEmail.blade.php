<div role="dialog" class="modal fade" id="custom-email-modal" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-mail"></i>
                    Send Custom Email to {{ $user->first_name }} {{ $user->last_name }}
                </h3>
            </div>
            <form id="custom-email-form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="email_subject" class="control-label required">Subject</label>
                                <input type="text" class="form-control" id="email_subject" name="subject"
                                       placeholder="Enter email subject" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="email_body" class="control-label required">Message</label>
                                <textarea class="form-control" id="email_body" name="body" rows="8"
                                          placeholder="Enter your message here..." required></textarea>
                                <small class="help-block">
                                    Available placeholders: {name}, {email}, {event_name}, {registration_code}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="ico-info"></i>
                                <strong>Recipient:</strong> {{ $user->email }}<br>
                                <strong>Event:</strong> {{ $event->title }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="send-custom-email-btn">
                        <i class="ico-mail"></i> Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#custom-email-form').on('submit', function(e) {
        e.preventDefault();

        const submitBtn = $('#send-custom-email-btn');
        const originalText = submitBtn.html();

        // Disable button and show loading state
        submitBtn.prop('disabled', true).html('<i class="ico-spinner"></i> Sending...');

        $.ajax({
            url: '{{ route('sendCustomEmail', ['event_id' => $event->id, 'user_id' => $user->id]) }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                subject: $('#email_subject').val(),
                body: $('#email_body').val()
            },
            success: function(response) {
                if (response.status === 'success') {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message);
                    } else {
                        alert(response.message);
                    }
                    $('#custom-email-modal').modal('hide');
                    $('#custom-email-form')[0].reset();
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message);
                    } else {
                        alert(response.message);
                    }
                }
            },
            error: function(xhr) {
                console.error(xhr);
                let errorMessage = 'An error occurred. Please try again.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join('\n');
                }

                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMessage);
                } else {
                    alert(errorMessage);
                }
            },
            complete: function() {
                // Re-enable button and restore original text
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
</script>
