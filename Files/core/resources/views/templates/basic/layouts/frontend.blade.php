@extends($activeTemplate . 'layouts.app')
@section('panel')
    @include($activeTemplate . 'partials.header')

    @includeWhen(!request()->routeIs('home'), $activeTemplate . 'partials.breadcrumb')

    @yield('content')

    @include($activeTemplate . 'partials.footer')
@endsection
