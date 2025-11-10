@extends('customer.layouts.master')

@section('content')
    <div class="login-main" style="background-image: url('{{ asset('assets/viseradmin/images/login.jpg') }}')">
        <div class="container custom-container">
            <div class="row justify-content-center">
                <div class="col-xxl-5 col-xl-6 col-lg-7 col-md-8">
                    <div class="login-area">
                        <div class="login-wrapper">
                            <div class="login-wrapper__top">
                                <h3 class="title text-white">@lang('Verify Your Login')</h3>
                                <p class="text-white">
                                    @lang('We sent a 6-digit code to') <br>
                                    <strong>{{ $otpMethod == 'email' ? $customer->email : $customer->mobile }}</strong>
                                </p>
                            </div>
                            <div class="login-wrapper__body">
                                <div class="text-center mb-4">
                                    <h5 class="text-white mb-2">{{ $customer->fullname }}</h5>
                                    <p class="text-white-50 small">{{ $customer->email }}</p>
                                </div>

                                <form action="{{ route('customer.login.verify.post') }}" method="POST" class="cmn-form mt-30">
                                    @csrf

                                    <div class="form-group">
                                        <label>@lang('Enter OTP Code')</label>
                                        <input type="text" class="form-control text-center otp-input" name="otp_code"
                                               placeholder="000000" maxlength="6" pattern="[0-9]{6}" required autofocus>
                                        <small class="text-white-50">@lang('Enter the 6-digit code sent to you')</small>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn cmn-btn w-100">
                                            @lang('Verify & Login')
                                        </button>
                                    </div>

                                    <div class="text-center mt-3">
                                        <p class="text-white mb-2">@lang("Didn't receive the code?")</p>
                                        <form action="{{ route('customer.login.resend') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-light">
                                                <i class="las la-redo-alt"></i> @lang('Resend OTP')
                                            </button>
                                        </form>
                                    </div>

                                    <hr class="my-4" style="border-color: rgba(255,255,255,0.2)">

                                    <p class="text-center text-white mb-0">
                                        <a href="{{ route('customer.login') }}" class="text-white fw-bold">
                                            <i class="las la-arrow-left"></i> @lang('Back to Login')
                                        </a>
                                    </p>
                                </form>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3 text-center">
                            <i class="las la-info-circle"></i>
                            @lang('OTP is valid for 1 minute. You have 3 attempts to enter the correct code.')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .otp-input {
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
