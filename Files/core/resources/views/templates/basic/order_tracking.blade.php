@extends($activeTemplate . 'layouts.frontend')
@php
    $orderTrackingContent = getContent('order_tracking.content', true);
@endphp
@section('content')
    <section class="track-section pt-120 pb-120">
        <div class="container">
            <div class="section__header section__header__center">
                <span class="section__cate">
                    {{ __(@$orderTrackingContent->data_values->title) }}
                </span>
                <h3 class="section__title"> {{ __(@$orderTrackingContent->data_values->heading) }}</h3>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-7 col-md-9 col-xl-6">
                    <form class="order-track-form mb-md-5 mb-4" action="{{ route('order.tracking') }}" method="POST">
                        @csrf
                        <div class="order-track-form-group">
                            <input class="form-control form--control" name="order_number" type="text" value="{{ request()->order_number ?? '' }}"
                                placeholder="@lang('Enter Your Order Id')">
                            <button type="submit">@lang('Track Now')</button>
                        </div>
                    </form>
                </div>
            </div>
            @if (@$orderNumber)
                <div class="track--wrapper">
                    <div class="track__item @if ($orderNumber->status >= Status::COURIER_QUEUE) done @endif">
                        <div class="track__thumb">
                            <i class="las la-briefcase"></i>
                        </div>
                        <div class="track__content">
                            <h5 class="track__title">@lang('Picked')</h5>
                        </div>
                    </div>
                    <div class="track__item @if ($orderNumber->status >= Status::COURIER_DISPATCH) done @endif">
                        <div class="track__thumb">
                            <i class="las la-truck-pickup"></i>
                        </div>
                        <div class="track__content">
                            <h5 class="track__title">@lang('Shipping')</h5>
                        </div>
                    </div>
                    <div class="track__item @if ($orderNumber->status >= Status::COURIER_DELIVERYQUEUE) done @endif">
                        <div class="track__thumb">
                            <i class="lar la-building"></i>
                        </div>
                        <div class="track__content">
                            <h5 class="track__title">@lang('Delivered')</h5>
                        </div>
                    </div>
                    <div class="track__item @if ($orderNumber->status == Status::COURIER_DELIVERED) done @endif">
                        <div class="track__thumb">
                            <i class="las la-check-circle"></i>
                        </div>
                        <div class="track__content">
                            <h5 class="track__title">@lang('Completed')</h5>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    @if (@$sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
