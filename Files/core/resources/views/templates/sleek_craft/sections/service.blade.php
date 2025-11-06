@php
    $serviceContent = getContent('service.content', true);
    $serviceElements = getContent('service.element', orderById: true);
@endphp

<section class="service-section py-60 section-bg">
    <div class="container">
        <div class="row">
            <div class="section-heading">
                <h3 class="section-heading__title"> {{ __(@$serviceContent->data_values->heading) }} </h3>
                <p class="section-heading__desc">
                    {{ __(@$serviceContent->data_values->sub_heading) }}
                </p>
            </div>
            <div class="col-12">
                <div class="service-card-list">
                    @foreach (@$serviceElements as $serviceElement)
                        <div class="service-card">
                            <div class="service-card__thumb">
                                <img src="{{ frontendImage('service', @$serviceElement->data_values->image, '100x100') }}" alt="service">
                            </div>
                            <div class="service-card__content">
                                <h6 class="service-card__title">
                                    {{ __(@$serviceElement->data_values->title) }}
                                </h6>
                                <p class="service-card__desc">
                                    {{ __(@$serviceElement->data_values->description) }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
