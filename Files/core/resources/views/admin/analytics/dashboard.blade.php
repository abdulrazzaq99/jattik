@extends('admin.layouts.app')

@section('panel')
    {{-- Key Statistics --}}
    <div class="row gy-4">
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="" icon="las la-dollar-sign" title="Total Revenue"
                value="{{ showAmount($dashboardStats['total_revenue']) }}" bg="success" outline="true"/>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="" icon="las la-shipping-fast" title="Total Shipments"
                value="{{ $dashboardStats['total_shipments'] }}" bg="primary" outline="true" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="" icon="las la-check-circle" title="Delivered"
                value="{{ $dashboardStats['delivered_shipments'] }}" bg="info" outline="true" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="" icon="las la-users" title="Active Customers"
                value="{{ $dashboardStats['active_customers'] }}" bg="orange" outline="true" />
        </div>
    </div>

    {{-- Revenue Trends Chart --}}
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">@lang('Revenue Trends (Last 30 Days)')</h5>
                    <a href="{{ route('admin.analytics.export', ['format' => 'csv']) }}" class="btn btn-sm btn--primary">
                        <i class="las la-download"></i> @lang('Export Data')
                    </a>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        {{-- Monthly Shipping Costs --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-chart-bar"></i> @lang('Monthly Shipping Costs')
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="shippingCostsChart" height="200"></canvas>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.analytics.shipping.costs') }}" class="text--primary">
                        @lang('View Detailed Report') <i class="las la-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Carrier Usage --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-truck"></i> @lang('Most Used Carriers')
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="carrierUsageChart" height="200"></canvas>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.analytics.carriers') }}" class="text--primary">
                        @lang('View Carrier Performance') <i class="las la-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        {{-- Shipment Status Breakdown --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-box"></i> @lang('Shipment Status Breakdown')
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="statusBreakdownChart" height="200"></canvas>
                </div>
            </div>
        </div>

        {{-- Top Regions --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-map-marker-alt"></i> @lang('Popular Destinations')
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table--light">
                            <thead>
                                <tr>
                                    <th>@lang('Region')</th>
                                    <th>@lang('Shipments')</th>
                                    <th>@lang('Revenue')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dashboardStats['top_regions'] ?? [] as $region)
                                    <tr>
                                        <td>{{ $region['region'] }}</td>
                                        <td>
                                            <span class="badge badge--primary">{{ $region['count'] }}</span>
                                        </td>
                                        <td>{{ showAmount($region['revenue']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.analytics.regions') }}" class="text--primary">
                        @lang('View Regional Analytics') <i class="las la-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Stats Grid --}}
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="las la-clock text--warning" style="font-size: 2.5rem;"></i>
                    <h4 class="mt-3">{{ number_format($dashboardStats['avg_delivery_days'], 1) }}</h4>
                    <p class="text-muted mb-0">@lang('Avg. Delivery Days')</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="las la-percentage text--success" style="font-size: 2.5rem;"></i>
                    <h4 class="mt-3">{{ number_format($dashboardStats['on_time_rate'], 1) }}%</h4>
                    <p class="text-muted mb-0">@lang('On-Time Rate')</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="las la-star text--warning" style="font-size: 2.5rem;"></i>
                    <h4 class="mt-3">{{ number_format($dashboardStats['avg_rating'], 1) }}/5</h4>
                    <p class="text-muted mb-0">@lang('Avg. Rating')</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="las la-smile text--success" style="font-size: 2.5rem;"></i>
                    <h4 class="mt-3">{{ number_format($dashboardStats['customer_satisfaction'], 1) }}%</h4>
                    <p class="text-muted mb-0">@lang('Satisfaction')</p>
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

    // Revenue Trends Chart
    var revenueCtx = document.getElementById('revenueChart').getContext('2d');
    var revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: @json($revenueChart['labels'] ?? []),
            datasets: [{
                label: 'Revenue',
                data: @json($revenueChart['data'] ?? []),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Shipping Costs Chart
    var costsCtx = document.getElementById('shippingCostsChart').getContext('2d');
    var costsChart = new Chart(costsCtx, {
        type: 'bar',
        data: {
            labels: @json($shippingCosts['labels'] ?? []),
            datasets: [{
                label: 'Shipping Costs',
                data: @json($shippingCosts['data'] ?? []),
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Carrier Usage Chart
    var carrierCtx = document.getElementById('carrierUsageChart').getContext('2d');
    var carrierChart = new Chart(carrierCtx, {
        type: 'doughnut',
        data: {
            labels: @json($carrierUsage['labels'] ?? []),
            datasets: [{
                data: @json($carrierUsage['data'] ?? []),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(153, 102, 255, 0.5)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Status Breakdown Chart
    var statusCtx = document.getElementById('statusBreakdownChart').getContext('2d');
    var statusChart = new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: @json($statusBreakdown['labels'] ?? []),
            datasets: [{
                data: @json($statusBreakdown['data'] ?? []),
                backgroundColor: [
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(75, 192, 192, 0.5)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Auto-refresh live stats every 60 seconds
    setInterval(function() {
        $.get('{{ route('admin.analytics.live.stats') }}', function(data) {
            console.log('Stats updated:', data);
        });
    }, 60000);
</script>
@endpush
