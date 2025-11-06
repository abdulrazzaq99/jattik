@php
    $contactInfoContent = getContent('contactInfo.content', true);
    $footerContent = getContent('footer.content', true);
    $socialElements = getContent('social_icon.element', orderById: true);
    $links = getContent('policy_pages.element', orderById: true);
@endphp

<footer class="footer-area">
    <div class="main-footer pt-120 bg-img pb-60"
        data-background-image="{{ frontendImage('footer', @$footerContent->data_values->background_image, '1900x430') }}">
        <div class="container">
            <div class="row justify-content-center gy-5">
                <div class="col-xl-3 col-sm-6 col-xsm-6">
                    <div class="footer-item">
                        <div class="footer-item__logo">
                            <a href="{{ route('home') }}">
                                <img src="{{ siteLogo() }}" alt="Logo">
                            </a>
                        </div>
                        <p class="footer-item__desc fs-18">
                            {{ __(@$footerContent->data_values->short_description) }}
                        </p>
                        <ul class="social-list">
                            @foreach (@$socialElements as $socialElement)
                                <li class="social-list__item">
                                    <a class="social-list__link flex-center" href="{{ @$socialElement->data_values->url }}" target="__blank">
                                        @php echo @$socialElement->data_values->social_icon @endphp
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-xsm-6">
                    <div class="footer-item">
                        <h5 class="footer-item__title">@lang('Site Link')</h5>
                        <ul class="footer-menu">
                            <li class="footer-menu__item">
                                <a class="footer-menu__link" href="{{ route('home') }}">
                                    @lang('Home')
                                </a>
                            </li>
                            @foreach (@$pages as $page)
                                <li class="footer-menu__item">
                                    <a class="footer-menu__link" href="{{ route('pages', $page->slug) }}">
                                        {{ __($page->name) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-xsm-6">
                    <div class="footer-item">
                        <h5 class="footer-item__title">@lang('Useful Link')</h5>
                        <ul class="footer-menu">
                            <li class="footer-menu__item">
                                <a class="footer-menu__link" href="{{ route('order.tracking') }}">
                                    @lang('Order Tracking')
                                </a>
                            </li>
                            @foreach (@$links as $link)
                                <li class="footer-menu__item">
                                    <a class="footer-menu__link" href="{{ route('policy.pages', @$link->slug) }}">
                                        {{ __(@$link->data_values->title) }}
                                    </a>
                                </li>
                            @endforeach
                            <li class="footer-menu__item">
                                <a class="footer-menu__link" href="{{ route('contact') }}">
                                    @lang('Contact')
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-xsm-6">
                    <div class="footer-item">
                        <h5 class="footer-item__title"> @lang('Contact With Us') </h5>
                        <ul class="footer-contact-menu">
                            <li class="footer-contact-menu__item">
                                <div class="footer-contact-menu__item-icon">
                                    @php echo @$contactInfoContent->data_values->address_icon; @endphp
                                </div>
                                <div class="footer-contact-menu__item-content">
                                    <p class="title">{{ __(@$contactInfoContent->data_values->address_title) }} </p>
                                    <span class="desc fs-14 d-block">{{ __(@$contactInfoContent->data_values->address) }}</span>
                                </div>
                            </li>
                            <li class="footer-contact-menu__item">
                                <div class="footer-contact-menu__item-icon">
                                    @php echo @$contactInfoContent->data_values->mobile_icon @endphp
                                </div>
                                <div class="footer-contact-menu__item-content">
                                    <p class="title">{{ __(@$contactInfoContent->data_values->mobile_title) }}</p>
                                    <a class="desc fs-14 d-block" href="tel:{{ @$contactInfoContent->data_values->mobile }}">
                                        {{ @$contactInfoContent->data_values->mobile }}
                                    </a>
                                </div>
                            </li>
                            <li class="footer-contact-menu__item">
                                <div class="footer-contact-menu__item-icon">
                                    @php echo @$contactInfoContent->data_values->email_icon @endphp
                                </div>
                                <div class="footer-contact-menu__item-content">
                                    <p class="title">{{ __(@$contactInfoContent->data_values->email_title) }}</p>
                                    <a class="desc fs-14 d-block" href="mailto:{{ @$contactInfoContent->data_values->email }}">
                                        {{ @$contactInfoContent->data_values->email }}
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bottom-footer py-3">
        <div class="container">
            <div class="row gy-3">
                <div class="col-md-12 text-center">
                    <div class="bottom-footer-text text-white">
                        @lang('Copyright') &copy; {{ date('Y') }}.
                        @lang('All Rights Reserved')
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
