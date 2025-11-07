@extends('staff.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="las la-exclamation-triangle text-warning"></i> @lang('Holdings Expiring Soon')
                    </h5>
                    <a href="{{ route('staff.warehouse.index') }}" class="btn btn-sm btn--secondary">
                        <i class="las la-arrow-left"></i> @lang('Back to All Holdings')
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($holdings) && $holdings->count() > 0)
                        <div class="alert alert-warning">
                            <i class="las la-info-circle"></i>
                            @lang('These holdings will expire within 7 days. Please take action to consolidate or ship them.')
                        </div>

                        <div class="table-responsive--sm table-responsive">
                            <table class="table table--light style--two">
                                <thead>
                                    <tr>
                                        <th>@lang('Holding Code')</th>
                                        <th>@lang('Customer')</th>
                                        <th>@lang('Received Date')</th>
                                        <th>@lang('Expires On')</th>
                                        <th>@lang('Days Left')</th>
                                        <th>@lang('Packages')</th>
                                        <th>@lang('Weight')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($holdings as $holding)
                                        <tr class="{{ $holding->days_remaining <= 3 ? 'table-danger' : 'table-warning' }}">
                                            <td>
                                                <span class="fw-bold">{{ $holding->holding_code }}</span>
                                            </td>
                                            <td>
                                                <span class="d-block fw-bold">{{ $holding->customer->fullname }}</span>
                                                <span class="small text-muted">{{ $holding->customer->mobile }}</span>
                                            </td>
                                            <td>{{ $holding->received_date->format('M d, Y') }}</td>
                                            <td>
                                                <span class="fw-bold text-danger">{{ $holding->max_holding_date->format('M d, Y') }}</span>
                                            </td>
                                            <td>
                                                @php $daysRemaining = $holding->days_remaining; @endphp
                                                @if($daysRemaining <= 3)
                                                    <span class="badge badge--danger" style="font-size: 14px;">
                                                        <i class="las la-exclamation-triangle"></i> {{ $daysRemaining }} days
                                                    </span>
                                                @else
                                                    <span class="badge badge--warning" style="font-size: 14px;">
                                                        {{ $daysRemaining }} days
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge--info">{{ $holding->package_count }}</span>
                                            </td>
                                            <td>{{ number_format($holding->total_weight, 2) }} kg</td>
                                            <td>
                                                <div class="button-group">
                                                    <a href="{{ route('staff.warehouse.show', $holding->id) }}" class="btn btn-sm btn--primary" title="@lang('View Details')">
                                                        <i class="las la-eye"></i>
                                                    </a>
                                                    <a href="{{ route('staff.warehouse.calculate.fee', $holding->id) }}" class="btn btn-sm btn--success" title="@lang('Calculate Fee')">
                                                        <i class="las la-calculator"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Action Recommendations --}}
                        <div class="card mt-4 bg-light border-warning">
                            <div class="card-body">
                                <h6 class="text-warning"><i class="las la-lightbulb"></i> @lang('Recommended Actions')</h6>
                                <ol class="mb-0">
                                    <li>@lang('Contact customers about holdings nearing expiry')</li>
                                    <li>@lang('Calculate shipping fees and provide quotes')</li>
                                    <li>@lang('Consolidate packages and prepare for shipment')</li>
                                    <li>@lang('Mark holdings as ready once customer confirms')</li>
                                </ol>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="las la-check-circle text-success" style="font-size: 64px;"></i>
                            <h5 class="mt-3 text-success">@lang('All Clear!')</h5>
                            <p class="text-muted">@lang('No holdings are expiring within the next 7 days.')</p>
                            <a href="{{ route('staff.warehouse.index') }}" class="btn btn--primary mt-2">
                                <i class="las la-warehouse"></i> @lang('View All Holdings')
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Statistics Cards --}}
            @if(isset($holdings) && $holdings->count() > 0)
            <div class="row mt-4 g-3">
                <div class="col-md-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <h2 class="mb-0">{{ $holdings->filter(fn($h) => $h->days_remaining <= 3)->count() }}</h2>
                            <p class="mb-0">@lang('Critical (≤3 days)')</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-dark">
                        <div class="card-body text-center">
                            <h2 class="mb-0">{{ $holdings->filter(fn($h) => $h->days_remaining > 3 && $h->days_remaining <= 7)->count() }}</h2>
                            <p class="mb-0">@lang('Warning (4-7 days)')</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h2 class="mb-0">{{ number_format($holdings->sum('total_weight'), 2) }}</h2>
                            <p class="mb-0">@lang('Total Weight (kg)')</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection

@push('style')
<style>
    .table-danger td {
        background-color: rgba(220, 53, 69, 0.1) !important;
    }
    .table-warning td {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }
</style>
@endpush
