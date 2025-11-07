@extends('customer.layouts.app')

@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Add New Address')</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('customer.addresses.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">@lang('Address Label') <span class="text-muted">(@lang('Optional'))</span></label>
                                <input type="text" class="form-control" name="label" value="{{ old('label') }}" placeholder="e.g., Home, Office, Warehouse">
                                <small class="text-muted">@lang('Give this address a memorable name')</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">@lang('Full Name')</label>
                                <input type="text" class="form-control @error('full_name') is-invalid @enderror" name="full_name" value="{{ old('full_name') }}" required>
                                @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">@lang('Phone Number')</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">@lang('Email Address') <span class="text-muted">(@lang('Optional'))</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label required">@lang('Address Line 1')</label>
                                <input type="text" class="form-control @error('address_line_1') is-invalid @enderror" name="address_line_1" value="{{ old('address_line_1') }}" placeholder="Street address, P.O. box, company name" required>
                                @error('address_line_1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">@lang('Address Line 2') <span class="text-muted">(@lang('Optional'))</span></label>
                                <input type="text" class="form-control @error('address_line_2') is-invalid @enderror" name="address_line_2" value="{{ old('address_line_2') }}" placeholder="Apartment, suite, unit, building, floor">
                                @error('address_line_2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">@lang('City')</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" name="city" value="{{ old('city') }}" required>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">@lang('State/Province') <span class="text-muted">(@lang('Optional'))</span></label>
                                <input type="text" class="form-control @error('state') is-invalid @enderror" name="state" value="{{ old('state') }}">
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">@lang('Postal Code')</label>
                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror" name="postal_code" value="{{ old('postal_code') }}" required>
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">@lang('Country')</label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror" name="country" value="{{ old('country') }}" required>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">
                                        @lang('Set as default address')
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn--primary w-100">
                                    <i class="las la-save"></i> @lang('Save Address')
                                </button>
                                <a href="{{ route('customer.addresses.index') }}" class="btn btn--secondary w-100 mt-2">
                                    <i class="las la-times"></i> @lang('Cancel')
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
