@extends('staff.layouts.app')

@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Calculate Shipping Fee') - {{ $holding->holding_code }}</h5>
                </div>
                <div class="card-body">
                    {{-- Holding Summary --}}
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>@lang('Customer'):</strong><br>
                                {{ $holding->customer->fullname }}
                            </div>
                            <div class="col-md-3">
                                <strong>@lang('Total Weight'):</strong><br>
                                {{ number_format($holding->total_weight, 2) }} kg
                            </div>
                            <div class="col-md-3">
                                <strong>@lang('Packages'):</strong><br>
                                {{ $holding->package_count }}
                            </div>
                            <div class="col-md-3">
                                <strong>@lang('Declared Value'):</strong><br>
                                ${{ number_format($holding->packages->sum('declared_value'), 2) }}
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('staff.warehouse.calculate.fee.post', $holding->id) }}" method="POST">
                        @csrf

                        <div class="row">
                            {{-- Courier Selection --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label required">@lang('Select Courier')</label>
                                <select name="courier_configuration_id" class="form-control @error('courier_configuration_id') is-invalid @enderror" required>
                                    <option value="">@lang('Choose courier service')</option>
                                    @foreach($couriers as $courier)
                                        <option value="{{ $courier->id }}" {{ old('courier_configuration_id') == $courier->id ? 'selected' : '' }}>
                                            {{ $courier->name }} - Base: ${{ number_format($courier->base_rate, 2) }}, Per kg: ${{ number_format($courier->per_kg_rate, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('courier_configuration_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Addresses --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">@lang('Origin Address')</label>
                                <select name="origin_address_id" class="form-control @error('origin_address_id') is-invalid @enderror" required>
                                    <option value="">@lang('Select origin')</option>
                                    @foreach($customerAddresses as $address)
                                        <option value="{{ $address->id }}" {{ old('origin_address_id') == $address->id ? 'selected' : '' }}>
                                            {{ $address->label ?? 'Address' }} - {{ $address->city }}, {{ $address->country }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('origin_address_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">@lang('Destination Address')</label>
                                <select name="destination_address_id" class="form-control @error('destination_address_id') is-invalid @enderror" required>
                                    <option value="">@lang('Select destination')</option>
                                    @foreach($customerAddresses as $address)
                                        <option value="{{ $address->id }}" {{ old('destination_address_id') == $address->id ? 'selected' : '' }}>
                                            {{ $address->label ?? 'Address' }} - {{ $address->city }}, {{ $address->country }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('destination_address_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($customerAddresses->count() == 0)
                                    <small class="text-danger">@lang('Customer has no addresses. Please add addresses first.')</small>
                                @endif
                            </div>

                            {{-- Additional Fees --}}
                            <div class="col-md-12 mb-3">
                                <h6 class="border-bottom pb-2">@lang('Additional Fees') <span class="text-muted">(@lang('Optional'))</span></h6>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">@lang('Handling Fee ($)')</label>
                                <input type="number" step="0.01" name="handling_fee" class="form-control @error('handling_fee') is-invalid @enderror" value="{{ old('handling_fee', 0) }}" placeholder="0.00">
                                @error('handling_fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">@lang('Customs Fee ($)')</label>
                                <input type="number" step="0.01" name="customs_fee" class="form-control @error('customs_fee') is-invalid @enderror" value="{{ old('customs_fee', 0) }}" placeholder="0.00">
                                @error('customs_fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">@lang('Discount Amount ($)')</label>
                                <input type="number" step="0.01" name="discount_amount" class="form-control @error('discount_amount') is-invalid @enderror" value="{{ old('discount_amount', 0) }}" placeholder="0.00">
                                <small class="text-muted">@lang('Enter discount if applicable')</small>
                                @error('discount_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">@lang('Notes')</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Add any special notes or instructions">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn--primary w-100">
                                    <i class="las la-calculator"></i> @lang('Calculate & Save Fee')
                                </button>
                                <a href="{{ route('staff.warehouse.show', $holding->id) }}" class="btn btn--secondary w-100 mt-2">
                                    <i class="las la-times"></i> @lang('Cancel')
                                </a>
                            </div>
                        </div>
                    </form>

                    {{-- Help Section --}}
                    <div class="card mt-4 bg-light">
                        <div class="card-body">
                            <h6><i class="las la-info-circle"></i> @lang('Fee Calculation')</h6>
                            <ul class="mb-0">
                                <li>@lang('Base fee and weight-based fee are automatically calculated from courier rates')</li>
                                <li>@lang('Insurance is calculated based on declared value')</li>
                                <li>@lang('Add handling and customs fees as needed')</li>
                                <li>@lang('Apply discounts for premium customers or promotions')</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
