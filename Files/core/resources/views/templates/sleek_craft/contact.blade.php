@php
    $contactContent = getContent('contact_us.content', true);
@endphp

@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="contact py-120">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="section-heading style-left">
                        <h4 class="section-heading__title">{{ __(@$contactContent->data_values->map_heading) }}</h4>
                        <p class="section-heading__desc">
                            {{ __(@$contactContent->data_values->map_sub_heading) }}
                        </p>
                    </div>
                    <div class="maps-section">
                        <iframe src="{{ @$contactContent->data_values->google_map }}" style="border:0;" width="100%"
                            allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="section-heading style-left">
                        <h4 class="section-heading__title">{{ __(@$contactContent->data_values->heading) }}</h4>
                        <p class="section-heading__desc">
                            {{ __(@$contactContent->data_values->sub_heading) }}
                        </p>
                    </div>
                    <form class="verify-gcaptcha" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12 form-group">
                                <div class="input--group">
                                    <input class="form-control form--control" name="name" type="text"
                                        value="{{ old('name') }}" placeholder="">
                                    <label class="form--label">@lang('Your Name')</label>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group">
                                <div class="input--group">
                                    <input class="form-control form--control" name="email" type="email"
                                        value="{{ old('email') }}" placeholder="">
                                    <label class="form--label">@lang('Enter Email Address')</label>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group">
                                <div class="input--group">
                                    <input class="form-control form--control" name="subject" type="text"
                                        value="{{ old('subject') }}" placeholder="">
                                    <label class="form--label">@lang('Enter Your Subject')</label>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group">
                                <div class="input--group">
                                    <textarea class="form-control form--control" name="message" wrap="off" placeholder="">{{ old('message') }}</textarea>
                                    <label class="form--label textarea-label" for="your-message">@lang('Enter Your Message')</label>
                                </div>
                            </div>
                            @php
                                $addStyle = 'style2';
                            @endphp
                            <x-captcha :addStyle="$addStyle" />

                            <div class="col-sm-12">
                                <button class="btn btn--base w-100" type="submit">
                                    <span class="btn--icon"><i class="icon-View-More"></i></span>
                                    {{ __(@$contactContent->data_values->button_text) }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    @if (@$sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
