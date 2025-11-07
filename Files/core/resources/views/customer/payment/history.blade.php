@extends('customer.layouts.app')

@section('panel')
<div class="row gy-4">
    <div class="col-12">
        <div class="card">
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
                                <th>@lang('Payment For')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Method')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                <td>
                                    <strong>{{ $payment->payment_reference }}</strong>
                                    @if($payment->transaction_id)
                                        <br><small class="text-muted">TXN: {{ $payment->transaction_id }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($payment->payable_type === 'App\Models\CustomerSubscription')
                                        <i class="las la-crown text--primary"></i> @lang('Subscription')
                                        @if($payment->payable)
                                            <br><small class="text-muted">{{ $payment->payable->plan->name }}</small>
                                        @endif
                                    @elseif($payment->payable_type === 'App\Models\CourierInfo')
                                        <i class="las la-box text--info"></i> @lang('Shipment')
                                        @if($payment->payable)
                                            <br><small class="text-muted">{{ $payment->payable->code }}</small>
                                        @endif
                                    @elseif($payment->payable_type === 'App\Models\InsurancePolicy')
                                        <i class="las la-shield-alt text--success"></i> @lang('Insurance')
                                        @if($payment->payable)
                                            <br><small class="text-muted">{{ $payment->payable->policy_number }}</small>
                                        @endif
                                    @else
                                        @lang('Other')
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</strong>
                                </td>
                                <td>{{ ucfirst($payment->payment_method) }}</td>
                                <td>
                                    @if($payment->status == 'completed')
                                        <span class="badge badge--success">@lang('Completed')</span>
                                    @elseif($payment->status == 'pending')
                                        <span class="badge badge--warning">@lang('Pending')</span>
                                    @elseif($payment->status == 'failed')
                                        <span class="badge badge--danger">@lang('Failed')</span>
                                    @elseif($payment->status == 'refunded')
                                        <span class="badge badge--info">@lang('Refunded')</span>
                                    @else
                                        <span class="badge badge--dark">{{ ucfirst($payment->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        @if($payment->isCompleted())
                                            <a href="{{ route('customer.payment.invoice', $payment->id) }}"
                                               class="btn btn--sm btn--base"
                                               title="@lang('View Invoice')">
                                                <i class="las la-file-invoice"></i>
                                            </a>
                                        @endif

                                        @if($payment->isPending())
                                            <a href="{{ route('customer.payment.checkout', $payment->id) }}"
                                               class="btn btn--sm btn--primary"
                                               title="@lang('Complete Payment')">
                                                <i class="las la-arrow-right"></i>
                                            </a>
                                        @endif

                                        @if($payment->isFailed())
                                            <a href="{{ route('customer.payment.failed', $payment->id) }}"
                                               class="btn btn--sm btn--danger"
                                               title="@lang('View Details')">
                                                <i class="las la-exclamation-triangle"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <img src="{{ asset('assets/images/empty_list.png') }}" alt="empty" style="width: 200px;">
                                    <p class="text-muted mt-3">@lang('No payment history found')</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($payments->hasPages())
            <div class="card-footer">
                {{ $payments->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Payment Statistics -->
@if($payments->count() > 0)
<div class="row gy-4 mt-4">
    <div class="col-md-3">
        <x-widget
            style="6"
            icon="las la-check-circle"
            title="Total Payments"
            value="{{ $payments->total() }}"
            bg="green"
            outline="true"
        />
    </div>
    <div class="col-md-3">
        <x-widget
            style="6"
            icon="las la-money-bill-wave"
            title="Total Spent"
            value="{{ number_format($payments->where('status', 'completed')->sum('amount'), 2) }} SAR"
            bg="blue"
            outline="true"
        />
    </div>
    <div class="col-md-3">
        <x-widget
            style="6"
            icon="las la-clock"
            title="Pending"
            value="{{ $payments->where('status', 'pending')->count() }}"
            bg="warning"
            outline="true"
        />
    </div>
    <div class="col-md-3">
        <x-widget
            style="6"
            icon="las la-times-circle"
            title="Failed"
            value="{{ $payments->where('status', 'failed')->count() }}"
            bg="red"
            outline="true"
        />
    </div>
</div>
@endif
@endsection
