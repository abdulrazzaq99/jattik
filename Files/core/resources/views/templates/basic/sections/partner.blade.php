@php
    $partnerElements = getContent('partner.element', null, false, true);
@endphp
<div class="partner-section pt-80 pb-80">
    <div class="container">
        <div class="partner-slider owl-theme owl-carousel">
            @foreach ($partnerElements as $partnerElement)
                <a class="partner-thumb" href="javascript:void(0)">
                    <img src="{{ frontendImage('partner', $partnerElement->data_values->partner_image, '135x45') }}"
                        alt="partner image">
                    <img src="{{ frontendImage('partner', $partnerElement->data_values->partner_image, '135x45') }}"
                        alt="partner image">
                </a>
            @endforeach
        </div>
    </div>
</div>
