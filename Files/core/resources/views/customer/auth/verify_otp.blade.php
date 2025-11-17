@extends('customer.layouts.master')

@section('content')
    <div class="login-main" style="background-image: url('{{ asset('assets/viseradmin/images/login.jpg') }}')">
        <div class="container custom-container">
            <div class="row justify-content-center">
                <div class="col-xxl-5 col-xl-6 col-lg-7 col-md-8">
                    <div class="login-area">
                        <div class="login-wrapper">
                            <div class="login-wrapper__top">
                                <h3 class="title text-white">@lang('Verify Your Account')</h3>
                                <p class="text-white">
                                    @lang('We sent a 6-digit code to') <br>
                                    <strong>{{ $registrationData['otp_method'] == 'email' ? $registrationData['email'] : $registrationData['mobile'] }}</strong>
                                </p>
                            </div>
                            <div class="login-wrapper__body">
                                @if ($errors->any() || session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        @if(session('error'))
                                            <p class="mb-0"><i class="las la-exclamation-circle"></i> {{ session('error') }}</p>
                                        @endif
                                        @if ($errors->any())
                                            <ul class="mb-0 ps-3">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="las la-check-circle"></i> {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <form action="{{ route('customer.register.verify.post') }}" method="POST" class="cmn-form mt-30">
                                    @csrf

                                    @if(isset($otpLog) && $otpLog)
                                        <div class="alert alert-info text-center">
                                            <small>
                                                <i class="las la-clock"></i>
                                                @lang('Attempts:') <strong>{{ $otpLog->attempts ?? 0 }}/3</strong>
                                                @if($otpLog->expires_at)
                                                    | @lang('Expires in:') <strong id="countdown" data-expires="{{ $otpLog->expires_at->toIso8601String() }}">{{ $otpLog->expires_at->diffForHumans(null, true) }}</strong>
                                                @endif
                                            </small>
                                        </div>
                                    @endif

                                    <div class="form-group">
                                        <label class="text-white">@lang('Enter OTP Code')</label>
                                        <input type="text" class="form-control text-center otp-input @error('otp_code') is-invalid @enderror" name="otp_code"
                                               placeholder="000000" maxlength="6" pattern="[0-9]{6}" required autofocus>
                                        @error('otp_code')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @else
                                            <small class="text-white-50">@lang('Enter the 6-digit code sent to you')</small>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn cmn-btn w-100" id="verifyBtn">
                                            <span class="btn-text">@lang('Verify & Complete Registration')</span>
                                            <span class="btn-loader d-none">
                                                <i class="las la-spinner la-spin"></i> @lang('Verifying...')
                                            </span>
                                        </button>
                                    </div>
                                </form>

                                <div class="text-center mt-3">
                                    <p class="text-white mb-2">@lang("Didn't receive the code?")</p>
                                    <form action="{{ route('customer.register.resend') }}" method="POST" class="d-inline" id="resendForm">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-light resend-btn" id="resendBtn">
                                            <span class="btn-text">
                                                <i class="las la-redo-alt"></i> @lang('Resend OTP')
                                            </span>
                                            <span class="btn-loader d-none">
                                                <i class="las la-spinner la-spin me-1"></i> @lang('Sending...')
                                            </span>
                                        </button>
                                    </form>

                                    <!-- Small inline loading indicator for resend -->
                                    <div id="resendLoading" class="resend-loading-inline d-none">
                                        <small class="text-white-50">
                                            <i class="las la-circle-notch la-spin"></i> @lang('Sending new code...')
                                        </small>
                                    </div>
                                </div>

                                <!-- Loading Overlay -->
                                <div class="loading-overlay" id="loadingOverlay">
                                    <div class="loading-spinner">
                                        <div class="spinner-border text-light" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="text-white mt-3">@lang('Verifying your code...')</p>
                                    </div>
                                </div>

                                <hr class="my-4" style="border-color: rgba(255,255,255,0.2)">

                                <p class="text-center text-white mb-0">
                                    <a href="{{ route('customer.register') }}" class="text-white fw-bold">
                                        <i class="las la-arrow-left"></i> @lang('Back to Registration')
                                    </a>
                                </p>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3 text-center">
                            <i class="las la-info-circle"></i>
                            @lang('OTP is valid for 2 minutes. You have 3 attempts to enter the correct code.')
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
        .alert-danger {
            background-color: rgba(235, 34, 34, 0.1);
            border: 1px solid #eb2222;
            color: #fff;
        }
        .alert-danger ul {
            list-style-type: disc;
        }
        .alert-danger li {
            color: #fff;
        }
        .alert-success {
            background-color: rgba(40, 199, 111, 0.1);
            border: 1px solid #28c76f;
            color: #fff;
        }
        .alert-info {
            background-color: rgba(30, 159, 242, 0.1);
            border: 1px solid #1e9ff2;
            color: #fff;
        }
        .invalid-feedback {
            display: block;
            color: #ff6b6b !important;
            font-weight: 500;
            margin-top: 0.25rem;
        }
        .form-control.is-invalid {
            border-color: #eb2222;
        }
        .form-control.is-invalid:focus {
            border-color: #eb2222;
            box-shadow: 0 0 0 0.2rem rgba(235, 34, 34, 0.25);
        }
        .btn-outline-light:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Loading Overlay */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        .loading-overlay.show {
            display: flex;
        }
        .loading-spinner {
            text-align: center;
        }
        .loading-spinner .spinner-border {
            width: 3rem;
            height: 3rem;
            border-width: 0.3rem;
        }

        /* Button Loading State */
        .btn-loader {
            display: none;
        }
        .btn.loading .btn-text {
            display: none;
        }
        .btn.loading .btn-loader {
            display: inline-block;
        }
        .btn.loading {
            opacity: 0.7;
            pointer-events: none;
        }

        /* Resend Button Enhanced Loading */
        .resend-btn.loading {
            background-color: rgba(255, 255, 255, 0.2) !important;
            border-color: rgba(255, 255, 255, 0.4) !important;
            cursor: not-allowed !important;
        }
        .resend-loading-inline {
            margin-top: 0.5rem;
            padding: 0.5rem;
            background-color: rgba(30, 159, 242, 0.1);
            border-radius: 0.25rem;
            border: 1px solid rgba(30, 159, 242, 0.3);
        }
        .resend-loading-inline.d-none {
            display: none !important;
        }

        .la-spin {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
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

            // Handle OTP verification form submission
            $('form.cmn-form').on('submit', function(e) {
                var form = $(this);
                var submitBtn = $('#verifyBtn');

                // Check if form is valid (HTML5 validation)
                if (form[0].checkValidity() === false) {
                    return true; // Let browser handle validation
                }

                // Show loading state
                submitBtn.addClass('loading');
                $('#loadingOverlay').addClass('show');

                // Disable form inputs
                form.find('input, button').prop('disabled', true);
            });

            // Handle resend OTP form submission
            $('#resendForm').on('submit', function(e) {
                var form = $(this);
                var resendBtn = $('#resendBtn');
                var resendLoading = $('#resendLoading');

                // Show loading state
                resendBtn.addClass('loading');
                resendLoading.removeClass('d-none');

                // Disable button
                resendBtn.prop('disabled', true);

                // Hide the "Didn't receive code?" text temporarily
                resendBtn.closest('.text-center').find('p').first().fadeOut(200);

                // Re-enable after response (in case of error, page will reload)
                setTimeout(function() {
                    resendBtn.removeClass('loading').prop('disabled', false);
                    resendLoading.addClass('d-none');
                    resendBtn.closest('.text-center').find('p').first().fadeIn(200);
                }, 5000);
            });

            // Countdown timer for OTP expiry
            var countdownElement = document.getElementById('countdown');
            if (countdownElement && countdownElement.getAttribute('data-expires')) {
                var expiresAt = new Date(countdownElement.getAttribute('data-expires')).getTime();

                var countdownInterval = setInterval(function() {
                    var now = new Date().getTime();
                    var distance = expiresAt - now;

                    if (distance < 0) {
                        clearInterval(countdownInterval);
                        $('#countdown').html('<span class="text-danger">Expired</span>');
                        return;
                    }

                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    $('#countdown').html(minutes + "m " + seconds + "s");
                }, 1000);
            }

        })(jQuery);
    </script>
@endpush
