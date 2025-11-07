@extends('staff.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="las la-list"></i> @lang('All Tracking Events')
                    </h5>
                    <div>
                        <a href="{{ route('staff.tracking.dashboard') }}" class="btn btn-sm btn-outline--secondary">
                            <i class="las la-arrow-left"></i> @lang('Dashboard')
                        </a>
                        <a href="{{ route('staff.tracking.exceptions') }}" class="btn btn-sm btn--danger">
                            <i class="las la-exclamation-triangle"></i> @lang('Exceptions')
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Tracking #')</th>
                                    <th>@lang('Courier Code')</th>
                                    <th>@lang('Carrier')</th>
                                    <th>@lang('Event Type')</th>
                                    <th>@lang('Description')</th>
                                    <th>@lang('Location')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Event Time')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($events as $event)
                                    <tr class="{{ $event->is_exception ? 'bg-light-danger' : '' }}">
                                        <td>
                                            <span class="fw-bold">{{ $event->tracking_number }}</span>
                                        </td>
                                        <td>
                                            @if($event->courierInfo)
                                                <a href="{{ route('staff.courier.details', $event->courierInfo->id) }}">
                                                    {{ $event->courierInfo->code }}
                                                </a>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($event->courierConfiguration)
                                                <span class="badge badge--primary">
                                                    {{ $event->courierConfiguration->carrier_name }}
                                                </span>
                                            @else
                                                <span class="text-muted">{{ $event->carrier_name }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge
                                                @if($event->event_type == 'picked_up') badge--info
                                                @elseif($event->event_type == 'in_transit') badge--primary
                                                @elseif($event->event_type == 'out_for_delivery') badge--warning
                                                @elseif($event->event_type == 'delivered') badge--success
                                                @elseif($event->event_type == 'exception') badge--danger
                                                @else badge--secondary
                                                @endif">
                                                {{ str_replace('_', ' ', ucwords($event->event_type)) }}
                                            </span>
                                        </td>
                                        <td>{{ Str::limit($event->description, 60) }}</td>
                                        <td>{{ $event->location ?? 'N/A' }}</td>
                                        <td>
                                            @if($event->is_exception)
                                                <span class="badge badge--danger">
                                                    <i class="las la-exclamation-triangle"></i> {{ ucwords(str_replace('_', ' ', $event->exception_type)) }}
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
                @if($events->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($events) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
