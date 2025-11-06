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
                                                {{ getAmount(@$courierInfo->paymentInfo->final_amount) }}
                                            </span>
                                            <span>{{ $courierInfo->code }}</span>
                                        </td>
                                        <td>{{ showDateTime($courierInfo->created_at, 'd M Y') }} </td>
                                        <td>{{ showDateTime($courierInfo->estimate_date, 'd M Y') }}</td>
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
                                            <button type="button" class="btn btn-sm btn-outline--primary"
                                                data-bs-toggle="dropdown" aria-expanded="false"><i
                                                    class="las la-ellipsis-v"></i>@lang('Action')
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
                                                    data-action="{{ route('staff.courier.receive', $courierInfo->id) }}"
                                                    data-question="@lang('Are you sure to received this courier?')">
                                                    <i class="las la-check-circle"></i> @lang('Received')
                                                </button>
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

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Courier Code" />

    <x-search-date-field placeholder="Start date - End date" />
@endpush
