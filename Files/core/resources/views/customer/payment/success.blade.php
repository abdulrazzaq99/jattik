@extends('customer.layouts.app')

@section('panel')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="success-icon mb-4">
                    <i class="las la-check-circle text--success" style="font-size: 100px;"></i>
                </div>

                <h2 class="text--success mb-3">@lang('Payment Successful!')</h2>
                <p class="text-muted mb-4">@lang('Thank you! Your payment has been processed successfully.')</p>

                <div class="payment-details bg-light p-4 rounded mb-4">
                    <div class="row">
                        <div class="col-md-6 text-start mb-3">
                            <label class="text-muted">@lang('Payment Reference')</label>
                            <h6>{{ $payment->payment_reference }}</h6>
                        </div>
                        <div class="col-md-6 text-start mb-3">
                            <label class="text-muted">@lang('Transaction ID')</label>
                            <h6>{{ $payment->transaction_id }}</h6>
                        </div>
                        <div class="col-md-6 text-start mb-3">
                            <label class="text-muted">@lang('Payment For')</label>
                            <h6>{{ $paymentFor['description'] }}</h6>
                        </div>
                        <div class="col-md-6 text-start mb-3">
                            <label class="text-muted">@lang('Amount Paid')</label>
                            <h6 class="text--success">{{ $payment->formatted_amount }}</h6>
                        </div>
                        <div class="col-md-6 text-start mb-3">
                            <label class="text-muted">@lang('Payment Method')</label>
                            <h6>{{ ucfirst($payment->payment_method) }}</h6>
                        </div>
                        <div class="col-md-6 text-start mb-3">
                            <label class="text-muted">@lang('Date & Time')</label>
                            <h6>{{ $payment->paid_at->format('M d, Y h:i A') }}</h6>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    @if($payment->payable_type === 'App\Models\CustomerSubscription')
                        <a href="{{ route('customer.subscription.current') }}" class="btn btn--primary">
                            <i class="las la-crown"></i> @lang('View Subscription')
                        </a>
                    @endif

                    <a href="{{ route('customer.payment.invoice', $payment->id) }}" class="btn btn--base">
                        <i class="las la-file-invoice"></i> @lang('View Invoice')
                    </a>

                    <a href="{{ route('customer.dashboard') }}" class="btn btn--dark">
                        <i class="las la-home"></i> @lang('Go to Dashboard')
                    </a>
                </div>

                <div class="alert alert--success mt-4">
                    <i class="las la-envelope"></i>
                    @lang('A confirmation email has been sent to your registered email address.')
                </div>
            </div>
        </div>

        <!-- Next Steps -->
        @if($payment->payable_type === 'App\Models\CustomerSubscription')
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('What\'s Next?')</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="py-2">
                        <i class="las la-check-circle text--success"></i>
                        <span>@lang('Your premium subscription is now active')</span>
                    </li>
                    <li class="py-2">
                        <i class="las la-check-circle text--success"></i>
                        <span>@lang('You can now enjoy unlimited shipments')</span>
                    </li>
                    <li class="py-2">
                        <i class="las la-check-circle text--success"></i>
                        <span>@lang('All your shipments are automatically insured for free')</span>
                    </li>
                    <li class="py-2">
                        <i class="las la-check-circle text--success"></i>
                        <span>@lang('Your subscription will auto-renew before expiry')</span>
                    </li>
                </ul>
            </div>
        </div>
        @endif
    </div>
</div>

@push('style')
<style>
    .success-icon i {
        animation: successPulse 1s ease-in-out;
    }

    @keyframes successPulse {
        0% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); opacity: 1; }
    }
</style>
@endpush
@endsection
