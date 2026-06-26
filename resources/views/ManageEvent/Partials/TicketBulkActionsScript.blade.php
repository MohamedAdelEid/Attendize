<script>
(function($) {
    'use strict';

    window.TicketBulkActions = {
        renderUrl: '{{ route('renderEventTickets', ['event_id' => $event->id]) }}',
        deleteUrl: '{{ route('deleteEventTickets', ['event_id' => $event->id]) }}',
        statsUrl: '{{ route('eventTicketStats', ['event_id' => $event->id]) }}',
        csrfToken: '{{ csrf_token() }}',
        renderBatchSize: 15,
        deleteBatchSize: 50,

        refreshStats: function($statsEl) {
            if (!$statsEl || !$statsEl.length) {
                return;
            }

            $.get(this.statsUrl, function(response) {
                if (response.status === 'success') {
                    $statsEl.html(
                        '<strong>' + response.rendered + '</strong> rendered / ' +
                        '<strong>' + response.eligible + '</strong> approved users' +
                        (response.pending > 0 ? ' (' + response.pending + ' pending)' : '')
                    );
                }
            });
        },

        runBatched: function(options) {
            const self = this;
            const url = options.action === 'delete' ? this.deleteUrl : this.renderUrl;
            const batchSize = options.action === 'delete' ? this.deleteBatchSize : this.renderBatchSize;
            let offset = 0;
            let total = 0;
            let totalGenerated = 0;
            let totalDeleted = 0;
            let totalFailed = 0;

            const $progress = options.$progress;
            const $bar = options.$bar;
            const $label = options.$label;
            const $buttons = options.$buttons;

            function setProgress(processed, totalCount, message) {
                const pct = totalCount > 0 ? Math.min(100, Math.round((processed / totalCount) * 100)) : 0;
                if ($bar && $bar.length) {
                    $bar.css('width', pct + '%').attr('aria-valuenow', pct).text(pct + '%');
                }
                if ($label && $label.length) {
                    $label.text(message || (processed + ' / ' + totalCount));
                }
            }

            function finish(successMessage) {
                if ($buttons && $buttons.length) {
                    $buttons.prop('disabled', false);
                }
                if ($progress && $progress.length) {
                    $progress.hide();
                }
                if (options.$stats && options.$stats.length) {
                    self.refreshStats(options.$stats);
                }
                alert(successMessage);
                if (options.reloadOnFinish) {
                    window.location.reload();
                }
            }

            function processBatch() {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: self.csrfToken,
                        user_ids: options.userIds || null,
                        offset: offset,
                        limit: batchSize
                    },
                    success: function(response) {
                        if (response.status !== 'success') {
                            finish('An error occurred. Please try again.');
                            return;
                        }

                        total = response.total;
                        totalGenerated += response.generated || 0;
                        totalDeleted += response.deleted || 0;
                        totalFailed += response.failed || 0;
                        offset = response.next_offset;

                        setProgress(
                            response.processed,
                            total,
                            (options.action === 'delete' ? 'Deleting' : 'Rendering') +
                            ': ' + response.processed + ' / ' + total
                        );

                        if (response.done) {
                            let msg;
                            if (options.action === 'delete') {
                                msg = 'Deleted tickets for ' + totalDeleted + ' user(s).';
                            } else {
                                msg = 'Rendered ' + totalGenerated + ' ticket(s).';
                                if (totalFailed > 0) {
                                    msg += ' ' + totalFailed + ' failed.';
                                }
                            }
                            finish(msg);
                        } else {
                            processBatch();
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        finish('An error occurred. Please try again.');
                    }
                });
            }

            if ($buttons && $buttons.length) {
                $buttons.prop('disabled', true);
            }
            if ($progress && $progress.length) {
                $progress.show();
            }
            setProgress(0, 0, options.action === 'delete' ? 'Starting delete...' : 'Starting render...');
            processBatch();
        }
    };
})(jQuery);
</script>
