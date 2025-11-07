@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="las la-chart-bar"></i> @lang('Detailed Monthly Shipping Costs')
                    </h5>
                    <a href="{{ route('admin.analytics.dashboard') }}" class="btn btn-sm btn-outline--secondary">
                        <i class="las la-arrow-left"></i> @lang('Dashboard')
                    </a>
                </div>
                <div class="card-body">
                    <canvas id="detailedCostsChart" height="60"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Detailed Cost Breakdown --}}
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Monthly Breakdown')</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table--light">
                            <thead>
                                <tr>
                                    <th>@lang('Month')</th>
                                    <th>@lang('Total Shipments')</th>
                                    <th>@lang('Shipping Costs')</th>
                                    <th>@lang('Insurance Costs')</th>
                                    <th>@lang('Additional Fees')</th>
                                    <th>@lang('Total')</th>
                                    <th>@lang('Avg per Shipment')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($costs as $cost)
                                    <tr>
                                        <td><strong>{{ $cost['month'] }}</strong></td>
                                        <td>{{ $cost['shipment_count'] }}</td>
                                        <td>{{ showAmount($cost['shipping_cost']) }}</td>
                                        <td>{{ showAmount($cost['insurance_cost'] ?? 0) }}</td>
                                        <td>{{ showAmount($cost['additional_fees'] ?? 0) }}</td>
                                        <td><strong>{{ showAmount($cost['total']) }}</strong></td>
                                        <td>{{ showAmount($cost['average_per_shipment']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <td><strong>@lang('Total')</strong></td>
                                    <td><strong>{{ array_sum(array_column($costs, 'shipment_count')) }}</strong></td>
                                    <td colspan="3"></td>
                                    <td><strong>{{ showAmount(array_sum(array_column($costs, 'total'))) }}</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
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
    var ctx = document.getElementById('detailedCostsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json(array_column($costs, 'month')),
            datasets: [{
                label: 'Total Costs',
                data: @json(array_column($costs, 'total')),
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 1
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
</script>
@endpush
