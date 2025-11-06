@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="contact-section pt-120 pb-120">
        <div class="container">
            <div class="row align-items-center justify-content-between">
                <div class="col-lg-6 d-none d-lg-block rtl pe-xxl-50">
                    <img src="{{ frontendImage('contact_us', @$contact->data_values->contact_image, '655x615') }}"
                        alt="contact image">
                </div>
                <div class="col-lg-6">
                    <div class="section__header">
                        <span class="section__cate">{{ __(@$contact->data_values->title) }}</span>
                        <h3 class="section__title">{{ __(@$contact->data_values->heading) }}</h3>
                        <p>{{ __(@$contact->data_values->sub_heading) }}</p>
                    </div>
                    <form class="contact-form" class="verify-gcaptcha" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label>@lang('Your Name')</label>
                            <input class="form-control form--control" name="name" type="text"
                                value="{{ old('name') }}"required>
                        </div>
                        <div class="form-group mb-3">
                            <label>@lang('Email Address')</label>
                            <input class="form-control form--control" name="email" type="text"
                                value="{{ old('email') }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>@lang('Subject')</label>
                            <input class="form-control form--control" name="subject" type="text"
                                value="{{ old('subject') }}" required="">
                        </div>
                        <div class="form-group mb-3">
                            <label>@lang('Your Message')</label>
                            <textarea class="form-control form--control" name="message" name="message" required="">{{ old('message') }}</textarea>
                        </div>
                        <x-captcha />
                        <div class="form-group mt-2">
                            <button class="cmn--btn btn--lg rounded" type="submit">@lang('Send Message')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if (@$sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif

@endsection
