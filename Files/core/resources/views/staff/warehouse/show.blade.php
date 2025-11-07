@extends('staff.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">@lang('Holding Details') - {{ $holding->holding_code }}</h5>
                    <a href="{{ route('staff.warehouse.index') }}" class="btn btn-sm btn--secondary">
                        <i class="las la-arrow-left"></i> @lang('Back')
                    </a>
                </div>
                <div class="card-body">
                    {{-- Holding Info --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">@lang('Customer'):</td>
                                    <td>{{ $holding->customer->fullname }} ({{ $holding->customer->mobile }})</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Status'):</td>
                                    <td>{!! $holding->status_badge !!}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Received Date'):</td>
                                    <td>{{ $holding->received_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Max Holding Date'):</td>
                                    <td>{{ $holding->max_holding_date->format('M d, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">@lang('Total Packages'):</td>
                                    <td><span class="badge badge--info">{{ $holding->package_count }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Total Weight'):</td>
                                    <td>{{ number_format($holding->total_weight, 2) }} kg</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">@lang('Days Remaining'):</td>
                                    <td>
                                        @php $daysRemaining = $holding->days_remaining; @endphp
                                        @if($daysRemaining > 30)
                                            <span class="badge badge--success">{{ $daysRemaining }} days</span>
                                        @elseif($daysRemaining > 7)
                                            <span class="badge badge--warning">{{ $daysRemaining }} days</span>
                                        @else
                                            <span class="badge badge--danger">{{ $daysRemaining }} days</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Actions --}}
                    @if($holding->status == 0)
                    <div class="mb-4">
                        <a href="{{ route('staff.warehouse.calculate.fee', $holding->id) }}" class="btn btn--primary">
                            <i class="las la-calculator"></i> @lang('Calculate Shipping Fee')
                        </a>
                        <form action="{{ route('staff.warehouse.consolidate', $holding->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn--success confirmationBtn" data-question="Mark this holding as consolidated and ready?">
                                <i class="las la-check"></i> @lang('Mark as Ready')
                            </button>
                        </form>
                    </div>
                    @elseif($holding->status == 1)
                    <div class="mb-4">
                        <button type="button" class="btn btn--success" data-bs-toggle="modal" data-bs-target="#shipModal">
                            <i class="las la-shipping-fast"></i> @lang('Mark as Shipped')
                        </button>
                    </div>
                    @endif

                    {{-- Packages --}}
                    <h6 class="mb-3">@lang('Packages')</h6>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('Code')</th>
                                    <th>@lang('Description')</th>
                                    <th>@lang('Weight')</th>
                                    <th>@lang('Dimensions')</th>
                                    <th>@lang('Value')</th>
                                    @if($holding->status == 0)
                                    <th>@lang('Action')</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($holding->packages as $package)
                                    <tr>
                                        <td><code>{{ $package->package_code }}</code></td>
                                        <td>{{ $package->description }}</td>
                                        <td>{{ number_format($package->weight, 2) }} kg</td>
                                        <td>
                                            @if($package->length)
                                                {{ $package->length }} × {{ $package->width }} × {{ $package->height }} cm
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>${{ number_format($package->declared_value, 2) }}</td>
                                        @if($holding->status == 0)
                                        <td>
                                            <form action="{{ route('staff.warehouse.remove.package', [$holding->id, $package->id]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn--danger confirmationBtn" data-question="Remove this package?">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">@lang('No packages')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Shipping Quotes --}}
                    @if($holding->shippingQuotes->count() > 0)
                    <h6 class="mb-3 mt-4">@lang('Shipping Quotes')</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>@lang('Quote #')</th>
                                    <th>@lang('Courier')</th>
                                    <th>@lang('Total Fee')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Created')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($holding->shippingQuotes as $quote)
                                    <tr>
                                        <td>{{ $quote->quote_number }}</td>
                                        <td>{{ $quote->courier_name }}</td>
                                        <td>${{ number_format($quote->total_fee, 2) }}</td>
                                        <td>{!! $quote->status_badge !!}</td>
                                        <td>{{ $quote->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Ship Modal --}}
    <div class="modal fade" id="shipModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('staff.warehouse.mark.shipped', $holding->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Mark as Shipped')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">@lang('Actual Ship Date')</label>
                            <input type="date" name="actual_ship_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                        <button type="submit" class="btn btn--success">@lang('Confirm Shipment')</button>
                    </div>
                </form>
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
            var question = $(this).data('question') || 'Are you sure?';
            if (confirm(question)) {
                form.submit();
            }
        });
    })(jQuery);
</script>
@endpush
