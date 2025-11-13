@php
    $sidenav = json_decode($sidenav);
    $routesData = [];

    foreach (\Illuminate\Support\Facades\Route::getRoutes() as $route) {
        $name = $route->getName();
        if (strpos($name, 'staff') !== false) {
            $routeData = [
                $name => url($route->uri()),
            ];
            $routesData[] = $routeData;
        }
    }
@endphp

<nav class="navbar-wrapper bg--dark d-flex flex-wrap">
    <div class="navbar__left">
        <button type="button" class="res-sidebar-open-btn me-3"><i class="las la-bars"></i></button>
        <form class="navbar-search">
            <input type="search" name="#0" class="navbar-search-field" id="searchInput" autocomplete="off"
                placeholder="@lang('Search here...')">
            <i class="las la-search"></i>
            <ul class="search-list"></ul>
        </form>
    </div>
    <div class="navbar__right">
        <ul class="navbar__action-list">
            <li>
                <button type="button" class="primary--layer" data-bs-toggle="tooltip" data-bs-placement="bottom"
                    title="@lang('Visit Website')">
                    <a href="{{ route('home') }}" target="_blank"><i class="las la-globe"></i></a>
                </button>
            </li>
            @if(gs('multi_language'))
            <li class="dropdown language-dropdown">
                @php
                    $languages = App\Models\Language::all();
                    $currentLang = session('lang', 'en');
                    $currentLanguage = $languages->where('code', $currentLang)->first() ?? $languages->where('is_default', 1)->first();
                @endphp
                <button type="button" class="primary--layer d-flex align-items-center" data-bs-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false" style="gap: 8px; padding: 8px 12px;">
                    <img src="{{ getImage(getFilePath('language') . '/' . $currentLanguage->image, getFileSize('language')) }}" alt="{{ $currentLanguage->name }}" style="width: 24px; height: 24px; object-fit: cover; border-radius: 4px;">
                    <span style="font-size: 14px; font-weight: 500;">{{ __($currentLanguage->name) }}</span>
                    <i class="las la-angle-down" style="font-size: 14px;"></i>
                </button>
                <div class="dropdown-menu dropdown-menu--sm p-0 border-0 box--shadow1 dropdown-menu-right">
                    @foreach($languages as $language)
                        <a href="{{ route('home') }}/change/{{ $language->code }}" class="dropdown-menu__item d-flex align-items-center px-3 py-2 @if($currentLang == $language->code) active @endif">
                            <img src="{{ getImage(getFilePath('language') . '/' . $language->image, getFileSize('language')) }}" alt="{{ $language->name }}" class="me-2" style="width: 24px; height: 24px; object-fit: cover; border-radius: 4px;">
                            <span class="dropdown-menu__caption">{{ __($language->name) }}</span>
                        </a>
                    @endforeach
                </div>
            </li>
            @endif
            <li class="dropdown d-flex profile-dropdown">
                <button type="button" data-bs-toggle="dropdown" data-display="static" aria-haspopup="true"
                    aria-expanded="false">
                    <span class="navbar-user">
                        <span class="navbar-user__thumb"><img
                                src="{{ getImage(getFilePath('userProfile') . '/' . auth()->user()->image, getFileSize('userProfile')) }}"
                                alt="image"></span>
                        <span class="navbar-user__info">
                            <span class="navbar-user__name">{{ auth()->user()->username }}</span>
                        </span>
                        <span class="icon"><i class="las la-chevron-circle-down"></i></span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu--sm p-0 border-0 box--shadow1 dropdown-menu-right">
                    <a href="{{ route('staff.profile') }}"
                        class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-user-circle"></i>
                        <span class="dropdown-menu__caption">@lang('Profile')</span>
                    </a>

                    <a href="{{ route('staff.password') }}"
                        class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-key"></i>
                        <span class="dropdown-menu__caption">@lang('Password')</span>
                    </a>

                    <a href="{{ route('staff.logout') }}"
                        class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-sign-out-alt"></i>
                        <span class="dropdown-menu__caption">@lang('Logout')</span>
                    </a>
                </div>
                <button type="button" class="breadcrumb-nav-open ms-2 d-none">
                    <i class="las la-sliders-h"></i>
                </button>
            </li>
        </ul>
    </div>
</nav>

@push('script')
    <script>
        "use strict";
        var routes = @json($routesData);
        var settingsData = Object.assign({}, @json($sidenav));
        $('.navbar__action-list .dropdown-menu').on('click', function(event) {
            event.stopPropagation();
        });
    </script>

    <script src="{{ asset('assets/viseradmin/js/search.js') }}"></script>

    <script>
        "use strict";

        function getEmptyMessage() {
            return `<li class="text-muted">
                <div class="empty-search text-center">
                    <img src="{{ getImage('assets/images/empty_list.png') }}" alt="empty">
                    <p class="text-muted">No search result found</p>
                </div>
            </li>`
        }
    </script>
@endpush
