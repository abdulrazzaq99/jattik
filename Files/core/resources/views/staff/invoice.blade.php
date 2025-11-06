@extends('staff.layouts.app')
@section('panel')
    <div class="card">
        <div class="card-body">
            <div id="printInvoice">
                <div class="content-header">
                    <div class="d-flex justify-content-between">
                        <div class="fw-bold">
                            @lang('Invoice Number'):
                            <small>#{{ $courierInfo->invoice_id }}</small>
                            <br>
                            @lang('Date'):
                            {{ showDateTime($courierInfo->created_at, 'd M Y') }}
                            <br>
                            @lang('Estimate Delivery Date'):
                            {{ showDateTime($courierInfo->estimate_date, 'd M Y') }}
                        </div>
                    </div>
                </div>
                <div class="invoice">
                    <div class="d-flex justify-content-between mt-3">
                        <div class="text-center">
                            @php
                                $barCode = DNS1D::getBarcodePNG($courierInfo->code, 'C128');
                                echo '<img src="data:image/png;base64, ' . $barCode . '" alt="barcode" />';
                            @endphp
                            <br>
                            <span>{{ $courierInfo->code }}</span>
                        </div>
                        <div>
                            <b>@lang('Order Id'):</b> {{ $courierInfo->code }}<br>
                            <b>@lang('Payment Status'):</b>
                            @if ($courierInfo->paymentInfo->status == Status::PAID)
                                <span class="badge badge--success">@lang('Paid')</span>
                            @else
                                <span class="badge badge--danger">@lang('Unpaid')</span>
                            @endif
                            <br>
                            <b>@lang('Sender Branch'):</b> {{ __($courierInfo->senderBranch->name) }}<br>
                            <b>@lang('Receiver Branch'):</b> {{ __($courierInfo->receiverBranch->name) }}
                        </div>
                    </div>
                    <hr>
                    <div class="invoice-info d-flex justify-content-between">
                        <div>
                            @lang('From')
                            <address>
                                <strong>{{ __(@$courierInfo->senderCustomer->fullname) }}</strong><br>
                                @lang('City'): {{ __(@$courierInfo->senderCustomer->city) }}<br>
                                @lang('State'): {{ __(@$courierInfo->senderCustomer->state) }}<br>
                                @lang('Address'): {{ __($courierInfo->senderCustomer->address) }}<br>
                                @lang('Phone'): {{ @$courierInfo->senderCustomer->mobile }}<br>
                                @lang('Email'): {{ @$courierInfo->senderCustomer->email }}
                            </address>
                        </div>
                        <div>
                            @lang('To')
                            <address>
                                <strong>{{ __(@$courierInfo->receiverCustomer->fullname) }}</strong><br>
                                @lang('City'): {{ __(@$courierInfo->receiverCustomer->city) }}<br>
                                @lang('State'): {{ __(@$courierInfo->receiverCustomer->state) }}<br>
                                @lang('Address'): {{ __(@$courierInfo->receiverCustomer->address) }}<br>
                                @lang('Phone'): {{ @$courierInfo->receiverCustomer->mobile }}<br>
                                @lang('Email'): {{ @$courierInfo->receiverCustomer->email }}
                            </address>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
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
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ __(@$courierProductInfo->type->name) }}</td>
                                            <td>{{ __(@$courierProductInfo->parcel_name) }}</td>
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

                    <div class="row mt-30 mb-none-30">
                        <div class="col-lg-12 mb-30">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th>@lang('Subtotal'):</th>
                                            <td>{{ showAmount($courierInfo->paymentInfo->amount) }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('Discount'):</th>
                                            <td>
                                                {{ showAmount($courierInfo->paymentInfo->discount) }}
                                                <small class="text--danger">
                                                    ({{ getAmount($courierInfo->paymentInfo->percentage) }}%)
                                                </small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>@lang('Total'):</th>
                                            <td>{{ showAmount($courierInfo->paymentInfo->final_amount) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
            </div>

            <div class="row no-print">
                <div class="col-sm-12">
                    <div class="float-sm-end">
                        @if (!$courierInfo->paymentInfo)
                            <td>@lang('Unpaid')</td>
                        @else
                            @if ($courierInfo->status == Status::UNPAID && $courierInfo->paymentInfo->status == Status::UNPAID)
                                <button type="button" class="btn btn-outline--success m-1 payment"
                                    data-code="{{ $courierInfo->code }}">
                                    <i class="fa fa-credit-card"></i> @lang('Make Payment')
                                </button>
                            @endif
                        @endif
                        <button class="btn btn-outline--primary m-1 printInvoice">
                            <i class="las la-download"></i>@lang('Print')
                        </button>
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
                    <button type="button" class="close" data-bs-dismiss="modal">
                        <i class="las la-times"></i> </button>
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
@endsection

@push('script-lib')
    <script src="{{ asset('assets/viseradmin/js/printThis.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.printInvoice').click(function() {
                $("#printInvoice").printThis();
            });

            $('.payment').on('click', function() {
                var modal = $('#paymentBy');
                modal.find('input[name=code]').val($(this).data('code'))
                modal.modal('show');
            });
        })(jQuery)
    </script>
@endpush
