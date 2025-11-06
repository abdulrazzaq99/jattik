@extends('customer.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Tracking Code')</th>
                                    <th>@lang('Sender')</th>
                                    <th>@lang('Origin Branch')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($couriers as $courier)
                                    <tr>
                                        <td>
                                            <span class="fw-bold text--base">{{ $courier->code }}</span>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $courier->senderCustomer->fullname ?? 'N/A' }}</strong><br>
                                                <small class="text-muted">{{ $courier->senderCustomer->mobile ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $courier->senderBranch->name ?? 'N/A' }}
                                        </td>
                                        <td>
                                            @if($courier->status == 0)
                                                <span class="badge badge--warning">@lang('Queue')</span>
                                            @elseif($courier->status == 1)
                                                <span class="badge badge--primary">@lang('In Transit')</span>
                                            @elseif($courier->status == 2)
                                                <span class="badge badge--info">@lang('Delivery Queue')</span>
                                            @elseif($courier->status == 3)
                                                <span class="badge badge--success">@lang('Delivered')</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ showDateTime($courier->created_at, 'd M, Y') }}<br>
                                            <small class="text-muted">{{ showDateTime($courier->created_at, 'h:i A') }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('customer.track') }}?tracking_code={{ $courier->code }}" class="btn btn-sm btn-outline--primary">
                                                <i class="las la-search-location"></i> @lang('Track')
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <div class="py-5">
                                                <i class="las la-inbox text-muted" style="font-size: 64px;"></i>
                                                <h5 class="mt-3 text-muted">@lang('No Received Couriers')</h5>
                                                <p class="text-muted">@lang('You have not received any couriers yet.')</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($couriers->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($couriers) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
