@extends('AbstractReview.layouts.app')

@section('title', trans('Abstract.dashboard'))

@section('content')
<div class="space-y-8 animate-fade-in">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">@lang('Abstract.dashboard')</h2>
            <p class="text-sm text-gray-500">@lang('Abstract.dashboard_subtitle')</p>
        </div>
        <a href="{{ route('showAbstractReviewSubmissions', ['event_id' => $event->id]) }}"
           class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-white bg-black rounded-lg hover:bg-gray-800">
            <i class="mr-2 fas fa-list"></i>@lang('Abstract.view_all_submissions')
            <span class="ml-2 inline-flex items-center justify-center min-w-[1.5rem] h-6 px-1.5 text-xs font-bold text-black bg-white rounded-full">{{ $stats['total'] }}</span>
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        @php
            $statCards = [
                ['label' => trans('Abstract.stat_total'), 'value' => $stats['total'], 'icon' => 'fa-inbox', 'bg' => 'bg-gray-100', 'color' => 'text-gray-700'],
                ['label' => trans('Abstract.pending'), 'value' => $stats['pending'], 'icon' => 'fa-clock', 'bg' => 'bg-yellow-100', 'color' => 'text-yellow-600'],
                ['label' => trans('Abstract.approved'), 'value' => $stats['approved'], 'icon' => 'fa-check-circle', 'bg' => 'bg-green-100', 'color' => 'text-green-600'],
                ['label' => trans('Abstract.rejected'), 'value' => $stats['rejected'], 'icon' => 'fa-times-circle', 'bg' => 'bg-red-100', 'color' => 'text-red-600'],
                ['label' => trans('Abstract.stat_approval_rate'), 'value' => $stats['approval_rate'] . '%', 'icon' => 'fa-chart-pie', 'bg' => 'bg-blue-100', 'color' => 'text-blue-600'],
                ['label' => trans('Abstract.stat_today'), 'value' => $stats['today'], 'icon' => 'fa-calendar-day', 'bg' => 'bg-indigo-100', 'color' => 'text-indigo-600'],
            ];
        @endphp
        @foreach($statCards as $card)
        <div class="p-5 transition-shadow duration-200 bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md">
            <div class="flex items-center">
                <div class="flex items-center justify-center w-11 h-11 {{ $card['bg'] }} rounded-lg">
                    <i class="fas {{ $card['icon'] }} {{ $card['color'] }}"></i>
                </div>
                <div class="ml-3 min-w-0">
                    <p class="text-xs font-medium text-gray-500 truncate">{{ $card['label'] }}</p>
                    <p class="text-xl font-bold text-gray-900">{{ $card['value'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
            <h3 class="mb-4 text-sm font-semibold text-gray-900">@lang('Abstract.chart_status')</h3>
            <div class="relative h-56">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
        <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl lg:col-span-2">
            <h3 class="mb-4 text-sm font-semibold text-gray-900">@lang('Abstract.chart_timeline')</h3>
            <div class="relative h-56">
                <canvas id="timelineChart"></canvas>
            </div>
        </div>
        <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl lg:col-span-3">
            <h3 class="mb-4 text-sm font-semibold text-gray-900">@lang('Abstract.chart_by_abstract')</h3>
            <div class="relative h-64">
                <canvas id="abstractChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var statusChartData = @json($statusChart);
    var timelineLabels = @json($timelineLabels);
    var timelineData = @json($timelineData);
    var abstractChartData = @json($abstractChart);

    if (document.getElementById('statusChart')) {
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: statusChartData.labels,
                datasets: [{
                    data: statusChartData.data,
                    backgroundColor: ['#eab308', '#16a34a', '#dc2626'],
                    borderWidth: 0
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });
    }
    if (document.getElementById('timelineChart')) {
        new Chart(document.getElementById('timelineChart'), {
            type: 'line',
            data: {
                labels: timelineLabels,
                datasets: [{
                    label: @json(trans('Abstract.submissions')),
                    data: timelineData,
                    borderColor: '#171717',
                    backgroundColor: 'rgba(23,23,23,0.08)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    }
    if (document.getElementById('abstractChart')) {
        new Chart(document.getElementById('abstractChart'), {
            type: 'bar',
            data: {
                labels: abstractChartData.labels,
                datasets: [{
                    label: @json(trans('Abstract.submissions')),
                    data: abstractChartData.data,
                    backgroundColor: '#171717',
                    borderRadius: 6
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    }
})();
</script>
@endpush
