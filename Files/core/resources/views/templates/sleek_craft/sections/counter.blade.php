@php
    $counterElements = getContent('counter.element', orderById: true);
@endphp
@if (!blank($counterElements))
    <section class="our-services py-60">
        <div class="container">
            <div class="row gy-5">
                @foreach (@$counterElements as $counterElement)
                    <div class="col-md-3 col-6 col-xsm-6">
                        <div class="our-service-card flex-align counterup-item">
                            <div class="our-service-card__thumb">
                                <div class="border-vertical"></div>
                                <div class="border-horizontal"></div>
                                <span class="icon">
                                    @php echo @$counterElement->data_values->counter_icon; @endphp
                                </span>
                            </div>
                            <div class="our-service-card__content">
                                <p class="our-service-card__subtitle">
                                    {{ __(@$counterElement->data_values->title) }}
                                </p>
                                <h4 class="our-service-card__title flex-align">
                                    <span class="odometer" data-odometer-final="{{ @$counterElement->data_values->counter_digit }}"></span>
                                </h4>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
