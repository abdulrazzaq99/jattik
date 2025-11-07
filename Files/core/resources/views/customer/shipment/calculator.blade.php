@extends('customer.layouts.app')

@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-calculator"></i> @lang('Shipping Fee Calculator')
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="las la-info-circle"></i>
                        @lang('Compare shipping rates from multiple couriers and get instant estimates.')
                    </div>

                    <form action="{{ route('customer.shipment.calculator.estimate') }}" method="POST" id="calculatorForm">
                        @csrf

                        <div class="row">
                            {{-- Origin Address --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">@lang('Origin Address')</label>
                                <select name="origin_address_id" class="form-control @error('origin_address_id') is-invalid @enderror" required>
                                    <option value="">@lang('Select origin address')</option>
                                    @foreach($addresses as $address)
                                        <option value="{{ $address->id }}" {{ old('origin_address_id') == $address->id ? 'selected' : '' }}>
                                            {{ $address->label ?? 'Address' }} - {{ $address->city }}, {{ $address->country }}
                                            @if($address->is_default) (@lang('Default')) @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('origin_address_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($addresses->count() == 0)
                                    <small class="text-muted">
                                        <a href="{{ route('customer.addresses.create') }}">@lang('Add an address first')</a>
                                    </small>
                                @endif
                            </div>

                            {{-- Destination Address --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">@lang('Destination Address')</label>
                                <select name="destination_address_id" class="form-control @error('destination_address_id') is-invalid @enderror" required>
                                    <option value="">@lang('Select destination address')</option>
                                    @foreach($addresses as $address)
                                        <option value="{{ $address->id }}" {{ old('destination_address_id') == $address->id ? 'selected' : '' }}>
                                            {{ $address->label ?? 'Address' }} - {{ $address->city }}, {{ $address->country }}
                                            @if($address->is_default) (@lang('Default')) @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('destination_address_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Package Details --}}
                            <div class="col-md-12 mb-3">
                                <h6 class="border-bottom pb-2">@lang('Package Details')</h6>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">@lang('Weight (kg)')</label>
                                <input type="number" step="0.1" name="weight" class="form-control @error('weight') is-invalid @enderror" value="{{ old('weight') }}" placeholder="e.g., 5.5" required>
                                @error('weight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">@lang('Declared Value ($)') <span class="text-muted">(@lang('Optional'))</span></label>
                                <input type="number" step="0.01" name="declared_value" class="form-control @error('declared_value') is-invalid @enderror" value="{{ old('declared_value') }}" placeholder="e.g., 500.00">
                                @error('declared_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">@lang('Length (cm)') <span class="text-muted">(@lang('Optional'))</span></label>
                                <input type="number" step="0.1" name="length" class="form-control @error('length') is-invalid @enderror" value="{{ old('length') }}" placeholder="e.g., 30">
                                @error('length')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">@lang('Width (cm)') <span class="text-muted">(@lang('Optional'))</span></label>
                                <input type="number" step="0.1" name="width" class="form-control @error('width') is-invalid @enderror" value="{{ old('width') }}" placeholder="e.g., 20">
                                @error('width')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">@lang('Height (cm)') <span class="text-muted">(@lang('Optional'))</span></label>
                                <input type="number" step="0.1" name="height" class="form-control @error('height') is-invalid @enderror" value="{{ old('height') }}" placeholder="e.g., 15">
                                @error('height')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">@lang('Number of Packages')</label>
                                <input type="number" name="package_count" class="form-control @error('package_count') is-invalid @enderror" value="{{ old('package_count', 1) }}" min="1">
                                @error('package_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn--primary btn-lg w-100">
                                    <i class="las la-calculator"></i> @lang('Calculate Rates from All Couriers')
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Available Couriers Info --}}
                    <div class="mt-4">
                        <h6 class="mb-3">@lang('Available Courier Services')</h6>
                        <div class="row g-2">
                            @foreach($couriers as $courier)
                                <div class="col-md-4">
                                    <div class="courier-badge">
                                        <i class="las la-shipping-fast text-primary"></i>
                                        <span class="fw-bold">{{ $courier->name }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Help Section --}}
            <div class="card mt-4">
                <div class="card-body">
                    <h6><i class="las la-question-circle"></i> @lang('How to Use the Calculator')</h6>
                    <ol class="mb-0">
                        <li>@lang('Select your origin and destination addresses')</li>
                        <li>@lang('Enter package weight (required) and dimensions (optional)')</li>
                        <li>@lang('Add declared value if you need insurance')</li>
                        <li>@lang('Click calculate to compare rates from all available couriers')</li>
                        <li>@lang('Save your preferred quote for later reference')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .courier-badge {
        padding: 10px;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .courier-badge i {
        font-size: 20px;
    }
</style>
@endpush
