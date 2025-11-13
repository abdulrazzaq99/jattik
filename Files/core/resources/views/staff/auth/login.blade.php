@extends('staff.layouts.master')
@section('content')
    <div class="login-main" style="background-image: url('{{ asset('assets/viseradmin/images/login.jpg') }}')">
        <div class="container custom-container">
            <div class="row justify-content-center">
                <div class="col-xxl-5 col-xl-5 col-lg-6 col-md-8 col-sm-11">
                    <div class="login-area">
                        <div class="login-wrapper">
                            <div class="login-wrapper__top">
                                <h3 class="title text-white">@lang('Welcome to') <strong>{{ __(gs('site_name')) }}</strong>
                                </h3>
                                <p class="text-white">{{ __($pageTitle) }} @lang('to') {{ __(gs('site_name')) }}
                                    @lang('Dashboard')</p>
                            </div>
                            <div class="login-wrapper__body">
                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong>@lang('Error!')&nbsp;</strong>
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <form action="{{ route('staff.login') }}" method="POST"
                                    class="cmn-form mt-30 verify-gcaptcha login-form">
                                    @csrf
                                    <div class="form-group">
                                        <label>@lang('Username')</label>
                                        <input type="text" class="form-control @error('username') is-invalid @enderror @error('email') is-invalid @enderror" value="{{ old('username') }}"
                                            name="username" required>
                                        @error('username')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>@lang('Password')</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <x-captcha />
                                    <div class="d-flex flex-wrap justify-content-between">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" name="remember" type="checkbox" id="remember">
                                            <label class="form-check-label" for="remember">@lang('Remember Me')</label>
                                        </div>
                                        <a href="{{ route('staff.password.request') }}"
                                            class="forget-text">@lang('Forgot Password?')</a>
                                    </div>
                                    <button type="submit" class="btn cmn-btn w-100">@lang('LOGIN')</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
