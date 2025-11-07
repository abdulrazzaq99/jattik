@extends('customer.layouts.app')

@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Extend Shipping Date')</h5>
                </div>
                <div class="card-body">
                    {{-- Current Info --}}
                    <div class="alert alert-info">
                        <h6><i class="las la-info-circle"></i> @lang('Current Information')</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>@lang('Holding Code'):</strong> {{ $holding->holding_code }}<br>
                                <strong>@lang('Current Ship Date'):</strong> {{ $holding->scheduled_ship_date->format('M d, Y') }}
                            </div>
                            <div class="col-md-6">
                                <strong>@lang('Maximum Date'):</strong> {{ $holding->max_holding_date->format('M d, Y') }}<br>
                                <strong>@lang('Days Remaining'):</strong>
                                <span class="badge badge--warning">{{ $holding->days_remaining }} days</span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('customer.shipment.holding.extend.post', $holding->id) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <h6 class="mb-3">@lang('Select New Shipping Date')</h6>

                            @if(count($availableDates) > 0)
                                <div class="row g-3">
                                    @foreach($availableDates as $schedule)
                                        @if($schedule['date']->greaterThan($holding->scheduled_ship_date) &&
                                            $schedule['date']->lessThanOrEqualTo($holding->max_holding_date))
                                            <div class="col-md-6">
                                                <label class="schedule-option">
                                                    <input type="radio" name="new_ship_date" value="{{ $schedule['date']->format('Y-m-d') }}" required>
                                                    <div class="schedule-card">
                                                        <div class="d-flex justify-content-between">
                                                            <div>
                                                                <h6 class="mb-1">{{ $schedule['schedule_name'] }}</h6>
                                                                <p class="mb-0 text-primary fw-bold">{{ $schedule['date']->format('F d, Y') }}</p>
                                                            </div>
                                                            <div class="text-end">
                                                                <span class="badge badge--success">Available</span>
                                                            </div>
                                                        </div>
                                                        <hr class="my-2">
                                                        <small class="text-muted">
                                                            <i class="las la-clock"></i> Cutoff: {{ $schedule['cutoff_date']->format('M d') }}
                                                        </small>
                                                    </div>
                                                </label>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                @error('new_ship_date')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror

                                <div class="alert alert-warning mt-4">
                                    <i class="las la-exclamation-triangle"></i>
                                    <strong>@lang('Important:')</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>@lang('You can only extend the date before dispatch')</li>
                                        <li>@lang('New date must be within 90 days of receipt')</li>
                                        <li>@lang('Extension is subject to warehouse availability')</li>
                                    </ul>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn--primary w-100">
                                        <i class="las la-check"></i> @lang('Confirm Extension')
                                    </button>
                                    <a href="{{ route('customer.shipment.holding.show', $holding->id) }}" class="btn btn--secondary w-100 mt-2">
                                        <i class="las la-times"></i> @lang('Cancel')
                                    </a>
                                </div>
                            @else
                                <div class="alert alert-danger">
                                    <i class="las la-times-circle"></i>
                                    @lang('No available dates for extension. Please contact support.')
                                </div>
                                <a href="{{ route('customer.shipment.holding.show', $holding->id) }}" class="btn btn--secondary w-100">
                                    <i class="las la-arrow-left"></i> @lang('Back to Details')
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .schedule-option {
        display: block;
        cursor: pointer;
    }
    .schedule-option input[type="radio"] {
        display: none;
    }
    .schedule-card {
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        transition: all 0.3s;
        background: #fff;
    }
    .schedule-option input[type="radio"]:checked + .schedule-card {
        border-color: var(--base-color);
        background: rgba(var(--base-color-rgb), 0.05);
    }
    .schedule-card:hover {
        border-color: var(--base-color);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
</style>
@endpush
