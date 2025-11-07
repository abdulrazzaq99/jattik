@extends('staff.layouts.app')

@section('panel')
    {{-- Statistics Widgets --}}
    <div class="row gy-4">
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('staff.courier.manage.list') }}" icon="las la-box" title="Total Shipments"
                value="{{ $statistics['totalShipments'] }}" bg="primary" outline="true"/>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('staff.courier.upcoming') }}" icon="las la-truck" title="Active Shipments"
                value="{{ $statistics['activeShipments'] }}" bg="success" outline="true" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('staff.tracking.exceptions') }}" icon="las la-exclamation-triangle"
                title="Exceptions Today" value="{{ $statistics['exceptionsToday'] }}" bg="danger" outline="true" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('staff.tracking.exceptions') }}" icon="las la-bell"
                title="Unnotified Exceptions" value="{{ $statistics['unnotifiedExceptions'] }}" bg="warning" outline="true" />
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-tasks"></i> @lang('Quick Actions')
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('staff.tracking.events') }}" class="btn btn-outline--primary w-100 mb-2">
                                <i class="las la-list"></i> @lang('View All Events')
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('staff.tracking.exceptions') }}" class="btn btn-outline--danger w-100 mb-2">
                                <i class="las la-exclamation-triangle"></i> @lang('View Exceptions')
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('staff.courier.upcoming') }}" class="btn btn-outline--info w-100 mb-2">
                                <i class="las la-shipping-fast"></i> @lang('Upcoming Deliveries')
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('staff.claims.pending') }}" class="btn btn-outline--warning w-100 mb-2">
                                <i class="las la-file-invoice-dollar"></i> @lang('Pending Claims')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Tracking Events --}}
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="las la-history"></i> @lang('Recent Tracking Events')
                    </h5>
                    <a href="{{ route('staff.tracking.events') }}" class="btn btn-sm btn--primary">
                        @lang('View All')
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Tracking #')</th>
                                    <th>@lang('Courier')</th>
                                    <th>@lang('Event')</th>
                                    <th>@lang('Location')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Time')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentEvents ?? [] as $event)
                                    <tr class="{{ $event->is_exception ? 'table-danger' : '' }}">
                                        <td>
                                            <span class="fw-bold">{{ $event->tracking_number }}</span>
                                        </td>
                                        <td>
                                            @if($event->courierInfo)
                                                {{ $event->courierInfo->code }}<br>
                                                <small class="text-muted">{{ $event->courierInfo->senderCustomer->fullname ?? 'N/A' }}</small>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $event->event_type }}<br>
                                            <small class="text-muted">{{ Str::limit($event->description, 40) }}</small>
                                        </td>
                                        <td>{{ $event->location ?? 'N/A' }}</td>
                                        <td>
                                            @if($event->is_exception)
                                                <span class="badge badge--danger">
                                                    <i class="las la-exclamation-triangle"></i> @lang('Exception')
                                                </span>
                                                @if(!$event->customer_notified)
                                                    <br><span class="badge badge--warning mt-1">@lang('Not Notified')</span>
                                                @endif
                                            @else
                                                <span class="badge badge--success">@lang('Normal')</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ showDateTime($event->event_time) }}<br>
                                            <small class="text-muted">{{ diffForHumans($event->event_time) }}</small>
                                        </td>
                                        <td>
                                            @if($event->courierInfo)
                                                <form action="{{ route('staff.tracking.refresh', $event->courierInfo->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline--info">
                                                        <i class="las la-sync"></i> @lang('Refresh')
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">
                                            @lang('No tracking events found')
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Exception Alerts --}}
    @if(isset($unnotifiedExceptions) && $unnotifiedExceptions->count() > 0)
        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="alert alert-danger">
                    <h5>
                        <i class="las la-exclamation-triangle"></i>
                        @lang('Unnotified Exceptions') ({{ $unnotifiedExceptions->count() }})
                    </h5>
                    <p class="mb-2">@lang('The following shipments have exceptions that customers have not been notified about:')</p>
                    <ul class="mb-0">
                        @foreach($unnotifiedExceptions->take(5) as $exception)
                            <li>
                                <strong>{{ $exception->tracking_number }}</strong> -
                                {{ $exception->exception_type }} -
                                {{ $exception->description }}
                                <form action="{{ route('staff.tracking.exceptions.resolve', $exception->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn--success ms-2">
                                        <i class="las la-check"></i> @lang('Mark as Notified')
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                    @if($unnotifiedExceptions->count() > 5)
                        <a href="{{ route('staff.tracking.exceptions') }}" class="btn btn-sm btn--warning mt-2">
                            @lang('View All') ({{ $unnotifiedExceptions->count() }})
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif
@endsection

@push('script')
<script>
    // Auto-refresh every 2 minutes
    setTimeout(function() {
        location.reload();
    }, 120000);
</script>
@endpush
