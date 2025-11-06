@php
    $clientContent = getContent('client.content', true);
    $clientElements = getContent('client.element', null, false, true);
@endphp
<section class="client-section client-section bg--title-overlay bg_fixed bg_img pt-120 pb-120"
    data-background="{{ frontendImage('client', @$clientContent->data_values->background_image, '1920x1280') }}">
    <div class="container">
        <div class="client-slider">
            <div class="sync1 owl-theme owl-carousel">
                @foreach ($clientElements as $clientElement)
                    <div class="client__content">
                        <p>{{ __($clientElement->data_values->testimonial) }}</p>
                        <div class="ratings">
                            @if (is_int((int) $clientElement->data_values->rating))
                                @for ($i = 1; $i <= $clientElement->data_values->rating; $i++)
                                    <span><i class="las la-star"></i></span>
                                @endfor
                            @endif
                        </div>
                        <h5 class="title text--white">{{ __($clientElement->data_values->name) }}</h5>
                        <span class="designation">{{ __($clientElement->data_values->designation) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="sync2 owl-theme owl-carousel">
                @foreach ($clientElements as $clientElement)
                    <div class="client__thumb">
                        <img src="{{ frontendImage('client', $clientElement->data_values->client_image, '120x120') }}" alt="client image">
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
