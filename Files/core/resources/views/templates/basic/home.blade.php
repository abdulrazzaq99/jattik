@extends($activeTemplate . 'layouts.frontend')
@php
    $banners = getContent('banner.element');
@endphp
@section('content')
    <section class="banner-slider owl-theme owl-carousel">
        @foreach ($banners as $banner)
            <div class="banner-slide-item bg_img" data-background="{{ frontendImage('banner', @$banner->data_values->background_image, '1920x1080') }}">
                <div class="container">
                    <div class="banner__content">
                        <h5 class="banner__subtitle">{{ __(@$banner->data_values->heading) }}</h5>
                        <h1 class="banner__title">{{ __(@$banner->data_values->sub_heading) }}</h1>
                        <div class="banner__btn__grp">
                            <a href="{{ $banner->data_values->first_button_url ?? '#' }}"
                                class="cmn--btn">{{ __(@$banner->data_values->first_button_name) }}</a>
                            <a href="{{ $banner->data_values->second_button_url ?? '#' }}"
                                class="cmn--btn">{{ __(@$banner->data_values->second_button_name) }}</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </section>

    @if (@$sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
