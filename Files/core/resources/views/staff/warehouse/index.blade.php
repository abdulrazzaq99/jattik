@extends('staff.layouts.app')

@section('panel')
    {{-- Statistics --}}
    <div class="row gy-4 mb-4">
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" title="Total Holdings" icon="las la-warehouse" value="{{ $statistics['total_holdings'] }}" bg="purple" type="2" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" title="In Holding" icon="las la-box" value="{{ $statistics['holding'] }}" bg="cyan" type="2" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" title="Ready to Ship" icon="las la-check-circle" value="{{ $statistics['ready'] }}" bg="green" type="2" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" title="Total Weight" icon="las la-weight" value="{{ number_format($statistics['total_weight'], 2) }} kg" bg="primary" type="2" />
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">@lang('Warehouse Holdings')</h5>
                    <div>
                        <a href="{{ route('staff.warehouse.expiring') }}" class="btn btn-sm btn--warning me-2">
                            <i class="las la-exclamation-triangle"></i> @lang('Expiring Soon')
                        </a>
                        <a href="{{ route('staff.warehouse.create') }}" class="btn btn-sm btn--primary">
                            <i class="las la-plus"></i> @lang('New Holding')
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($holdings->count() > 0)
                        <div class="table-responsive--sm table-responsive">
                            <table class="table table--light style--two">
                                <thead>
                                    <tr>
                                        <th>@lang('Holding Code')</th>
                                        <th>@lang('Customer')</th>
                                        <th>@lang('Received')</th>
                                        <th>@lang('Ship Date')</th>
                                        <th>@lang('Packages')</th>
                                        <th>@lang('Weight')</th>
                                        <th>@lang('Days Left')</th>
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
                                                <span class="d-block fw-bold">{{ $holding->customer->fullname }}</span>
                                                <span class="small text-muted">{{ $holding->customer->mobile }}</span>
                                            </td>
                                            <td>{{ $holding->received_date->format('M d, Y') }}</td>
                                            <td>
                                                @if($holding->scheduled_ship_date)
                                                    {{ $holding->scheduled_ship_date->format('M d, Y') }}
                                                @else
                                                    <span class="text-muted">@lang('Not scheduled')</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge--info">{{ $holding->package_count }}</span>
                                            </td>
                                            <td>{{ number_format($holding->total_weight, 2) }} kg</td>
                                            <td>
                                                @php $daysRemaining = $holding->days_remaining; @endphp
                                                @if($daysRemaining > 30)
                                                    <span class="badge badge--success">{{ $daysRemaining }}d</span>
                                                @elseif($daysRemaining > 7)
                                                    <span class="badge badge--warning">{{ $daysRemaining }}d</span>
                                                @else
                                                    <span class="badge badge--danger">{{ $daysRemaining }}d</span>
                                                @endif
                                            </td>
                                            <td>{!! $holding->status_badge !!}</td>
                                            <td>
                                                <div class="button-group">
                                                    <a href="{{ route('staff.warehouse.show', $holding->id) }}" class="btn btn-sm btn--primary" title="@lang('View')">
                                                        <i class="las la-eye"></i>
                                                    </a>
                                                    @if($holding->status == 0)
                                                        <a href="{{ route('staff.warehouse.calculate.fee', $holding->id) }}" class="btn btn-sm btn--info" title="@lang('Calculate Fee')">
                                                            <i class="las la-calculator"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer">
                            {{ $holdings->links() }}
                        </div>
                    @else
                        <div class="text-center p-5">
                            <i class="las la-warehouse" style="font-size: 64px; color: #ccc;"></i>
                            <p class="text-muted mt-3">@lang('No warehouse holdings found.')</p>
                            <a href="{{ route('staff.warehouse.create') }}" class="btn btn--primary mt-2">
                                <i class="las la-plus"></i> @lang('Create First Holding')
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
