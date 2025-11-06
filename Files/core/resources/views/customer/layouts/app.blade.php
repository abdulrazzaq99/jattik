@extends('customer.layouts.master')

@section('content')
    <div class="page-wrapper default-version">

        @include('customer.partials.sidenav')
        @include('customer.partials.topnav')

        <div class="container-fluid px-3 px-sm-0">
            <div class="body-wrapper">
                <div class="bodywrapper__inner">

                    @include('customer.partials.breadcrumb')

                    @yield('panel')

                </div>
            </div>
        </div>
    </div>
@endsection
