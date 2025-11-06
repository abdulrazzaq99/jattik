@extends('customer.layouts.master')

@section('content')
    <div class="login-main" style="background-image: url('{{ asset('assets/viseradmin/images/login.jpg') }}')">
        <div class="container custom-container">
            <div class="row justify-content-center">
                <div class="col-xxl-5 col-xl-5 col-lg-6 col-md-8 col-sm-11">
                    <div class="login-area">
                        <div class="login-wrapper">
                            <div class="login-wrapper__top">
                                <h3 class="title text-white">@lang('Welcome to') <strong>{{ __(gs('site_name')) }}</strong></h3>
                                <p class="text-white">@lang('Customer Login') @lang('to') {{ __(gs('site_name')) }} @lang('Dashboard')</p>
                            </div>
                            <div class="login-wrapper__body">
                                <form action="{{ route('customer.login.post') }}" method="POST" class="cmn-form mt-30 verify-gcaptcha login-form">
                                    @csrf

                                    <div class="form-group">
                                        <label>@lang('Email or Mobile Number')</label>
                                        <input type="text" class="form-control" name="contact" value="{{ old('contact') }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label>@lang('Password')</label>
                                        <input type="password" class="form-control" name="password" required>
                                    </div>

                                    <x-captcha />

                                    <div class="d-flex flex-wrap justify-content-between">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" name="remember" type="checkbox" id="remember">
                                            <label class="form-check-label text-white" for="remember">@lang('Remember Me')</label>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn cmn-btn w-100">@lang('LOGIN')</button>

                                    <p class="text-center text-white mt-3">
                                        @lang("Don't have an account?")
                                        <a href="{{ route('customer.register') }}" class="text-white fw-bold">@lang('Register Here')</a>
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
