@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Username')</th>
                                    <th>@lang('Email')</th>
                                    @if ($adminId == Status::SUPER_ADMIN_ID)
                                        <th>@lang('Action')</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($admins as $admin)
                                    <tr>
                                        <td><span>{{ __($admin->name) }}</span></td>
                                        <td><span>{{ $admin->username }}</span></td>
                                        <td>{{ $admin->email }}</td>
                                        @if ($adminId == Status::SUPER_ADMIN_ID)
                                            <td>
                                                @if ($admin->id != Status::SUPER_ADMIN_ID)
                                                    <div class="button--group">
                                                        <button class="btn btn-sm btn-outline--primary editBtn" data-name="{{ $admin->name }}"
                                                            data-username="{{ $admin->username }}" data-email="{{ $admin->email }}"
                                                            data-id="{{ $admin->id }}">
                                                            <i class="la la-pen"></i>@lang('Edit')
                                                        </button>
                                                        <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                            data-action="{{ route('admin.remove', $admin->id) }}" data-question="@lang('Are you sure to remove this admin?')">
                                                            <i class="las la-trash"></i> @lang('Delete')
                                                        </button>
                                                    </div>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($admins->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($admins) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="manageAdmin">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('Create Admin')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button">
                        <i class="las la-times"></i></button>
                </div>
                <form class="resetForm" action="{{ route('admin.store') }}" method="post">
                    @csrf
                    <input name="id" type="hidden">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input class="form-control" name="name" type="text" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Email')</label>
                            <input class="form-control" name="email" type="email" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Username')</label>
                            <input class="form-control" name="username" type="text" required>
                        </div>
                        <div class="form-group pass">
                            <label>@lang('Password')</label>
                            <input class="form-control" name="password" type="password" required>
                        </div>
                        <div class="form-group confirmPassword">
                            <label>@lang('Confirm Password')</label>
                            <input class="form-control" name="password_confirmation" type="password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-sm btn-outline--primary addAdmin" type="button">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.editBtn').on('click', function() {
                let title = 'Update Admin'
                let name = $(this).data('name');
                let id = $(this).data('id');
                let username = $(this).data('username');
                let email = $(this).data('email');
                let modal = $('#manageAdmin');
                modal.find('.modal-title').text(title)
                modal.find('input[name=name]').val(name);
                modal.find('input[name=id]').val(id);
                modal.find('input[name=username]').val(username);
                modal.find('input[name=email]').val(email);
                modal.find('input[name="password"]').attr('required', false);
                modal.find('input[name="password_confirmation"]').attr('required', false);
                modal.find('.pass').addClass('d-none');
                modal.find('.confirmPassword').addClass('d-none');
                modal.modal('show');
            });

            $('.addAdmin').on('click', function() {
                let modal = $('#manageAdmin');
                $('.resetForm').trigger('reset');
                $(`input[name=id]`).val(0);
                modal.find('.pass').removeClass('d-none');
                modal.find('.confirmPassword').removeClass('d-none');
                modal.modal('show')
            });
        })(jQuery);
    </script>
@endpush
