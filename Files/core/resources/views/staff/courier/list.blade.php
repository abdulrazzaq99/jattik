@extends('staff.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="show-filter mb-3 text-end">
                <button type="button" class="btn btn-outline--primary showFilterBtn btn-sm"><i class="las la-filter"></i>
                    @lang('Filter')</button>
            </div>
            <div class="card responsive-filter-card  mb-3">
                <div class="card-body">
                    <form>
                        <div class="d-flex flex-wrap gap-4">
                            <div class="flex-grow-1">
                                <label>@lang('Search')</label>
                                <input type="text" name="search" value="{{ request()->search }}" class="form-control"
                                    placeholder="Order Number">
                            </div>
                            @if (request()->routeIs('staff.courier.manage.list'))
                                <div class="flex-grow-1">
                                    <label>@lang('Status')</label>
                                    <select name="status" class="form-control select2"
                                        data-minimum-results-for-search="-1">
                                        <option value="">@lang('All')</option>
                                        <option value="0">@lang('Queue')</option>
                                        <option value="1">@lang('Dispatch')</option>
                                        <option value="1">@lang('Upcoming')</option>
                                        <option value="2">@lang('Received')</option>
                                        <option value="3">@lang('Delevered')</option>
                                    </select>
                                </div>
                                <div class="flex-grow-1">
                                    <label>@lang('Payment Status')</label>
                                    <select name="payment_status" class="form-control select2"
                                        data-minimum-results-for-search="-1">
                                        <option value="">@lang('All')</option>
                                        <option value="1">@lang('Paid')</option>
                                        <option value="0">@lang('Unpaid')</option>
                                    </select>
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <label>@lang('Date')</label>
                                <input name="date" type="search"
                                    class="datepicker-here form-control bg--white pe-2 date-range"
                                    placeholder="@lang('Start Date - End Date')" autocomplete="off" value="{{ request()->date }}">
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i>
                                    @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

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
                                    <th>@lang('Estimate Delivery Date')</th>
                                    <th>@lang('Payment Status')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($courierLists as $courierInfo)
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
                                            <span class="fw-bold d-block">
                                                {{ showAmount(@$courierInfo->paymentInfo->final_amount) }}
                                            </span>
                                            <span>{{ $courierInfo->code }}</span>
                                        </td>
                                        <td>{{ showDateTime($courierInfo->created_at, 'd M Y') }}</td>
                                        <td>{{ showDateTime($courierInfo->estimate_date, 'd M Y') }}</td>
                                        <td>
                                            @if (@$courierInfo->paymentInfo->status == Status::PAID)
                                                <span class="badge badge--success">@lang('Paid')</span>
                                            @elseif(@$courierInfo->paymentInfo->status == Status::UNPAID)
                                                <span class="badge badge--danger">@lang('Unpaid')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($courierInfo->status == Status::COURIER_QUEUE)
                                                <span class="badge badge--warning">@lang('Sent in queue')</span>
                                            @elseif ($courierInfo->status == Status::COURIER_DISPATCH)
                                                @if (auth()->user()->branch_id == $courierInfo->sender_branch_id)
                                                    <span class="badge badge--warning">@lang('Dispatch')</span>
                                                @else
                                                    <span class="badge badge--primary">@lang('Upcoming')</span>
                                                @endif
                                            @elseif ($courierInfo->status == Status::COURIER_DELIVERYQUEUE)
                                                <span class="badge badge--danger">@lang('Delivery in queue')</span>
                                            @elseif($courierInfo->status == Status::COURIER_DELIVERED)
                                                <span class="badge badge--success">@lang('Delivery')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('staff.courier.invoice', encrypt($courierInfo->id)) }}"
                                                    title="" class="btn btn-sm btn-outline--info">
                                                    <i class="las la-file-invoice"></i> @lang('Invoice')
                                                </a>
                                                <a href="{{ route('staff.courier.details', encrypt($courierInfo->id)) }}"
                                                    title="" class="btn btn-sm btn-outline--primary">
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

                @if ($courierLists->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($courierLists) }}
                    </div>
                @endif
            </div>
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
        (function($) {
            "use strict";

            const datePicker = $('.date-range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                },
                showDropdowns: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 15 Days': [moment().subtract(14, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
                        .endOf('month')
                    ],
                    'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                },
                maxDate: moment()
            });
            const changeDatePickerText = (event, startDate, endDate) => {
                $(event.target).val(startDate.format('MMMM DD, YYYY') + ' - ' + endDate.format('MMMM DD, YYYY'));
            }

            $('.date-range').on('apply.daterangepicker', (event, picker) => changeDatePickerText(event, picker
                .startDate, picker.endDate));


            if ($('.date-range').val()) {
                let dateRange = $('.date-range').val().split(' - ');
                $('.date-range').data('daterangepicker').setStartDate(new Date(dateRange[0]));
                $('.date-range').data('daterangepicker').setEndDate(new Date(dateRange[1]));
            }

            let url = new URL(window.location).searchParams;
            if (url.get('status') != undefined && url.get('status') != '') {
                $('select[name=status]').find(`option[value=${url.get('status')}]`).attr('selected', true).change();
            }
            if (url.get('payment_status') != undefined && url.get('payment_status') != '') {
                $('select[name=payment_status]').find(`option[value=${url.get('payment_status')}]`).attr('selected',
                    true).change();
            }
        })(jQuery)
    </script>
@endpush
