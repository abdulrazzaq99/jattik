@extends('staff.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-danger">
                <div class="card-header bg--danger d-flex justify-content-between align-items-center">
                    <h5 class="card-title text-white mb-0">
                        <i class="las la-exclamation-triangle"></i> @lang('Tracking Exceptions')
                    </h5>
                    <a href="{{ route('staff.tracking.dashboard') }}" class="btn btn-sm btn-light">
                        <i class="las la-arrow-left"></i> @lang('Dashboard')
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Priority')</th>
                                    <th>@lang('Tracking #')</th>
                                    <th>@lang('Courier')</th>
                                    <th>@lang('Customer')</th>
                                    <th>@lang('Exception Type')</th>
                                    <th>@lang('Description')</th>
                                    <th>@lang('Notified')</th>
                                    <th>@lang('Occurred')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($exceptions as $exception)
                                    <tr class="table-{{ !$exception->customer_notified ? 'warning' : 'light' }}">
                                        <td>
                                            @if($exception->exception_type == 'lost' || $exception->exception_type == 'damaged')
                                                <span class="badge badge--danger">@lang('High')</span>
                                            @elseif($exception->exception_type == 'delay')
                                                <span class="badge badge--warning">@lang('Medium')</span>
                                            @else
                                                <span class="badge badge--info">@lang('Low')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold text--danger">{{ $exception->tracking_number }}</span>
                                        </td>
                                        <td>
                                            @if($exception->courierInfo)
                                                <a href="{{ route('staff.courier.details', $exception->courierInfo->id) }}">
                                                    {{ $exception->courierInfo->code }}
                                                </a>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($exception->courierInfo)
                                                <strong>{{ $exception->courierInfo->senderCustomer->fullname ?? 'N/A' }}</strong><br>
                                                <small class="text-muted">{{ $exception->courierInfo->senderCustomer->mobile ?? '' }}</small>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($exception->exception_type == 'delay')
                                                <span class="badge badge--warning">
                                                    <i class="las la-clock"></i> @lang('Delay')
                                                </span>
                                            @elseif($exception->exception_type == 'wrong_address')
                                                <span class="badge badge--danger">
                                                    <i class="las la-map-marker-alt"></i> @lang('Wrong Address')
                                                </span>
                                            @elseif($exception->exception_type == 'damaged')
                                                <span class="badge badge--danger">
                                                    <i class="las la-box-open"></i> @lang('Damaged')
                                                </span>
                                            @elseif($exception->exception_type == 'lost')
                                                <span class="badge badge--danger">
                                                    <i class="las la-question-circle"></i> @lang('Lost')
                                                </span>
                                            @elseif($exception->exception_type == 'refused')
                                                <span class="badge badge--warning">
                                                    <i class="las la-ban"></i> @lang('Refused')
                                                </span>
                                            @else
                                                <span class="badge badge--secondary">@lang('Other')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ Str::limit($exception->description, 80) }}</small>
                                        </td>
                                        <td class="text-center">
                                            @if($exception->customer_notified)
                                                <span class="badge badge--success">
                                                    <i class="las la-check-circle"></i> @lang('Yes')
                                                </span>
                                            @else
                                                <span class="badge badge--danger">
                                                    <i class="las la-times-circle"></i> @lang('No')
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ showDateTime($exception->event_time) }}<br>
                                            <small class="text-muted">{{ diffForHumans($exception->event_time) }}</small>
                                        </td>
                                        <td>
                                            @if(!$exception->customer_notified)
                                                <form action="{{ route('staff.tracking.exceptions.resolve', $exception->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn--success"
                                                            onclick="return confirm('@lang("Mark this exception as notified?")')">
                                                        <i class="las la-check"></i> @lang('Mark Notified')
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text--success">
                                                    <i class="las la-check-circle"></i> @lang('Resolved')
                                                </span>
                                            @endif

                                            @if($exception->courierInfo)
                                                <form action="{{ route('staff.tracking.refresh', $exception->courierInfo->id) }}" method="POST" class="mt-1">
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
                                            <i class="las la-check-circle text--success" style="font-size: 3rem;"></i><br>
                                            @lang('No exceptions found - All shipments are on track!')
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($exceptions->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($exceptions) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Exception Statistics --}}
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <i class="las la-exclamation-triangle" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $totalExceptions ?? 0 }}</h3>
                    <p class="mb-0">@lang('Total Exceptions')</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="las la-bell" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $unnotifiedCount ?? 0 }}</h3>
                    <p class="mb-0">@lang('Unnotified')</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="las la-check-circle" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $notifiedCount ?? 0 }}</h3>
                    <p class="mb-0">@lang('Notified')</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="las la-calendar-day" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $todayExceptions ?? 0 }}</h3>
                    <p class="mb-0">@lang('Today')</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    // Auto-refresh every 1 minute for urgent exceptions
    setTimeout(function() {
        location.reload();
    }, 60000);
</script>
@endpush
