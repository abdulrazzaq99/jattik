@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-truck"></i> @lang('Carrier Usage Distribution')
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="carrierUsageChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-clock"></i> @lang('Average Delivery Performance')
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="deliveryPerformanceChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Detailed Carrier Statistics --}}
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Carrier Performance Metrics')</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table--light">
                            <thead>
                                <tr>
                                    <th>@lang('Carrier')</th>
                                    <th>@lang('Total Shipments')</th>
                                    <th>@lang('Usage %')</th>
                                    <th>@lang('Avg Delivery Days')</th>
                                    <th>@lang('On-Time %')</th>
                                    <th>@lang('Exceptions')</th>
                                    <th>@lang('Exception Rate')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deliveryPerformance as $carrier)
                                    <tr>
                                        <td><strong>{{ $carrier['carrier_name'] }}</strong></td>
                                        <td>{{ $carrier['shipment_count'] }}</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" style="width: {{ $carrier['usage_percentage'] }}%">
                                                    {{ number_format($carrier['usage_percentage'], 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ number_format($carrier['avg_delivery_days'], 1) }} days</td>
                                        <td>
                                            <span class="badge
                                                @if($carrier['on_time_percentage'] >= 90) badge--success
                                                @elseif($carrier['on_time_percentage'] >= 70) badge--warning
                                                @else badge--danger
                                                @endif">
                                                {{ number_format($carrier['on_time_percentage'], 1) }}%
                                            </span>
                                        </td>
                                        <td>{{ $carrier['exception_count'] ?? 0 }}</td>
                                        <td>
                                            <span class="badge
                                                @if(($carrier['exception_rate'] ?? 0) < 5) badge--success
                                                @elseif(($carrier['exception_rate'] ?? 0) < 10) badge--warning
                                                @else badge--danger
                                                @endif">
                                                {{ number_format($carrier['exception_rate'] ?? 0, 1) }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-lib')
<script src="{{ asset('assets/admin/js/vendor/chart.js.2.8.0.js') }}"></script>
@endpush

@push('script')
<script>
    "use strict";

    // Carrier Usage Chart
    var usageCtx = document.getElementById('carrierUsageChart').getContext('2d');
    new Chart(usageCtx, {
        type: 'doughnut',
        data: {
            labels: @json(array_column($carrierUsage, 'carrier_name')),
            datasets: [{
                data: @json(array_column($carrierUsage, 'shipment_count')),
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Delivery Performance Chart
    var perfCtx = document.getElementById('deliveryPerformanceChart').getContext('2d');
    new Chart(perfCtx, {
        type: 'bar',
        data: {
            labels: @json(array_column($deliveryPerformance, 'carrier_name')),
            datasets: [{
                label: 'Avg Delivery Days',
                data: @json(array_column($deliveryPerformance, 'avg_delivery_days')),
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgb(75, 192, 192)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
@endpush
