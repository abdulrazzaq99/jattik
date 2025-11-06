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
                                    <th>@lang('Name-Address')</th>
                                    <th>@lang('Email-Phone')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Creations Date')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($branches as $branch)
                                    <tr>
                                        <td>
                                            <span class="fw-bold d-block">{{ __($branch->name) }}</span>
                                            <small class="text-muted"><i>{{ __($branch->address) }}</i></span>
                                        </td>
                                        <td>
                                            <span class="d-block">{{ $branch->email }}</span>
                                            <span>{{ $branch->phone }}</span>
                                        </td>
                                        <td>@php echo $branch->statusBadge; @endphp</td>
                                        <td>
                                            <span class="d-block">{{ showDateTime($branch->created_at) }}</span>
                                            <span>{{ diffForHumans($branch->created_at) }}</span>
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <button type="button" class="btn btn-sm btn-outline--primary editBranch" data-id="{{ $branch->id }}"
                                                    data-name="{{ $branch->name }}" data-email="{{ $branch->email }}" data-phone="{{ $branch->phone }}"
                                                    data-address="{{ $branch->address }}">
                                                    <i class="las la-pen"></i>@lang('Edit')
                                                </button>

                                                @if ($branch->status == Status::DISABLE)
                                                    <button type="button" class="btn btn-sm btn-outline--success  confirmationBtn"
                                                        data-action="{{ route('admin.branch.status', $branch->id) }}" data-question="@lang('Are you sure to enable this branch?')">
                                                        <i class="la la-eye"></i>@lang('Enable')
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-action="{{ route('admin.branch.status', $branch->id) }}" data-question="@lang('Are you sure to disable this branch?')">
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
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($branches->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($branches) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>

    <div id="branchModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Create New Branch')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
                </div>
                <form action="{{ route('admin.branch.store') }}" class="resetForm" method="POST">
                    @csrf
                    <input type="hidden" name="id">

                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>

                        <div class="form-group">
                            <label>@lang('Email Address')</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>

                        <div class="form-group">
                            <label>@lang('Phone')</label>
                            <input type="text" class="form-control" name="phone" required>
                        </div>


                        <div class="form-group">
                            <label>@lang('Address')</label>
                            <input type="text" class="form-control" name="address" required>
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
    <x-search-form placeholder="Search here..." />
    <button class="btn  btn-outline--primary h-45 addNewBranch"><i class="las la-plus"></i>@lang('Add New')</button>
@endpush


@push('script')
    <script>
        (function($) {
            "use strict";
            $('.addNewBranch').on('click', function() {
                $('.resetForm').trigger('reset');
                $('#branchModel').modal('show');
            });
            $('.editBranch').on('click', function() {
                let title = "@lang('Update Branch')";
                var modal = $('#branchModel');
                let id = $(this).data('id');
                let name = $(this).data('name');
                let email = $(this).data('email');
                let phone = $(this).data('phone');
                let address = $(this).data('address');
                modal.find('.modal-title').text(title)
                modal.find('input[name=id]').val(id);
                modal.find('input[name=name]').val(name);
                modal.find('input[name=email]').val(email);
                modal.find('input[name=phone]').val(phone);
                modal.find('input[name=address]').val(address);
                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
