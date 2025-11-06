@php
    $partnerElements = getContent('partner.element', orderById: true);
@endphp

<div class="client py-60 section-bg">
    <div class="container">
        <div class="client-logos client-slider">
            @foreach (@$partnerElements as $partnerElement)
                <img src="{{ frontendImage('partner', @$partnerElement->data_values->partner_image, '160x60') }}" alt="partner">
            @endforeach
        </div>
    </div>
</div>
