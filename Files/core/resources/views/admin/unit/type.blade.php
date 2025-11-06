@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card  ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Unit')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Last Update')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($types as $type)
                                    <tr>
                                        <td><span class="fw-bold">{{ __($type->name) }}</span></td>
                                        <td><span>{{ __($type->unit->name) }}</span></td>
                                        <td>
                                            <span>{{ showAmount($type->price) }}</span>
                                        </td>
                                        <td>
                                            @php echo $type->statusBadge; @endphp
                                        </td>
                                        <td>
                                            <span class="d-block">{{ showDateTime($type->updated_at) }}</span>
                                            <span>{{ diffForHumans($type->updated_at) }}</span>
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <button type="button" class="btn btn-sm btn-outline--primary  updateUnit" data-id="{{ $type->id }}"
                                                    data-name="{{ $type->name }}" data-price="{{ getAmount($type->price) }}"
                                                    data-unit="{{ $type->unit_id }}"><i class="las la-pen"></i>@lang('Edit')</button>

                                                @if ($type->status == Status::DISABLE)
                                                    <button type="button" class="btn btn-sm btn-outline--success confirmationBtn"
                                                        data-action="{{ route('admin.courier.unit.type.status', $type->id) }}"
                                                        data-question="@lang('Are you sure to enable this type?')">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-action="{{ route('admin.courier.unit.type.status', $type->id) }}"
                                                        data-question="@lang('Are you sure to disable this type?')">
                                                        <i class="la la-eye-slash"></i>@lang('Disable')
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
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
                @if ($types->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($types) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="unitModel" class="modal fade">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Update Courier Type')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.courier.unit.type.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input type="text" class="form-control" name="name" placeholder="@lang('Enter Name')" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Select Unit')</label>
                            <select class="select2 form-control" name="unit" data-minimum-results-for-search="-1" required>
                                <option value="">@lang('Select One')</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ __($unit->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('Price')</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="@lang('Enter Price')" name="price" required>
                                <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-sm btn-outline--primary addUnit">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.addUnit').on('click', function() {
                var modal = $('#unitModel');
                modal.find('input[name=id]').val('');
                modal.find('input[name=name]').val('');
                modal.find('input[name=price]').val('');
                modal.find('select[name=unit]').val('').change();
                modal.modal('show');
            });

            $('.updateUnit').on('click', function() {
                var modal = $('#unitModel');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('input[name=name]').val($(this).data('name'));
                modal.find('input[name=price]').val($(this).data('price'));
                modal.find('select[name=unit]').val($(this).data('unit')).change();
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
