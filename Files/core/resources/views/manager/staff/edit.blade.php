@extends('manager.layouts.app')

@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('manager.staff.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $staff->id }}">
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label>@lang('First Name')</label>
                                <input type="text" class="form-control" name="firstname" value="{{ $staff->firstname }}" required>
                            </div>
                            <div class="form-group col-lg-4">
                                <label>@lang('Last Name')</label>
                                <input type="text" class="form-control" value="{{ $staff->lastname }}" name="lastname" required>
                            </div>
                            <div class="form-group col-lg-4">
                                <label>@lang('Username')</label>
                                <input type="text" class="form-control" name="username" value="{{ $staff->username }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label>@lang('Email Address')</label>
                                <input type="email" class="form-control" name="email" value="{{ $staff->email }}" required>
                            </div>
                            <div class="form-group col-lg-6">
                                <label>@lang('Phone')</label>
                                <input type="text" class="form-control" name="mobile" value="{{ $staff->mobile }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn--primary w-100 h-45"> @lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.staff.index') }}" />
@endpush
