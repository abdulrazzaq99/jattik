@php
    $aboutContent = getContent('about.content', true);
    $aboutElements = getContent('about.element', orderById: true);
@endphp

<section class="about-section py-60">
    <div class="container">
        <div class="row gx-4 gy-4">
            <div class="col-lg-7">
                <div class="row gy-3">
                    <div class="col-sm-7 col-xsm-6">
                        <div class="about-img-overlay position-relative h-100">
                            <div class="about-card-content flex-align position-absolute">
                                <div class="overlay-card">
                                    <h3 class="overlay-card__title">
                                        {{ __(@$aboutContent->data_values->count_one_value) }}
                                    </h3>
                                    <p class="overlay-card__desc">
                                        {{ __(@$aboutContent->data_values->count_one_text) }}
                                    </p>
                                </div>
                                <div class="overlay-card">
                                    <h3 class="overlay-card__title">
                                        {{ __(@$aboutContent->data_values->count_two_value) }}
                                    </h3>
                                    <p class="overlay-card__desc">
                                        {{ __(@$aboutContent->data_values->count_two_text) }}
                                    </p>
                                </div>
                            </div>
                            <div class="section-thumb h-100">
                                <img src="{{ frontendImage('about', @$aboutContent->data_values->image_one, '420x525') }}" alt="about image">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5 col-xsm-6">
                        <div class="about-img-overlay-alt h-100">
                            <div class="about-card-content">
                                <div class="overlay-card w-100">
                                    <h3 class="overlay-card__title d-inline">
                                        {{ __(@$aboutContent->data_values->count_three_value) }}
                                    </h3>
                                    <p class="overlay-card__desc d-inline">
                                        {{ __(@$aboutContent->data_values->count_three_text) }}
                                    </p>
                                </div>
                            </div>
                            <div class="section-thumb h-100">
                                <img src="{{ frontendImage('about', @$aboutContent->data_values->image_two, '295x405') }}" alt="about image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="section-heading style-left">
                    <h3 class="section-heading__title">{{ __(@$aboutContent->data_values->heading) }}</h3>
                    <p class="section-heading__desc">
                        {{ __(@$aboutContent->data_values->sub_heading) }}
                    </p>
                </div>
                @if (!blank(@$aboutElements))
                    <ul class="about-services">
                        @foreach (@$aboutElements as $aboutElement)
                            <li class="about-services__item">
                                <div class="about-services__thumb">
                                    <span class="icon">
                                        @php echo @$aboutElement->data_values->about_icon; @endphp
                                    </span>
                                </div>
                                <div class="about-services__content">
                                    <h6 class="about-services__title">
                                        {{ __(@$aboutElement->data_values->title) }}
                                    </h6>
                                    <p class="about-services__desc">
                                        {{ __(@$aboutElement->data_values->sub_title) }}
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</section>
