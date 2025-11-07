@extends('customer.layouts.app')

@section('panel')
<div class="row gy-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">@lang('Subscription History')</h5>
                    <a href="{{ route('customer.subscription.plans') }}" class="btn btn--sm btn--primary">
                        <i class="las la-crown"></i> @lang('View Plans')
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table--light">
                        <thead>
                            <tr>
                                <th>@lang('Plan')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Started')</th>
                                <th>@lang('Expires')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Auto-Renew')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subscriptions as $subscription)
                            <tr>
                                <td>
                                    <strong>{{ $subscription->plan->name }}</strong>
                                    @if($subscription->plan->isPremium())
                                        <span class="badge badge--primary badge--sm">@lang('Premium')</span>
                                    @endif
                                </td>
                                <td>{{ ucfirst($subscription->plan->type) }}</td>
                                <td>
                                    @if($subscription->started_at)
                                        {{ $subscription->started_at->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($subscription->expires_at)
                                        {{ $subscription->expires_at->format('M d, Y') }}
                                    @else
                                        <span class="badge badge--success">@lang('Never')</span>
                                    @endif
                                </td>
                                <td>
                                    @if($subscription->status == 'active')
                                        <span class="badge badge--success">@lang('Active')</span>
                                    @elseif($subscription->status == 'cancelled')
                                        <span class="badge badge--warning">@lang('Cancelled')</span>
                                    @elseif($subscription->status == 'expired')
                                        <span class="badge badge--danger">@lang('Expired')</span>
                                    @else
                                        <span class="badge badge--info">{{ ucfirst($subscription->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($subscription->auto_renew)
                                        <span class="badge badge--success">@lang('Yes')</span>
                                    @else
                                        <span class="badge badge--danger">@lang('No')</span>
                                    @endif
                                </td>
                                <td>
                                    @if($subscription->id == auth()->guard('customer')->user()->active_subscription_id)
                                        <a href="{{ route('customer.subscription.current') }}" class="btn btn--sm btn--base">
                                            <i class="las la-eye"></i> @lang('View')
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <img src="{{ asset('assets/images/empty_list.png') }}" alt="empty" style="width: 200px;">
                                    <p class="text-muted mt-3">@lang('No subscription history found')</p>
                                    <a href="{{ route('customer.subscription.plans') }}" class="btn btn--primary btn--sm mt-2">
                                        <i class="las la-crown"></i> @lang('Subscribe Now')
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($subscriptions->hasPages())
            <div class="card-footer">
                {{ $subscriptions->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
