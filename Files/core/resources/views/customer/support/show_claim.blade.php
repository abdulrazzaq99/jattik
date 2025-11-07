@extends('customer.layouts.app')

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
                                <i class="las la-hourglass-half"></i> @lang('Claim Pending Review')
                            @elseif($claim->status == 1)
                                <i class="las la-search"></i> @lang('Claim Under Review')
                            @elseif($claim->status == 2)
                                <i class="las la-check-circle"></i> @lang('Claim Approved')
                            @elseif($claim->status == 3)
                                <i class="las la-times-circle"></i> @lang('Claim Rejected')
                            @elseif($claim->status == 4)
                                <i class="las la-money-check-alt"></i> @lang('Compensation Paid')
                            @endif
                        </h4>
                        @if(in_array($claim->status, [0, 1]))
                            <p class="mb-0">
                                <strong>{{ $claim->days_until_deadline }}</strong> @lang('days remaining in SLA')
                                @if($claim->days_until_deadline <= 2)
                                    <br><small>@lang('Your claim will be processed soon!')</small>
                                @endif
                            </p>
                        @endif
                    </div>

                    <div class="row">
                        {{-- Left Column --}}
                        <div class="col-md-6">
                            {{-- Claim Information --}}
                            <h6 class="mb-3"><i class="las la-info-circle"></i> @lang('Claim Information')</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>@lang('Claim Number'):</strong></td>
                                    <td><span class="badge badge--dark">{{ $claim->claim_number }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Claim Type'):</strong></td>
                                    <td>
                                        @if($claim->claim_type == 'damage')
                                            <span class="badge badge--danger">@lang('Damage')</span>
                                        @elseif($claim->claim_type == 'loss')
                                            <span class="badge badge--danger">@lang('Loss')</span>
                                        @elseif($claim->claim_type == 'delay_compensation')
                                            <span class="badge badge--warning">@lang('Delay')</span>
                                        @else
                                            <span class="badge badge--info">@lang('Other')</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Submitted'):</strong></td>
                                    <td>
                                        {{ showDateTime($claim->submitted_at) }}<br>
                                        <small class="text-muted">{{ diffForHumans($claim->submitted_at) }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>@lang('Processing Days'):</strong></td>
                                    <td>{{ $claim->processing_days ?? 0 }} / 10 days</td>
                                </tr>
                            </table>

                            {{-- Shipment Information --}}
                            @if($claim->courierInfo)
                                <div class="card bg-light mt-3">
                                    <div class="card-body">
                                        <h6><i class="las la-box"></i> @lang('Related Shipment')</h6>
                                        <p class="mb-1"><strong>@lang('Tracking'):</strong> {{ $claim->courierInfo->code }}</p>
                                        <p class="mb-0"><strong>@lang('Receiver'):</strong> {{ $claim->courierInfo->receiverCustomer->fullname ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Right Column --}}
                        <div class="col-md-6">
                            {{-- Financial Information --}}
                            <h6 class="mb-3"><i class="las la-dollar-sign"></i> @lang('Financial Details')</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>@lang('Claimed Amount'):</strong></td>
                                    <td><h5 class="text--primary">{{ showAmount($claim->claimed_amount) }}</h5></td>
                                </tr>
                                @if($claim->approved_amount)
                                    <tr>
                                        <td><strong>@lang('Approved Amount'):</strong></td>
                                        <td><h5 class="text--success">{{ showAmount($claim->approved_amount) }}</h5></td>
                                    </tr>
                                @endif
                                @if($claim->status == 2 || $claim->status == 4)
                                    <tr>
                                        <td><strong>@lang('Approved By'):</strong></td>
                                        <td>{{ $claim->reviewedBy->name ?? 'Admin' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>@lang('Approved Date'):</strong></td>
                                        <td>{{ showDateTime($claim->resolved_at) }}</td>
                                    </tr>
                                @endif
                            </table>

                            {{-- SLA Progress --}}
                            <div class="mt-3">
                                <h6>@lang('SLA Progress')</h6>
                                <div class="progress" style="height: 25px;">
                                    @php
                                        $progress = min(100, ($claim->processing_days / 10) * 100);
                                    @endphp
                                    <div class="progress-bar
                                        @if($progress < 50) bg-success
                                        @elseif($progress < 80) bg-warning
                                        @else bg-danger
                                        @endif"
                                        style="width: {{ $progress }}%">
                                        {{ number_format($progress, 0) }}%
                                    </div>
                                </div>
                                <small class="text-muted">
                                    @if($claim->isOverdue())
                                        <i class="las la-exclamation-triangle text--danger"></i> @lang('Overdue - Priority processing')
                                    @else
                                        @lang('Target: Within 10 business days')
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- Claim Description --}}
                    <div class="mb-4">
                        <h6><i class="las la-file-alt"></i> @lang('Claim Description')</h6>
                        <div class="alert alert-info">
                            {{ $claim->description }}
                        </div>
                    </div>

                    {{-- Evidence Files --}}
                    @if($claim->evidence && count(json_decode($claim->evidence, true) ?? []) > 0)
                        <div class="mb-4">
                            <h6><i class="las la-images"></i> @lang('Supporting Evidence')</h6>
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
                                                <a href="{{ asset('storage/' . $evidence['path']) }}"
                                                   class="btn btn-sm btn-outline--primary mt-2" download>
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
                            <div class="card-body">
                                {{ $claim->rejection_reason }}
                            </div>
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
                            <div class="card-body">
                                {{ $claim->admin_notes }}
                            </div>
                        </div>
                    @endif

                    {{-- Related Issue --}}
                    @if($claim->supportIssue)
                        <div class="mt-3">
                            <p class="text-muted">
                                <i class="las la-link"></i>
                                @lang('Related Issue:')
                                <a href="{{ route('customer.support.issues.show', $claim->supportIssue->id) }}">
                                    {{ $claim->supportIssue->issue_number }}
                                </a>
                            </p>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('customer.support.claims') }}" class="btn btn-sm btn-outline--secondary">
                        <i class="las la-arrow-left"></i> @lang('Back to Claims')
                    </a>
                    @if($claim->status == 2 || $claim->status == 4)
                        <span class="float-end text--success">
                            <i class="las la-check-circle"></i>
                            @if($claim->status == 4)
                                @lang('Compensation has been paid')
                            @else
                                @lang('Approved - Payment processing')
                            @endif
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
