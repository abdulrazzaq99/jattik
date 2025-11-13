@extends('customer.layouts.app')

@section('panel')
<div class="row gy-4 mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">@lang('My Subscription')</h4>
            <a href="{{ route('customer.subscription.plans') }}" class="btn btn--sm btn--base">
                <i class="las la-th-large"></i> @lang('View All Plans')
            </a>
        </div>
    </div>
</div>

<div class="row gy-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg--primary">
                <h5 class="card-title text-white mb-0">{{ $plan->name }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="text-muted">@lang('Plan Type')</label>
                            <h5>{{ ucfirst($plan->type) }}</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="text-muted">@lang('Price')</label>
                            <h5>{{ $plan->formatted_price }}</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="text-muted">@lang('Status')</label>
                            <h5>
                                <span class="badge badge--{{ $subscription->status == 'active' ? 'success' : 'warning' }}">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            </h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="text-muted">@lang('Auto-Renewal')</label>
                            <h5>
                                <span class="badge badge--{{ $subscription->auto_renew ? 'success' : 'danger' }}">
                                    {{ $subscription->auto_renew ? __('Enabled') : __('Disabled') }}
                                </span>
                            </h5>
                        </div>
                    </div>
                    @if($subscription->started_at)
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="text-muted">@lang('Started On')</label>
                            <h5>{{ $subscription->started_at->format('M d, Y') }}</h5>
                        </div>
                    </div>
                    @endif
                    @if($subscription->expires_at)
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="text-muted">@lang('Expires On')</label>
                            <h5>{{ $subscription->expires_at->format('M d, Y') }}</h5>
                        </div>
                    </div>
                    @endif
                </div>

                <hr>

                <h6 class="mb-3">@lang('Plan Features')</h6>
                <ul class="list-unstyled">
                    @foreach($plan->features as $feature)
                        <li class="py-2">
                            <i class="las la-check-circle text--success"></i>
                            <span>{{ $feature }}</span>
                        </li>
                    @endforeach
                </ul>

                @if($plan->includes_insurance)
                <div class="alert alert--success mt-4">
                    <i class="las la-shield-alt"></i>
                    <strong>@lang('Free Insurance Included!')</strong>
                    <p class="mb-0 mt-2">@lang('All your shipments are automatically insured up to') {{ number_format($plan->insurance_coverage, 2) }} @lang('SAR')</p>
                </div>
                @endif
            </div>
        </div>

        @if(!$plan->isFree())
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('Payment History')</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table--light">
                        <thead>
                            <tr>
                                <th>@lang('Date')</th>
                                <th>@lang('Reference')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                <td>{{ $payment->payment_reference }}</td>
                                <td>{{ $payment->formatted_amount }}</td>
                                <td>
                                    <span class="badge badge--{{ $payment->status == 'completed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($payment->isCompleted())
                                    <a href="{{ route('customer.payment.invoice', $payment->id) }}" class="btn btn--sm btn--base">
                                        <i class="las la-file-invoice"></i> @lang('Invoice')
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">@lang('No payments yet')</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $payments->links() }}
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('Manage Subscription')</h5>
            </div>
            <div class="card-body">
                @if(!$plan->isFree())
                    @if($subscription->status !== 'cancelled')
                    <form action="{{ route('customer.subscription.toggle.auto.renew') }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn--{{ $subscription->auto_renew ? 'warning' : 'success' }} btn--sm w-100">
                            <i class="las la-sync"></i>
                            {{ $subscription->auto_renew ? __('Disable Auto-Renewal') : __('Enable Auto-Renewal') }}
                        </button>
                    </form>
                    @endif

                    @if($subscription->status === 'active')
                    <button type="button" class="btn btn--danger btn--sm w-100" data-bs-toggle="modal" data-bs-target="#cancelModal">
                        <i class="las la-times-circle"></i> @lang('Cancel Subscription')
                    </button>
                    @elseif($subscription->status === 'cancelled' && !$subscription->isExpired())
                    <form action="{{ route('customer.subscription.resume') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn--success btn--sm w-100">
                            <i class="las la-play-circle"></i> @lang('Resume Subscription')
                        </button>
                    </form>
                    @endif
                @else
                    <p class="text-muted text-center">@lang('You are on the free plan. Upgrade to premium for unlimited features and free insurance!')</p>
                    <a href="{{ route('customer.subscription.plans') }}" class="btn btn--primary btn--sm w-100">
                        <i class="las la-crown"></i> @lang('Upgrade Now')
                    </a>
                @endif

                <hr>

                <a href="{{ route('customer.subscription.history') }}" class="btn btn--base btn--sm w-100">
                    <i class="las la-history"></i> @lang('View History')
                </a>
            </div>
        </div>

        @if($plan->max_shipments_per_month)
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('Usage This Period')</h5>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <h2 class="mb-0">{{ $subscription->shipments_this_period }}</h2>
                    <p class="text-muted">@lang('of') {{ $plan->max_shipments_per_month }} @lang('shipments')</p>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar" role="progressbar"
                             style="width: {{ ($subscription->shipments_this_period / $plan->max_shipments_per_month) * 100 }}%">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('customer.subscription.cancel') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Cancel Subscription')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>@lang('Are you sure you want to cancel your subscription?')</p>
                    <p class="text-muted">@lang('You will have access until') {{ $subscription->expires_at->format('M d, Y') }}</p>
                    <div class="form-group">
                        <label>@lang('Reason for cancellation') (@lang('optional'))</label>
                        <textarea name="reason" class="form-control form--control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark btn--sm" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--danger btn--sm">@lang('Yes, Cancel')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
