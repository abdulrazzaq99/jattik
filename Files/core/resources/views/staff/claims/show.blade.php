@extends('staff.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-file-invoice-dollar"></i> @lang('Claim Details') - {{ $claim->claim_number }}
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Status Banner --}}
                    <div class="alert
                        @if($claim->status == 0) alert-warning
                        @elseif($claim->status == 1) alert-info
                        @elseif($claim->status == 2) alert-success
                        @elseif($claim->status == 3) alert-danger
                        @elseif($claim->status == 4) alert-success
                        @endif text-center mb-4">
                        <h4 class="mb-2">
                            @if($claim->status == 0)
                                <i class="las la-hourglass-half"></i> @lang('Pending Review')
                            @elseif($claim->status == 1)
                                <i class="las la-search"></i> @lang('Under Review')
                            @elseif($claim->status == 2)
                                <i class="las la-check-circle"></i> @lang('Approved')
                            @elseif($claim->status == 3)
                                <i class="las la-times-circle"></i> @lang('Rejected')
                            @elseif($claim->status == 4)
                                <i class="las la-money-check-alt"></i> @lang('Paid')
                            @endif
                        </h4>
                        @if(in_array($claim->status, [0, 1]))
                            <p class="mb-0">
                                <strong>{{ $claim->days_until_deadline }}</strong> @lang('days remaining in SLA')
                                @if($claim->isOverdue())
                                    <br><span class="text--danger">@lang('OVERDUE - Priority Processing Required')</span>
                                @endif
                            </p>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            {{-- Claim Information --}}
                            <h6 class="mb-3"><i class="las la-info-circle"></i> @lang('Claim Information')</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td width="40%"><strong>@lang('Claim Number'):</strong></td>
                                    <td><span class="badge badge--dark">{{ $claim->claim_number }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Type'):</strong></td>
                                    <td>
                                        <span class="badge badge--danger">
                                            {{ ucwords(str_replace('_', ' ', $claim->claim_type)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Submitted'):</strong></td>
                                    <td>{{ showDateTime($claim->submitted_at) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Processing Days'):</strong></td>
                                    <td>{{ $claim->processing_days ?? 0 }} / 10 days</td>
                                </tr>
                            </table>

                            {{-- Customer Information --}}
                            @if($claim->customer)
                                <div class="card bg-light mt-3">
                                    <div class="card-body">
                                        <h6><i class="las la-user"></i> @lang('Customer')</h6>
                                        <p class="mb-1"><strong>{{ $claim->customer->fullname }}</strong></p>
                                        <p class="mb-1"><small>{{ $claim->customer->email }}</small></p>
                                        <p class="mb-0"><small>{{ $claim->customer->mobile }}</small></p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            {{-- Financial Details --}}
                            <h6 class="mb-3"><i class="las la-dollar-sign"></i> @lang('Financial Details')</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td width="40%"><strong>@lang('Claimed'):</strong></td>
                                    <td><h5 class="text--primary mb-0">{{ showAmount($claim->claimed_amount) }}</h5></td>
                                </tr>
                                @if($claim->approved_amount)
                                    <tr>
                                        <td><strong>@lang('Approved'):</strong></td>
                                        <td><h5 class="text--success mb-0">{{ showAmount($claim->approved_amount) }}</h5></td>
                                    </tr>
                                @endif
                                @if($claim->status >= 2)
                                    <tr>
                                        <td><strong>@lang('Reviewed By'):</strong></td>
                                        <td>{{ $claim->reviewedBy->name ?? 'Staff' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>@lang('Decision Date'):</strong></td>
                                        <td>{{ showDateTime($claim->resolved_at) }}</td>
                                    </tr>
                                @endif
                            </table>

                            {{-- Shipment Link --}}
                            @if($claim->courierInfo)
                                <div class="card bg-light mt-3">
                                    <div class="card-body">
                                        <h6><i class="las la-box"></i> @lang('Shipment')</h6>
                                        <p class="mb-2"><strong>{{ $claim->courierInfo->code }}</strong></p>
                                        <a href="{{ route('staff.courier.details', $claim->courierInfo->id) }}"
                                           class="btn btn-sm btn--info">
                                            <i class="las la-eye"></i> @lang('View Details')
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- Description --}}
                    <div class="mb-4">
                        <h6><i class="las la-file-alt"></i> @lang('Claim Description')</h6>
                        <div class="alert alert-info">{{ $claim->description }}</div>
                    </div>

                    {{-- Evidence --}}
                    @if($claim->evidence && count(json_decode($claim->evidence, true) ?? []) > 0)
                        <div class="mb-4">
                            <h6><i class="las la-images"></i> @lang('Evidence')</h6>
                            <div class="row">
                                @foreach(json_decode($claim->evidence, true) as $evidence)
                                    <div class="col-md-2 mb-3">
                                        <div class="card">
                                            <div class="card-body text-center p-2">
                                                @if(str_contains($evidence['mime'] ?? '', 'image'))
                                                    <a href="{{ asset('storage/' . $evidence['path']) }}" target="_blank">
                                                        <img src="{{ asset('storage/' . $evidence['path']) }}"
                                                             class="img-fluid rounded" style="max-height: 100px;">
                                                    </a>
                                                @else
                                                    <i class="las la-file-pdf text--danger" style="font-size: 3rem;"></i>
                                                @endif
                                                <br>
                                                <a href="{{ asset('storage/' . $evidence['path']) }}" download
                                                   class="btn btn-sm btn-outline--primary mt-2">
                                                    <i class="las la-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Rejection Reason --}}
                    @if($claim->status == 3 && $claim->rejection_reason)
                        <div class="card border-danger">
                            <div class="card-header bg--danger">
                                <h6 class="card-title text-white mb-0">
                                    <i class="las la-times-circle"></i> @lang('Rejection Reason')
                                </h6>
                            </div>
                            <div class="card-body">{{ $claim->rejection_reason }}</div>
                        </div>
                    @endif

                    {{-- Admin Notes --}}
                    @if($claim->admin_notes)
                        <div class="card border-info mt-3">
                            <div class="card-header bg--info">
                                <h6 class="card-title text-white mb-0">
                                    <i class="las la-comment"></i> @lang('Admin Notes')
                                </h6>
                            </div>
                            <div class="card-body">{{ $claim->admin_notes }}</div>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('staff.claims.index') }}" class="btn btn-sm btn-outline--secondary">
                        <i class="las la-arrow-left"></i> @lang('Back to Claims')
                    </a>
                    @if($claim->status == 0)
                        <a href="{{ route('staff.claims.review', $claim->id) }}" class="btn btn-sm btn--primary float-end">
                            <i class="las la-clipboard-check"></i> @lang('Review This Claim')
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
