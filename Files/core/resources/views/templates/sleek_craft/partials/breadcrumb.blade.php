@php
    $breadcrumbContent = getContent('breadcrumb.content', true);
@endphp

<section class="breadcrumb bg-img mb-0"
    data-background-image="{{ frontendImage('breadcrumb', @$breadcrumbContent->data_values->background_image, '1900x500') }}">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="breadcrumb__wrapper">
                    <h2 class="breadcrumb__title">{{ __(@$pageTitle) }}</h2>
                </div>
            </div>
        </div>
    </div>
</section>
