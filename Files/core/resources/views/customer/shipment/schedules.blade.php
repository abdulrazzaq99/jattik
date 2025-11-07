@extends('customer.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Available Shipping Schedules')</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="las la-info-circle"></i>
                        @lang('Select your preferred shipping date. Packages must be ready 2-3 days before the scheduled date.')
                    </div>

                    @if(count($availableDates) > 0)
                        <div class="row g-4">
                            @foreach($availableDates as $schedule)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card schedule-card border">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div>
                                                    <h6 class="mb-1">{{ $schedule['schedule_name'] }}</h6>
                                                    <small class="text-muted">Day {{ $schedule['day_of_month'] }} of Month</small>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge badge--primary">Available</span>
                                                </div>
                                            </div>

                                            <div class="schedule-info">
                                                <div class="mb-2">
                                                    <i class="las la-calendar text-primary"></i>
                                                    <strong>@lang('Ship Date'):</strong><br>
                                                    <span class="ms-4">{{ $schedule['date']->format('F d, Y') }}</span>
                                                </div>

                                                <div class="mb-2">
                                                    <i class="las la-clock text-warning"></i>
                                                    <strong>@lang('Cutoff Date'):</strong><br>
                                                    <span class="ms-4">{{ $schedule['cutoff_date']->format('F d, Y') }}</span>
                                                </div>

                                                <div class="mb-3">
                                                    <i class="las la-hourglass-half text-info"></i>
                                                    <strong>@lang('Days Until'):</strong><br>
                                                    <span class="ms-4">{{ now()->diffInDays($schedule['date']) }} days</span>
                                                </div>
                                            </div>

                                            @php
                                                $daysUntilCutoff = now()->diffInDays($schedule['cutoff_date'], false);
                                            @endphp

                                            @if($daysUntilCutoff > 0)
                                                <div class="alert alert-success py-2 mb-0">
                                                    <small>
                                                        <i class="las la-check-circle"></i>
                                                        @lang('Available - ') {{ $daysUntilCutoff }} @lang(' days to prepare')
                                                    </small>
                                                </div>
                                            @else
                                                <div class="alert alert-warning py-2 mb-0">
                                                    <small>
                                                        <i class="las la-exclamation-triangle"></i>
                                                        @lang('Cutoff period - contact staff')
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="las la-calendar-times" style="font-size: 64px; color: #ccc;"></i>
                            <p class="text-muted mt-3">@lang('No shipping schedules available at the moment.')</p>
                        </div>
                    @endif

                    <div class="mt-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="las la-question-circle"></i> @lang('How It Works')</h6>
                                <ol class="mb-0">
                                    <li>@lang('Select a shipping schedule when creating your shipment')</li>
                                    <li>@lang('Your packages will be held at our warehouse until the scheduled date')</li>
                                    <li>@lang('Maximum holding period is 90 days')</li>
                                    <li>@lang('You can extend the shipping date before dispatch')</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .schedule-card {
        transition: all 0.3s ease;
    }
    .schedule-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .schedule-info {
        font-size: 14px;
    }
    .schedule-info i {
        font-size: 18px;
    }
</style>
@endpush
