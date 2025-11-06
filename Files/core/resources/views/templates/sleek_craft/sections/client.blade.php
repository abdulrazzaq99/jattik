@php
    $clientContent = getContent('client.content', true);
    $clientElements = getContent('client.element', orderById: true);
@endphp

<section class="testimonials py-120 section-bg">
    <div class="container-fluid">
        <div class="section-overlay">
            <div class="row g-0 h-100">
                <div class="col-xl-7 col-lg-8">
                    <div class="left-thumb">
                        <img src="{{ frontendImage('client', @$clientContent->data_values->background_image, '1110x570') }}" alt="client">
                    </div>
                </div>
                <div class="col-xl-5 col-lg-4 d-lg-block d-none">
                    <div class="right-thumb">
                        <img src="{{ frontendImage('client', @$clientContent->data_values->image, '795x570') }}" alt="client">
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="testimonial-slider">
                        @foreach (@$clientElements as $clientElement)
                            <div class="testimonails-card">
                                <div class="testimonial-item">
                                    <div class="testimonial-item__content">
                                        <div class="testimonial-item__info">
                                            <div class="testimonial-item__thumb">
                                                <img class="fit-image"
                                                    src="{{ frontendImage('client', @$clientElement->data_values->client_image, '80x80') }}"
                                                    alt="client">
                                            </div>
                                            <div class="testimonial-item__details">
                                                <h6 class="testimonial-item__name">
                                                    {{ __(@$clientElement->data_values->name) }}</h6>
                                                <span class="testimonial-item__designation">
                                                    {{ __(@$clientElement->data_values->designation) }}
                                                </span>
                                                @php
                                                    $review = (int) @$clientElement->data_values->rating;
                                                    $noReview = 5 - $review;
                                                @endphp
                                                <div class="testimonial-item__rating">
                                                    <ul class="rating-list">
                                                        @for ($i = 0; $i < $review; $i++)
                                                            <li class="rating-list__item"><i class="fas fa-star"></i></li>
                                                        @endfor
                                                        @for ($i = 0; $i < $noReview; $i++)
                                                            <li class="rating-list__item"><i class="far fa-star"></i></li>
                                                        @endfor
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="testimonial-item__desc">
                                        {{ __(@$clientElement->data_values->testimonial) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
