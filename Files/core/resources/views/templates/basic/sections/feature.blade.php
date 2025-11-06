@php
    $featureElements = getContent('feature.element', null, false, true);
@endphp
<section class="special-feature-section pb-60 pt-120 mt--200">
    <div class="container">
        <div class="row g-4 justify-content-center">
            @foreach ($featureElements as $featureElement)
                <div class="col-md-6 col-lg-4">
                    <div class="special__feature">
                        <div class="special__feature-icon">
                            @php echo @$featureElement->data_values->feature_icon @endphp
                        </div>
                        <div class="special__feature-content">
                            <h5 class="special__feature-content-title">{{ __(@$featureElement->data_values->heading) }}</h5>
                            <p class="special__feature-content-txt">
                                {{ __(@$featureElement->data_values->sub_heading) }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
