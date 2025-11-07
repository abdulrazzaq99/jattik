@extends('customer.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-bell"></i> @lang('Notification Details')
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Notification Status --}}
                    <div class="mb-3">
                        @if($notification->is_read)
                            <span class="badge badge--success">
                                <i class="las la-check-circle"></i> @lang('Read')
                            </span>
                        @else
                            <span class="badge badge--warning">
                                <i class="las la-exclamation-circle"></i> @lang('Unread')
                            </span>
                        @endif

                        {{-- Notification Type --}}
                        @if($notification->notification_type == 'facility_arrival')
                            <span class="badge badge--primary ms-2">
                                <i class="las la-warehouse"></i> @lang('Facility Arrival')
                            </span>
                        @elseif($notification->notification_type == 'dispatched')
                            <span class="badge badge--info ms-2">
                                <i class="las la-shipping-fast"></i> @lang('Dispatched')
                            </span>
                        @elseif($notification->notification_type == 'tracking_link')
                            <span class="badge badge--success ms-2">
                                <i class="las la-link"></i> @lang('Tracking Link')
                            </span>
                        @elseif($notification->notification_type == 'fee_quote')
                            <span class="badge badge--warning ms-2">
                                <i class="las la-dollar-sign"></i> @lang('Fee Quote')
                            </span>
                        @elseif($notification->notification_type == 'exception')
                            <span class="badge badge--danger ms-2">
                                <i class="las la-exclamation-triangle"></i> @lang('Exception')
                            </span>
                        @elseif($notification->notification_type == 'delivered')
                            <span class="badge badge--success ms-2">
                                <i class="las la-check-circle"></i> @lang('Delivered')
                            </span>
                        @endif
                    </div>

                    <hr>

                    {{-- Notification Title --}}
                    <h4 class="mb-3">{{ $notification->title }}</h4>

                    {{-- Notification Message --}}
                    <div class="alert alert-info">
                        <p class="mb-0">{{ $notification->message }}</p>
                    </div>

                    {{-- Related Courier Info --}}
                    @if($notification->courierInfo)
                        <div class="card bg-light mt-4">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="las la-box"></i> @lang('Related Shipment')</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p>
                                            <strong>@lang('Tracking Code'):</strong><br>
                                            <span class="badge badge--dark">{{ $notification->courierInfo->code }}</span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p>
                                            <strong>@lang('Status'):</strong><br>
                                            @if($notification->courierInfo->status == 0)
                                                <span class="badge badge--warning">@lang('Queue')</span>
                                            @elseif($notification->courierInfo->status == 1)
                                                <span class="badge badge--primary">@lang('In Transit')</span>
                                            @elseif($notification->courierInfo->status == 2)
                                                <span class="badge badge--info">@lang('Delivery Queue')</span>
                                            @elseif($notification->courierInfo->status == 3)
                                                <span class="badge badge--success">@lang('Delivered')</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('customer.track', ['code' => $notification->courierInfo->code]) }}"
                                   class="btn btn-sm btn--primary mt-2">
                                    <i class="las la-search-location"></i> @lang('Track Shipment')
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Metadata (if available) --}}
                    @if($notification->metadata)
                        <div class="card bg-light mt-4">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="las la-info-circle"></i> @lang('Additional Information')</h6>
                                @php
                                    $metadata = is_string($notification->metadata) ? json_decode($notification->metadata, true) : $notification->metadata;
                                @endphp
                                @if(is_array($metadata))
                                    <ul class="list-unstyled mb-0">
                                        @foreach($metadata as $key => $value)
                                            <li><strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Notification Channels --}}
                    <div class="mt-4">
                        <p class="text-muted small">
                            <i class="las la-paper-plane"></i> @lang('Sent via'):
                            @if($notification->sent_via_email)
                                <span class="badge badge--info">@lang('Email')</span>
                            @endif
                            @if($notification->sent_via_sms)
                                <span class="badge badge--success">@lang('SMS')</span>
                            @endif
                            @if($notification->sent_via_whatsapp)
                                <span class="badge badge--success">@lang('WhatsApp')</span>
                            @endif
                        </p>
                        <p class="text-muted small">
                            <i class="las la-clock"></i> @lang('Received'):
                            {{ showDateTime($notification->created_at) }}
                            ({{ diffForHumans($notification->created_at) }})
                        </p>
                        @if($notification->read_at)
                            <p class="text-muted small">
                                <i class="las la-check-double"></i> @lang('Read at'):
                                {{ showDateTime($notification->read_at) }}
                            </p>
                        @endif
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('customer.notifications.index') }}" class="btn btn-sm btn-outline--secondary">
                        <i class="las la-arrow-left"></i> @lang('Back to Notifications')
                    </a>
                    @if(!$notification->is_read)
                        <form action="{{ route('customer.notifications.mark.read', $notification->id) }}" method="POST" class="d-inline float-end">
                            @csrf
                            <button type="submit" class="btn btn-sm btn--success">
                                <i class="las la-check"></i> @lang('Mark as Read')
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
