@extends('tickets.layouts.check-in-layout')

@section('title', 'Scanner')

@section('content')
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-5">
        <div class="p-6 transition-shadow duration-200 bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-lg">
                        <i class="text-xl text-gray-700 fas fa-users"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Registrations</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $event->registrations->sum(function($registration) { return $registration->registrationUsers()->count(); }) }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 transition-shadow duration-200 bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                        <i class="text-xl text-green-600 fas fa-sign-in-alt"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Checked In</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $event->registrations->sum(function($registration) { return $registration->registrationUsers()->whereNotNull('check_in')->whereNull('check_out')->count(); }) }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 transition-shadow duration-200 bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                        <i class="text-xl text-blue-600 fas fa-sign-out-alt"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Checked Out</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $event->registrations->sum(function($registration) { return $registration->registrationUsers()->whereNotNull('check_out')->count(); }) }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 transition-shadow duration-200 bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-lg">
                        <i class="text-xl text-yellow-600 fas fa-users"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Currently in Event</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $event->registrations->sum(function($registration) {
                            return $registration->registrationUsers()
                                ->whereNotNull('check_in')
                                ->whereNull('check_out')
                                ->count();
                        }) }}
                    </p>
                </div>
            </div>
        </div>

        <div class="p-6 transition-shadow duration-200 bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-lg">
                        <i class="text-xl text-yellow-600 fas fa-clock"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending Approval</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $event->registrations->sum(function($registration) { return $registration->registrationUsers()->where('status','!=','approved')->count(); }) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scanner Section -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- QR Scanner -->
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="flex items-center text-lg font-semibold text-gray-900">
                    <i class="mr-3 text-gray-700 fas fa-camera"></i>
                    QR Code Scanner
                </h3>
            </div>
            <div class="p-6">
                <div class="relative w-full mb-4 overflow-hidden bg-black rounded-lg scanner-container h-80">
                    <video id="qrVideo" class="hidden object-cover w-full h-full" autoplay playsinline></video>
                    <div id="scannerPlaceholder" class="absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                        <i class="mb-4 text-6xl opacity-50 fas fa-qrcode"></i>
                        <p class="text-lg font-medium">Camera not active</p>
                        <p class="text-sm">Click start to begin scanning</p>
                    </div>
                    <canvas id="qrCanvas" class="hidden"></canvas>

                    <!-- Scanner overlay -->
                    <div id="scannerOverlay" class="absolute inset-0 hidden pointer-events-none">
                        <div class="absolute w-48 h-48 transform -translate-x-1/2 -translate-y-1/2 border-2 border-white rounded-lg top-1/2 left-1/2">
                            <div class="absolute w-6 h-6 border-t-4 border-l-4 border-green-400 rounded-tl-lg -top-1 -left-1"></div>
                            <div class="absolute w-6 h-6 border-t-4 border-r-4 border-green-400 rounded-tr-lg -top-1 -right-1"></div>
                            <div class="absolute w-6 h-6 border-b-4 border-l-4 border-green-400 rounded-bl-lg -bottom-1 -left-1"></div>
                            <div class="absolute w-6 h-6 border-b-4 border-r-4 border-green-400 rounded-br-lg -bottom-1 -right-1"></div>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <button id="startCameraBtn" onclick="startCamera()"
                            class="flex items-center justify-center w-full px-4 py-3 space-x-2 font-medium text-white transition-colors duration-200 bg-black rounded-lg hover:bg-gray-800">
                        <i class="fas fa-camera"></i>
                        <span>Start Camera</span>
                    </button>
                    <button id="stopCameraBtn" onclick="stopCamera()"
                            class="flex items-center justify-center hidden w-full px-4 py-3 space-x-2 font-medium text-gray-700 transition-colors duration-200 bg-gray-100 rounded-lg hover:bg-gray-200">
                        <i class="fas fa-camera-slash"></i>
                        <span>Stop Camera</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Manual Check-in/Check-out -->
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div>
                    <h3 class="flex items-center text-lg font-semibold text-gray-900">
                        <i class="mr-3 text-gray-700 fas fa-keyboard"></i>
                        Manual Check-in/Check-out
                    </h3>
                    <p class="mt-1 text-sm text-gray-600">First scan: Check-in • Second scan: Check-out</p>
                </div>
                <div class="relative">
                    <button type="button" onclick="toggleBulkMenu(event)" id="bulkMenuBtn"
                            class="flex items-center px-4 py-2 space-x-2 text-sm font-medium text-gray-700 transition-colors duration-200 border border-gray-300 rounded-lg hover:bg-gray-100 hover:border-gray-400">
                        <i class="fas fa-ellipsis-v"></i>
                        <span>Bulk actions</span>
                        <i class="text-xs fas fa-chevron-down" id="bulkMenuChevron"></i>
                    </button>
                    <div id="bulkMenuDropdown" class="absolute right-0 z-20 hidden w-48 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg top-full">
                        <button type="button" onclick="confirmBulkAction('check_in'); closeBulkMenu();"
                                class="flex items-center w-full px-4 py-3 text-left text-sm text-gray-700 hover:bg-green-50 hover:text-green-800 rounded-t-lg">
                            <i class="mr-3 text-green-600 fas fa-sign-in-alt"></i>
                            Check-in All
                        </button>
                        <button type="button" onclick="confirmBulkAction('check_out'); closeBulkMenu();"
                                class="flex items-center w-full px-4 py-3 text-left text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-800 rounded-b-lg border-t border-gray-100">
                            <i class="mr-3 text-blue-600 fas fa-sign-out-alt"></i>
                            Check-out All
                        </button>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('PostScanTicket', ['event_id' => $event->id]) }}" id="checkInForm" class="space-y-4">
                    @csrf
                    <div>
                        <label for="uniqueCode" class="block mb-2 text-sm font-medium text-gray-700">
                            Unique Code or Email
                        </label>
                        <input type="text"
                               name="unique_code"
                               id="uniqueCode"
                               value="{{ old('unique_code', session('unique_code_input', '')) }}"
                               class="w-full px-4 py-3 font-mono text-lg transition-colors duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent"
                               placeholder="Enter unique code or email"
                               required>
                    </div>
                    <button type="submit"
                            id="manualCheckInBtn"
                            class="flex items-center justify-center w-full px-4 py-3 space-x-2 font-medium text-white transition-colors duration-200 bg-green-600 rounded-lg hover:bg-green-700">
                        <i class="fas fa-exchange-alt"></i>
                        <span>Check In/Out</span>
                    </button>

                    @if(session('success'))
                    <div class="mt-6 p-4 rounded-lg border-l-4 {{ session('action') === 'check_out' ? 'bg-blue-50 border-blue-400' : 'bg-green-50 border-green-400' }} animate-fade-in">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas {{ session('action') === 'check_out' ? 'fa-sign-out-alt text-blue-400' : 'fa-sign-in-alt text-green-400' }} text-xl"></i>
                            </div>
                            <div class="flex-1 ml-3">
                                <p class="text-sm font-medium {{ session('action') === 'check_out' ? 'text-blue-800' : 'text-green-800' }}">
                                    {{ session('success') }}
                                </p>
                                @if(session('user'))
                                    <div class="p-3 mt-3 bg-white border border-gray-200 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 {{ session('action') === 'check_out' ? 'bg-blue-100' : 'bg-green-100' }} rounded-full flex items-center justify-center">
                                                    <i class="fas {{ session('action') === 'check_out' ? 'fa-sign-out-alt text-blue-600' : 'fa-sign-in-alt text-green-600' }}"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ session('user')->first_name }} {{ session('user')->last_name }}
                                                </p>
                                                <p class="text-sm text-gray-500">{{ session('user')->email }}</p>
                                                <p class="text-xs {{ session('action') === 'check_out' ? 'text-blue-600' : 'text-green-600' }} mt-1">
                                                    <i class="mr-1 fas fa-clock"></i>
                                                    {{ session('action') === 'check_out' ? 'Checked out' : 'Checked in' }} successfully
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(session('error'))
                        <div class="p-4 mt-6 border-l-4 border-red-400 rounded-lg bg-red-50 animate-fade-in">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="text-xl text-red-400 fas fa-times-circle"></i>
                                </div>
                                <div class="flex-1 ml-3">
                                    <p class="text-sm font-medium text-red-800">
                                        {{ session('error') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Action Confirmation Modal -->
    <div id="bulkConfirmModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeBulkConfirmModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <div class="px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900" id="bulkConfirmTitle">Confirm</h3>
                    <p class="mt-2 text-sm text-gray-600" id="bulkConfirmMessage"></p>
                </div>
                <div class="px-6 py-4 space-y-2 bg-gray-50 sm:flex sm:flex-row-reverse sm:space-y-0 sm:space-x-2 sm:space-x-reverse">
                    <button type="button" id="bulkConfirmBtn" onclick="executeBulkAction()"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white rounded-lg sm:w-auto sm:text-sm">
                        Confirm
                    </button>
                    <button type="button" onclick="closeBulkConfirmModal()"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/script.js') }}"></script>
    <script>
        function startCamera() {
            var constraints = { video: { facingMode: "environment", width: { ideal: 640 }, height: { ideal: 480 } } };
            navigator.mediaDevices.getUserMedia(constraints).then(function(stream) {
                window.currentStream = stream;
                var video = document.getElementById("qrVideo");
                video.srcObject = stream;
                video.classList.remove("hidden");
                document.getElementById("scannerPlaceholder").classList.add("hidden");
                document.getElementById("scannerOverlay").classList.remove("hidden");
                document.getElementById("startCameraBtn").classList.add("hidden");
                document.getElementById("stopCameraBtn").classList.remove("hidden");
                window.isScanning = true;
                scanQRCode();
                if (typeof showToast === "function") showToast("Camera started successfully", "success");
            }).catch(function(err) {
                console.error("Error accessing camera:", err);
                if (typeof showToast === "function") showToast("Unable to access camera. Please check permissions.", "error");
            });
        }

        function stopCamera() {
            if (window.currentStream) {
                window.currentStream.getTracks().forEach(function(track) { track.stop(); });
                window.currentStream = null;
            }
            window.isScanning = false;
            document.getElementById("qrVideo").classList.add("hidden");
            document.getElementById("scannerPlaceholder").classList.remove("hidden");
            document.getElementById("scannerOverlay").classList.add("hidden");
            document.getElementById("startCameraBtn").classList.remove("hidden");
            document.getElementById("stopCameraBtn").classList.add("hidden");
        }

        function scanQRCode() {
            if (!window.isScanning) return;
            var video = document.getElementById("qrVideo");
            var canvas = document.getElementById("qrCanvas");
            var context = canvas.getContext("2d");
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                var imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                var code = jsQR(imageData.data, imageData.width, imageData.height);
                if (code) {
                    handleQRCodeDetected(code.data);
                    return;
                }
            }
            requestAnimationFrame(scanQRCode);
        }

        function handleQRCodeDetected(qrData) {
            window.isScanning = false;
            var value = qrData.trim().toUpperCase();
            document.getElementById("uniqueCode").value = value;
            document.getElementById("checkInForm").submit();
        }

        var pendingBulkAction = null;
        var eventIdScanner = document.querySelector('meta[name="event-id"]') ? document.querySelector('meta[name="event-id"]').getAttribute('content') : '';
        var csrfTokenScanner = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

        function toggleBulkMenu(e) {
            e = e || window.event;
            if (e) e.stopPropagation();
            var dd = document.getElementById('bulkMenuDropdown');
            var chev = document.getElementById('bulkMenuChevron');
            if (dd.classList.contains('hidden')) {
                dd.classList.remove('hidden');
                if (chev) chev.classList.add('fa-chevron-up'), chev.classList.remove('fa-chevron-down');
                setTimeout(function() { document.addEventListener('click', closeBulkMenuOnClickOutside); }, 0);
            } else {
                closeBulkMenu();
            }
        }
        function closeBulkMenu() {
            var dd = document.getElementById('bulkMenuDropdown');
            var chev = document.getElementById('bulkMenuChevron');
            if (dd) dd.classList.add('hidden');
            if (chev) chev.classList.remove('fa-chevron-up'), chev.classList.add('fa-chevron-down');
            document.removeEventListener('click', closeBulkMenuOnClickOutside);
        }
        function closeBulkMenuOnClickOutside(e) {
            var dd = document.getElementById('bulkMenuDropdown');
            var btn = document.getElementById('bulkMenuBtn');
            if (dd && btn && e.target && !dd.contains(e.target) && !btn.contains(e.target)) closeBulkMenu();
        }
        window.toggleBulkMenu = toggleBulkMenu;
        window.closeBulkMenu = closeBulkMenu;

        function confirmBulkAction(action) {
            pendingBulkAction = action;
            var modal = document.getElementById('bulkConfirmModal');
            var title = document.getElementById('bulkConfirmTitle');
            var msg = document.getElementById('bulkConfirmMessage');
            var btn = document.getElementById('bulkConfirmBtn');
            if (action === 'check_in') {
                title.textContent = 'Check-in All';
                msg.textContent = 'Are you sure you want to check in all eligible attendees (approved, not currently in the event)?';
                btn.className = 'inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 sm:w-auto sm:text-sm';
            } else {
                title.textContent = 'Check-out All';
                msg.textContent = 'Are you sure you want to check out all attendees currently in the event?';
                btn.className = 'inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 sm:w-auto sm:text-sm';
            }
            modal.classList.remove('hidden');
        }

        function closeBulkConfirmModal() {
            document.getElementById('bulkConfirmModal').classList.add('hidden');
            pendingBulkAction = null;
        }

        function executeBulkAction() {
            if (!pendingBulkAction || !eventIdScanner) return;
            var url = '/events/' + eventIdScanner + (pendingBulkAction === 'check_in' ? '/bulk-check-in' : '/bulk-check-out');
            var formData = new FormData();
            formData.append('_token', csrfTokenScanner);
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            }).then(function(r) { return r.json(); })
              .then(function(data) {
                closeBulkConfirmModal();
                if (data.status === 'success') {
                    if (typeof showToast === 'function') showToast(data.message || 'Done.', 'success');
                    window.location.reload();
                } else {
                    if (typeof showToast === 'function') showToast(data.message || 'Error.', 'error');
                }
              })
              .catch(function() {
                closeBulkConfirmModal();
                if (typeof showToast === 'function') showToast('Request failed.', 'error');
              });
        }
    </script>
@endpush