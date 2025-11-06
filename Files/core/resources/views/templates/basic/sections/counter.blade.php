@php
    $counterContent = getContent('counter.content', true);
    $counterElements = getContent('counter.element', false, null, true);
@endphp
<div class="counter-section pt-80 pb-80 bg--title-overlay bg_fixed bg_img"
    data-background="{{ frontendImage('counter', @$counterContent->data_values->background_image, '1920x1080') }}">
    <div class="container">
        <div class="row justify-content-center g-4">
            @foreach ($counterElements as $counterElement)
                <div class="col-lg-3 col-sm-6">
                    <div class="counter-item">
                        <div class="counter-header">
                            <h3 class="title rafcounter" data-counter-end="{{ $counterElement->data_values->counter_digit }}">
                                {{ $counterElement->data_values->counter_digit }}
                            </h3>
                        </div>
                        <div class="counter-content">
                            {{ __($counterElement->data_values->title) }}
                        </div>
                        <div class="icon">
                            @php echo $counterElement->data_values->counter_icon @endphp
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
