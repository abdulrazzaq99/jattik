@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.branch.manager.store', $manager->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label>@lang('Select Branch')</label>
                                <select class="form-control select2" data-minimum-results-for-search="-1" name="branch"
                                    required>
                                    <option value="">@lang('Select One')</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" @selected($branch->id == $manager->branch_id)>
                                            {{ __($branch->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-lg-6">
                                <label>@lang('Username')</label>
                                <input type="text" class="form-control" name="username" value="{{ $manager->username }}"
                                    required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label>@lang('First Name')</label>
                                <input type="text" class="form-control" name="firstname"
                                    value="{{ $manager->firstname }}" required>
                            </div>

                            <div class="form-group col-lg-6">
                                <label>@lang('Last Name')</label>
                                <input type="text" class="form-control" name="lastname" value="{{ $manager->lastname }}"
                                    required>
                            </div>
                        </div>
                        <div class="row">

                            <div class="form-group col-lg-6">
                                <label>@lang('Email Address')</label>
                                <input type="email" class="form-control" name="email" value="{{ $manager->email }}"
                                    required>
                            </div>

                            <div class="form-group col-lg-6">
                                <label>@lang('Phone')</label>
                                <input type="text" class="form-control" name="mobile" value="{{ $manager->mobile }}"
                                    required>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn--primary btn-block h-45 w-100">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.branch.manager.index') }}" />
@endpush
