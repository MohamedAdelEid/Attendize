@extends('Shared.Layouts.Master')

@section('title')
    @parent
    {{ $event->title }} - Payment Reports
@stop

@section('top_nav')
    @include('ManageEvent.Partials.TopNav')
@stop

@section('menu')
    @include('ManageEvent.Partials.Sidebar')
@stop

@section('page_title')
    <i class="ico-money mr5"></i>
    Payment Reports - Event
@stop

@section('page_header')
    <div class="col-md-9 col-sm-6">
        <div class="btn-toolbar" role="toolbar">
            <a href="{{ route('exportEventPayments', ['event_id' => $event->id]) }}?{{ request()->getQueryString() }}"
                class="btn btn-success">
                <i class="ico-download"></i> Export CSV
            </a>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <form method="get" action="{{ route('showEventPayments', ['event_id' => $event->id]) }}" class="form-inline">
            <input type="hidden" name="registration_id" value="{{ request('registration_id') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <div class="input-group">
                <input name="q" value="{{ request('q') }}" placeholder="Search (name, email)" type="text" class="form-control">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="submit"><i class="ico-search"></i></button>
                </span>
            </div>
        </form>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            {{-- Filters --}}
            <form method="get" action="{{ route('showEventPayments', ['event_id' => $event->id]) }}" class="well well-sm mb-4">
                <input type="hidden" name="q" value="{{ request('q') }}">
                <div class="row">
                    <div class="col-sm-4">
                        <label>Registration Form</label>
                        <select name="registration_id" class="form-control">
                            <option value="">-- All --</option>
                            @foreach ($registrations as $id => $name)
                                <option value="{{ $id }}" {{ request('registration_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">-- All --</option>
                            <option value="captured" {{ request('status') === 'captured' ? 'selected' : '' }}>Captured</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-sm-4" style="padding-top: 22px;">
                        <button type="submit" class="btn btn-primary">Apply</button>
                        <a href="{{ route('showEventPayments', ['event_id' => $event->id]) }}" class="btn btn-default">Reset</a>
                    </div>
                </div>
            </form>

            {{-- Summary --}}
            <div class="panel panel-default mb-4">
                <div class="panel-body">
                    <strong>Total Captured:</strong>
                    {{ number_format($totals['total_amount'], 2) }} {{ ($payments->count() && $payments->first()->currency) ? $payments->first()->currency : 'SAR' }}
                    ({{ $totals['total_count'] }} transaction{{ $totals['total_count'] !== 1 ? 's' : '' }})
                </div>
            </div>

            @if ($payments->count())
                <div class="panel">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Registration Form</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Payment Gateway</th>
                                    <th>Transaction ID</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $p)
                                    @php $u = $p->registrationUser; @endphp
                                    <tr>
                                        <td>{{ $p->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $u->first_name }} {{ $u->last_name }}</td>
                                        <td>{{ $u->email }}</td>
                                        <td>{{ $u->registration ? $u->registration->name : '-' }}</td>
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
                    <div class="panel-footer">
                        {{ $payments->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <div class="panel">
                    <div class="panel-body text-center text-muted">
                        No payments match your search.
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop
