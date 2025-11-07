@extends('customer.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">@lang('Holding Details')</h5>
                    <a href="{{ route('customer.shipment.holdings') }}" class="btn btn-sm btn--secondary">
                        <i class="las la-arrow-left"></i> @lang('Back to List')
                    </a>
                </div>
                <div class="card-body">
                    {{-- Holding Info --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="mb-3">@lang('Holding Information')</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">@lang('Holding Code'):</td>
                                    <td><span class="text-primary">{{ $holding->holding_code }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Status'):</td>
                                    <td>{!! $holding->status_badge !!}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Branch'):</td>
                                    <td>{{ $holding->branch->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Received Date'):</td>
                                    <td>{{ $holding->received_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Scheduled Ship'):</td>
                                    <td>
                                        @if($holding->scheduled_ship_date)
                                            {{ $holding->scheduled_ship_date->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">@lang('Not scheduled')</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($holding->original_ship_date)
                                <tr>
                                    <td class="fw-bold">@lang('Original Ship Date'):</td>
                                    <td>{{ $holding->original_ship_date->format('M d, Y') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">@lang('Package Summary')</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">@lang('Total Packages'):</td>
                                    <td><span class="badge badge--info">{{ $holding->package_count }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Total Weight'):</td>
                                    <td>{{ number_format($holding->total_weight, 2) }} kg</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Total Volume'):</td>
                                    <td>{{ number_format($holding->total_volume, 2) }} m³</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Max Holding Date'):</td>
                                    <td>{{ $holding->max_holding_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Days Remaining'):</td>
                                    <td>
                                        @php $daysRemaining = $holding->days_remaining; @endphp
                                        @if($daysRemaining > 30)
                                            <span class="badge badge--success">{{ $daysRemaining }} days</span>
                                        @elseif($daysRemaining > 7)
                                            <span class="badge badge--warning">{{ $daysRemaining }} days</span>
                                        @else
                                            <span class="badge badge--danger">{{ $daysRemaining }} days</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($holding->notes)
                    <div class="alert alert-info">
                        <strong>@lang('Notes'):</strong><br>
                        {{ $holding->notes }}
                    </div>
                    @endif

                    {{-- Actions --}}
                    @if($holding->status == 0)
                    <div class="d-flex gap-2 mb-4">
                        @if($holding->scheduled_ship_date)
                        <a href="{{ route('customer.shipment.holding.extend', $holding->id) }}" class="btn btn--primary">
                            <i class="las la-calendar-plus"></i> @lang('Extend Shipping Date')
                        </a>
                        @endif
                    </div>
                    @endif

                    {{-- Packages List --}}
                    <h6 class="mb-3">@lang('Packages in This Holding')</h6>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('Package Code')</th>
                                    <th>@lang('Description')</th>
                                    <th>@lang('Weight')</th>
                                    <th>@lang('Dimensions')</th>
                                    <th>@lang('Value')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($holding->packages as $package)
                                    <tr>
                                        <td><code>{{ $package->package_code }}</code></td>
                                        <td>{{ $package->description }}</td>
                                        <td>{{ number_format($package->weight, 2) }} kg</td>
                                        <td>
                                            @if($package->length && $package->width && $package->height)
                                                {{ $package->length }} × {{ $package->width }} × {{ $package->height }} cm
                                            @else
                                                <span class="text-muted">@lang('N/A')</span>
                                            @endif
                                        </td>
                                        <td>${{ number_format($package->declared_value, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">@lang('No packages found')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <td colspan="2">@lang('Total')</td>
                                    <td>{{ number_format($holding->total_weight, 2) }} kg</td>
                                    <td>-</td>
                                    <td>${{ number_format($holding->packages->sum('declared_value'), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Timeline --}}
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">@lang('Timeline')</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <p class="mb-1 fw-bold">@lang('Received')</p>
                                <small class="text-muted">{{ $holding->received_date->format('M d, Y') }}</small>
                            </div>
                        </div>

                        @if($holding->consolidated_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <p class="mb-1 fw-bold">@lang('Consolidated')</p>
                                <small class="text-muted">{{ $holding->consolidated_at->format('M d, Y') }}</small>
                            </div>
                        </div>
                        @endif

                        @if($holding->scheduled_ship_date)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <p class="mb-1 fw-bold">@lang('Scheduled to Ship')</p>
                                <small class="text-muted">{{ $holding->scheduled_ship_date->format('M d, Y') }}</small>
                            </div>
                        </div>
                        @endif

                        @if($holding->actual_ship_date)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <p class="mb-1 fw-bold">@lang('Shipped')</p>
                                <small class="text-muted">{{ $holding->actual_ship_date->format('M d, Y') }}</small>
                            </div>
                        </div>
                        @endif

                        <div class="timeline-item">
                            <div class="timeline-marker bg-secondary"></div>
                            <div class="timeline-content">
                                <p class="mb-1 fw-bold">@lang('Expires')</p>
                                <small class="text-muted">{{ $holding->max_holding_date->format('M d, Y') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 7px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e0e0e0;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }
    .timeline-marker {
        position: absolute;
        left: -27px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 3px solid #fff;
    }
    .timeline-content {
        padding-left: 5px;
    }
</style>
@endpush
