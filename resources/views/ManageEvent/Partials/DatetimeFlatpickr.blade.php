<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script>
    (function () {
        if (window.__datetimeFlatpickrLoaded) {
            return;
        }
        window.__datetimeFlatpickrLoaded = true;

        var script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/flatpickr';
        script.async = false;
        document.head.appendChild(script);
    })();

    window.initDatetimeFlatpickr = function ($scope) {
        if (!$scope || !$scope.length) {
            return;
        }

        var runInit = function () {
            if (typeof flatpickr === 'undefined') {
                return;
            }

            $scope.find('input.registration-start-date, input.start[data-field="datetime"]').each(function () {
                var $start = $(this);
                var $container = $start.closest('form, .modal-body, .modal-content, .tab-pane, .panel-body');
                if (!$container.length) {
                    $container = $scope;
                }

                var $end = $container.find('input.registration-end-date, input.end[data-field="datetime"]').first();
                if (!$end.length && $start.data('startendelem')) {
                    $end = $container.find($start.data('startendelem')).filter('input').first();
                }

                if (!$end.length) {
                    return;
                }

                if ($end[0]._flatpickr) {
                    $end[0]._flatpickr.destroy();
                }
                if ($start[0]._flatpickr) {
                    $start[0]._flatpickr.destroy();
                }

                var endPicker = flatpickr($end[0], {
                    enableTime: true,
                    dateFormat: 'Y-m-d H:i',
                    time_24hr: true,
                    allowInput: true,
                    minuteIncrement: 1,
                });

                flatpickr($start[0], {
                    enableTime: true,
                    dateFormat: 'Y-m-d H:i',
                    time_24hr: true,
                    allowInput: true,
                    minuteIncrement: 1,
                    onChange: function (selectedDates) {
                        if (selectedDates[0]) {
                            endPicker.set('minDate', selectedDates[0]);
                        }
                    },
                });
            });
        };

        if (typeof flatpickr !== 'undefined') {
            runInit();
            return;
        }

        var attempts = 0;
        var timer = setInterval(function () {
            attempts++;
            if (typeof flatpickr !== 'undefined') {
                clearInterval(timer);
                runInit();
            } else if (attempts > 50) {
                clearInterval(timer);
            }
        }, 100);
    };

    window.initRegistrationFlatpickr = function ($modal) {
        window.initDatetimeFlatpickr($modal);
    };

    $(function () {
        initDatetimeFlatpickr($(document));
        $(document).on('shown.bs.modal', '.modal', function () {
            initDatetimeFlatpickr($(this));
        });
    });
</script>
