@extends('staff.layouts.app')

@section('panel')
    {{-- SLA Warnings --}}
    @if($nearingSLA->count() > 0 || $overdue->count() > 0)
        <div class="row mb-4">
            @if($overdue->count() > 0)
                <div class="col-lg-12 mb-3">
                    <div class="alert alert-danger">
                        <h5>
                            <i class="las la-exclamation-triangle"></i>
                            @lang('Overdue Claims') ({{ $overdue->count() }})
                        </h5>
                        <p>@lang('The following claims have exceeded the 10-day SLA and require immediate attention:')</p>
                        <ul class="mb-0">
                            @foreach($overdue as $claim)
                                <li>
                                    <strong>{{ $claim->claim_number }}</strong> -
                                    {{ $claim->customer->fullname }} -
                                    {{ showAmount($claim->claimed_amount) }} -
                                    <span class="text--danger">Overdue by {{ abs($claim->days_until_deadline) }} days</span>
                                    <a href="{{ route('staff.claims.review', $claim->id) }}" class="btn btn-sm btn--danger ms-2">
                                        <i class="las la-clipboard-check"></i> @lang('Review Now')
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if($nearingSLA->count() > 0)
                <div class="col-lg-12">
                    <div class="alert alert-warning">
                        <h5>
                            <i class="las la-clock"></i>
                            @lang('Claims Nearing SLA') ({{ $nearingSLA->count() }})
                        </h5>
                        <p>@lang('These claims are approaching the 10-day SLA deadline:')</p>
                        <ul class="mb-0">
                            @foreach($nearingSLA as $claim)
                                <li>
                                    <strong>{{ $claim->claim_number }}</strong> -
                                    {{ $claim->customer->fullname }} -
                                    <span class="text--warning">{{ $claim->days_until_deadline }} days remaining</span>
                                    <a href="{{ route('staff.claims.review', $claim->id) }}" class="btn btn-sm btn--warning ms-2">
                                        <i class="las la-clipboard-check"></i> @lang('Review')
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Pending Claims Table --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-hourglass-half"></i> @lang('Pending Claims for Review')
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Priority')</th>
                                    <th>@lang('Claim #')</th>
                                    <th>@lang('Customer')</th>
                                    <th>@lang('Courier')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('SLA Days Left')</th>
                                    <th>@lang('Submitted')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($claims as $claim)
                                    <tr class="{{ $claim->isOverdue() ? 'table-danger' : ($claim->days_until_deadline <= 2 ? 'table-warning' : '') }}">
                                        <td>
                                            @if($claim->isOverdue())
                                                <span class="badge badge--danger">@lang('URGENT')</span>
                                            @elseif($claim->days_until_deadline <= 2)
                                                <span class="badge badge--warning">@lang('High')</span>
                                            @else
                                                <span class="badge badge--info">@lang('Normal')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $claim->claim_number }}</span>
                                        </td>
                                        <td>
                                            @if($claim->customer)
                                                <strong>{{ $claim->customer->fullname }}</strong><br>
                                                <small class="text-muted">{{ $claim->customer->mobile }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($claim->courierInfo)
                                                <a href="{{ route('staff.courier.details', $claim->courierInfo->id) }}">
                                                    {{ $claim->courierInfo->code }}
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            @if($claim->claim_type == 'damage')
                                                <span class="badge badge--danger">@lang('Damage')</span>
                                            @elseif($claim->claim_type == 'loss')
                                                <span class="badge badge--danger">@lang('Loss')</span>
                                            @elseif($claim->claim_type == 'delay_compensation')
                                                <span class="badge badge--warning">@lang('Delay')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <h6 class="mb-0">{{ showAmount($claim->claimed_amount) }}</h6>
                                        </td>
                                        <td>
                                            @if($claim->isOverdue())
                                                <span class="badge badge--danger">
                                                    <i class="las la-exclamation-triangle"></i>
                                                    @lang('Overdue by') {{ abs($claim->days_until_deadline) }}d
                                                </span>
                                            @elseif($claim->days_until_deadline <= 2)
                                                <span class="badge badge--warning">
                                                    {{ $claim->days_until_deadline }} @lang('days')
                                                </span>
                                            @else
                                                <span class="badge badge--success">
                                                    {{ $claim->days_until_deadline }} @lang('days')
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ showDateTime($claim->submitted_at) }}<br>
                                            <small class="text-muted">{{ diffForHumans($claim->submitted_at) }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('staff.claims.review', $claim->id) }}"
                                               class="btn btn-sm btn--primary">
                                                <i class="las la-clipboard-check"></i> @lang('Review')
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">
                                            <i class="las la-check-circle text--success" style="font-size: 3rem;"></i><br>
                                            @lang('No pending claims - All caught up!')
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

    {{-- Statistics --}}
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="las la-hourglass-half" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $pendingCount ?? 0 }}</h3>
                    <p class="mb-0">@lang('Pending Review')</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <i class="las la-exclamation-triangle" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $overdue->count() }}</h3>
                    <p class="mb-0">@lang('Overdue')</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="las la-clock" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $nearingSLA->count() }}</h3>
                    <p class="mb-0">@lang('Nearing SLA')</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="las la-dollar-sign" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ showAmount($totalClaimedAmount ?? 0) }}</h3>
                    <p class="mb-0">@lang('Total Claimed')</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    // Auto-refresh every 5 minutes
    setTimeout(function() {
        location.reload();
    }, 300000);
</script>
@endpush
