@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Full Name')</th>
                                    <th>@lang('Contact')</th>
                                    <th>@lang('Address')</th>
                                    <th>@lang('City')</th>
                                    <th>@lang('State')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $customer->fullname }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">
                                                {{ $customer->mobile }}
                                                <br>
                                                {{ $customer->email }}
                                            </span>
                                        </td>
                                        <td>{{ __($customer->address) }}</td>
                                        <td>{{ __($customer->city) }}</td>
                                        <td>{{ __($customer->state) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($customers->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($customers) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- import modal --}}
    <div class="modal fade" id="importModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Import Customer')</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="la la-times" aria-hidden="true"></i>
                    </button>
                </div>
                <form method="post" action="{{ route('admin.customer.import') }}" id="importForm"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body">
                        <div class="form-group">
                            <div class="alert alert-warning p-3" role="alert">
                                <p>
                                    @lang('The file you wish to upload has to be formatted as we provided template files.Any changes to these files will be considered as an invalid file format. Download links are provided below.')
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="fw-bold required" for="file">@lang('Select File')</label>
                            <input type="file" class="form-control" name="file" accept=".txt,.csv,.xlsx" id="file">
                            <div class="mt-1">
                                <small class="d-block">
                                    @lang('Supported files:')
                                    <b class="fw-bold">@lang('csv, excel, txt')</b>
                                </small>
                                <small>
                                    @lang('Download all of the template files from here')
                                    <a href="{{ asset('/assets/viseradmin/file_template/sample.csv') }}" title=""
                                        class="text--primary" download="" data-bs-original-title="Download csv file"
                                        target="_blank">
                                        <b>@lang('csv'),</b>
                                    </a>
                                    <a href="{{ asset('/assets/viseradmin/file_template/sample.xlsx') }}" title=""
                                        class="text--primary" download="" data-bs-original-title="Download excel file"
                                        target="_blank">
                                        <b>@lang('excel'),</b>
                                    </a>
                                    <a href="{{ asset('/assets/viseradmin/file_template/sample.txt') }}" title=""
                                        class="text--primary" download="" data-bs-original-title="Download txt file"
                                        target="_blank">
                                        <b>@lang('txt')</b>
                                    </a>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="Submit" class="btn btn--primary w-100 h-45">@lang('Upload')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- export modal --}}
    <div class="modal fade" id="exportModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Export Filter')</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="la la-close" aria-hidden="true"></i>
                    </button>
                </div>
                <form method="post" action="{{ route('admin.customer.export') }}" id="importForm"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="fw-bold">@lang('Export Column')</label>
                            <div class="row">
                                @foreach ($columns as $column)
                                    <div class="{{ $loop->last && $loop->odd ? 'col-lg-12' : 'col-lg-6' }} mb-3">
                                        <label>{{ __(keyToTitle($column)) }} </label>
                                        <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success"
                                            data-offstyle="-danger" data-bs-toggle="toggle" data-height="50"
                                            data-on="@lang('Yes')" data-off="@lang('No')" name="columns[]"
                                            value="{{ $column }}"
                                            {{ $column == 'created_at' || $column == 'updated_at' || $column == 'id' ? 'unchecked' : 'checked' }}>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="fw-bold">@lang('Order By')</label>
                            <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success"
                                data-offstyle="-danger" data-bs-toggle="toggle" data-height="50"
                                data-on="@lang('ASC')" data-off="@lang('DESC')" name="order_by">
                        </div>

                        <div class="form-group">
                            <label class="fw-bold">@lang('Export Item')</label>
                            <select class="form-control form-control-lg" name="export_item">
                                <option value="10">@lang('10')</option>
                                <option value="50">@lang('50')</option>
                                <option value="100">@lang('100')</option>
                                @if ($customers->total() > 100)
                                    <option value="{{ $customers->total() }}">{{ $customers->total() }}
                                        @lang('Customers')
                                    </option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="Submit" class="btn btn--primary w-100 h-45"
                            data-bs-dismiss="modal">@lang('Export')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search here..." />

    <button type="button" class="btn  btn-outline--info h-45 importBtn">
        <i class="las la-cloud-upload-alt"></i> @lang('Import')
    </button>

    <button type="button" class="btn  btn-outline--warning h-45 exportBtn">
        <i class="las la-cloud-download-alt"></i> @lang('Export')
    </button>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.importBtn').on('click', function() {
                var modal = $('#importModal');
                $('#importModal').modal('show');
            });

            $('.exportBtn').on('click', function() {
                var modal = $('#exportModal');
                $('#exportModal').modal('show');
            });
        })(jQuery);
    </script>
@endpush
