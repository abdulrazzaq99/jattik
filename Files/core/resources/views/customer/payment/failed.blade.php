@extends('customer.layouts.app')

@section('panel')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="failed-icon mb-4">
                    <i class="las la-times-circle text--danger" style="font-size: 100px;"></i>
                </div>

                <h2 class="text--danger mb-3">@lang('Payment Failed')</h2>
                <p class="text-muted mb-4">@lang('We couldn\'t process your payment. Please try again.')</p>

                <div class="payment-details bg-light p-4 rounded mb-4">
                    <div class="row">
                        <div class="col-md-6 text-start mb-3">
                            <label class="text-muted">@lang('Payment Reference')</label>
                            <h6>{{ $payment->payment_reference }}</h6>
                        </div>
                        <div class="col-md-6 text-start mb-3">
                            <label class="text-muted">@lang('Payment For')</label>
                            <h6>{{ $paymentFor['description'] }}</h6>
                        </div>
                        <div class="col-md-6 text-start mb-3">
                            <label class="text-muted">@lang('Amount')</label>
                            <h6>{{ $payment->formatted_amount }}</h6>
                        </div>
                        <div class="col-md-6 text-start mb-3">
                            <label class="text-muted">@lang('Status')</label>
                            <h6>
                                <span class="badge badge--danger">{{ ucfirst($payment->status) }}</span>
                            </h6>
                        </div>
                        @if($payment->failure_reason)
                        <div class="col-12 text-start">
                            <label class="text-muted">@lang('Failure Reason')</label>
                            <h6 class="text--danger">{{ $payment->failure_reason }}</h6>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="{{ route('customer.payment.checkout', $payment->id) }}" class="btn btn--primary">
                        <i class="las la-redo"></i> @lang('Try Again')
                    </a>

                    <a href="{{ route('customer.dashboard') }}" class="btn btn--dark">
                        <i class="las la-home"></i> @lang('Go to Dashboard')
                    </a>
                </div>

                <div class="alert alert--warning mt-4">
                    <i class="las la-exclamation-triangle"></i>
                    <strong>@lang('Common Reasons for Payment Failure')</strong>
                    <ul class="text-start mt-3 mb-0">
                        <li>@lang('Insufficient funds in your account')</li>
                        <li>@lang('Incorrect card details or expired card')</li>
                        <li>@lang('Payment declined by your bank')</li>
                        <li>@lang('Network or technical issues')</li>
                    </ul>
                </div>

                <div class="alert alert--info mt-3">
                    <i class="las la-headset"></i>
                    @lang('Need help? Contact our support team at') <strong>support@{{ request()->getHost() }}</strong>
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('How to Resolve Payment Issues')</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded">
                            <h6 class="mb-2"><i class="las la-check-circle text--primary"></i> @lang('Check Your Card')</h6>
                            <p class="text-muted text-sm mb-0">@lang('Ensure your card details are correct and the card is not expired')</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded">
                            <h6 class="mb-2"><i class="las la-university text--primary"></i> @lang('Contact Your Bank')</h6>
                            <p class="text-muted text-sm mb-0">@lang('Your bank may have declined the transaction. Contact them to authorize')</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded">
                            <h6 class="mb-2"><i class="las la-credit-card text--primary"></i> @lang('Try Another Card')</h6>
                            <p class="text-muted text-sm mb-0">@lang('Use a different payment method or card')</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded">
                            <h6 class="mb-2"><i class="las la-wifi text--primary"></i> @lang('Check Connection')</h6>
                            <p class="text-muted text-sm mb-0">@lang('Ensure you have a stable internet connection')</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('style')
<style>
    .failed-icon i {
        animation: failedShake 0.5s ease-in-out;
    }

    @keyframes failedShake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }
</style>
@endpush
@endsection
