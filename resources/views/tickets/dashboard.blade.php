@extends('tickets.layouts.check-in-layout')

@section('title', 'Reports')

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
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $event->registrations->sum(function ($registration) {return $registration->registrationUsers()->count();}) }}
                    </p>
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
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $event->registrations->sum(function ($registration) {return $registration->registrationUsers()->whereNotNull('check_in')->whereNull('check_out')->count();}) }}
                    </p>
                </div>
            </div>
        </div>

        <div
            class="p-6 transition-shadow duration-200 bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                        <i class="text-xl text-blue-600 fas fa-sign-out-alt"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Checked Out</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $event->registrations->sum(function ($registration) {return $registration->registrationUsers()->whereNotNull('check_out')->count();}) }}
                    </p>
                </div>
            </div>
        </div>

        <div
            class="p-6 transition-shadow duration-200 bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-lg">
                        <i class="text-xl text-yellow-600 fas fa-users"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Currently in Event</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $event->registrations->sum(function ($registration) {
                            return $registration->registrationUsers()->whereNotNull('check_in')->whereNull('check_out')->count();
                        }) }}
                    </p>
                </div>
            </div>
        </div>

        <div
            class="p-6 transition-shadow duration-200 bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-lg">
                        <i class="text-xl text-yellow-600 fas fa-clock"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending Approval</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $event->registrations->sum(function ($registration) {return $registration->registrationUsers()->where('status', '!=', 'approved')->count();}) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Section -->
    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-col space-y-3 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                <h3 class="flex items-center text-lg font-semibold text-gray-900">
                    <i class="mr-3 text-gray-700 fas fa-users"></i>
                    Registration Management
                </h3>
            </div>
        </div>

        <div class="p-6">
            <!-- Search and Filter -->
            <form method="GET" action="{{ route('showCheckInDashboard', ['event_id' => $event->id]) }}" id="filterForm">
                <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="text-gray-400 fas fa-search"></i>
                        </div>
                        <input type="text" name="search" id="searchInput" value="{{ request()->get('search') }}"
                            class="w-full py-3 pl-10 pr-4 transition-colors duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent"
                            placeholder="Search by name, email, or code...">
                    </div>
                    <select name="attendance" id="attendanceFilter"
                        class="w-full px-4 py-3 transition-colors duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent">
                        <option value="">All Attendance</option>
                        <option value="not_checked_in"
                            {{ request()->get('attendance') == 'not_checked_in' ? 'selected' : '' }}>Not Checked In
                        </option>
                        <option value="checked_in" {{ request()->get('attendance') == 'checked_in' ? 'selected' : '' }}>
                            Checked In</option>
                        <option value="checked_out" {{ request()->get('attendance') == 'checked_out' ? 'selected' : '' }}>
                            Checked Out</option>
                    </select>
                </div>
                <div class="flex justify-end mb-6 space-x-2">
                    <button type="submit"
                        class="px-4 py-2 font-medium text-white transition-colors duration-200 bg-black rounded-lg hover:bg-gray-800">
                        <i class="mr-2 fas fa-filter"></i>Apply Filters
                    </button>
                    <a href="{{ route('showCheckInDashboard', ['event_id' => $event->id]) }}"
                        class="px-4 py-2 font-medium text-gray-700 transition-colors duration-200 bg-gray-100 rounded-lg hover:bg-gray-200">
                        <i class="mr-2 fas fa-times"></i>Clear
                    </a>
                </div>
            </form>

            <!-- Registrations Table -->
            @if (isset($registrationUsers) && $registrationUsers->total() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Name</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Email</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Unique Code</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Attendance Status</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($registrationUsers as $registrationUser)
                                <tr class="transition-colors duration-150 hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $registrationUser->first_name }}
                                            {{ $registrationUser->last_name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $registrationUser->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($registrationUser->unique_code)
                                            <code class="px-2 py-1 font-mono text-xs bg-gray-100 rounded">
                                                {{ $registrationUser->unique_code }}
                                            </code>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $latestLog = $registrationUser
                                                ->checkInCheckOutLogs()
                                                ->orderBy('action_time', 'desc')
                                                ->first();
                                        @endphp
                                        @if ($latestLog && $latestLog->action == 'check_out')
                                            <div class="flex items-center text-blue-600">
                                                <i class="mr-2 fas fa-sign-out-alt"></i>
                                                <div>
                                                    <span class="text-sm font-medium">Checked Out</span>
                                                    <div class="text-xs text-gray-500">
                                                        {{ \Carbon\Carbon::parse($latestLog->action_time)->format('M d, H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif ($latestLog && $latestLog->action == 'check_in')
                                            <div class="flex items-center text-green-600">
                                                <i class="mr-2 fas fa-sign-in-alt"></i>
                                                <div>
                                                    <span class="text-sm font-medium">Checked In</span>
                                                    <div class="text-xs text-gray-500">
                                                        {{ \Carbon\Carbon::parse($latestLog->action_time)->format('M d, H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-center text-gray-400">
                                                <i class="mr-2 fas fa-minus-circle"></i>
                                                <span class="text-sm font-medium">Not Checked In</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button
                                            onclick="viewLogs({{ $registrationUser->id }}, '{{ $registrationUser->first_name }} {{ $registrationUser->last_name }}')"
                                            class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                            <i class="fas fa-history mr-1"></i>View History
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-between px-4 py-3 mt-4 bg-white border-t border-gray-200 sm:px-6">
                    <div class="flex justify-between flex-1 sm:hidden">
                        @if ($registrationUsers->onFirstPage())
                            <span
                                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-default rounded-md">
                                Previous
                            </span>
                        @else
                            <a href="{{ $registrationUsers->appends(request()->query())->previousPageUrl() }}"
                                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Previous
                            </a>
                        @endif

                        @if ($registrationUsers->hasMorePages())
                            <a href="{{ $registrationUsers->appends(request()->query())->nextPageUrl() }}"
                                class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Next
                            </a>
                        @else
                            <span
                                class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-default rounded-md">
                                Next
                            </span>
                        @endif
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{ $registrationUsers->firstItem() }}</span>
                                to
                                <span class="font-medium">{{ $registrationUsers->lastItem() }}</span>
                                of
                                <span class="font-medium">{{ $registrationUsers->total() }}</span>
                                results
                            </p>
                        </div>
                        <nav class="relative z-0 inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                            @php
                                $currentPage = $registrationUsers->currentPage();
                                $lastPage = $registrationUsers->lastPage();
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($lastPage, $currentPage + 2);
                            @endphp

                            @if ($registrationUsers->onFirstPage())
                                <span
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-400 cursor-default">
                                    <span class="sr-only">Previous</span>
                                    <i class="fas fa-chevron-left"></i>
                                </span>
                            @else
                                <a href="{{ $registrationUsers->appends(request()->query())->previousPageUrl() }}"
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    <span class="sr-only">Previous</span>
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            @endif

                            @if ($startPage > 1)
                                <a href="{{ $registrationUsers->appends(request()->query())->url(1) }}"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    1
                                </a>
                                @if ($startPage > 2)
                                    <span
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                        ...
                                    </span>
                                @endif
                            @endif

                            @for ($page = $startPage; $page <= $endPage; $page++)
                                @if ($page == $currentPage)
                                    <span aria-current="page"
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-black text-sm font-medium text-white">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $registrationUsers->appends(request()->query())->url($page) }}"
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endfor

                            @if ($endPage < $lastPage)
                                @if ($endPage < $lastPage - 1)
                                    <span
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                        ...
                                    </span>
                                @endif
                                <a href="{{ $registrationUsers->appends(request()->query())->url($lastPage) }}"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    {{ $lastPage }}
                                </a>
                            @endif

                            @if ($registrationUsers->hasMorePages())
                                <a href="{{ $registrationUsers->appends(request()->query())->nextPageUrl() }}"
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    <span class="sr-only">Next</span>
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            @else
                                <span
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-400 cursor-default">
                                    <span class="sr-only">Next</span>
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                            @endif
                        </nav>
                    </div>
                </div>
            @else
                <div class="py-12 text-center">
                    <i class="mb-4 text-6xl text-gray-300 fas fa-users"></i>
                    <p class="text-lg font-medium text-gray-500">No registrations found</p>
                    <p class="text-sm text-gray-400">Try adjusting your search criteria</p>
                </div>
            @endif
        </div>
    </div>

    <!-- View History Modal -->
    <div id="historyModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeHistoryModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="px-6 py-4 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-gray-900" id="modal-title">
                            <i class="mr-2 text-gray-700 fas fa-history"></i>
                            Check-in/Check-out History - <span id="modalUserName"></span>
                        </h3>
                        <button type="button" onclick="closeHistoryModal()" class="text-gray-400 hover:text-gray-500">
                            <i class="text-2xl fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4 bg-white">
                    <div id="historyLoading" class="py-12 text-center">
                        <div class="inline-block w-12 h-12 mb-4 border-b-2 border-black rounded-full animate-spin"></div>
                        <p class="text-gray-600">Loading history...</p>
                    </div>

                    <div id="historyContent" class="hidden">
                        <!-- Statistics Cards -->
                        <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-4">
                            <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="text-2xl text-green-600 fas fa-sign-in-alt"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-green-600">Total Check-ins</p>
                                        <p id="totalCheckIns" class="text-2xl font-bold text-green-900">0</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="text-2xl text-blue-600 fas fa-sign-out-alt"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-blue-600">Total Check-outs</p>
                                        <p id="totalCheckOuts" class="text-2xl font-bold text-blue-900">0</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="text-2xl text-purple-600 fas fa-sync-alt"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-purple-600">Total Cycles</p>
                                        <p id="totalCycles" class="text-2xl font-bold text-purple-900">0</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="text-2xl text-amber-600 fas fa-clock"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-amber-600">Total Hours in Event</p>
                                        <p id="totalHours" class="text-2xl font-bold text-amber-900">0h 0m</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Chart - Compact Version (Collapsible) -->
                        <div class="mb-6 border border-gray-200 rounded-lg overflow-hidden">
                            <button onclick="toggleChart()"
                                class="w-full px-4 py-3 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-chart-line text-gray-600"></i>
                                    <span class="text-sm font-medium text-gray-700">Activity Chart</span>
                                </div>
                                <i id="chartToggleIcon"
                                    class="fas fa-chevron-down text-gray-500 transition-transform duration-200"></i>
                            </button>
                            <div id="chartContainer" class="hidden p-4 bg-white">
                                <div class="relative" style="height: 200px;">
                                    <canvas id="historyChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="space-y-4">
                            <h4 class="text-lg font-semibold text-gray-900">Timeline</h4>
                            <div id="historyTimeline" class="space-y-3">
                                <!-- Timeline items will be inserted here -->
                            </div>
                        </div>
                    </div>

                    <div id="historyEmpty" class="hidden py-12 text-center">
                        <i class="mb-4 text-6xl text-gray-300 fas fa-history"></i>
                        <p class="text-lg font-medium text-gray-500">No history found</p>
                        <p class="text-sm text-gray-400">This user has no check-in/check-out records</p>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <button type="button" onclick="closeHistoryModal()"
                        class="px-4 py-2 font-medium text-white transition-colors duration-200 bg-black rounded-lg hover:bg-gray-800">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const eventId = document.querySelector('meta[name="event-id"]')?.getAttribute("content");
        let historyChart = null;

        // Auto-submit form on filter change
        document.getElementById('attendanceFilter')?.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        // Debounce search input
        let searchTimeout;
        document.getElementById('searchInput')?.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500);
        });

        // View logs function
        function viewLogs(userId, userName) {
            document.getElementById('modalUserName').textContent = userName;
            document.getElementById('historyModal').classList.remove('hidden');
            document.getElementById('historyLoading').classList.remove('hidden');
            document.getElementById('historyContent').classList.add('hidden');
            document.getElementById('historyEmpty').classList.add('hidden');

            fetch(`/events/${eventId}/registration-user/${userId}/logs`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('historyLoading').classList.add('hidden');

                    if (data.logs && data.logs.length > 0) {
                        displayHistory(data.logs);
                    } else {
                        document.getElementById('historyEmpty').classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error fetching logs:', error);
                    document.getElementById('historyLoading').classList.add('hidden');
                    showToast('Error loading history.', 'error');
                });
        }

        function displayHistory(logs) {
            // Calculate statistics
            const checkIns = logs.filter(log => log.action === 'check_in').length;
            const checkOuts = logs.filter(log => log.action === 'check_out').length;
            const cycles = Math.min(checkIns, checkOuts);

            // Total hours: pair check_in with next check_out, sum durations
            const sorted = [...logs].sort((a, b) => new Date(a.action_time) - new Date(b.action_time));
            let totalMinutes = 0;
            for (let i = 0; i < sorted.length - 1; i++) {
                if (sorted[i].action === 'check_in' && sorted[i + 1].action === 'check_out') {
                    const inTime = new Date(sorted[i].action_time).getTime();
                    const outTime = new Date(sorted[i + 1].action_time).getTime();
                    totalMinutes += (outTime - inTime) / (1000 * 60);
                }
            }
            const hours = Math.floor(totalMinutes / 60);
            const mins = Math.round(totalMinutes % 60);
            const totalHoursStr = hours + 'h ' + mins + 'm';

            document.getElementById('totalCheckIns').textContent = checkIns;
            document.getElementById('totalCheckOuts').textContent = checkOuts;
            document.getElementById('totalCycles').textContent = cycles;
            document.getElementById('totalHours').textContent = totalHoursStr;

            // Prepare chart data
            const chartData = prepareChartData(logs);
            renderChart(chartData);

            // Display timeline
            displayTimeline(logs);

            document.getElementById('historyContent').classList.remove('hidden');
        }

        function prepareChartData(logs) {
            const labels = logs.map(log => {
                const date = new Date(log.action_time);
                return date.toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            });

            const checkInData = logs.map(log => log.action === 'check_in' ? 1 : 0);
            const checkOutData = logs.map(log => log.action === 'check_out' ? 1 : 0);

            return {
                labels: labels,
                checkIns: checkInData,
                checkOuts: checkOutData
            };
        }

        function renderChart(chartData) {
            const ctx = document.getElementById('historyChart').getContext('2d');

            if (historyChart) {
                historyChart.destroy();
            }

            historyChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                            label: 'Check-ins',
                            data: chartData.checkIns,
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.3,
                            fill: false,
                            pointRadius: 2,
                            pointHoverRadius: 4,
                            borderWidth: 1.5
                        },
                        {
                            label: 'Check-outs',
                            data: chartData.checkOuts,
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.3,
                            fill: false,
                            pointRadius: 2,
                            pointHoverRadius: 4,
                            borderWidth: 1.5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 8,
                            bottom: 8,
                            left: 8,
                            right: 8
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 10,
                                padding: 6,
                                font: {
                                    size: 10
                                },
                                usePointStyle: true
                            }
                        },
                        title: {
                            display: false
                        },
                        tooltip: {
                            enabled: true,
                            padding: 6,
                            titleFont: {
                                size: 10
                            },
                            bodyFont: {
                                size: 10
                            },
                            displayColors: true
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45,
                                font: {
                                    size: 8
                                },
                                maxTicksLimit: 6,
                                autoSkip: true
                            },
                            grid: {
                                display: false,
                                drawBorder: false
                            }
                        },
                        y: {
                            display: false,
                            beginAtZero: true,
                            max: 1,
                            ticks: {
                                stepSize: 1,
                                font: {
                                    size: 9
                                }
                            },
                            grid: {
                                display: false,
                                drawBorder: false
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    elements: {
                        point: {
                            radius: 2,
                            hoverRadius: 4
                        },
                        line: {
                            borderWidth: 1.5
                        }
                    }
                }
            });

            // Resize chart after rendering
            setTimeout(() => {
                if (historyChart && !document.getElementById('chartContainer').classList.contains('hidden')) {
                    historyChart.resize();
                }
            }, 100);
        }

        function displayTimeline(logs) {
            const timeline = document.getElementById('historyTimeline');
            timeline.innerHTML = '';

            logs.reverse().forEach((log, index) => {
                const date = new Date(log.action_time);
                const isCheckIn = log.action === 'check_in';

                const timelineItem = document.createElement('div');
                timelineItem.className =
                    `flex items-start space-x-4 ${isCheckIn ? 'text-green-600' : 'text-blue-600'}`;

                timelineItem.innerHTML = `
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-10 h-10 ${isCheckIn ? 'bg-green-100' : 'bg-blue-100'} rounded-full">
                            <i class="fas ${isCheckIn ? 'fa-sign-in-alt' : 'fa-sign-out-alt'} ${isCheckIn ? 'text-green-600' : 'text-blue-600'}"></i>
                        </div>
                        ${index < logs.length - 1 ? `<div class="w-0.5 h-full ${isCheckIn ? 'bg-green-200' : 'bg-blue-200'} mx-auto mt-2" style="min-height: 20px;"></div>` : ''}
                    </div>
                    <div class="flex-1 pb-4">
                        <div class="flex items-center justify-between">
                            <p class="font-medium ${isCheckIn ? 'text-green-900' : 'text-blue-900'}">
                                ${isCheckIn ? 'Checked In' : 'Checked Out'}
                            </p>
                            <p class="text-sm ${isCheckIn ? 'text-green-600' : 'text-blue-600'}">
                                ${date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                                ${date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}
                            </p>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            ${date.toLocaleDateString('en-US', { weekday: 'long' })}
                        </p>
                    </div>
                `;

                timeline.appendChild(timelineItem);
            });
        }

        function closeHistoryModal() {
            document.getElementById('historyModal').classList.add('hidden');
            if (historyChart) {
                historyChart.destroy();
                historyChart = null;
            }
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeHistoryModal();
            }
        });

        // Toggle chart visibility
        function toggleChart() {
            const chartContainer = document.getElementById('chartContainer');
            const chartToggleIcon = document.getElementById('chartToggleIcon');

            if (chartContainer.classList.contains('hidden')) {
                chartContainer.classList.remove('hidden');
                chartToggleIcon.classList.remove('fa-chevron-down');
                chartToggleIcon.classList.add('fa-chevron-up');

                // Resize chart if it exists
                if (historyChart) {
                    setTimeout(() => {
                        historyChart.resize();
                    }, 150);
                }
            } else {
                chartContainer.classList.add('hidden');
                chartToggleIcon.classList.remove('fa-chevron-up');
                chartToggleIcon.classList.add('fa-chevron-down');
            }
        }
    </script>
@endpush
