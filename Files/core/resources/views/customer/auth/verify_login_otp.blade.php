@extends('customer.layouts.master')

@section('content')
    <section class="account-section bg_img" style="background-image: url('{{ asset('assets/viseradmin/images/login.jpg') }}');">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-5 col-xl-6 col-lg-7 col-md-8">
                    <div class="account-card">
                        <div class="account-card__header bg--base text-center">
                            <a href="{{ route('home') }}" class="account-card__logo">
                                <img src="{{ siteLogo('dark') }}" alt="@lang('logo')">
                            </a>
                            <h4 class="account-card__title text-white mt-3 mb-0">@lang('Verify Login OTP')</h4>
                            <p class="text-white mt-2">
                                @lang('We sent a 6-digit code to your') <strong>{{ $otpMethod == 'email' ? $customer->email : $customer->mobile }}</strong>
                            </p>
                        </div>
                        <div class="account-card__body">
                            <div class="text-center mb-4">
                                <div class="user-thumb">
                                    <img src="{{ $customer->image ?? getImage('assets/images/default.png') }}" alt="@lang('customer')" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                                </div>
                                <h5 class="mt-2 mb-0">{{ $customer->fullname }}</h5>
                                <p class="text-muted small">{{ $customer->email }}</p>
                            </div>

                            <form action="{{ route('customer.login.verify.post') }}" method="POST" class="disableSubmission">
                                @csrf

                                <div class="form-group">
                                    <label class="form-label">@lang('Enter OTP Code')</label>
                                    <input type="text" class="form-control form--control text-center" name="otp_code"
                                           placeholder="000000" maxlength="6" pattern="[0-9]{6}" required autofocus>
                                    <small class="text-muted">@lang('Enter the 6-digit code sent to you')</small>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn cmn--btn w-100">@lang('Verify & Login')</button>
                                </div>

                                <div class="text-center">
                                    <p class="mb-2">@lang("Didn't receive the code?")</p>
                                    <form action="{{ route('customer.login.resend') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            <i class="las la-redo-alt"></i> @lang('Resend OTP')
                                        </button>
                                    </form>
                                </div>

                                <hr class="my-3">

                                <p class="text-center mb-0">
                                    <a href="{{ route('customer.login') }}" class="text--base">
                                        <i class="las la-arrow-left"></i> @lang('Back to Login')
                                    </a>
                                </p>
                            </form>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3 text-center">
                        <i class="las la-info-circle"></i>
                        @lang('OTP is valid for 10 minutes. You have 5 attempts to enter the correct code.')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <style>
        .account-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 50px 0;
        }
        .account-card {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .account-card__header {
            padding: 30px;
        }
        .account-card__logo img {
            max-height: 60px;
        }
        .account-card__body {
            padding: 30px;
        }
        .form-control.text-center {
            font-size: 24px;
            letter-spacing: 10px;
            font-weight: 600;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($){
            "use strict";

            // Auto-format OTP input to only accept numbers
            $('input[name="otp_code"]').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

        })(jQuery);
    </script>
@endpush
