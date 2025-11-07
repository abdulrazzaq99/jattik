@extends('customer.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">@lang('My Claims')</h5>
                    <a href="{{ route('customer.support.claims.create') }}" class="btn btn-sm btn--primary">
                        <i class="las la-plus"></i> @lang('File New Claim')
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Claim #')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Courier')</th>
                                    <th>@lang('Claimed Amount')</th>
                                    <th>@lang('Approved Amount')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('SLA Days Left')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($claims as $claim)
                                    <tr>
                                        <td>
                                            <span class="fw-bold text--primary">{{ $claim->claim_number }}</span>
                                        </td>
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
                                        <td>
                                            @if($claim->courierInfo)
                                                <span class="fw-bold">{{ $claim->courierInfo->code }}</span>
                                            @else
                                                <span class="text-muted">@lang('N/A')</span>
                                            @endif
                                        </td>
                                        <td>{{ showAmount($claim->claimed_amount) }}</td>
                                        <td>
                                            @if($claim->approved_amount)
                                                <span class="text--success fw-bold">{{ showAmount($claim->approved_amount) }}</span>
                                            @else
                                                <span class="text-muted">@lang('Pending')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($claim->status == 0)
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @elseif($claim->status == 1)
                                                <span class="badge badge--info">@lang('Under Review')</span>
                                            @elseif($claim->status == 2)
                                                <span class="badge badge--success">@lang('Approved')</span>
                                            @elseif($claim->status == 3)
                                                <span class="badge badge--danger">@lang('Rejected')</span>
                                            @elseif($claim->status == 4)
                                                <span class="badge badge--success">@lang('Paid')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(in_array($claim->status, [0, 1]))
                                                @if($claim->days_until_deadline > 2)
                                                    <span class="badge badge--success">{{ $claim->days_until_deadline }} @lang('days')</span>
                                                @elseif($claim->days_until_deadline > 0)
                                                    <span class="badge badge--warning">{{ $claim->days_until_deadline }} @lang('days')</span>
                                                @else
                                                    <span class="badge badge--danger">@lang('Overdue')</span>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('customer.support.claims.show', $claim->id) }}"
                                               class="btn btn-sm btn-outline--primary">
                                                <i class="la la-eye"></i> @lang('View')
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">
                                            <i class="las la-file-invoice"></i> @lang('No claims filed yet')
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($claims->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($claims) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Information Card --}}
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="alert alert-info">
                <h6><i class="las la-info-circle"></i> @lang('Claim Processing Information')</h6>
                <ul class="mb-0">
                    <li>@lang('All claims are processed within 10 business days (SLA)')</li>
                    <li>@lang('Approved amounts will be credited within 5 business days')</li>
                    <li>@lang('You can track claim status in real-time')</li>
                    <li>@lang('Upload clear evidence photos for faster processing')</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Statistics --}}
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="las la-hourglass-half text--warning" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $pendingCount }}</h3>
                    <p class="text-muted mb-0">@lang('Pending')</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="las la-check-circle text--success" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $approvedCount }}</h3>
                    <p class="text-muted mb-0">@lang('Approved')</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="las la-times-circle text--danger" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $rejectedCount }}</h3>
                    <p class="text-muted mb-0">@lang('Rejected')</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="las la-dollar-sign text--success" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ showAmount($totalApproved) }}</h3>
                    <p class="text-muted mb-0">@lang('Total Approved')</p>
                </div>
            </div>
        </div>
    </div>
@endsection
