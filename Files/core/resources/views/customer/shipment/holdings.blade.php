@extends('customer.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('My Warehouse Holdings')</h5>
                </div>
                <div class="card-body p-0">
                    @if($holdings->count() > 0)
                        <div class="table-responsive--sm table-responsive">
                            <table class="table table--light style--two">
                                <thead>
                                    <tr>
                                        <th>@lang('Holding Code')</th>
                                        <th>@lang('Received Date')</th>
                                        <th>@lang('Scheduled Ship')</th>
                                        <th>@lang('Packages')</th>
                                        <th>@lang('Weight')</th>
                                        <th>@lang('Days Remaining')</th>
                                        <th>@lang('Status')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($holdings as $holding)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-primary">{{ $holding->holding_code }}</span>
                                            </td>
                                            <td>
                                                {{ $holding->received_date->format('M d, Y') }}
                                            </td>
                                            <td>
                                                @if($holding->scheduled_ship_date)
                                                    {{ $holding->scheduled_ship_date->format('M d, Y') }}
                                                @else
                                                    <span class="text-muted">@lang('Not scheduled')</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge--info">{{ $holding->package_count }} @lang('pkg')</span>
                                            </td>
                                            <td>
                                                {{ number_format($holding->total_weight, 2) }} kg
                                            </td>
                                            <td>
                                                @php
                                                    $daysRemaining = $holding->days_remaining;
                                                @endphp
                                                @if($daysRemaining > 30)
                                                    <span class="badge badge--success">{{ $daysRemaining }} days</span>
                                                @elseif($daysRemaining > 7)
                                                    <span class="badge badge--warning">{{ $daysRemaining }} days</span>
                                                @else
                                                    <span class="badge badge--danger">{{ $daysRemaining }} days</span>
                                                @endif
                                            </td>
                                            <td>
                                                {!! $holding->status_badge !!}
                                            </td>
                                            <td>
                                                <div class="button-group">
                                                    <a href="{{ route('customer.shipment.holding.show', $holding->id) }}" class="btn btn-sm btn--primary" title="@lang('View Details')">
                                                        <i class="las la-eye"></i>
                                                    </a>
                                                    @if($holding->status == 0 && $holding->scheduled_ship_date)
                                                        <a href="{{ route('customer.shipment.holding.extend', $holding->id) }}" class="btn btn-sm btn--info" title="@lang('Extend Date')">
                                                            <i class="las la-calendar-plus"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center p-5">
                            <i class="las la-warehouse" style="font-size: 64px; color: #ccc;"></i>
                            <p class="text-muted mt-3">@lang('No warehouse holdings found.')</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Info Cards --}}
            <div class="row mt-4 g-3">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="las la-clock text-primary" style="font-size: 36px;"></i>
                            <h6 class="mt-2">@lang('Maximum Holding')</h6>
                            <p class="text-muted mb-0">@lang('90 days from received date')</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="las la-calendar-check text-success" style="font-size: 36px;"></i>
                            <h6 class="mt-2">@lang('Consolidation')</h6>
                            <p class="text-muted mb-0">@lang('Multiple packages combined')</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="las la-shipping-fast text-info" style="font-size: 36px;"></i>
                            <h6 class="mt-2">@lang('Flexible Shipping')</h6>
                            <p class="text-muted mb-0">@lang('Extend date before dispatch')</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
