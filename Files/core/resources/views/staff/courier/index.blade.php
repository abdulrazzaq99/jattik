@extends('staff.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <form action="{{ route('staff.courier.store') }}" method="POST">
                @csrf
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 form-group">
                                <label for="">@lang('Estimate Date')</label>
                                <div class="input-group">
                                    <input name="estimate_date" type="text"
                                        class="datepicker-here form-control bg--white pe-2 date-range"
                                        placeholder="@lang('Estimate Delivery Date')" autocomplete="off"
                                        value="{{ old('estimate_date') }}">
                                    <span class="input-group-text"><i class="las la-calendar"></i></span>
                                </div>
                            </div>
                            <div class="col-6 form-group">
                                <label for="">@lang('Payment Status')</label>
                                <select class="select2 form-control" name="payment_status"
                                    data-minimum-results-for-search="-1">
                                    <option value="0" selected>@lang('UNPAID')</option>
                                    <option value="1">@lang('PAID')</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-xl-6">
                        <div class="card">
                            <h5 class="card-header bg--primary  text-white">@lang('Sender Information')</h5>
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-lg-6">
                                        <label>@lang('Email')</label>
                                        <input type="email" class="form-control" name="sender_customer_email"
                                            value="{{ old('sender_customer_email') }}" id="sender_email" required>
                                    </div>
                                    <div class=" form-group col-lg-6">
                                        <label>@lang('Phone')</label>
                                        <input type="text" class="form-control" name="sender_customer_phone"
                                            value="{{ old('sender_customer_phone') }}" id="sender_phone" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-6">
                                        <label>@lang('First Name')</label>
                                        <input type="text" class="form-control" name="sender_customer_firstname"
                                            value="{{ old('sender_customer_firstname') }}" required>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <label>@lang('Last Name')</label>
                                        <input type="text" class="form-control" name="sender_customer_lastname"
                                            value="{{ old('sender_customer_lastname') }}" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-6">
                                        <label>@lang('City')</label>
                                        <input type="text" class="form-control" name="sender_customer_city"
                                            value="{{ old('sender_customer_city') }}" required>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <label>@lang('State')</label>
                                        <input type="text" class="form-control" name="sender_customer_state"
                                            value="{{ old('sender_customer_state') }}" required>
                                    </div>
                                    <div class="form-group col-lg-12">
                                        <label>@lang('Address')</label>
                                        <input type="text" class="form-control" name="sender_customer_address"
                                            value="{{ old('sender_customer_address') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card mt-4 mt-xl-0">
                            <h5 class="card-header bg--primary  text-white">@lang('Receiver Information')</h5>
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-lg-6">
                                        <label>@lang('Email')</label>
                                        <input type="email" class="form-control" name="receiver_customer_email"
                                            value="{{ old('receiver_customer_email') }}" id="receiver_email" required>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <label>@lang('Phone')</label>
                                        <input type="text" class="form-control" name="receiver_customer_phone"
                                            value="{{ old('receiver_customer_phone') }}" id="receiver_phone" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-6">
                                        <label>@lang('First Name')</label>
                                        <input type="text" class="form-control" name="receiver_customer_firstname"
                                            value="{{ old('receiver_customer_firstname') }}" required>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <label>@lang('Last Name')</label>
                                        <input type="text" class="form-control" name="receiver_customer_lastname"
                                            value="{{ old('receiver_customer_lastname') }}" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-6">
                                        <label>@lang('City')</label>
                                        <input type="text" class="form-control" name="receiver_customer_city"
                                            value="{{ old('receiver_customer_city') }}" required>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <label>@lang('State')</label>
                                        <input type="text" class="form-control" name="receiver_customer_state"
                                            value="{{ old('receiver_customer_state') }}" required>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <label>@lang('Address')</label>
                                        <input type="text" class="form-control" name="receiver_customer_address"
                                            value="{{ old('receiver_customer_address') }}" required>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <label>@lang('Select Branch')</label>
                                        <select class="select2 form-control" name="branch"
                                            data-minimum-results-for-search="-1" required>
                                            <option value>@lang('Select One')</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}" @selected(old('branch') == $branch->id)>
                                                    {{ __($branch->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border--primary">
                            <h5 class="card-header bg--primary text-white">@lang('Courier Information')
                                <button type="button" class="btn btn-sm btn-outline-light float-end addUserData"><i
                                        class="la la-fw la-plus"></i>@lang('Add New One')
                                </button>
                            </h5>
                            <div class="card-body">
                                <div id="addedField">
                                    @if (old('items'))
                                        @foreach (old('items') as $item)
                                            <div class="row single-item gy-2">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <select class="select2 selected_type form-control" name="items[{{ $loop->index }}][type]" data-minimum-results-for-search="-1" required>
                                                            <option disabled selected value="">@lang('Select Courier/Parcel Type')</option>
                                                            @foreach ($types as $type)
                                                                <option value="{{ $type->id }}"
                                                                    @selected($item['type'] == $type->id)
                                                                    data-unit="{{ $type->unit->name }}"
                                                                    data-price="{{ getAmount($type->price) }}">
                                                                    {{ __($type->name) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control"
                                                            placeholder="@lang('Courier/Parcel Name')"
                                                            name="items[{{ $loop->index }}][name]"
                                                            value="{{ $item['name'] ?? '' }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <input type="number" class="form-control quantity"
                                                                value="{{ $item['quantity'] }}"
                                                                name="items[{{ $loop->index }}][quantity]" required>
                                                            <span class="input-group-text unit"><i
                                                                    class="las la-balance-scale"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control single-item-amount"
                                                                value="{{ $item['amount'] }}"
                                                                name="items[{{ $loop->index }}][amount]" required readonly>
                                                            <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <button class="btn btn--danger w-100 removeBtn w-100 h-45"
                                                        type="button">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="border-line-area">
                                    <h6 class="border-line-title">@lang('Summary')</h6>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <span class="input-group-text">@lang('Discount')</span>
                                            <input type="number" name="discount" value="{{ old('discount') }}"
                                                class="form-control bg-white text-dark discount" value="0">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class=" d-flex justify-content-end mt-2">
                                    <div class="col-md-3  d-flex justify-content-between">
                                        <span class="fw-bold">@lang('Subtotal'):</span>
                                        <div><span class="subtotal">0.00</span></div>
                                    </div>
                                </div>
                                <div class=" d-flex justify-content-end mt-2">
                                    <div class="col-md-3  d-flex justify-content-between">
                                        <span class="fw-bold">@lang('Total'):</span>
                                        <div><span class="total">0.00</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn--primary h-45 w-100 Submitbtn"> @lang('Submit')</button>
            </form>
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/viseradmin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/viseradmin/js/daterangepicker.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/viseradmin/css/daterangepicker.css') }}">
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            function getCustomerInformation(value, searchBy = 'mobile', customerType = 'sender') {
                $.ajax({
                    url: `{{ route('staff.search.customer') }}`,
                    method: 'get',
                    data: {
                        searchBy: searchBy,
                        search: value
                    },
                    success: function(response) {
                        if (!$.isEmptyObject(response)) {

                            $(`input[name='${customerType}_customer_email']`).val(response.email);
                            $(`input[name='${customerType}_customer_phone']`).val(response.mobile);

                            $(`input[name='${customerType}_customer_firstname']`).val(response.firstname)
                                .attr('readonly', true);
                            $(`input[name='${customerType}_customer_lastname']`).val(response.lastname)
                                .attr('readonly', true);
                            $(`input[name='${customerType}_customer_address']`).val(response.address).attr(
                                'readonly', true);
                            $(`input[name='${customerType}_customer_city']`).val(response.city).attr(
                                'readonly', true);
                            $(`input[name='${customerType}_customer_state']`).val(response.state).attr(
                                'readonly', true);
                        }
                    }
                });
            }

            $('#sender_phone').on('focusout', function() {
                let value = $(this).val();
                if (value.length > 0) {
                    getCustomerInformation(value);
                }
            });

            $('#sender_email').on('focusout', function() {
                let value = $(this).val();
                if (value.length > 0) {
                    getCustomerInformation(value, 'email');
                }
            });

            $('#receiver_phone').on('focusout', function() {
                let value = $(this).val();
                if (value.length > 0) {
                    getCustomerInformation(value, 'mobile', 'receiver');
                }
            });

            $('#receiver_email').on('focusout', function() {
                let value = $(this).val();
                if (value.length > 0) {
                    getCustomerInformation(value, 'email', 'receiver');
                }
            });

            $('.addUserData').on('click', function() {
                let length = $("#addedField").find('.single-item').length;
                let html = `
                <div class="row single-item mb-4">
                    <div class="col-md-2">
                        <div class="form-group">
                            <select class="select2 selected_type form-control" name="items[${length}][type]" data-minimum-results-for-search="-1" required>
                                <option disabled selected value="">@lang('Select Courier/Parcel Type')</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}" data-unit="{{ $type->unit->name }}" data-price={{ getAmount($type->price) }} >{{ __($type->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="@lang('Courier/Parcel Name')"  name="items[${length}][name]">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="number" class="form-control quantity" placeholder="@lang('Quantity')" disabled name="items[${length}][quantity]"  required>
                                <span class="input-group-text unit"><i class="las la-balance-scale"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text"  class="form-control single-item-amount" placeholder="@lang('Enter Price')" name="items[${length}][amount]" required readonly>
                                <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn--danger w-100 removeBtn h-45" type="button">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>`;
                $('#addedField').append(html)
            });

            $('#addedField').on('change', '.selected_type', function(e) {
                let unit = $(this).find('option:selected').data('unit');
                let parent = $(this).closest('.single-item');
                $(parent).find('.quantity').attr('disabled', false);
                $(parent).find('.unit').html(`${unit || '<i class="las la-balance-scale"></i>'}`);
                calculation();
            });

            $('#addedField').on('click', '.removeBtn', function(e) {
                let length = $("#addedField").find('.single-item').length;
                if (length <= 1) {
                    notify('warning', "@lang('At least one item required')");
                } else {
                    $(this).closest('.single-item').remove();
                }
                calculation();
            });

            let discount = 0;

            $('.discount').on('input', function(e) {
                this.value = this.value.replace(/^\.|[^\d\.]/g, '');

                discount = parseFloat($(this).val() || 0);
                if (discount >= 100) {
                    discount = 100;
                    notify('warning', "@lang('Discount can not bigger than 100 %')");
                    $(this).val(discount);
                }
                calculation();
            });

            $('#addedField').on('input', '.quantity', function(e) {
                this.value = this.value.replace(/^\.|[^\d\.]/g, '');

                let quantity = $(this).val();
                if (quantity <= 0) {
                    quantity = 0;
                }
                quantity = parseFloat(quantity);

                let parent = $(this).closest('.single-item');
                let price = parseFloat($(parent).find('.selected_type option:selected').data('price') || 0);
                let subTotal = price * quantity;

                $(parent).find('.single-item-amount').val(subTotal.toFixed(2));

                calculation()
            });

            function calculation() {
                let items = $('#addedField').find('.single-item');
                let subTotal = 0;

                $.each(items, function(i, item) {
                    let price = parseFloat($(item).find('.selected_type option:selected').data('price') || 0);
                    let quantity = parseFloat($(item).find('.quantity').val() || 0);
                    subTotal += price * quantity;
                });

                subTotal = parseFloat(subTotal);

                let discountAmount = (subTotal / 100) * discount;
                let total = subTotal - discountAmount;

                $('.subtotal').text(subTotal.toFixed(2));
                $('.total').text(total.toFixed(2));
            };

            let latestDate = `{{ now() }}`;
            $('input[name="estimate_date"]').daterangepicker({
                singleDatePicker: true,
                opens: "right",
                autoApply: true,
                startDate: latestDate,
                locale: {
                    format: "YYYY-MM-DD",
                }
            });

            @if (old('items'))
                calculation();
            @endif

        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .border-line-area {
            position: relative;
            text-align: center;
            z-index: 1;
        }

        .border-line-area::before {
            position: absolute;
            content: '';
            top: 50%;
            left: 0;
            width: 100%;
            height: 1px;
            background-color: #e5e5e5;
            z-index: -1;
        }

        .border-line-title {
            display: inline-block;
            padding: 3px 10px;
            background-color: #fff;
        }

        .card {
            border-radius: 5px !important;
        }

        .card-header:first-child {
            border-radius: 5px 5px 0 0 !important;
        }
    </style>
@endpush
