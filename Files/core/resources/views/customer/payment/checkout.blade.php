@extends('customer.layouts.app')

@section('panel')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg--primary">
                <h5 class="card-title text-white mb-0">
                    <i class="las la-credit-card"></i> @lang('Checkout')
                </h5>
            </div>
            <div class="card-body">
                <!-- Order Summary -->
                <div class="checkout-summary mb-4">
                    <h5 class="mb-3">@lang('Order Summary')</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted">@lang('Payment For')</label>
                                <h6>{{ $paymentFor['type'] }}</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted">@lang('Reference')</label>
                                <h6>{{ $paymentFor['reference'] }}</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted">@lang('Description')</label>
                                <h6>{{ $paymentFor['description'] }}</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted">@lang('Payment Reference')</label>
                                <h6>{{ $payment->payment_reference }}</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Payment Amount -->
                <div class="payment-amount text-center py-4 mb-4" style="background: #f8f9fa; border-radius: 10px;">
                    @if($payment->hasDiscount())
                        <div class="mb-3">
                            <label class="text-muted mb-1">@lang('Original Amount')</label>
                            <h5 class="text-muted mb-0"><del>{{ number_format($payment->original_amount, 2) }} {{ $payment->currency }}</del></h5>
                        </div>
                        <div class="mb-3">
                            <label class="text-success mb-1">@lang('Discount')</label>
                            <h5 class="text--success mb-0">- {{ number_format($payment->discount_amount, 2) }} {{ $payment->currency }}</h5>
                            <small class="badge badge--success">@lang('Coupon Applied:') {{ $payment->coupon_code }}</small>
                        </div>
                    @endif
                    <label class="text-muted mb-2">@lang('Total Amount')</label>
                    <h2 class="text--primary mb-0" id="finalAmount">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</h2>
                </div>

                <!-- Coupon Code Section -->
                <div class="card mb-4" style="border: 2px dashed #{{ gs('base_color') }};">
                    <div class="card-body">
                        <h6 class="mb-3">
                            <i class="las la-tags text--primary"></i> @lang('Have a Coupon Code?')
                        </h6>
                        <div id="couponSection">
                            @if($payment->coupon_code)
                                <div class="alert alert--success mb-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="las la-check-circle"></i>
                                            <strong>{{ $payment->coupon_code }}</strong>
                                            <span class="text-muted">@lang('applied')</span>
                                            <br>
                                            <small>@lang('You saved') {{ number_format($payment->discount_amount, 2) }} {{ $payment->currency }}</small>
                                        </div>
                                        <button type="button" class="btn btn--sm btn--danger" id="removeCouponBtn">
                                            <i class="las la-times"></i> @lang('Remove')
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="input-group">
                                    <input type="text" class="form-control form--control" id="couponCode"
                                           placeholder="@lang('Enter coupon code')" style="text-transform: uppercase;">
                                    <button class="btn btn--primary" type="button" id="applyCouponBtn">
                                        <i class="las la-check"></i> @lang('Apply')
                                    </button>
                                </div>
                                <div id="couponMessage" class="mt-2"></div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Payment Method Selection -->
                <form action="{{ route('customer.payment.process', $payment->id) }}" method="POST" id="paymentForm">
                    @csrf

                    <h5 class="mb-3">@lang('Select Payment Method')</h5>

                    <div class="payment-methods">
                        <div class="row gy-3">
                            <div class="col-md-4">
                                <div class="payment-method-card">
                                    <input type="radio" name="payment_method" id="stripe" value="stripe" required>
                                    <label for="stripe" class="payment-method-label">
                                        <div class="payment-method-icon">
                                            <i class="lab la-stripe"></i>
                                        </div>
                                        <span>@lang('Credit Card')</span>
                                        <small class="text-muted d-block">@lang('Stripe')</small>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="payment-method-card">
                                    <input type="radio" name="payment_method" id="paypal" value="paypal">
                                    <label for="paypal" class="payment-method-label">
                                        <div class="payment-method-icon">
                                            <i class="lab la-paypal"></i>
                                        </div>
                                        <span>@lang('PayPal')</span>
                                        <small class="text-muted d-block">@lang('Fast & Secure')</small>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="payment-method-card">
                                    <input type="radio" name="payment_method" id="credit_card" value="credit_card">
                                    <label for="credit_card" class="payment-method-label">
                                        <div class="payment-method-icon">
                                            <i class="las la-credit-card"></i>
                                        </div>
                                        <span>@lang('Debit Card')</span>
                                        <small class="text-muted d-block">@lang('Visa/Mastercard')</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert--info mt-4">
                        <i class="las la-info-circle"></i>
                        <strong>@lang('Secure Payment')</strong>
                        <p class="mb-0 mt-2">@lang('Your payment information is encrypted and secure. We never store your card details.')</p>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('customer.dashboard') }}" class="btn btn--dark">
                            <i class="las la-arrow-left"></i> @lang('Cancel')
                        </a>
                        <button type="submit" class="btn btn--primary btn--lg">
                            <i class="las la-lock"></i> @lang('Pay Now')
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <i class="las la-shield-alt text--primary" style="font-size: 32px;"></i>
                        <h6 class="mt-2">@lang('Secure Payment')</h6>
                        <p class="text-muted text-sm">@lang('256-bit SSL encryption')</p>
                    </div>
                    <div class="col-md-4">
                        <i class="las la-lock text--primary" style="font-size: 32px;"></i>
                        <h6 class="mt-2">@lang('Private & Confidential')</h6>
                        <p class="text-muted text-sm">@lang('Your data is protected')</p>
                    </div>
                    <div class="col-md-4">
                        <i class="las la-check-circle text--primary" style="font-size: 32px;"></i>
                        <h6 class="mt-2">@lang('Money Back Guarantee')</h6>
                        <p class="text-muted text-sm">@lang('30-day refund policy')</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.payment-method-card {
    position: relative;
}

.payment-method-card input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.payment-method-label {
    display: block;
    padding: 20px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.payment-method-card input[type="radio"]:checked + .payment-method-label {
    border-color: #{{ gs('base_color') }};
    background: rgba({{ hexdec(substr(gs('base_color'), 0, 2)) }}, {{ hexdec(substr(gs('base_color'), 2, 2)) }}, {{ hexdec(substr(gs('base_color'), 4, 2)) }}, 0.1);
}

.payment-method-icon {
    font-size: 48px;
    margin-bottom: 10px;
    color: #{{ gs('base_color') }};
}

.payment-method-label:hover {
    border-color: #{{ gs('base_color') }};
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}
</style>

@push('script')
<script>
    'use strict';

    (function ($) {
        // Payment form submission
        $('#paymentForm').on('submit', function(e) {
            let selectedMethod = $('input[name="payment_method"]:checked').val();

            if (!selectedMethod) {
                e.preventDefault();
                alert('@lang("Please select a payment method")');
                return false;
            }

            // Show loading state
            $(this).find('button[type="submit"]').html('<i class="las la-spinner la-spin"></i> @lang("Processing...")').prop('disabled', true);
        });

        // Apply Coupon
        $('#applyCouponBtn').on('click', function() {
            let couponCode = $('#couponCode').val().trim().toUpperCase();

            if (!couponCode) {
                showCouponMessage('error', '@lang("Please enter a coupon code")');
                return;
            }

            let $btn = $(this);
            let originalHtml = $btn.html();

            $btn.html('<i class="las la-spinner la-spin"></i>').prop('disabled', true);

            $.ajax({
                url: '{{ route("customer.payment.apply.coupon", $payment->id) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    coupon_code: couponCode
                },
                success: function(response) {
                    if (response.success) {
                        showCouponMessage('success', response.message);

                        // Update the amount display
                        updateAmountDisplay(response.data);

                        // Reload page after 1 second to show updated coupon section
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showCouponMessage('error', response.message);
                        $btn.html(originalHtml).prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    let message = '@lang("Error applying coupon. Please try again.")';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showCouponMessage('error', message);
                    $btn.html(originalHtml).prop('disabled', false);
                }
            });
        });

        // Remove Coupon
        $('#removeCouponBtn').on('click', function() {
            if (!confirm('@lang("Are you sure you want to remove this coupon?")')) {
                return;
            }

            let $btn = $(this);
            let originalHtml = $btn.html();

            $btn.html('<i class="las la-spinner la-spin"></i>').prop('disabled', true);

            $.ajax({
                url: '{{ route("customer.payment.remove.coupon", $payment->id) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Reload page to show updated state
                        window.location.reload();
                    } else {
                        alert(response.message);
                        $btn.html(originalHtml).prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    let message = '@lang("Error removing coupon. Please try again.")';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                    $btn.html(originalHtml).prop('disabled', false);
                }
            });
        });

        // Allow Enter key to apply coupon
        $('#couponCode').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('#applyCouponBtn').click();
            }
        });

        // Helper function to show coupon messages
        function showCouponMessage(type, message) {
            let alertClass = type === 'success' ? 'alert--success' : 'alert--danger';
            let icon = type === 'success' ? 'las la-check-circle' : 'las la-times-circle';

            let html = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="${icon}"></i> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;

            $('#couponMessage').html(html);

            // Auto-dismiss success messages after 3 seconds
            if (type === 'success') {
                setTimeout(function() {
                    $('#couponMessage .alert').fadeOut('slow', function() {
                        $(this).remove();
                    });
                }, 3000);
            }
        }

        // Helper function to update amount display
        function updateAmountDisplay(data) {
            let html = `
                <div class="mb-3">
                    <label class="text-muted mb-1">@lang('Original Amount')</label>
                    <h5 class="text-muted mb-0"><del>${data.original_amount} {{ $payment->currency }}</del></h5>
                </div>
                <div class="mb-3">
                    <label class="text-success mb-1">@lang('Discount')</label>
                    <h5 class="text--success mb-0">- ${data.discount_amount} {{ $payment->currency }}</h5>
                    <small class="badge badge--success">@lang('You saved') ${data.savings} {{ $payment->currency }}</small>
                </div>
                <label class="text-muted mb-2">@lang('Total Amount')</label>
                <h2 class="text--primary mb-0">${data.final_amount} {{ $payment->currency }}</h2>
            `;

            $('.payment-amount').html(html);
        }
    })(jQuery);
</script>
@endpush
@endsection
