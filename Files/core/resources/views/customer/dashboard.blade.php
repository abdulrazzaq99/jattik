@extends('customer.layouts.app')

@section('panel')
    {{-- Statistics Widgets --}}
    <div class="row gy-4">
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('customer.sent.couriers') }}" icon="las la-paper-plane" title="{{ __('Total Sent') }}"
                value="{{ $totalSent }}" bg="green" outline="true"/>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('customer.received.couriers') }}" icon="las la-inbox" title="{{ __('Total Received') }}"
                value="{{ $totalReceived }}" bg="primary" outline="true" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('customer.sent.couriers') }}" icon="las la-truck"
                title="{{ __('Active Couriers') }}" value="{{ $activeCouriers }}" bg="orange" outline="true" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('customer.received.couriers') }}" icon="las la-check-circle"
                title="{{ __('Delivered') }}" value="{{ $deliveredCouriers }}" bg="info" outline="true" />
        </div>
    </div>

    {{-- Recent Couriers Tables --}}
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Tracking Code')</th>
                                    <th>@lang('Receiver')</th>
                                    <th>@lang('Branch')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Date')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSent as $courier)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $courier->code }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $courier->receiverCustomer->fullname ?? 'N/A' }}</span><br>
                                            <span class="small text-muted">{{ $courier->receiverCustomer->mobile ?? 'N/A' }}</span>
                                        </td>
                                        <td>{{ $courier->receiverBranch->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($courier->status == 0)
                                                <span class="badge badge--warning">@lang('Queue')</span>
                                            @elseif($courier->status == 1)
                                                <span class="badge badge--primary">@lang('In Transit')</span>
                                            @elseif($courier->status == 2)
                                                <span class="badge badge--info">@lang('Delivery Queue')</span>
                                            @elseif($courier->status == 3)
                                                <span class="badge badge--success">@lang('Delivered')</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ showDateTime($courier->created_at) }}<br>
                                            <span class="small text-muted">{{ diffForHumans($courier->created_at) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">@lang('No sent couriers yet')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($recentSent->count() > 0)
                <div class="card-footer">
                    <a href="{{ route('customer.sent.couriers') }}" class="btn btn-sm btn--primary">@lang('View All Sent Couriers')</a>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
