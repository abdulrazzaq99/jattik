@extends('customer.layouts.app')

@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-8 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="las la-search-location text--base"></i> @lang('Track Your Courier')
                    </h5>

                    <form action="{{ route('customer.track') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="tracking_code" class="form-control form--control" placeholder="@lang('Enter tracking code')" value="{{ request()->tracking_code }}" required>
                            <button type="submit" class="btn btn--primary">
                                <i class="las la-search"></i> @lang('Track')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($courier)
        <div class="row">
            <div class="col-lg-12 mb-30">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="mb-1">@lang('Tracking Code'): <span class="text--base">{{ $courier->code }}</span></h4>
                                <p class="text-muted mb-0">
                                    @lang('Created on') {{ showDateTime($courier->created_at, 'd M, Y h:i A') }}
                                </p>
                            </div>
                            <div>
                                @if($courier->status == 0)
                                    <span class="badge badge--warning badge--lg">@lang('In Queue')</span>
                                @elseif($courier->status == 1)
                                    <span class="badge badge--primary badge--lg">@lang('In Transit')</span>
                                @elseif($courier->status == 2)
                                    <span class="badge badge--info badge--lg">@lang('Delivery Queue')</span>
                                @elseif($courier->status == 3)
                                    <span class="badge badge--success badge--lg">@lang('Delivered')</span>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card bg--light">
                                    <div class="card-body">
                                        <h6 class="mb-3">
                                            <i class="las la-user-circle text--success"></i> @lang('Sender Information')
                                        </h6>
                                        <ul class="list-unstyled">
                                            <li class="mb-2">
                                                <strong>@lang('Name'):</strong> {{ $courier->senderCustomer->fullname ?? 'N/A' }}
                                            </li>
                                            <li class="mb-2">
                                                <strong>@lang('Email'):</strong> {{ $courier->senderCustomer->email ?? 'N/A' }}
                                            </li>
                                            <li class="mb-2">
                                                <strong>@lang('Mobile'):</strong> {{ $courier->senderCustomer->mobile ?? 'N/A' }}
                                            </li>
                                            <li class="mb-2">
                                                <strong>@lang('Branch'):</strong> {{ $courier->senderBranch->name ?? 'N/A' }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card bg--light">
                                    <div class="card-body">
                                        <h6 class="mb-3">
                                            <i class="las la-user-circle text--primary"></i> @lang('Receiver Information')
                                        </h6>
                                        <ul class="list-unstyled">
                                            <li class="mb-2">
                                                <strong>@lang('Name'):</strong> {{ $courier->receiverCustomer->fullname ?? 'N/A' }}
                                            </li>
                                            <li class="mb-2">
                                                <strong>@lang('Email'):</strong> {{ $courier->receiverCustomer->email ?? 'N/A' }}
                                            </li>
                                            <li class="mb-2">
                                                <strong>@lang('Mobile'):</strong> {{ $courier->receiverCustomer->mobile ?? 'N/A' }}
                                            </li>
                                            <li class="mb-2">
                                                <strong>@lang('Branch'):</strong> {{ $courier->receiverBranch->name ?? 'N/A' }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card bg--light mt-3">
                            <div class="card-body">
                                <h6 class="mb-3">
                                    <i class="las la-truck text--info"></i> @lang('Courier Status Timeline')
                                </h6>
                                <div class="tracking-timeline">
                                    <div class="timeline-item {{ $courier->status >= 0 ? 'active' : '' }}">
                                        <div class="timeline-icon">
                                            <i class="las la-check-circle"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6>@lang('Order Placed')</h6>
                                            <p class="text-muted small mb-0">@lang('Courier created and in queue')</p>
                                            @if($courier->status >= 0)
                                                <p class="text-muted small mb-0">{{ showDateTime($courier->created_at) }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="timeline-item {{ $courier->status >= 1 ? 'active' : '' }}">
                                        <div class="timeline-icon">
                                            <i class="las la-shipping-fast"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6>@lang('In Transit')</h6>
                                            <p class="text-muted small mb-0">@lang('Courier dispatched and on the way')</p>
                                        </div>
                                    </div>

                                    <div class="timeline-item {{ $courier->status >= 2 ? 'active' : '' }}">
                                        <div class="timeline-icon">
                                            <i class="las la-warehouse"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6>@lang('At Destination')</h6>
                                            <p class="text-muted small mb-0">@lang('Courier arrived at destination branch')</p>
                                        </div>
                                    </div>

                                    <div class="timeline-item {{ $courier->status >= 3 ? 'active' : '' }}">
                                        <div class="timeline-icon">
                                            <i class="las la-box-open"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6>@lang('Delivered')</h6>
                                            <p class="text-muted small mb-0">@lang('Courier successfully delivered')</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif(request()->has('tracking_code'))
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="las la-box-open text-muted" style="font-size: 64px;"></i>
                        <h5 class="mt-3">@lang('Courier Not Found')</h5>
                        <p class="text-muted">@lang('No courier found with the tracking code you entered, or you do not have access to this courier.')</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('style')
<style>
    .tracking-timeline {
        position: relative;
        padding-left: 40px;
    }
    .tracking-timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e0e0e0;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 30px;
        opacity: 0.5;
    }
    .timeline-item.active {
        opacity: 1;
    }
    .timeline-item.active .timeline-icon {
        background: hsl(var(--base));
        color: white;
    }
    .timeline-icon {
        position: absolute;
        left: -40px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        z-index: 1;
    }
    .timeline-content h6 {
        margin-bottom: 5px;
        font-weight: 600;
    }
    .badge--lg {
        padding: 8px 16px;
        font-size: 14px;
    }
</style>
@endpush
