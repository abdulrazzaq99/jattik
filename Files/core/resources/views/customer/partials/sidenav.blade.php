<div class="sidebar bg--dark">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="{{ route('customer.dashboard') }}" class="sidebar__main-logo">
                <img src="{{ siteLogo() }}" alt="@lang('image')">
            </a>
        </div>

        <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">
                <li class="sidebar-menu-item {{ menuActive('customer.dashboard') }}">
                    <a href="{{ route('customer.dashboard') }}" class="nav-link">
                        <i class="menu-icon las la-home"></i>
                        <span class="menu-title">@lang('Dashboard')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('customer.track') }}">
                    <a href="{{ route('customer.track') }}" class="nav-link">
                        <i class="menu-icon las la-search-location"></i>
                        <span class="menu-title">@lang('Track Courier')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{ menuActive(['customer.sent.couriers', 'customer.received.couriers'], 3) }}">
                        <i class="menu-icon las la-boxes"></i>
                        <span class="menu-title">@lang('My Couriers')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive(['customer.sent.couriers', 'customer.received.couriers'], 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('customer.sent.couriers') }}">
                                <a href="{{ route('customer.sent.couriers') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Sent Couriers')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('customer.received.couriers') }}">
                                <a href="{{ route('customer.received.couriers') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Received Couriers')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{ menuActive(['customer.subscription.*'], 3) }}">
                        <i class="menu-icon las la-crown"></i>
                        <span class="menu-title">@lang('Subscription')</span>
                        @if(auth()->guard('customer')->user()->isPremium())
                            <span class="menu-badge pill bg--primary">@lang('Premium')</span>
                        @endif
                    </a>
                    <div class="sidebar-submenu {{ menuActive(['customer.subscription.*'], 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('customer.subscription.plans') }}">
                                <a href="{{ route('customer.subscription.plans') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('View Plans')</span>
                                </a>
                            </li>
                            @if(auth()->guard('customer')->user()->activeSubscription)
                            <li class="sidebar-menu-item {{ menuActive('customer.subscription.current') }}">
                                <a href="{{ route('customer.subscription.current') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('My Subscription')</span>
                                </a>
                            </li>
                            @endif
                            <li class="sidebar-menu-item {{ menuActive('customer.subscription.history') }}">
                                <a href="{{ route('customer.subscription.history') }}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('History')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item {{ menuActive('customer.payment.history') }}">
                    <a href="{{ route('customer.payment.history') }}" class="nav-link">
                        <i class="menu-icon las la-credit-card"></i>
                        <span class="menu-title">@lang('Payments')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item {{ menuActive('customer.profile') }}">
                    <a href="{{ route('customer.profile') }}" class="nav-link">
                        <i class="menu-icon las la-user"></i>
                        <span class="menu-title">@lang('Profile')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a href="{{ route('customer.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link">
                        <i class="menu-icon las la-sign-out-alt"></i>
                        <span class="menu-title">@lang('Logout')</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- sidebar end -->
