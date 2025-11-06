@extends('staff.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-3 col-lg-5 col-md-5 col-sm-12">
            <div class="card  overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Sender Staff')</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Fullname')
                            <span>{{ __($courierInfo->senderStaff->fullname) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Email')
                            <span>{{ __($courierInfo->senderStaff->email) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Branch')
                            <span>{{ __($courierInfo->senderBranch->name) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Status')
                            @if ($courierInfo->senderStaff->status == Status::ENABLE)
                                <span class="badge badge-pill badge--success">@lang('Active')</span>
                            @elseif($courierInfo->senderStaff->status == Status::DISABLE)
                                <span class="badge badge-pill badge--danger">@lang('Banned')</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>

            @if ($courierInfo->receiver_staff_id)
                <div class="card  overflow-hidden mt-30 box--shadow1">
                    <div class="card-body">
                        <h5 class="mb-20 text-muted">@lang('Receiver Staff')</h5>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Fullname')
                                <span>{{ __($courierInfo->receiverStaff->fullname) }}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Email')
                                <span>{{ __($courierInfo->receiverStaff->email) }}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Branch')
                                <span>{{ __($courierInfo->receiverBranch->name) }}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Status')
                                @if ($courierInfo->receiverStaff->status == Status::ENABLE)
                                    <span class="badge badge-pill badge--success">@lang('Active')</span>
                                @elseif($courierInfo->receiverStaff->status == Status::DISABLE)
                                    <span class="badge badge-pill badge--danger">@lang('Banned')</span>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-xl-9 col-lg-7 col-md-7 col-sm-12">
            <div class="row mb-30">
                <div class="col-xl-6">
                    <div class="card border--dark mb-4 mb-xl-0 mt-4 mt-md-0">
                        <h5 class="card-header bg--dark">@lang('Sender Information')</h5>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Name')
                                    <span>{{ __(@$courierInfo->senderCustomer->fullname) }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Email')
                                    <span>{{ __(@$courierInfo->senderCustomer->email) }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Phone')
                                    <span>{{ __(@$courierInfo->senderCustomer->mobile) }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Address')
                                    <span>{{ __($courierInfo->senderCustomer->address) }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('City')
                                    <span>{{ __(@$courierInfo->senderCustomer->city) }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('State')
                                    <span>{{ __(@$courierInfo->senderCustomer->state) }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Order Number')
                                    <span class="fw-bold">{{ $courierInfo->code }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card border--dark">
                        <h5 class="card-header bg--dark">@lang('Receiver Information')</h5>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Name')
                                    <span>{{ __(@$courierInfo->receiverCustomer->fullname) }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Email')
                                    <span>{{ @$courierInfo->receiverCustomer->email }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Phone')
                                    <span>{{ @$courierInfo->receiverCustomer->mobile }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Address')
                                    <span>{{ __(@$courierInfo->receiverCustomer->address) }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('City')
                                    <span>{{ __(@$courierInfo->receiverCustomer->city) }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('State')
                                    <span>{{ __(@$courierInfo->receiverCustomer->state) }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Status')
                                    @if ($courierInfo->status != Status::COURIER_DELIVERED)
                                        <span class="badge badge--primary fw-bold">@lang('Waiting')</span>
                                    @elseif($courierInfo->status == Status::COURIER_DELIVERED)
                                        <span class="badge badge--success">@lang('Delivery')</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-30">
                <div class="col-lg-12">
                    <div class="card border--dark">
                        <h5 class="card-header bg--dark">@lang('Courier Details')</h5>
                        <div class="card-body">
                            <div class="table-responsive--md  table-responsive">
                                <table class="table table--light style--two">
                                    <thead>
                                        <tr>
                                            <th>@lang('Courier Type')</th>
                                            <th>@lang('Product Name')</th>
                                            <th>@lang('Price')</th>
                                            <th>@lang('Qty')</th>
                                            <th>@lang('Subtotal')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($courierInfo->products as $courierProductInfo)
                                            <tr>
                                                <td>{{ __($courierProductInfo->type->name) }}</td>
                                                <td>{{ __($courierProductInfo->parcel_name) }}</td>
                                                <td>{{ showAmount($courierProductInfo->type_price) }}</td>
                                                <td>
                                                    {{ $courierProductInfo->qty }}
                                                    {{ __(@$courierProductInfo->type->unit->name) }}
                                                </td>
                                                <td>{{ showAmount($courierProductInfo->fee) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-30">
                <div class="col-lg-12 mt-2">
                    <div class="card border--dark">
                        <h5 class="card-header bg--dark">@lang('Payment Information')</h5>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Payment Received By ')
                                    @if (!empty($courierInfo->paymentInfo->branch_id))
                                        <span>{{ __(@$courierInfo->paymentInfo->branch->name) }}</span>
                                    @else
                                        <span>@lang('N/A')</span>
                                    @endif
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Date')
                                    @if (!empty($courierInfo->paymentInfo->date))
                                        <span>{{ showDateTime($courierInfo->paymentInfo->date, 'd M Y') }}</span>
                                    @else
                                        <span>@lang('N/A')</span>
                                    @endif
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Subtotal')
                                    @if(!$courierInfo->paymentInfo)
                                        <p>@lang('Unpaid')</p>
                                    @else
                                        <span>{{ showAmount($courierInfo->paymentInfo->amount) }}</span>
                                    @endif
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Discount')
                                    @if(!$courierInfo->paymentInfo)
                                        <p>@lang('Unpaid')</p>
                                    @else
                                        <span>
                                            {{ showAmount($courierInfo->paymentInfo->discount) }}
                                            <small class="text--danger">({{ getAmount($courierInfo->paymentInfo->percentage)}}%)</small>
                                        </span>
                                    @endif
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Total')
                                    @if(!$courierInfo->paymentInfo)
                                        <p>@lang('Unpaid')</p>
                                    @else
                                        <span>{{ showAmount($courierInfo->paymentInfo->final_amount) }}</span>
                                    @endif
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Status')
                                    @if(!$courierInfo->paymentInfo)
                                        <p>@lang('Unpaid')</p>
                                    @else
                                        @if ($courierInfo->paymentInfo->status == Status::PAID)
                                            <span class="badge badge--success">@lang('Paid')</span>
                                        @else
                                            <span class="badge badge--danger">@lang('Unpaid')</span>
                                        @endif
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />

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
    <x-back route="{{ route('staff.courier.manage.list') }}" />

    <a href="{{ route('staff.courier.invoice', encrypt($courierInfo->id)) }}" title=""
        class="btn btn-sm btn-outline--info">
        <i class="las la-file-invoice"></i>
        @lang('Invoice')
    </a>

    @if ($courierInfo->status == Status::COURIER_DELIVERYQUEUE && $courierInfo->paymentInfo->status == Status::PAID)
        <button class="btn btn-sm btn-outline--info delivery"
            data-code="{{ $courierInfo->code }}"><i class="las la-truck"></i>
            @lang('Delivery')</button>
    @endif

    @if (($courierInfo->status == Status::COURIER_DELIVERYQUEUE || $courierInfo->status == Status::COURIER_QUEUE) &&
                $courierInfo->paymentInfo->status == Status::UNPAID)
            <button class="btn btn-sm btn-outline--success payment"
                data-code="{{ $courierInfo->code }}"><i class="las la-credit-card"></i>
                @lang('Payment')</button>
    @endif

    @if ($courierInfo->status == Status::COURIER_QUEUE || $courierInfo->status == Status::COURIER_DISPATCH)
        @php
            $class = '';
            if ($courierInfo->sender_branch_id == $staff->branch_id) {
                $icon = 'la-arrow-circle-right';
                $text = 'Dispatch';
                $route = route('staff.courier.dispatched', $courierInfo->id);
                $question = "Are you sure to despatched this courier";
                if($courierInfo->sender_branch_id == $staff->branch_id && $courierInfo->status == Status::COURIER_DISPATCH){
                    $class = 'd-none';
                }
            } else {
                $icon = 'la-check-circle';
                $text = 'Received';
                $route = route('staff.courier.receive', $courierInfo->id);
                $question = "Are you sure to receive this courier";
            }
        @endphp

        <button type="button" class="btn btn-sm btn-outline--warning confirmationBtn {{ $class }}"
            data-action="{{ $route }}" data-question="{{ __($question) }}?">
            <i class="las {{ $icon }}"></i>
            @lang($text)
        </button>
    @endif
@endpush

@push('script')
    <script>
        (function($) {
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
        })(jQuery)
    </script>
@endpush

@push('style')
    <style>
        .card-header{
            border-radius: 5px 5px 0 0 !important;
        }
    </style>
@endpush
