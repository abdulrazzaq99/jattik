@extends('customer.layouts.app')

@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-4 mb-30">
            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="mb-3">@lang('Profile Information')</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Full Name')
                            <span class="fw-bold">{{ $customer->fullname }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Email')
                            <span class="fw-bold">{{ $customer->email }}</span>
                            @if($customer->isEmailVerified())
                                <span class="badge badge--success ms-2"><i class="las la-check-circle"></i></span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Mobile')
                            <span class="fw-bold">{{ $customer->full_mobile }}</span>
                            @if($customer->isMobileVerified())
                                <span class="badge badge--success ms-2"><i class="las la-check-circle"></i></span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Status')
                            @if($customer->isActive())
                                <span class="badge badge--success">@lang('Active')</span>
                            @else
                                <span class="badge badge--danger">@lang('Inactive')</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Member Since')
                            <span>{{ showDateTime($customer->created_at, 'd M, Y') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Last Login')
                            <span>{{ $customer->last_login_at ? showDateTime($customer->last_login_at) : 'N/A' }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            @if($virtualAddress)
            <div class="card b-radius--10 overflow-hidden box--shadow1 mt-30">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">@lang('Virtual Address')</h5>
                        <span class="badge badge--success">@lang('Active')</span>
                    </div>
                    <div class="virtual-address-box p-3 bg--light rounded">
                        <p class="mb-2 text-muted small">@lang('Address Code')</p>
                        <h4 class="text--base mb-3">{{ $virtualAddress->address_code }}</h4>
                        <p class="mb-1 text-muted small">@lang('Full Address')</p>
                        <div class="virtual-address-text" style="white-space: pre-line; font-size: 14px;">{{ $virtualAddress->full_address }}</div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="las la-calendar"></i>
                            @lang('Assigned on') {{ showDateTime($virtualAddress->assigned_at, 'd M, Y') }}
                        </small>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-8 mb-30">
            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="card-title mb-3">@lang('Update Profile')</h5>

                    <form action="{{ route('customer.profile.update') }}" method="POST" class="disableSubmission">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">@lang('First Name')</label>
                                    <input type="text" class="form-control form--control" name="firstname" value="{{ old('firstname', $customer->firstname) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">@lang('Last Name')</label>
                                    <input type="text" class="form-control form--control" name="lastname" value="{{ old('lastname', $customer->lastname) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">@lang('Email Address')</label>
                            <input type="email" class="form-control form--control" value="{{ $customer->email }}" readonly>
                            <small class="text-muted">@lang('Email cannot be changed')</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">@lang('Mobile Number')</label>
                            <input type="text" class="form-control form--control" value="{{ $customer->full_mobile }}" readonly>
                            <small class="text-muted">@lang('Mobile number cannot be changed')</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">@lang('Address')</label>
                            <textarea class="form-control form--control" name="address" rows="3" required>{{ old('address', $customer->address) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">@lang('City')</label>
                                    <input type="text" class="form-control form--control" name="city" value="{{ old('city', $customer->city) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">@lang('State/Province')</label>
                                    <input type="text" class="form-control form--control" name="state" value="{{ old('state', $customer->state) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">@lang('Postal Code')</label>
                                    <input type="text" class="form-control form--control" name="postal_code" value="{{ old('postal_code', $customer->postal_code) }}" maxlength="5" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn--primary w-100 h-45">
                                <i class="las la-save"></i> @lang('Update Profile')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
