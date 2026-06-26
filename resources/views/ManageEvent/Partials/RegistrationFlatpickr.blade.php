<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script>
    (function () {
        if (window.__registrationFlatpickrLoaded) {
            return;
        }
        window.__registrationFlatpickrLoaded = true;

        var script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/flatpickr';
        script.async = false;
        document.head.appendChild(script);
    })();

    window.initRegistrationFlatpickr = function ($modal) {
        if (!$modal || !$modal.length) {
            return;
        }

        var runInit = function () {
            if (typeof flatpickr === 'undefined') {
                return;
            }

            var $start = $modal.find('.registration-start-date');
            var $end = $modal.find('.registration-end-date');

            if (!$start.length || !$end.length) {
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
</script>
