@php
    $contactInfo = getContent('contactInfo.content', true);
@endphp
<header>
    <div class="header-top d-none d-md-block">
        <div class="container">
            <div class="header-top-wrapper">
                <ul class="header-contact-info">
                    <li>
                        <a href="Mailto:{{ @$contactInfo->data_values->email }}"><i class="las la-envelope"></i>
                            {{ @$contactInfo->data_values->email }}</a>
                    </li>
                    <li>
                        <a href="Tel:{{ @$contactInfo->data_values->mobile }}">
                            <i class="las la-phone"></i>{{ @$contactInfo->data_values->mobile }}
                        </a>
                    </li>
                </ul>
                @if (gs('multi_language'))
                    @include($activeTemplate . 'partials.language')
                @endif
                <div class="right-area d-none d-md-block">
                    <a class="cmn--btn btn--sm me-3 text-white" href="{{ route('order.tracking') }}">
                        @lang('Order Tracking')
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="header-bottom">
        <div class="container">
            <div class="header__wrapper">
                <div class="logo">
                    <a href="{{ route('home') }}">
                        <img src="{{ siteLogo() }}" alt="logo">
                    </a>
                </div>
                <div class="header-bar d-lg-none ms-auto">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <div class="menu-area align-items-center">
                    <div class="d-lg-none cross--btn">
                        <i class="las la-times"></i>
                    </div>
                    <div class="right-area d-md-none mb-4 text-center">
                        @if (gs('multi_language'))
                            @include($activeTemplate . 'partials.language')
                        @endif

                        <a class="cmn--btn btn--sm me-3" href="{{ route('order.tracking') }}">@lang('Order Tracking')</a>
                        <ul class="header-contact-info">
                            <li>
                                <a href="Mailto:{{ @$contactInfo->data_values->email }}">
                                    <i class="las la-envelope"></i> {{ @$contactInfo->data_values->email }}
                                </a>
                            </li>
                            <li>
                                <a href="Tel:{{ @$contactInfo->data_values->mobile }}">
                                    <i class="las la-phone"></i>{{ __(@$contactInfo->data_values->mobile) }}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <ul class="menu">
                        <li class="active">
                            <a class="active" href="{{ route('home') }}">@lang('Home')</a>
                        </li>
                        @foreach ($pages as $data)
                            <li>
                                <a href="{{ route('pages', $data->slug) }}">
                                    {{ __($data->name) }}
                                </a>
                            </li>
                        @endforeach
                        <li>
                            <a href="{{ route('blog') }}">@lang('Blog')</a>
                        </li>
                        <li>
                            <a href="{{ route('contact') }}">@lang('Contact')</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>
