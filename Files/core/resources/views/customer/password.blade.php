@extends('customer.layouts.app')

@section('panel')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="card-title mb-4">@lang('Change Password')</h5>

                    <form action="{{ route('customer.password.update') }}" method="POST" class="disableSubmission">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">@lang('Current Password')</label>
                            <input type="password" class="form-control form--control" name="current_password" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">@lang('New Password')</label>
                            <input type="password" class="form-control form--control" name="password" required>
                            <small class="text-muted">@lang('Minimum 6 characters')</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">@lang('Confirm Password')</label>
                            <input type="password" class="form-control form--control" name="password_confirmation" required>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Update Password')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
