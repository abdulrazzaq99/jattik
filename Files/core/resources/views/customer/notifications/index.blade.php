@extends('customer.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">@lang('My Notifications')</h5>
                    @if($notifications->where('is_read', false)->count() > 0)
                        <form action="{{ route('customer.notifications.mark.all.read') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn--primary">
                                <i class="las la-check-double"></i> @lang('Mark All as Read')
                            </button>
                        </form>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Notification')</th>
                                    <th>@lang('Tracking Number')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($notifications as $notification)
                                    <tr class="{{ !$notification->is_read ? 'table-active' : '' }}">
                                        <td>
                                            @if($notification->is_read)
                                                <span class="badge badge--success">@lang('Read')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('Unread')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($notification->notification_type == 'facility_arrival')
                                                <i class="las la-warehouse text--primary"></i> @lang('Arrival')
                                            @elseif($notification->notification_type == 'dispatched')
                                                <i class="las la-shipping-fast text--info"></i> @lang('Dispatched')
                                            @elseif($notification->notification_type == 'tracking_link')
                                                <i class="las la-link text--success"></i> @lang('Tracking')
                                            @elseif($notification->notification_type == 'fee_quote')
                                                <i class="las la-dollar-sign text--warning"></i> @lang('Fee Quote')
                                            @elseif($notification->notification_type == 'exception')
                                                <i class="las la-exclamation-triangle text--danger"></i> @lang('Exception')
                                            @elseif($notification->notification_type == 'delivered')
                                                <i class="las la-check-circle text--success"></i> @lang('Delivered')
                                            @else
                                                <i class="las la-bell"></i> @lang('General')
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $notification->title }}</strong><br>
                                            <span class="small text-muted">{{ Str::limit($notification->message, 80) }}</span>
                                        </td>
                                        <td>
                                            @if($notification->courierInfo)
                                                <span class="fw-bold">{{ $notification->courierInfo->code }}</span>
                                            @else
                                                <span class="text-muted">@lang('N/A')</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ showDateTime($notification->created_at) }}<br>
                                            <span class="small text-muted">{{ diffForHumans($notification->created_at) }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('customer.notifications.show', $notification->id) }}"
                                               class="btn btn-sm btn-outline--primary">
                                                <i class="la la-eye"></i> @lang('View')
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">
                                            <i class="las la-bell-slash"></i> @lang('No notifications found')
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($notifications->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($notifications) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Statistics --}}
    <div class="row mt-4">
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="las la-bell text--primary" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $totalNotifications }}</h3>
                    <p class="text-muted mb-0">@lang('Total Notifications')</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="las la-envelope-open text--warning" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $unreadCount }}</h3>
                    <p class="text-muted mb-0">@lang('Unread')</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="las la-check-circle text--success" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $readCount }}</h3>
                    <p class="text-muted mb-0">@lang('Read')</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="las la-calendar-day text--info" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $todayCount }}</h3>
                    <p class="text-muted mb-0">@lang('Today')</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    // Auto-refresh unread count
    setInterval(function() {
        $.get('{{ route('customer.notifications.unread.count') }}', function(data) {
            if (data.count > 0) {
                $('.notification-badge').text(data.count).show();
            } else {
                $('.notification-badge').hide();
            }
        });
    }, 30000); // Every 30 seconds
</script>
@endpush
