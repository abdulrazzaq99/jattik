@extends('manager.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-xxl-4 col-sm-6">
            <x-widget style="6" link="{{ route('manager.courier.sentQueue') }}" title="{{ __('Sent In Queue') }}"
                icon="las la-hourglass-start f-size--56" value="{{ $courierQueueCount }}" bg="purple" type="2"  outline=false/>
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget style="6" link="{{ route('manager.courier.upcoming') }}" title="{{ __('Upcoming Courier') }}"
                icon="las la-history f-size--56" value="{{ $upcomingCount }}" bg="cyan" type="2" outline=false  />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget style="6" link="{{ route('manager.courier.deliveryInQueue') }}" title="{{ __('Delivery In Queue') }}"
                icon="lab la-accessible-icon f-size--56" value="{{ $deliveryQueueCount }}" bg="pink" type="2" outline=false  />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget style="6" link="{{ route('manager.courier.sent') }}" title="{{ __('Total Sent') }}"
                icon="las la-check-double f-size--56" value="{{ $totalSentCount }}" bg="green" type="2" outline=false  />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget style="6" link="{{ route('manager.courier.delivered') }}" title="{{ __('Total Delivered') }}"
                icon="las la-list-alt f-size--56" value="{{ $courierDelivered }}" bg="deep-purple" type="2" outline=false  />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget style="6" link="{{ route('manager.courier.index') }}" title="{{ __('All Courier') }}"
                icon="las la-shipping-fast f-size--56" value="{{ $courierInfoCount }}" bg="teal" type="2" outline=false  />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget style="6" link="{{ route('manager.staff.index') }}" title="{{ __('Total Staff') }}"
                icon="las la-user-friends f-size--56" value="{{ $totalStaffCount }}" bg="primary" type="2" outline=false  />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget style="6" link="{{ route('manager.branch.index') }}" title="{{ __('Total Branch') }}"
                icon="las la-university f-size--56" value="{{ $branchCount }}" bg="lime" type="2" outline=false  />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget style="6" link="{{ route('manager.branch.income') }}" title="{{ __('Total Cash Collection') }}"
                icon="las la-money-bill-wave f-size--56" value="{{ showAmount($branchIncome) }}" bg="orange"
                type="2" outline=false  />
        </div>
    </div>

    <div class="row mt-50">
        <div class="col-lg-12">
            <div class="card  ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Sender Branch - Staff')</th>
                                    <th>@lang('Receiver Branch - Staff')</th>
                                    <th>@lang('Amount - Order Number')</th>
                                    <th>@lang('Creations Date')</th>
                                    <th>@lang('Payment Status')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($courierInfos as $courierInfo)
                                    <tr>
                                        <td>
                                            <span>{{ __($courierInfo->senderBranch->name) }}</span><br>
                                            <a
                                                href="{{ route('manager.staff.edit', encrypt($courierInfo->senderStaff->id)) }}"><span>@</span>{{ __($courierInfo->senderStaff->username) }}</a>
                                        </td>
                                        <td>
                                            <span>
                                                @if ($courierInfo->receiver_branch_id)
                                                    {{ __($courierInfo->receiverBranch->name) }}
                                                @else
                                                    @lang('N/A')
                                                @endif
                                            </span>
                                            <br>
                                            @if ($courierInfo->receiver_staff_id)
                                                <a
                                                    href="{{ route('manager.staff.edit', encrypt($courierInfo->receiverStaff->id)) }}"><span>@</span>{{ __($courierInfo->receiverStaff->username) }}</a>
                                            @else
                                                <span>@lang('N/A')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="fw-bold">{{ showAmount(@$courierInfo->paymentInfo->final_amount) }}</span>
                                            <br>
                                            <span>{{ $courierInfo->code }}</span>
                                        </td>
                                        <td>
                                            <span>{{ showDateTime($courierInfo->created_at, 'd M Y') }}</span><br>
                                            <span>{{ diffForHumans($courierInfo->created_at) }}</span>
                                        </td>
                                        <td>
                                            @if (!$courierInfo->paymentInfo)
                                                <span class="badge badge--danger">@lang('Unpaid')</span>
                                            @else
                                                @if ($courierInfo->paymentInfo->status == Status::PAID)
                                                    <span class="badge badge--success">@lang('Paid')</span>
                                                @elseif($courierInfo->paymentInfo->status == Status::UNPAID)
                                                    <span class="badge badge--danger">@lang('Unpaid')</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if ($courierInfo->status == Status::COURIER_QUEUE)
                                                @if (auth()->user()->branch_id == $courierInfo->sender_branch_id)
                                                    <span class="badge badge--danger">@lang('Sent In Queue')</span>
                                                @else
                                                    <span></span>
                                                @endif
                                            @elseif($courierInfo->status == Status::COURIER_DISPATCH)
                                                @if (auth()->user()->branch_id == $courierInfo->sender_branch_id)
                                                    <span class="badge badge--warning">@lang('Dispatch')</span>
                                                @else
                                                    <span class="badge badge--warning">@lang('Upcoming')</span>
                                                @endif
                                            @elseif($courierInfo->status == Status::COURIER_DELIVERYQUEUE)
                                                <span class="badge badge--primary">@lang('Delivery In Queue')</span>
                                            @elseif($courierInfo->status == Status::COURIER_DELIVERED)
                                                <span class="badge badge--success">@lang('Delivered')</span>
                                            @endif
                                        </td>

                                        <td>
                                            <a href="{{ route('manager.courier.invoice', encrypt($courierInfo->id)) }}"
                                                title="" class="btn btn-sm btn-outline--info"><i
                                                    class="las la-file-invoice"></i> @lang('Invoice')</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end">
        <h3>{{ __(auth()->user()->branch->name) }}</h3>
    </div>
@endpush
