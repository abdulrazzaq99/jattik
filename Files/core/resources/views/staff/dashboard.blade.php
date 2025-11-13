@extends('staff.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-xxl-4 col-sm-6">
            <x-widget style="6" link="{{ route('staff.courier.sent.queue') }}" title="{{ __('Sent In Queue') }}" icon="las la-hourglass-start"
                value="{{ $sentInQueue }}" bg="purple" type="2"  />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget style="6" link="{{ route('staff.courier.upcoming') }}" title="{{ __('Upcoming Courier') }}" icon="las la-history"
                value="{{ $upcomingCourier }}" bg="cyan" type="2"  />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget style="6" link="{{ route('staff.courier.dispatch') }}" title="{{ __('Total Shipping Courier') }}" icon="las la-dolly"
                value="{{ $dispatchCourier }}" bg="primary" type="2"  />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget style="6" link="{{ route('staff.courier.delivery.queue') }}" title="{{ __('Delivery in queue') }}" icon="lab la-accessible-icon"
                value="{{ $deliveryInQueue }}" bg="pink" type="2"  />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget style="6" link="{{ route('staff.courier.manage.sent.list') }}" title="{{ __('Total Sent') }}" icon="las la-check-double"
                value="{{ $totalSent }}" bg="green" type="2"  />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget style="6" link="{{ route('staff.courier.manage.delivered') }}" title="{{ __('Total Delivered') }}" icon="las la-list-alt"
                value="{{ $totalDelivery }}" bg="deep-purple" type="2"  />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget style="6" link="{{ route('staff.branch.index') }}" title="{{ __('Total Branch') }}" icon="las la-university" value="{{ $branchCount }}"
                bg="lime" type="2"  />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget style="6" link="{{ route('staff.cash.courier.income') }}" title="{{ __('Total Cash Collection') }}" icon="las la-money-bill-wave"
                value="{{ showAmount($cashCollection) }}" bg="orange" type="2"  />
        </div>
        <div class="col-xxl-4 col-sm-6">
            <x-widget style="6" link="{{ route('staff.courier.manage.list') }}" title="{{ __('All Courier') }}" icon="las la-shipping-fast"
                value="{{ $totalCourier }}" bg="teal" type="2"  />
        </div>
    </div>

    <div class="row mt-30 ">
        <div class="col-lg-12">
            <div class="card  ">
                <div class="card-header mb-1">
                    <h6>@lang('Upcoming Courier')</h6>
                </div>
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
                                @forelse($courierDelivery as $courierInfo)
                                    <tr>
                                        <td>
                                            <span>{{ __($courierInfo->senderBranch->name) }}</span><br>
                                            {{ __($courierInfo->senderStaff->fullname) }}
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
                                                {{ __($courierInfo->receiverStaff->fullname) }}
                                            @else
                                                <span>@lang('N/A')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ getAmount($courierInfo->paymentInfo->final_amount) }}</span>
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
                                                <span class="badge badge--primary">@lang('Received')</span>
                                            @elseif($courierInfo->status == Status::COURIER_UPCOMING)
                                                <span class="badge badge--warning">@lang('Upcoming')</span>
                                            @elseif($courierInfo->status == Status::COURIER_DELIVERED)
                                                <span class="badge badge--success">@lang('Delivered')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--primary" data-bs-toggle="dropdown"
                                                aria-expanded="false"><i class="las la-ellipsis-v"></i>@lang('Action')
                                            </button>
                                            <div class="dropdown-menu p-0">
                                                @if ($courierInfo->status == Status::COURIER_DELIVERYQUEUE && $courierInfo->paymentInfo->status == Status::COURIER_UPCOMING)
                                                    <a href="javascript:void(0)" title="" class="dropdown-item delivery"
                                                        data-code="{{ $courierInfo->code }}">
                                                        <i class="las la-truck"></i> @lang('Delivery')
                                                    </a>
                                                @endif
                                                @if ($courierInfo->status == Status::COURIER_DELIVERYQUEUE && $courierInfo->paymentInfo->status == Status::COURIER_QUEUE)
                                                    <a href="javascript:void(0)" title="" class="dropdown-item payment"
                                                        data-code="{{ $courierInfo->code }}">
                                                        <i class="las la-credit-card"></i> @lang('Payment')
                                                    </a>
                                                @endif
                                                <a href="{{ route('staff.courier.invoice', encrypt($courierInfo->id)) }}" title=""
                                                    class="dropdown-item">
                                                    <i class="las la-file-invoice"></i> @lang('Invoice')
                                                </a>
                                                <a href="{{ route('staff.courier.details', encrypt($courierInfo->id)) }}" title=""
                                                    class="dropdown-item">
                                                    <i class="las la-info-circle"></i> @lang('Details')
                                                </a>
                                            </div>
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


    <div class="modal fade" id="paymentBy">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="" lass="modal-title" id="exampleModalLabel">@lang('Payment Confirmation')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('staff.courier.payment') }}" method="POST">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="code">
                    <div class="modal-body">
                        <p>@lang('Are you sure to collect this amount?')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deliveryBy">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="" lass="modal-title" id="exampleModalLabel">@lang('Delivery Confirmation')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span class="fa fa-times"></span>
                    </button>
                </div>
                <form action="{{ route('staff.courier.delivery') }}" method="POST">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="code">
                    <div class="modal-body">
                        <p>@lang('Are you sure to delivery this courier?')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary">@lang('Confirm')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end">
        <h3>{{ __(auth()->user()->branch->name) }}</h3>
    </div>
@endpush


@push('script')
    <script>
        (function() {
            'use strict';
            $('.payment').on('click', function() {
                var modal = $('#paymentBy');
                modal.find('input[name=code]').val($(this).data('code'))
                modal.modal('show');
            });

            $('.delivery').on('click', function() {
                var modal = $('#deliveryBy');
                modal.find('input[name=code]').val($(this).data('code'))
                modal.modal('show');
            });
        })(jQuery())
    </script>
@endpush
