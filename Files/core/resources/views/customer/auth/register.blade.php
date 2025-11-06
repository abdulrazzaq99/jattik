@extends('customer.layouts.master')

@section('content')
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
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('First Name')</label>
                                                <input type="text" class="form-control" name="firstname" value="{{ old('firstname') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Last Name')</label>
                                                <input type="text" class="form-control" name="lastname" value="{{ old('lastname') }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>@lang('Email Address')</label>
                                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Password')</label>
                                                <input type="password" class="form-control" name="password" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Confirm Password')</label>
                                                <input type="password" class="form-control" name="password_confirmation" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>@lang('Country Code')</label>
                                                <input type="text" class="form-control" name="country_code" value="{{ old('country_code', '+966') }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label>@lang('Mobile Number')</label>
                                                <input type="text" class="form-control" name="mobile" value="{{ old('mobile') }}" placeholder="05XXXXXXXX" required>
                                                <small class="text-white-50">@lang('KSA mobile format: 05XXXXXXXX or +9665XXXXXXXX')</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>@lang('Address')</label>
                                        <textarea class="form-control" name="address" rows="2" required>{{ old('address') }}</textarea>
                                        <small class="text-white-50">@lang('Enter your detailed address (minimum 10 characters)')</small>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>@lang('City')</label>
                                                <input type="text" class="form-control" name="city" value="{{ old('city') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>@lang('State/Province')</label>
                                                <input type="text" class="form-control" name="state" value="{{ old('state') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>@lang('Postal Code')</label>
                                                <input type="text" class="form-control" name="postal_code" value="{{ old('postal_code') }}" placeholder="12345" maxlength="5" required>
                                                <small class="text-white-50">@lang('5 digits')</small>
                                            </div>
                                        </div>
                                    </div>

                                    <x-captcha />

                                    <div class="form-group">
                                        <button type="submit" class="btn cmn-btn w-100">@lang('Register Now')</button>
                                    </div>

                                    <p class="text-center text-white">
                                        @lang('Already have an account?')
                                        <a href="{{ route('customer.login') }}" class="text-white fw-bold">@lang('Login Here')</a>
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
