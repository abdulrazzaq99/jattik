@extends('customer.layouts.master')

@section('content')
    <style>
        .login-area .form-group label {
            color: white !important;
        }
        .login-area .form-control {
            color: white !important;
            background-color: rgba(0, 0, 0, 0.3) !important;
            border-color: rgba(255, 255, 255, 0.3) !important;
        }
        .login-area .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6) !important;
        }
        .login-area .form-control:focus {
            background-color: rgba(0, 0, 0, 0.4) !important;
            border-color: rgba(255, 255, 255, 0.5) !important;
            color: white !important;
        }
    </style>
    <div class="login-main" style="background-image: url('{{ asset('assets/viseradmin/images/login.jpg') }}')">
        <div class="container custom-container">
            <div class="row justify-content-center">
                <div class="col-xxl-7 col-xl-8 col-lg-9 col-md-10">
                    <div class="login-area">
                        <div class="login-wrapper">
                            <div class="login-wrapper__top">
                                <h3 class="title text-white">@lang('Welcome to') <strong>{{ __(gs('site_name')) }}</strong></h3>
                                <p class="text-white">@lang('Customer Registration') - @lang('Create your account to start using our courier services')</p>
                            </div>
                            <div class="login-wrapper__body">
                                <form action="{{ route('customer.register.post') }}" method="POST" class="cmn-form mt-30 verify-gcaptcha">
                                    @csrf

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

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('First Name')</label>
                                                <input type="text" class="form-control @error('firstname') is-invalid @enderror" name="firstname" value="{{ old('firstname') }}" required>
                                                @error('firstname')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Last Name')</label>
                                                <input type="text" class="form-control @error('lastname') is-invalid @enderror" name="lastname" value="{{ old('lastname') }}" required>
                                                @error('lastname')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>@lang('Email Address')</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                                        @error('email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Password')</label>
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                                                @error('password')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Confirm Password')</label>
                                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required>
                                                @error('password_confirmation')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>@lang('Country Code')</label>
                                                <input type="text" class="form-control @error('country_code') is-invalid @enderror" name="country_code" value="{{ old('country_code', '+966') }}" readonly>
                                                @error('country_code')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label>@lang('Mobile Number')</label>
                                                <input type="text" class="form-control @error('mobile') is-invalid @enderror" name="mobile" value="{{ old('mobile') }}" placeholder="05XXXXXXXX" required>
                                                @error('mobile')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @else
                                                    <small class="text-white-50">@lang('KSA mobile format: 05XXXXXXXX or +9665XXXXXXXX')</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>@lang('Address')</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" name="address" rows="2" required>{{ old('address') }}</textarea>
                                        @error('address')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @else
                                            <small class="text-white-50">@lang('Enter your detailed address (minimum 10 characters)')</small>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>@lang('City')</label>
                                                <input type="text" class="form-control @error('city') is-invalid @enderror" name="city" value="{{ old('city') }}" required>
                                                @error('city')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>@lang('State/Province')</label>
                                                <input type="text" class="form-control @error('state') is-invalid @enderror" name="state" value="{{ old('state') }}" required>
                                                @error('state')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>@lang('Postal Code')</label>
                                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror" name="postal_code" value="{{ old('postal_code') }}" placeholder="12345" maxlength="5" required>
                                                @error('postal_code')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @else
                                                    <small class="text-white-50">@lang('5 digits')</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <x-captcha />

                                    <div id="g-recaptcha-error"></div>

                                    <div class="form-group">
                                        <button type="submit" class="btn cmn-btn w-100" id="registerBtn">
                                            <span class="btn-text">@lang('Register Now')</span>
                                            <span class="btn-loader d-none">
                                                <i class="las la-spinner la-spin"></i> @lang('Processing...')
                                            </span>
                                        </button>
                                    </div>

                                    <p class="text-center text-white">
                                        @lang('Already have an account?')
                                        <a href="{{ route('customer.login') }}" class="text-white fw-bold">@lang('Login Here')</a>
                                    </p>
                                </form>

                                <!-- Loading Overlay -->
                                <div class="loading-overlay" id="loadingOverlay">
                                    <div class="loading-spinner">
                                        <div class="spinner-border text-light" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="text-white mt-3">@lang('Creating your account...')</p>
                                    </div>
                                </div>
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

            // Handle registration form submission
            $('form.verify-gcaptcha').on('submit', function(e) {
                var form = $(this);
                var submitBtn = $('#registerBtn');

                // Check if form is valid (HTML5 validation)
                if (form[0].checkValidity() === false) {
                    return true; // Let browser handle validation
                }

                // Show loading state
                submitBtn.addClass('loading');
                $('#loadingOverlay').addClass('show');

                // Disable all form inputs to prevent changes during submission
                form.find('input, select, textarea, button').prop('disabled', true);
            });

        })(jQuery);
    </script>
@endpush
