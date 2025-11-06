@php
    $footerContent      = getContent('footer.content', true);
    $contactInfoContent = getContent('contactInfo.content', true);
    $socialIconElements = getContent('social_icon.element');
    $policyPageElements = getContent('policy_pages.element', orderById: true);
@endphp

<footer class="footer-section bg--title-overlay bg_img bg_fixed"
    data-background="{{ frontendImage('footer', @$footerContent->data_values->background_image, '1920x1080') }}">
    <div class="footer-top pt-120 pb-120 position-relative">
        <div class="container">
            <div class="row gy-5 justify-content-between">
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="footer__widget">
                        <div class="logo">
                            <a href="{{ route('home') }}">
                                <img src="{{ siteLogo() }}" alt="@lang('logo')">
                            </a>
                        </div>
                        <p>
                            {{ __(@$footerContent->data_values->heading) }}
                        </p>
                        <ul class="social-icons justify-content-start">
                            @foreach ($socialIconElements as $socialIcon)
                                <li>
                                    <a href="{{ $socialIcon->data_values->url }}" target="__blank">
                                        @php echo $socialIcon->data_values->social_icon @endphp
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="footer__widget">
                        <h5 class="title text--white">@lang('Company')</h5>
                        <ul class="useful-link">
                            <li>
                                <a href="{{ route('home') }}">@lang('Home')</a>
                            </li>
                            @foreach ($pages->take(4) as $data)
                                <li>
                                    <a href="{{ route('pages', $data->slug) }}">
                                        {{ __($data->name) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="footer__widget">
                        <h5 class="title text--white">@lang('Useful Link')</h5>
                        <ul class="useful-link">
                            <li>
                                <a href="{{ route('order.tracking') }}">@lang('Order Tracking')</a>
                            </li>
                            @foreach ($policyPageElements as $policyPageElement)
                                <li>
                                    <a href="{{ route('policy.pages', @$policyPageElement->slug) }}" target="_blank">
                                        {{ __($policyPageElement->data_values->title) }}
                                    </a>
                                </li>
                            @endforeach
                            <li>
                                <a href="{{ route('contact') }}">@lang('Support')</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="footer__widget">
                        <h5 class="title text--white">@lang('Get In Touch')</h5>
                        <ul class="footer__widget-contact">
                            <li>
                                <i class="las la-map-marker"></i>
                                {{ __(@$contactInfoContent->data_values->address) }}
                            </li>
                            <li>
                                <span><i class="las la-mobile"></i> @lang('Mobile'):</span>
                                <a href="Tel:{{ @$contactInfoContent->data_values->mobile }}">
                                    {{ @$contactInfoContent->data_values->mobile }}
                                </a>
                            </li>
                            <li>
                                <i class="las la-fax"></i> @lang('Fax') :
                                {{ @$contactInfoContent->data_values->fax }}
                            </li>
                            <li>
                                <a href="Mailto:{{ @$contactInfoContent->data_values->email }}">
                                    <i class="las la-envelope"></i>
                                    {{ @$contactInfoContent->data_values->email }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom position-relative text-center">
        <div class="container">
            &copy; @lang('All Right Reserved by')
            <a href="{{ route('home') }}">{{ __(gs('site_name')) }}</a>
        </div>
    </div>
</footer>

@php
    $cookie = App\Models\Frontend::where('data_keys', 'cookie.data')->first();
@endphp

@if ($cookie->data_values->status == Status::YES && !\Cookie::get('gdpr_cookie'))
    <div class="cookies-card text-center hide">
        <div class="cookies-card__icon bg--base">
            <i class="las la-cookie-bite"></i>
        </div>
        <p class="mt-4 cookies-card__content">
            {{ $cookie->data_values->short_desc }}
            <a href="{{ route('cookie.policy') }}" target="_blank">
                @lang('learn more')
            </a>
        </p>
        <div class="cookies-card__btn mt-4">
            <button class="cmn--btn btn--lg w-100 policy">@lang('Allow')</button>
        </div>
    </div>
@endif
