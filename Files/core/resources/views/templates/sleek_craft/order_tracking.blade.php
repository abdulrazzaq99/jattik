@extends($activeTemplate . 'layouts.frontend')
@php
    $orderTrackingContent = getContent('order_tracking.content', true);
@endphp
@section('content')
    <section class="tracking py-120">
        <div class="container">
            @if (!empty($orderTrackingContent))
                <div class="section-heading">
                    <h3 class="section-heading__title">{{ __(@$orderTrackingContent->data_values->title) }}</h3>
                    <p class="section-heading__desc">
                        {{ __(@$orderTrackingContent->data_values->heading) }}
                    </p>
                </div>
                <div class="track-form">
                    <form action="{{ route('order.tracking') }}" method="post">
                        @csrf
                        <div class="input-group form-group flex-align mb-0">
                            <input class="form-control form--control" name="order_number" type="text"
                                value="{{ request()->order_number ?? '' }}" placeholder="@lang('Enter Your Order Id')">
                            <button class="input-group-text btn btn--base" type="submit">
                                <span class="btn--icon"> <i class="icon-Product-Box"> </i> </span>
                                @lang('Track Now')
                            </button>
                        </div>
                    </form>
                </div>
                @if (@$orderNumber)
                    <div class="track--wrapper pt-120">
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
            @endif
        </div>
    </section>

    @if (@$sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif

@endsection

@push('style')
    <style>
        button.input-group-text.btn:hover {
            border: 0 !important;
        }
    </style>
@endpush
