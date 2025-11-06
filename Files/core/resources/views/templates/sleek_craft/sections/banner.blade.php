@php
    $contactContent = getContent('banner.content', true);
    $socialElements = getContent('social_icon.element', orderById: true);
@endphp

<section class="banner-section bg-img mb-60"
    data-background-image="{{ frontendImage('banner', @$contactContent->data_values->background_image, '1900x955') }}">
    <div class="h-100 container">
        <div class="row h-100">
            <div class="col-xl-5 col-lg-6">
                <div class="banner-content">
                    <h1 class="banner-content__title">{{ __(@$contactContent->data_values->heading) }}</h1>
                    <div class="banner-content__button d-flex align-items-center gap-3">
                        <a class="btn btn--base" href="{{ @$contactContent->data_values->button_one_link }}">
                            <span class="btn--icon"><i class="icon-View-More"></i></span>
                            {{ __(@$contactContent->data_values->button_one) }}
                        </a>
                        <a class="btn btn-outline--base" href="{{ @$contactContent->data_values->button_two_link }}">
                            <span class="btn--icon"><i class="icon-View-More"></i></span>
                            {{ __(@$contactContent->data_values->button_two) }}
                        </a>
                    </div>
                    <div class="banner-social-list">
                        <ul class="banner-social-list-item flex-align">
                            @foreach (@$socialElements as $socialElement)
                                <li>
                                    <a class="banner-social-list-link" href="{{ @$socialElement->data_values->url }}" target="_blank">
                                        {{ __(@$socialElement->data_values->title) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xl-7 col-lg-6">
                <div class="banner-thumb h-100">
                    <span class="img">
                        <img src="{{ frontendImage('banner', @$contactContent->data_values->image, '915x505') }}" alt="banner-thumb">
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>
