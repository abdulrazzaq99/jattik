@extends('staff.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card  ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" class="checkAll"> @lang('Select All')
                                    </th>
                                    <th>@lang('Sender Branch - Staff')</th>
                                    <th>@lang('Receiver Branch - Staff')</th>
                                    <th>@lang('Amount - Order Number')</th>
                                    <th>@lang('Creations Date')</th>
                                    <th>@lang('Estimate Delivery Date')</th>
                                    <th>@lang('Payment Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($courierLists as $courierInfo)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="childCheckBox" data-id="{{ $courierInfo->id }}">
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ __($courierInfo->senderBranch->name) }}</span><br>
                                            <small class="text-mute">
                                                <i>{{ __($courierInfo->senderStaff->fullname) }}</i>
                                            </small>
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
                                            <span class="fw-bold">
                                                {{ showAmount(@$courierInfo->paymentInfo->final_amount) }}
                                            </span>
                                            <br>
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
                                            <button class="btn btn-sm btn-outline--primary" id="actionButton" data-bs-toggle="dropdown">
                                                <i class="las la-ellipsis-v"></i> @lang('Action')
                                            </button>
                                            <div class="dropdown-menu p-0">
                                                <a href="{{ route('staff.courier.invoice', encrypt($courierInfo->id)) }}"
                                                    title="" class="dropdown-item">
                                                    <i class="las la-file-invoice"></i> @lang('Invoice')
                                                </a>
                                                <a href="{{ route('staff.courier.details', encrypt($courierInfo->id)) }}"
                                                    title="" class="dropdown-item">
                                                    <i class="las la-info-circle"></i> @lang('Details')
                                                </a>
                                                <button type="button"
                                                    class="dropdown-item confirmationBtn"
                                                    data-action="{{ route('staff.courier.dispatched', $courierInfo->id) }}"
                                                    data-question="@lang('Are you sure to dispatch this courier?')">
                                                    <i class="las la-arrow-circle-right"></i> @lang('Dispatch')
                                                </button>
                                                <a href="{{ route('staff.courier.edit', encrypt($courierInfo->id)) }}"
                                                    class="dropdown-item">
                                                    <i class="las la-pen"></i> @lang('Edit')
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                                <tr class="d-none dispatch">
                                    <td colspan="8">
                                        <button class="btn btn-sm btn--primary h-45 w-100 " id="dispatch_all">
                                            <i class="las la-arrow-circle-right "></i> @lang('Dispatch')
                                        </button>
                                    </td>
                                </tr>
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

    <x-confirmation-modal />
@endsection


@push('breadcrumb-plugins')
    <x-search-form placeholder="Search here..." />
    <x-search-date-field placeholder="Start date - End date" />
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";
            $(".childCheckBox").on('change', function(e) {
                let totalLength = $(".childCheckBox").length;
                let checkedLength = $(".childCheckBox:checked").length;
                if (totalLength == checkedLength) {
                    $('.checkAll').prop('checked', true);
                } else {
                    $('.checkAll').prop('checked', false);
                }
                if (checkedLength) {
                    $('.dispatch').removeClass('d-none')
                } else {
                    $('.dispatch').addClass('d-none')
                }
            });

            $('.checkAll').on('change', function() {
                if ($('.checkAll:checked').length) {
                    $('.childCheckBox').prop('checked', true);
                } else {
                    $('.childCheckBox').prop('checked', false);
                }
                $(".childCheckBox").change();
            });
            $('#dispatch_all').on('click', function() {
                let ids = [];
                $('.childCheckBox:checked').each(function() {
                    ids.push($(this).attr('data-id'))
                })
                let id = ids.join(',')
                $.ajax({
                    url: "{{ route('staff.courier.dispatch.all') }}",
                    type: "post",
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        notify('success', 'Courier dispatched successfully')
                        location.reload();
                    }
                })
            });

        })(jQuery)
    </script>
@endpush
