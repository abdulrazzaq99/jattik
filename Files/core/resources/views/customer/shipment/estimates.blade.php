@extends('customer.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">@lang('Shipping Rate Comparison')</h5>
                    <a href="{{ route('customer.shipment.calculator') }}" class="btn btn-sm btn--secondary">
                        <i class="las la-arrow-left"></i> @lang('Back to Calculator')
                    </a>
                </div>
                <div class="card-body">
                    {{-- Shipment Summary --}}
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>@lang('Weight'):</strong> {{ number_format($shipmentData['weight'], 2) }} kg
                            </div>
                            <div class="col-md-3">
                                <strong>@lang('Packages'):</strong> {{ $shipmentData['package_count'] }}
                            </div>
                            <div class="col-md-3">
                                <strong>@lang('Declared Value'):</strong> ${{ number_format($shipmentData['declared_value'], 2) }}
                            </div>
                            <div class="col-md-3">
                                @if(isset($shipmentData['dimensions']['length']) && $shipmentData['dimensions']['length'])
                                    <strong>@lang('Dimensions'):</strong>
                                    {{ $shipmentData['dimensions']['length'] }} ×
                                    {{ $shipmentData['dimensions']['width'] }} ×
                                    {{ $shipmentData['dimensions']['height'] }} cm
                                @endif
                            </div>
                        </div>
                    </div>

                    @if(count($quotes) > 0)
                        <div class="row g-4">
                            @foreach($quotes as $quote)
                                <div class="col-md-6 col-lg-4">
                                    <div class="quote-card">
                                        <div class="quote-header">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h5 class="mb-1">{{ $quote['courier']->name }}</h5>
                                                    <small class="text-muted">{{ $quote['courier']->description }}</small>
                                                </div>
                                                @if($loop->first)
                                                    <span class="badge badge--success">Best Rate</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="quote-body">
                                            <div class="total-price">
                                                <span class="currency">$</span>
                                                <span class="amount">{{ number_format($quote['rate']['total_fee'], 2) }}</span>
                                            </div>

                                            <div class="price-breakdown mt-3">
                                                <div class="breakdown-item">
                                                    <span>@lang('Base Fee')</span>
                                                    <span>${{ number_format($quote['rate']['base_fee'], 2) }}</span>
                                                </div>
                                                <div class="breakdown-item">
                                                    <span>@lang('Weight Fee')</span>
                                                    <span>${{ number_format($quote['rate']['weight_fee'], 2) }}</span>
                                                </div>
                                                @if($quote['rate']['insurance_fee'] > 0)
                                                    <div class="breakdown-item">
                                                        <span>@lang('Insurance')</span>
                                                        <span>${{ number_format($quote['rate']['insurance_fee'], 2) }}</span>
                                                    </div>
                                                @endif
                                                @if(isset($quote['rate']['fuel_surcharge']) && $quote['rate']['fuel_surcharge'] > 0)
                                                    <div class="breakdown-item">
                                                        <span>@lang('Fuel Surcharge')</span>
                                                        <span>${{ number_format($quote['rate']['fuel_surcharge'], 2) }}</span>
                                                    </div>
                                                @endif
                                            </div>

                                            @if(isset($quote['rate']['transit_days']) && $quote['rate']['transit_days'])
                                                <div class="mt-3 text-center">
                                                    <small class="text-muted">
                                                        <i class="las la-clock"></i>
                                                        @lang('Estimated Transit:') {{ $quote['rate']['transit_days'] }} @lang('days')
                                                    </small>
                                                </div>
                                            @endif

                                            @if(isset($quote['rate']['calculation_method']) && $quote['rate']['calculation_method'] == 'manual')
                                                <div class="mt-2 text-center">
                                                    <small class="badge badge--warning">@lang('Estimated Rate')</small>
                                                </div>
                                            @else
                                                <div class="mt-2 text-center">
                                                    <small class="badge badge--success">@lang('Live Rate')</small>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="quote-footer">
                                            <form action="{{ route('customer.shipment.calculator.save') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="courier_configuration_id" value="{{ $quote['courier']->id }}">
                                                <input type="hidden" name="origin_address_id" value="{{ $shipmentData['origin_address']->id }}">
                                                <input type="hidden" name="destination_address_id" value="{{ $shipmentData['destination_address']->id }}">
                                                <input type="hidden" name="weight" value="{{ $shipmentData['weight'] }}">
                                                <input type="hidden" name="declared_value" value="{{ $shipmentData['declared_value'] }}">
                                                <input type="hidden" name="package_count" value="{{ $shipmentData['package_count'] }}">
                                                <button type="submit" class="btn btn--primary w-100">
                                                    <i class="las la-save"></i> @lang('Save This Quote')
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="text-center mt-4">
                            <p class="text-muted">
                                <i class="las la-info-circle"></i>
                                @lang('Rates are estimates and may vary based on actual package inspection and destination.')
                            </p>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="las la-exclamation-triangle" style="font-size: 64px; color: #ff9800;"></i>
                            <p class="text-muted mt-3">@lang('No rates available for this shipment. Please try different parameters or contact support.')</p>
                            <a href="{{ route('customer.shipment.calculator') }}" class="btn btn--primary mt-2">
                                <i class="las la-calculator"></i> @lang('Try Again')
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .quote-card {
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .quote-card:hover {
        border-color: var(--base-color);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        transform: translateY(-5px);
    }
    .quote-header {
        background: #f8f9fa;
        padding: 20px;
        border-bottom: 1px solid #e0e0e0;
    }
    .quote-body {
        padding: 20px;
        flex: 1;
    }
    .quote-footer {
        padding: 20px;
        border-top: 1px solid #e0e0e0;
        background: #fff;
    }
    .total-price {
        text-align: center;
        font-size: 48px;
        font-weight: bold;
        color: var(--base-color);
        line-height: 1;
    }
    .total-price .currency {
        font-size: 24px;
        vertical-align: top;
    }
    .price-breakdown {
        border-top: 1px dashed #e0e0e0;
        padding-top: 15px;
    }
    .breakdown-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 14px;
    }
    .breakdown-item:not(:last-child) {
        border-bottom: 1px solid #f0f0f0;
    }
</style>
@endpush
