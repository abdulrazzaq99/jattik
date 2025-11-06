@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-3 col-lg-5 col-md-5 col-sm-12">
            <div class="card overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Sender Staff')</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>@lang('Name')</span>
                            <span>{{ __($courierInfo->senderStaff->fullname) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>@lang('Email')</span>
                            <span>{{ $courierInfo->senderStaff->email }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span> @lang('Branch')</span>
                            <span>{{ __($courierInfo->senderBranch->name) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>@lang('Status')</span>
                            @if ($courierInfo->senderStaff->status == Status::ACTIVE_USER)
                                <span class="badge badge-pill badge--success">@lang('Active')</span>
                            @elseif($courierInfo->senderStaff->status == Status::BAN_USER)
                                <span class="badge badge-pill badge--danger">@lang('Banned')</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>

            @if ($courierInfo->receiver_staff_id)
                <div class="card overflow-hidden mt-30 box--shadow1">
                    <div class="card-body">
                        <h5 class="mb-20 text-muted">@lang('Receiver Staff')</h5>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Name')
                                <span>{{ __($courierInfo->receiverStaff->fullname) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Email')
                                <span>{{ $courierInfo->receiverStaff->email }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Branch')
                                <span>{{ __($courierInfo->receiverBranch->name) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Status')
                                @if ($courierInfo->receiverStaff->status == Status::ACTIVE_USER)
                                    <span class="badge badge-pill badge--success">@lang('Active')</span>
                                @elseif($courierInfo->receiverStaff->status == Status::BAN_USER)
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
                <div class="col-lg-6 ">
                    <div class="card border--dark">
                        <h5 class="card-header bg--dark">@lang('Sender Information')</h5>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Name')
                                    <span>{{ __(@$courierInfo->senderCustomer->fullname) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Email')
                                    <span>{{ @$courierInfo->senderCustomer->email }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Phone')
                                    <span>{{ @$courierInfo->senderCustomer->mobile }}</span>
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
                                    @lang('Address')
                                    <span>{{ __($courierInfo->senderCustomer->address) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Order Number')
                                    <span class="fw-bold">{{ $courierInfo->code }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
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
                                    @lang('City')
                                    <span>{{ __(@$courierInfo->receiverCustomer->city) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('State')
                                    <span>{{ __(@$courierInfo->receiverCustomer->state) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Address')
                                    <span>{{ __(@$courierInfo->receiverCustomer->address) }}</span>
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
                                            <th>@lang('Price')</th>
                                            <th>@lang('Qty')</th>
                                            <th>@lang('Subtotal')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($courierInfo->products as $courierProductInfo)
                                            <tr>
                                                <td>{{ __($courierProductInfo->type->name) }}</td>
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
                                        <span>{{ showDateTime($courierInfo->date, 'd M Y') }}</span>
                                    @else
                                        <span>@lang('N/A')</span>
                                    @endif
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Subtotal')
                                    <span>{{ showAmount($courierInfo->paymentInfo->amount) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Discount')
                                    <span>
                                        {{ showAmount($courierInfo->paymentInfo->discount) }}
                                        <small class="text--danger">
                                            ({{ getAmount($courierInfo->paymentInfo->percentage) }}%)
                                        </small>
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Total')
                                    <span>{{ showAmount($courierInfo->paymentInfo->final_amount) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    @lang('Status')
                                    @if ($courierInfo->paymentInfo->status == Status::PAID)
                                        <span class="badge badge--success">@lang('Paid')</span>
                                    @else
                                        <span class="badge badge--danger">@lang('Unpaid')</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.courier.info.index') }}" />
@endpush

@push('style')
    <style>
        .card-header {
            border-radius: 5px 5px 0 0 !important;
        }
    </style>
@endpush
