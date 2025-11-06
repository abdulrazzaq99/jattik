@php
    $featureElements = getContent('feature.element', orderById: true);
@endphp

@if (!blank($featureElements))
    <section class="feature-section py-60">
        <div class="container">
            <div class="row gx-0 gy-5">
                @foreach (@$featureElements as $key => $featureElement)
                    <div class="col-sm-4">
                        <div class="feature-card {{ $key % 2 != 0 ? 'center-card' : '' }}">
                            <div class="feature-card__thumb flex-center">
                                <span class="icon">
                                    @php echo @$featureElement->data_values->feature_icon @endphp
                                </span>
                            </div>
                            <div class="feature-card__content">
                                <h5 class="feature-card__title"> {{ __(@$featureElement->data_values->heading) }} </h5>
                                <p class="feature-card__desc">
                                    {{ __(@$featureElement->data_values->sub_heading) }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
