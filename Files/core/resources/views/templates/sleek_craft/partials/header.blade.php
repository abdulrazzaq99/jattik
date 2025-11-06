@php
    $contactInfoContent = getContent('contactInfo.content', true);
@endphp

<div class="header-top d-lg-block d-none">
    <div class="container">
        <div class="top-header-wrapper d-flex justify-content-between align-items-center flex-wrap">
            <div class="top-contact">
                <ul class="contact-list">
                    <li class="contact-list__item flex-align">
                        <span class="contact-list__item-icon flex-center">
                            @php echo @$contactInfoContent->data_values->email_icon @endphp
                        </span>
                        <a class="contact-list__link" href="mailto:{{ @$contactInfoContent->data_values->email }}">
                            {{ @$contactInfoContent->data_values->email }}
                        </a>
                    </li>
                    <li class="contact-list__item flex-align">
                        <span class="contact-list__item-icon flex-center">
                            @php echo @$contactInfoContent->data_values->mobile_icon @endphp
                        </span>
                        <a class="contact-list__link" href="tel:{{ @$contactInfoContent->data_values->mobile }}">
                            {{ @$contactInfoContent->data_values->mobile }}
                        </a>
                    </li>
                </ul>
            </div>
            @if (gs('multi_language'))
                <div class="top-button d-flex justify-content-between align-items-center flex-wrap">
                    <div class="top-button d-flex justify-content-between align-items-center flex-wrap">
                        @include($activeTemplate . 'partials.language')
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
<header class="header" id="header">
    <div class="container">
        <div class="col-12">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a class="navbar-brand logo" href="{{ route('home') }}">
                    <img src="{{ siteLogo() }}"  alt="logo image">
                </a>
                <button class="navbar-toggler header-button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" type="button" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span id="hiddenNav"><i class="las la-bars"></i></span>
                </button>
                <div class="navbar-collapse collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav nav-menu align-items-lg-center ms-auto">
                        <li class="nav-item d-block d-lg-none">
                            <div class="top-button d-flex justify-content-between align-items-center flex-wrap">
                                <div class="top-button d-flex justify-content-between align-items-center flex-wrap">
                                    @if (gs('multi_language'))
                                        @include($activeTemplate . 'partials.language')
                                    @endif
                                </div>
                                <a class="nav-button flex-align btn btn--base" href="{{ route('order.tracking') }}"
                                    aria-current="page">
                                    <span class="icon"><i class="icon-Product-Box"></i></span>
                                    @lang('Order Tracking')
                                </a>
                            </div>
                        </li>
                        <li class="nav-item {{ menuActive(['home']) }}">
                            <a class="nav-link" href="{{ route('home') }}" aria-current="page">
                                @lang('Home')
                            </a>
                        </li>
                        @foreach ($pages as $page)
                            <li class="nav-item {{ menuActive('pages', null, @$page->slug) }}">
                                <a class="nav-link" href="{{ route('pages', $page->slug) }}" aria-current="page">
                                    {{ __($page->name) }}
                                </a>
                            </li>
                        @endforeach
                        <li class="nav-item {{ menuActive('blog') }}">
                            <a class="nav-link" href="{{ route('blog') }}" aria-current="page">@lang('Blog')</a>
                        </li>
                        <li class="nav-item {{ menuActive('contact') }}">
                            <a class="nav-link" href="{{ route('contact') }}">@lang('Contact')</a>
                        </li>
                        <li class="nav-item d-lg-block d-none">
                            <a class="nav-button flex-align btn btn--base" href="{{ route('order.tracking') }}"
                                aria-current="page">
                                <span class="icon"><i class="icon-Product-Box"></i></span>
                                @lang('Order Tracking')
                            </a>
                        </li>
                    </ul>

                </div>
            </nav>
        </div>
    </div>
</header>

@push('script')
    <script>
        /*==================== custom dropdown select js ====================*/
        (function($) {
            ("use strict");
            $('.custom--dropdown > .custom--dropdown__selected').on('click', function() {
                $(this).parent().toggleClass('open');
            });

            $('.custom--dropdown > .dropdown-list > .dropdown-list__item').on('click', function() {
                $('.custom--dropdown > .dropdown-list > .dropdown-list__item').removeClass('selected');
                $(this).addClass('selected').parent().parent().removeClass('open').children(
                    '.custom--dropdown__selected').html($(this).html());
            });

            $(document).on('keyup', function(evt) {
                if ((evt.keyCode || evt.which) === 27) {
                    $('.custom--dropdown').removeClass('open');
                }
            });
            $(document).on('click', function(evt) {
                if ($(evt.target).closest(".custom--dropdown > .custom--dropdown__selected").length === 0) {
                    $('.custom--dropdown').removeClass('open');
                }
            });
        })(jQuery);

        /*==================== custom dropdown select js end  ====================*/
    </script>
@endpush
