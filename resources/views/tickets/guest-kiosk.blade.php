<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="event-id" content="{{ $event->id }}">
    <title>{{ $event->title }} — Check In</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        kiosk: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    },
                    animation: {
                        'fade-in': 'kioskFadeIn 0.4s ease-out',
                        'scale-in': 'kioskScaleIn 0.35s ease-out',
                    },
                    keyframes: {
                        kioskFadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(8px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        kioskScaleIn: {
                            '0%': { opacity: '0', transform: 'scale(0.96)' },
                            '100%': { opacity: '1', transform: 'scale(1)' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { -webkit-tap-highlight-color: transparent; }
        .kiosk-card { transition: box-shadow 0.2s ease, transform 0.2s ease; }
        .kiosk-card:active { transform: scale(0.99); }
    </style>
</head>
<body class="h-full font-sans antialiased bg-gradient-to-br from-slate-50 via-white to-emerald-50/30">
    <div class="flex flex-col min-h-full">
        <!-- Minimal header -->
        <header class="flex-shrink-0 py-4 px-4 sm:px-6 border-b border-slate-200/80 bg-white/70 backdrop-blur-sm">
            <div class="max-w-6xl mx-auto flex items-center justify-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-500 text-white shadow-lg shadow-emerald-500/25">
                    <i class="fas fa-qrcode text-lg"></i>
                </div>
                <div class="text-center sm:text-left min-w-0">
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800 truncate" title="{{ $event->title }}">{{ $event->title ?: 'Check-In' }}</h1>
                    <p class="text-xs sm:text-sm text-slate-500">Self-Service Check-In</p>
                </div>
            </div>
        </header>

        <!-- Main content -->
        <main class="flex-1 flex items-center justify-center px-4 py-6 sm:p-6 lg:p-8">
            <div class="max-w-6xl mx-auto w-full">
                <div class="grid grid-cols-1 gap-6 lg:gap-8 lg:grid-cols-2">
                    <!-- QR Scanner -->
                    <div class="kiosk-card overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-xl shadow-slate-200/50 animate-fade-in">
                        <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                            <h2 class="flex items-center gap-2 text-base font-semibold text-slate-800">
                                <i class="fas fa-camera text-emerald-600"></i>
                                Scan QR Code
                            </h2>
                            <p class="mt-0.5 text-sm text-slate-500">Position the ticket QR code in the frame</p>
                        </div>
                        <div class="p-5">
                            <div class="relative w-full overflow-hidden rounded-xl bg-slate-900 aspect-[4/3]">
                                <video id="qrVideo" class="hidden object-cover w-full h-full" autoplay playsinline></video>
                                <div id="scannerPlaceholder" class="absolute inset-0 flex flex-col items-center justify-center text-slate-500 bg-slate-800/50">
                                    <i class="mb-3 text-5xl opacity-60 fas fa-qrcode"></i>
                                    <p class="text-sm font-medium">Camera off</p>
                                    <p class="text-xs mt-0.5">Tap Start to scan</p>
                                </div>
                                <canvas id="qrCanvas" class="hidden"></canvas>
                                <div id="scannerOverlay" class="absolute inset-0 hidden pointer-events-none">
                                    <div class="absolute w-40 h-40 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 border-2 border-emerald-400 rounded-xl shadow-lg shadow-emerald-500/20">
                                        <span class="absolute -top-1 -left-1 w-5 h-5 border-t-2 border-l-2 border-emerald-400 rounded-tl-lg"></span>
                                        <span class="absolute -top-1 -right-1 w-5 h-5 border-t-2 border-r-2 border-emerald-400 rounded-tr-lg"></span>
                                        <span class="absolute -bottom-1 -left-1 w-5 h-5 border-b-2 border-l-2 border-emerald-400 rounded-bl-lg"></span>
                                        <span class="absolute -bottom-1 -right-1 w-5 h-5 border-b-2 border-r-2 border-emerald-400 rounded-br-lg"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 flex gap-3">
                                <button type="button" id="startCameraBtn" onclick="kioskStartCamera()"
                                    class="flex-1 flex items-center justify-center gap-2 px-4 py-3.5 rounded-xl font-medium text-white bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 shadow-lg shadow-emerald-500/25 transition-colors">
                                    <i class="fas fa-camera"></i>
                                    <span>Start Camera</span>
                                </button>
                                <button type="button" id="stopCameraBtn" onclick="kioskStopCamera()"
                                    class="hidden flex flex-1 items-center justify-center gap-2 px-4 py-3.5 rounded-xl font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                                    <i class="fas fa-camera-slash"></i>
                                    <span>Stop</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Manual Check-in/Check-out -->
                    <div class="kiosk-card overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-xl shadow-slate-200/50 animate-fade-in" style="animation-delay: 0.05s">
                        <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                            <h2 class="flex items-center gap-2 text-base font-semibold text-slate-800">
                                <i class="fas fa-keyboard text-emerald-600"></i>
                                Enter Code or Email
                            </h2>
                            <p class="mt-0.5 text-sm text-slate-500">First time: Check-in · Second time: Check-out</p>
                        </div>
                        <div class="p-5">
                            <form id="kioskForm" class="space-y-4" method="POST" action="{{ route('PostScanTicket', ['event_id' => $event->id]) }}">
                                @csrf
                                <input type="hidden" name="event_id" value="{{ $event->id }}">
                                <div>
                                    <label for="uniqueCode" class="block text-sm font-medium text-slate-700 mb-1.5">Unique code or email</label>
                                    <input type="text" name="unique_code" id="uniqueCode" autocomplete="off"
                                        placeholder="e.g. ABC123 or name@email.com"
                                        class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 text-slate-800 placeholder-slate-400 transition-colors font-mono text-lg">
                                </div>
                                <button type="submit" id="submitBtn"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-3.5 rounded-xl font-semibold text-white bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 shadow-lg shadow-emerald-500/25 transition-colors disabled:opacity-60 disabled:pointer-events-none">
                                    <i class="fas fa-exchange-alt"></i>
                                    <span id="submitBtnText">Check In / Out</span>
                                </button>
                            </form>
                            <div id="kioskResult" class="mt-4 min-h-[60px]"></div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        (function() {
            var eventId = document.querySelector('meta[name="event-id"]').getAttribute('content');
            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            var postUrl = '/events/' + eventId + '/post-scan-ticket';
            var kioskStream = null;
            var kioskScanning = false;

            function kioskStartCamera() {
                var constraints = { video: { facingMode: 'environment', width: { ideal: 640 }, height: { ideal: 480 } } };
                navigator.mediaDevices.getUserMedia(constraints).then(function(stream) {
                    kioskStream = stream;
                    var video = document.getElementById('qrVideo');
                    video.srcObject = stream;
                    video.classList.remove('hidden');
                    document.getElementById('scannerPlaceholder').classList.add('hidden');
                    document.getElementById('scannerOverlay').classList.remove('hidden');
                    document.getElementById('startCameraBtn').classList.add('hidden');
                    document.getElementById('stopCameraBtn').classList.remove('hidden');
                    kioskScanning = true;
                    kioskScanLoop();
                }).catch(function(err) {
                    console.error(err);
                    showKioskResult('Cannot access camera. Allow camera permission and try again.', 'error');
                });
            }

            function kioskStopCamera() {
                if (kioskStream) {
                    kioskStream.getTracks().forEach(function(t) { t.stop(); });
                    kioskStream = null;
                }
                kioskScanning = false;
                document.getElementById('qrVideo').classList.add('hidden');
                document.getElementById('scannerPlaceholder').classList.remove('hidden');
                document.getElementById('scannerOverlay').classList.add('hidden');
                document.getElementById('startCameraBtn').classList.remove('hidden');
                document.getElementById('stopCameraBtn').classList.add('hidden');
            }

            function kioskScanLoop() {
                if (!kioskScanning) return;
                var video = document.getElementById('qrVideo');
                var canvas = document.getElementById('qrCanvas');
                var ctx = canvas.getContext('2d');
                if (video.readyState >= video.HAVE_ENOUGH_DATA) {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    var data = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    var code = jsQR(data.data, data.width, data.height);
                    if (code && code.data) {
                        kioskScanning = false;
                        var value = code.data.trim().toUpperCase();
                        document.getElementById('uniqueCode').value = value;
                        kioskSubmit(value);
                        setTimeout(function() { kioskScanning = true; kioskScanLoop(); }, 1500);
                        return;
                    }
                }
                requestAnimationFrame(kioskScanLoop);
            }

            function showKioskResult(html, type) {
                var el = document.getElementById('kioskResult');
                el.innerHTML = html;
                el.classList.remove('hidden');
            }

            function kioskSubmit(value) {
                value = (value || document.getElementById('uniqueCode').value || '').trim();
                if (!value) {
                    showKioskResult(
                        '<div class="p-4 rounded-xl border border-amber-200 bg-amber-50 text-amber-800 text-sm">Please enter a code or email.</div>',
                        'error'
                    );
                    return;
                }
                var btn = document.getElementById('submitBtn');
                var btnText = document.getElementById('submitBtnText');
                btn.disabled = true;
                btnText.textContent = 'Processing...';
                var formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('unique_code', value);
                formData.append('event_id', eventId);
                fetch(postUrl, {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                }).then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.status === 'success') {
                        var isOut = data.action === 'check_out';
                        var user = data.user || {};
                        var name = (user.first_name || '') + ' ' + (user.last_name || '');
                        var checkInStr = user.check_in ? new Date(user.check_in).toLocaleString() : '';
                        var checkOutStr = user.check_out ? new Date(user.check_out).toLocaleString() : '';
                        var ticketToken = user.ticket_token;
                        var printBtn = '';
                        if (ticketToken && !isOut) {
                            var ticketUrl = window.location.origin + '/ticket/print-view/' + encodeURIComponent(ticketToken);
                            printBtn = '<a href="' + ticketUrl + '" target="_blank" rel="noopener" class="mt-3 inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-emerald-700 bg-emerald-100 hover:bg-emerald-200 transition-colors animate-scale-in">' +
                                '<i class="fas fa-print"></i><span>View &amp; Print Ticket</span></a>';
                        }
                        var html = '<div class="p-4 rounded-xl border-l-4 ' + (isOut ? 'bg-blue-50 border-blue-400' : 'bg-emerald-50 border-emerald-400') + ' animate-scale-in">' +
                            '<div class="flex items-start gap-3">' +
                            '<div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center ' + (isOut ? 'bg-blue-100 text-blue-600' : 'bg-emerald-100 text-emerald-600') + '">' +
                            '<i class="fas ' + (isOut ? 'fa-sign-out-alt' : 'fa-sign-in-alt') + '"></i></div>' +
                            '<div class="flex-1 min-w-0">' +
                            '<p class="font-medium ' + (isOut ? 'text-blue-800' : 'text-emerald-800') + '">' + (data.message || '') + '</p>' +
                            '<p class="text-sm text-slate-600 mt-0.5">' + name + '</p>' +
                            '<p class="text-xs text-slate-500">' + (user.email || '') + '</p>' +
                            (checkInStr && checkOutStr ? '<p class="text-xs text-slate-500 mt-1">Check-in: ' + checkInStr + ' · Out: ' + checkOutStr + '</p>' : '') +
                            printBtn +
                            '</div></div></div>';
                        showKioskResult(html);
                    } else {
                        showKioskResult(
                            '<div class="p-4 rounded-xl border border-red-200 bg-red-50 text-red-800 text-sm animate-scale-in">' +
                            '<i class="fas fa-times-circle mr-2"></i>' + (data.message || 'Something went wrong.') + '</div>',
                            'error'
                        );
                    }
                })
                .catch(function() {
                    showKioskResult(
                        '<div class="p-4 rounded-xl border border-red-200 bg-red-50 text-red-800 text-sm">Network error. Please try again.</div>',
                        'error'
                    );
                })
                .finally(function() {
                    btn.disabled = false;
                    btnText.textContent = 'Check In / Out';
                });
            }

            document.getElementById('kioskForm').addEventListener('submit', function(e) {
                e.preventDefault();
                kioskSubmit();
            });

            window.kioskStartCamera = kioskStartCamera;
            window.kioskStopCamera = kioskStopCamera;
        })();
    </script>
</body>
</html>
