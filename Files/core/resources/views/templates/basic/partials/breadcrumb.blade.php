@php
    $breadcrumbsContent = getContent('breadcrumb.content', true);
@endphp
<div class="hero-section bg--title-overlay bg_img"
    data-background="{{ frontendImage('breadcrumb', @$breadcrumbsContent->data_values->background_image, '1920x770') }}">
    <div class="container">
        <div class="hero__content">
            <h2 class="hero__title">{{ __($pageTitle) }}</h2>
            <div class="breadcrumb">
                <li>
                    <a href="{{ route('home') }}">@lang('Home')</a>
                </li>
                <li>
                    {{ __($pageTitle) }}
                </li>
            </div>
        </div>
    </div>
</div>
