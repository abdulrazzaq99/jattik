@extends('customer.layouts.app')

@section('panel')
<div class="row gy-4 mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">@lang('Choose Your Plan')</h4>
            @if($currentSubscription)
                <a href="{{ route('customer.subscription.current') }}" class="btn btn--sm btn--base">
                    <i class="las la-eye"></i> @lang('My Subscription')
                </a>
            @endif
        </div>
    </div>
</div>

<div class="row gy-4">
    @foreach($plans as $plan)
    <div class="col-lg-4 col-md-6">
        <div class="card pricing-card {{ $plan->isPremium() ? 'pricing-card--featured' : '' }}">
            @if($plan->isPremium())
                <div class="pricing-card__badge">@lang('Popular')</div>
            @endif

            <div class="card-body">
                <div class="text-center mb-4">
                    <h3 class="pricing-card__title">{{ $plan->name }}</h3>
                    <div class="pricing-card__price">
                        @if($plan->isFree())
                            <span class="h2 mb-0">@lang('Free')</span>
                        @else
                            <span class="h2 mb-0">{{ number_format($plan->price, 2) }}</span>
                            <span class="text-muted">@lang('SAR')</span>
                            <span class="d-block text-sm text-muted">
                                / {{ $plan->billing_period === 'month' ? __('month') : __('year') }}
                            </span>
                        @endif
                    </div>
                    <p class="text-muted mt-3">{{ $plan->description }}</p>
                </div>

                <ul class="pricing-card__features list-unstyled">
                    @if($plan->features)
                        @foreach($plan->features as $feature)
                            <li class="py-2">
                                <i class="las la-check-circle text--success"></i>
                                <span>{{ $feature }}</span>
                            </li>
                        @endforeach
                    @endif
                </ul>

                <div class="mt-4">
                    @if($currentSubscription && $currentSubscription->plan_id == $plan->id)
                        <button class="btn btn--base btn--lg w-100" disabled>
                            <i class="las la-check-circle"></i> @lang('Current Plan')
                        </button>
                    @else
                        <form action="{{ route('customer.subscription.subscribe') }}" method="POST">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            <button type="submit" class="btn btn--{{ $plan->isPremium() ? 'primary' : 'base' }} btn--lg w-100">
                                @if($plan->isFree())
                                    <i class="las la-hand-point-right"></i> @lang('Switch to Free')
                                @else
                                    <i class="las la-crown"></i> @lang('Subscribe Now')
                                @endif
                            </button>
                        </form>
                    @endif
                </div>

                @if($plan->type === 'yearly')
                    <div class="mt-3 text-center">
                        <span class="badge badge--success">@lang('Save 200 SAR/year')</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row mt-5">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('Subscription Benefits')</h5>
            </div>
            <div class="card-body">
                <div class="row gy-4">
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="mb-3">
                                <i class="las la-shield-alt text--primary" style="font-size: 48px;"></i>
                            </div>
                            <h5>@lang('Free Insurance')</h5>
                            <p class="text-muted">@lang('Premium subscribers get free insurance up to 5,000 SAR on all shipments')</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="mb-3">
                                <i class="las la-infinity text--primary" style="font-size: 48px;"></i>
                            </div>
                            <h5>@lang('Unlimited Shipments')</h5>
                            <p class="text-muted">@lang('Send as many shipments as you need with no monthly limits')</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="mb-3">
                                <i class="las la-sync text--primary" style="font-size: 48px;"></i>
                            </div>
                            <h5>@lang('Auto-Renewal')</h5>
                            <p class="text-muted">@lang('Never worry about expiring - subscriptions renew automatically')</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.pricing-card {
    position: relative;
    border: 2px solid #e0e0e0;
    transition: all 0.3s ease;
    height: 100%;
}

.pricing-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.pricing-card--featured {
    border-color: #{{ gs('base_color') }};
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.pricing-card__badge {
    position: absolute;
    top: -12px;
    right: 20px;
    background: #{{ gs('base_color') }};
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.pricing-card__title {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 10px;
}

.pricing-card__price {
    margin: 20px 0;
}

.pricing-card__features li {
    border-bottom: 1px solid #f0f0f0;
}

.pricing-card__features li:last-child {
    border-bottom: none;
}

.pricing-card__features i {
    font-size: 20px;
    margin-right: 10px;
}
</style>
@endsection
