@extends('admin.layouts.app')

@section('panel')
    {{-- Top Regions Cards --}}
    <div class="row">
        @foreach($popularDestinations->take(4) as $index => $region)
            <div class="col-lg-3 col-sm-6">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="las la-map-marker-alt text--primary" style="font-size: 2.5rem;"></i>
                        <h4 class="mt-3">{{ $region['region'] }}</h4>
                        <p class="text-muted mb-1">{{ $region['shipment_count'] }} @lang('Shipments')</p>
                        <p class="mb-0"><strong>{{ showAmount($region['total_revenue']) }}</strong></p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Regional Distribution Chart --}}
    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-globe"></i> @lang('Shipment Distribution by Region')
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="regionalDistChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-clock"></i> @lang('Peak Shipping Times')
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="peakTimesChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Detailed Regional Table --}}
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('All Regions Performance')</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table--light">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('Region/City')</th>
                                    <th>@lang('Total Shipments')</th>
                                    <th>@lang('% of Total')</th>
                                    <th>@lang('Total Revenue')</th>
                                    <th>@lang('Avg Revenue/Shipment')</th>
                                    <th>@lang('Avg Delivery Days')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($popularDestinations as $index => $region)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $region['region'] }}</strong></td>
                                        <td>{{ $region['shipment_count'] }}</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-primary"
                                                     style="width: {{ $region['percentage'] }}%">
                                                    {{ number_format($region['percentage'], 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ showAmount($region['total_revenue']) }}</td>
                                        <td>{{ showAmount($region['avg_revenue']) }}</td>
                                        <td>{{ number_format($region['avg_delivery_days'] ?? 0, 1) }} days</td>
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

    // Regional Distribution Chart
    var distCtx = document.getElementById('regionalDistChart').getContext('2d');
    new Chart(distCtx, {
        type: 'pie',
        data: {
            labels: @json($regionalDistribution['labels'] ?? []),
            datasets: [{
                data: @json($regionalDistribution['data'] ?? []),
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                    '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Peak Times Chart
    var peakCtx = document.getElementById('peakTimesChart').getContext('2d');
    new Chart(peakCtx, {
        type: 'line',
        data: {
            labels: @json($peakTimes['labels'] ?? []),
            datasets: [{
                label: 'Shipments',
                data: @json($peakTimes['data'] ?? []),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
@endpush
