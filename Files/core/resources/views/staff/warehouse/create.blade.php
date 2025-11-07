@extends('staff.layouts.app')

@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Create New Warehouse Holding')</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('staff.warehouse.store') }}" method="POST" id="warehouseForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">@lang('Customer')</label>
                                <select name="customer_id" class="form-control @error('customer_id') is-invalid @enderror" required>
                                    <option value="">@lang('Select Customer')</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->fullname }} - {{ $customer->mobile }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">@lang('Received Date')</label>
                                <input type="date" name="received_date" class="form-control @error('received_date') is-invalid @enderror" value="{{ old('received_date', date('Y-m-d')) }}">
                                @error('received_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">@lang('Scheduled Ship Date')</label>
                                <input type="date" name="scheduled_ship_date" class="form-control @error('scheduled_ship_date') is-invalid @enderror" value="{{ old('scheduled_ship_date') }}">
                                <small class="text-muted">@lang('Leave empty if not yet scheduled')</small>
                                @error('scheduled_ship_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">@lang('Notes')</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Packages Section --}}
                            <div class="col-md-12">
                                <hr>
                                <h6 class="mb-3">@lang('Packages') <span class="text-danger">*</span></h6>
                                <div id="packagesContainer">
                                    <div class="package-item card mb-3">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12 mb-2">
                                                    <label class="form-label required">@lang('Description')</label>
                                                    <input type="text" name="packages[0][description]" class="form-control" required>
                                                </div>

                                                <div class="col-md-3 mb-2">
                                                    <label class="form-label required">@lang('Weight (kg)')</label>
                                                    <input type="number" step="0.1" name="packages[0][weight]" class="form-control" required>
                                                </div>

                                                <div class="col-md-3 mb-2">
                                                    <label class="form-label">@lang('Length (cm)')</label>
                                                    <input type="number" step="0.1" name="packages[0][length]" class="form-control">
                                                </div>

                                                <div class="col-md-3 mb-2">
                                                    <label class="form-label">@lang('Width (cm)')</label>
                                                    <input type="number" step="0.1" name="packages[0][width]" class="form-control">
                                                </div>

                                                <div class="col-md-3 mb-2">
                                                    <label class="form-label">@lang('Height (cm)')</label>
                                                    <input type="number" step="0.1" name="packages[0][height]" class="form-control">
                                                </div>

                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">@lang('Declared Value ($)')</label>
                                                    <input type="number" step="0.01" name="packages[0][declared_value]" class="form-control">
                                                </div>

                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">@lang('Category')</label>
                                                    <input type="text" name="packages[0][category]" class="form-control" placeholder="e.g., Electronics, Clothing">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-sm btn--success" id="addPackageBtn">
                                    <i class="las la-plus"></i> @lang('Add Another Package')
                                </button>
                            </div>

                            <div class="col-md-12 mt-4">
                                <button type="submit" class="btn btn--primary w-100">
                                    <i class="las la-save"></i> @lang('Create Holding')
                                </button>
                                <a href="{{ route('staff.warehouse.index') }}" class="btn btn--secondary w-100 mt-2">
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

@push('script')
<script>
    (function ($) {
        "use strict";

        let packageIndex = 1;

        $('#addPackageBtn').on('click', function() {
            const packageHtml = `
                <div class="package-item card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Package ${packageIndex + 1}</h6>
                            <button type="button" class="btn btn-sm btn--danger remove-package">
                                <i class="las la-times"></i>
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <label class="form-label required">Description</label>
                                <input type="text" name="packages[${packageIndex}][description]" class="form-control" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label required">Weight (kg)</label>
                                <input type="number" step="0.1" name="packages[${packageIndex}][weight]" class="form-control" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Length (cm)</label>
                                <input type="number" step="0.1" name="packages[${packageIndex}][length]" class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Width (cm)</label>
                                <input type="number" step="0.1" name="packages[${packageIndex}][width]" class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Height (cm)</label>
                                <input type="number" step="0.1" name="packages[${packageIndex}][height]" class="form-control">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Declared Value ($)</label>
                                <input type="number" step="0.01" name="packages[${packageIndex}][declared_value]" class="form-control">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Category</label>
                                <input type="text" name="packages[${packageIndex}][category]" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('#packagesContainer').append(packageHtml);
            packageIndex++;
        });

        $(document).on('click', '.remove-package', function() {
            $(this).closest('.package-item').remove();
        });
    })(jQuery);
</script>
@endpush
