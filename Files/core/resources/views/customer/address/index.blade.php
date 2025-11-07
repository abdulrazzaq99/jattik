@extends('customer.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">@lang('My Addresses')</h5>
                    <a href="{{ route('customer.addresses.create') }}" class="btn btn-sm btn--primary">
                        <i class="las la-plus"></i> @lang('Add New Address')
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($addresses->count() > 0)
                        <div class="table-responsive--sm table-responsive">
                            <table class="table table--light style--two">
                                <thead>
                                    <tr>
                                        <th>@lang('Label')</th>
                                        <th>@lang('Name & Contact')</th>
                                        <th>@lang('Address')</th>
                                        <th>@lang('Status')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($addresses as $address)
                                        <tr>
                                            <td>
                                                <span class="fw-bold">{{ $address->label ?? 'Address' }}</span>
                                                @if($address->is_default)
                                                    <span class="badge badge--success">@lang('Default')</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="d-block fw-bold">{{ $address->full_name }}</span>
                                                <span class="small text-muted">{{ $address->phone }}</span>
                                                @if($address->email)
                                                    <br><span class="small text-muted">{{ $address->email }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="small">
                                                    {{ $address->address_line_1 }}<br>
                                                    @if($address->address_line_2)
                                                        {{ $address->address_line_2 }}<br>
                                                    @endif
                                                    {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}<br>
                                                    {{ $address->country }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($address->is_active)
                                                    <span class="badge badge--success">@lang('Active')</span>
                                                @else
                                                    <span class="badge badge--warning">@lang('Inactive')</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="button-group">
                                                    @if(!$address->is_default)
                                                        <form action="{{ route('customer.addresses.set.default', $address->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn--info" title="@lang('Set as Default')">
                                                                <i class="las la-check-circle"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('customer.addresses.edit', $address->id) }}" class="btn btn-sm btn--primary" title="@lang('Edit')">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                    @if(!$address->is_default)
                                                        <form action="{{ route('customer.addresses.destroy', $address->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn--danger confirmationBtn" title="@lang('Delete')">
                                                                <i class="las la-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center p-5">
                            <i class="las la-map-marked-alt" style="font-size: 48px; color: #ccc;"></i>
                            <p class="text-muted mt-3">@lang('No addresses found. Add your first address!')</p>
                            <a href="{{ route('customer.addresses.create') }}" class="btn btn--primary mt-2">
                                <i class="las la-plus"></i> @lang('Add Address')
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function ($) {
            "use strict";
            $('.confirmationBtn').on('click', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                var message = 'Are you sure you want to delete this address?';

                if (confirm(message)) {
                    form.submit();
                }
            });
        })(jQuery);
    </script>
@endpush
