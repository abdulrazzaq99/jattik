@extends('staff.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="las la-file-invoice-dollar"></i> @lang('All Claims')
                    </h5>
                    <a href="{{ route('staff.claims.pending') }}" class="btn btn-sm btn--warning">
                        <i class="las la-clock"></i> @lang('Pending Claims')
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Claim #')</th>
                                    <th>@lang('Customer')</th>
                                    <th>@lang('Courier')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('SLA')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($claims as $claim)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $claim->claim_number }}</span>
                                        </td>
                                        <td>
                                            @if($claim->customer)
                                                <strong>{{ $claim->customer->fullname }}</strong><br>
                                                <small class="text-muted">{{ $claim->customer->email }}</small>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($claim->courierInfo)
                                                {{ $claim->courierInfo->code }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
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
                                        <td>{{ showAmount($claim->claimed_amount) }}</td>
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
                                                    <span class="badge badge--success">{{ $claim->days_until_deadline }}d</span>
                                                @elseif($claim->days_until_deadline > 0)
                                                    <span class="badge badge--warning">{{ $claim->days_until_deadline }}d</span>
                                                @else
                                                    <span class="badge badge--danger">@lang('Overdue')</span>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('staff.claims.show', $claim->id) }}"
                                               class="btn btn-sm btn-outline--primary">
                                                <i class="las la-eye"></i> @lang('View')
                                            </a>
                                            @if($claim->status == 0)
                                                <a href="{{ route('staff.claims.review', $claim->id) }}"
                                                   class="btn btn-sm btn--info">
                                                    <i class="las la-clipboard-check"></i> @lang('Review')
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">
                                            @lang('No claims found')
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
@endsection
